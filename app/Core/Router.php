<?php

namespace App\Core;

use PDO;

/**
 * Class Router
 * Handles incoming requests and dispatches them to the appropriate controller action.
 * This version is refactored to be self-contained and use modern syntax.
 */
class Router
{
    /**
     * @var array The routes defined for the application. Organized by HTTP method.
     */
    protected array $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * Router constructor.
     * It no longer requires a Request object to be injected.
     */
    public function __construct()
    {
        // El constructor ahora está vacío.
    }

    /**
     * Registers a GET route.
     *
     * @param string $uri The URI to match.
     * @param array $controllerAction An array containing [Controller::class, 'methodName'].
     * @return void
     */
    public function get(string $uri, array $controllerAction): void
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri The URI to match.
     * @param array $controllerAction An array containing [Controller::class, 'methodName'].
     * @return void
     */
    public function post(string $uri, array $controllerAction): void
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    /**
     * Resolves the current request and dispatches to the correct controller.
     *
     * @param PDO $pdo The database connection instance, to be injected into controllers.
     * @return void
     */
    public function resolve(PDO $pdo): void
    {
        // Obtiene la URI y el método directamente de las superglobales de PHP.
        $uri = strtok($_SERVER['REQUEST_URI'], '?');
        $method = $_SERVER['REQUEST_METHOD'];

        foreach ($this->routes[$method] as $routePattern => $controllerAction) {
            
            // La lógica para rutas dinámicas (ej. /profile/{id}) se mantiene.
            $regexPattern = preg_replace('/\{([a-zA-Z_]+)\}/', '(?P<$1>[^/]+)', $routePattern);
            $regexPattern = '#^' . $regexPattern . '$#';

            if (preg_match($regexPattern, $uri, $matches)) {
                
                // Extrae los parámetros de la ruta (ej. 'id' => '123').
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // Crea un nuevo objeto Request para esta petición específica.
                $request = new Request($_GET, $_POST, $_FILES, $_SERVER, $params);

                // CAMBIO: Se adapta a la nueva sintaxis de array [Controller::class, 'action'].
                $controllerName = $controllerAction[0];
                $action = $controllerAction[1];

                if (!class_exists($controllerName)) {
                    $this->abort(404, "Controlador no encontrado: $controllerName");
                }

                // Inyecta la conexión PDO al constructor del controlador.
                $controller = new $controllerName($pdo);

                if (!method_exists($controller, $action)) {
                    $this->abort(404, "Método no encontrado: $action en el controlador $controllerName");
                }

                // Llama al método del controlador, pasándole el objeto Request.
                // Esto permite al controlador acceder a los datos de la petición.
                $controller->$action($request);
                return; // Termina la ejecución una vez que se encuentra la ruta.
            }
        }

        $this->abort(404, "No se encontró una ruta para la URI: $uri");
    }

    /**
     * Helper para mostrar una página de error.
     * @param int $code El código de respuesta HTTP (ej. 404).
     * @param string $message El mensaje a mostrar.
     */
    protected function abort(int $code, string $message): void
    {
        http_response_code($code);
        // En un futuro, podrías renderizar una vista de error aquí.
        // View::render("errors/{$code}", ['message' => $message]);
        echo "Error $code: $message";
        exit();
    }
}