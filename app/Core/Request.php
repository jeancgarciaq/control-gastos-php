<?php

namespace App\Core;

/**
 * Class Request
 * Represents an HTTP request.
 */
class Request
{
    /**
     * Gets the URI of the request.
     *
     * @return string The URI.
     */
    public function getUri(): string
    {
        $uri = $_SERVER['REQUEST_URI'];
        $uri = strtok($uri, '?');
        return rtrim($uri, '/');
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
}