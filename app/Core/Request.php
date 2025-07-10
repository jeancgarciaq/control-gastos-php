<?php
/**
 * @file Request.php
 * @package App\Core
 * @author jeancgarciaq
 * @version 1.0
 * @date 2025-07-10
 * @brief Clase para encapsular la información de la petición HTTP, incluyendo parámetros de ruta.
 */

namespace App\Core;

class Request
{
    /**
     * @var array Almacena los parámetros de la ruta extraídos por el Router (ej: ['id' => 1]).
     */
    private array $routeParams = [];

    /**
     * Obtiene la ruta de la URI de la petición.
     * @return string La ruta limpia, ej: "/profiles/1".
     */
    public function getPath(): string
    {
        return parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?? '/';
    }

    /**
     * Obtiene el método HTTP de la petición en minúsculas.
     * @return string 'get', 'post', etc.
     */
    public function getMethod(): string
    {
        return strtolower($_SERVER['REQUEST_METHOD']);
    }

    /**
     * Obtiene los datos del cuerpo de la petición (GET o POST) de forma segura.
     * @return array Los datos saneados.
     */
    public function getBody(): array
    {
        $body = [];
        if ($this->getMethod() === 'get') {
            foreach ($_GET as $key => $value) {
                $body[$key] = filter_input(INPUT_GET, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        if ($this->getMethod() === 'post') {
            foreach ($_POST as $key => $value) {
                $body[$key] = filter_input(INPUT_POST, $key, FILTER_SANITIZE_SPECIAL_CHARS);
            }
        }
        return $body;
    }

    /**
     * Establece los parámetros de la ruta que el Router ha extraído.
     * Este método es llamado por el Router.
     *
     * @param array $params Los parámetros clave-valor extraídos de la URL.
     */
    public function setRouteParams(array $params): void
    {
        $this->routeParams = $params;
    }

    /**
     * Obtiene un parámetro específico de la ruta por su nombre.
     *
     * @param string $param El nombre del parámetro (ej: 'id').
     * @return mixed|null El valor del parámetro o null si no existe.
     */
    public function getRouteParam(string $param)
    {
        return $this->routeParams[$param] ?? null;
    }

    /**
     * @brief Verifica si la petición actual es una petición AJAX.
     * @description Este método comprueba la existencia y el valor del encabezado HTTP 'X-Requested-With',
     * que es el estándar utilizado por la mayoría de las librerías JavaScript (como jQuery, Axios, etc.)
     * para identificar llamadas asíncronas.
     *
     * @return bool True si es una petición AJAX, false en caso contrario.
    */
    public function isAjax(): bool
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
}