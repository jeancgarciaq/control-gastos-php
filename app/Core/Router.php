<?php

namespace App\Core;

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
     * @param string $controllerAction The controller and action to execute (e.g., "AuthController@processLogin").
     * @return void
     */
    public function post(string $uri, string $controllerAction): void
    {
        $this->routes['POST'][$uri] = $controllerAction;
    }

    /**
     * Resolves the current request and dispatches it to the appropriate controller action.
     *
     * @return void
     */
    public function resolve()
    {
        $uri = $this->request->getUri();
        $method = $this->request->getMethod();

        $routeParts = explode('@', $this->routes[$method][$uri] ?? '');
        $controllerName = "App\\Controllers\\" . $routeParts[0];
        $action = $routeParts[1] ?? 'index'; // Default to 'index' action

        if (!class_exists($controllerName)) {
            echo "Controller not found: " . $controllerName;
            return;
        }

        $controller = new $controllerName();

        if (!method_exists($controller, $action)) {
            echo "Method not found: " . $action . " in " . $controllerName;
            return;
        }

        $controller->$action($this->request);
    }
}