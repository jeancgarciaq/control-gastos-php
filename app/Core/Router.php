<?php
/**
 * @file Router.php
 * @package App\Core
 * @author jeancgarciaq
 * @version 1.0
 * @date 2025-07-10
 * @brief Maneja el enrutamiento de las peticiones HTTP a los controladores.
*/

namespace App\Core;

use PDO;

/**
 * @class Router
 * @brief Responsable de registrar rutas y resolverlas para ejecutar el controlador adecuado.
 * Esta clase mapea las URIs a acciones de controladores, manejando tanto rutas estáticas
 * como dinámicas con parámetros.
 */
class Router
{
    /**
     * @var array Almacena todas las rutas registradas, agrupadas por método HTTP.
     * @example ['get' => ['/path' => callback], 'post' => ['/path' => callback]]
     */
    protected array $routes = [];

    /**
     * @var Request La instancia del objeto Request asociada a la petición actual.
    */
    private Request $request;

    /**
     * @brief Constructor de la clase Router.
     * Inicializa el objeto Request para encapsular la información de la petición.
     */
    public function __construct()
    {
        $this->request = new Request();
    }

    /**
     * @brief Registra una nueva ruta para el método HTTP GET.
     * @param string $path La URI de la ruta (ej: "/users/{id}").
     * @param callable|array $callback La función o el array [controlador::class, 'método'] a ejecutar.
     * @return void
     */
    public function get(string $path, $callback): void
    {
        $this->routes['get'][$path] = $callback;
    }

    /**
     * @brief Registra una nueva ruta para el método HTTP POST.
     * @param string $path La URI de la ruta (ej: "/users").
     * @param callable|array $callback La función o el array [controlador::class, 'método'] a ejecutar.
     * @return void
     */
    public function post(string $path, $callback): void
    {
        $this->routes['post'][$path] = $callback;
    }
    
    /**
     * @brief Resuelve la ruta actual, encuentra el controlador y método correspondiente, y lo ejecuta.
     * Maneja tanto rutas estáticas como dinámicas (con parámetros), extrayendo los valores
     * y poniéndolos a disposición a través del objeto Request.
     *
     * @param PDO $pdo La conexión a la base de datos para inyectar en los controladores.
     * @return void
     */
    public function resolve(PDO $pdo): void
    {
        $path = $this->request->getPath();
        $method = $this->request->getMethod();
        $callback = $this->routes[$method][$path] ?? false;

        if ($callback === false) {
            foreach ($this->routes[$method] as $route => $routeCallback) {
                if (strpos($route, '{') !== false) {
                    $routeRegex = preg_replace('/\/\{([a-zA-Z0-9_]+)\}/', '/([^\/]+)', $route);
                    if (preg_match('#^' . $routeRegex . '$#', $path, $matches)) {
                        array_shift($matches);

                        preg_match_all('/\{([a-zA-Z0-9_]+)\}/', $route, $paramNames);
                        $paramNames = $paramNames[1];

                        $routeParams = array_combine($paramNames, $matches);
                        $this->request->setRouteParams($routeParams);
                        
                        $callback = $routeCallback;
                        break;
                    }
                }
            }
        }
        
        if ($callback === false) {
            http_response_code(404);
            View::render('errors/404', ['title' => 'Página no encontrada']);
            return;
        }

        if (is_array($callback)) {
            $controllerClass = $callback[0];
            $methodName = $callback[1];

            if (class_exists($controllerClass)) {
                $controller = new $controllerClass($pdo);
                call_user_func([$controller, $methodName], $this->request);
            } else {
                echo "Error: Controlador '$controllerClass' no encontrado.";
            }
        }
    }
}