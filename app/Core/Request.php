<?php

namespace App\Core;

/**
 * Class Request
 * Represents an HTTP request.
 */
class Request
{
    /**
     * @var array Almacena los parámetros extraídos de la ruta (ej. ['id' => 5]).
    */
    protected array $routeParams = [];

    /**
     * Gets the URI of the request.
     *
     * @return string The URI.
     */
     public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');
        // Quitamos la barra final
        $uri = rtrim($uri, '/');

        // Si era exactamente "/", al quitar la slash queda vacío
        if ($uri === '') {
            return '/';
        }

        return $uri;
    }


    /**
     * Gets the HTTP method of the request (GET, POST, etc.).
     *
     * @return string The HTTP method.
     */
    public function getMethod(): string
    {
        return $_SERVER['REQUEST_METHOD'];
    }

    /**
     * Gets the request body (POST or GET data).
     *
     * @return array An associative array of request parameters.
     */
    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'GET') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        if ($this->getMethod() === 'POST') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }

        return $body;
    }

    /**
     * Guarda los parámetros de la ruta. Es usado por el Router.
     *
     * @param array $params
     * @return void
    */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Obtiene un parámetro específico de la ruta por su nombre.
     *
     * @param string $paramName El nombre del parámetro (ej. 'id').
     * @param mixed|null $default El valor a devolver si no se encuentra.
     * @return mixed
     */
    public function getRouteParam(string $paramName, $default = null)
    {
        return $this->routeParams[$paramName] ?? $default;
    }

    /**
     * Determines if the request is an AJAX request.
     *
     * @return bool
    */
    public function isAjax(): bool
    {
        return isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}