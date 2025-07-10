<?php

namespace App\Core;

use PDO;

/**
 * Class Router
 * Handles incoming requests and dispatches them to the appropriate controller action.
 */
class Router
{
    /**
     * @var array The routes defined for the application.  Organized by HTTP method (GET, POST).
     */
    protected array $routes = [
        'GET' => [],
        'POST' => []
    ];

    /**
     * @var Request The request object.
     */
    protected Request $request;

    /**
     * Router constructor.
     *
     * @param Request $request The request object.
     */
    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * Registers a GET route.
     *
     * @param string $uri The URI to match.
     * @param string $controllerAction The controller and action to execute (e.g., "HomeController@index").
     * @return void
     */
    public function get(string $uri, string $controllerAction): void
    {
        $this->routes['GET'][$uri] = $controllerAction;
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri The URI to match.
     * @param string $controllerAction The controller@action string.
     */
    public function post(string $uri, string $controllerAction): void
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    /**
     * Registers a POST route.
     *
     * @param string $uri The URI to match.
     * @param string $controllerAction The controller and action to execute (e.g., "AuthController@processLogin").
     * @return void
     */
    public function resolve(PDO $pdo)
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        // 1. ITERAR SOBRE LAS RUTAS REGISTRADAS PARA EL MÉTODO ACTUAL
        foreach ($this->routes[$method] as $routePattern => $controllerAction) {
            
            // 2. CONVERTIR EL PATRÓN DE RUTA EN UNA EXPRESIÓN REGULAR
            // Reemplaza los marcadores como {id} con un regex que captura dígitos.
            // Por ejemplo, '/profile/{id}/edit' se convierte en '#^/profile/(\d+)/edit$#'.
            $regexPattern = preg_replace('/\{([a-zA-Z]+)\}/', '(?P<$1>\d+)', $routePattern);
            $regexPattern = '#^' . $regexPattern . '$#';

            // 3. COMPROBAR SI LA URI ACTUAL COINCIDE CON EL PATRÓN REGEX
            if (preg_match($regexPattern, $uri, $matches)) {
                
                // 4. SI HAY COINCIDENCIA, EXTRAER LOS PARÁMETROS
                // $matches contendrá los valores capturados (ej. 'id' => '5').
                // Eliminamos las coincidencias numéricas para quedarnos solo con las asociativas.
                $params = array_filter($matches, 'is_string', ARRAY_FILTER_USE_KEY);
                
                // 5. GUARDAR LOS PARÁMETROS EN EL OBJETO REQUEST
                // Así, el controlador podrá acceder a ellos fácilmente.
                $this->request->setRouteParams($params);

                // 6. LLAMAR AL CONTROLADOR (La lógica que ya tenías)
                $routeParts = explode('@', $controllerAction);
                $controllerName = "App\\Controllers\\" . $routeParts[0];
                $action = $routeParts[1] ?? 'index';

                if (!class_exists($controllerName)) {
                    http_response_code(404);
                    echo "404 Not Found - Controller not found";
                    return;
                }

                $controller = new $controllerName($pdo);

                if (!method_exists($controller, $action)) {
                    http_response_code(404);
                    echo "404 Not Found - Action not found in $controllerName";
                    return;
                }

                // Ejecutamos la acción y terminamos el bucle, ya que encontramos la ruta.
                $controller->$action($this->request);
                return;
            }
        }

        // Si el bucle termina y no se encontró ninguna ruta, es un 404.
        http_response_code(404);
        echo "404 Not Found - Route '$uri' not found for method '$method'";
    }
}