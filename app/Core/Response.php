<?php

namespace App\Core;

/**
 * Class Response
 * Handles HTTP responses.
 */
class Response
{
    public static $mock = false;
    public static $mockRedirectedTo = null;
    /**
     * Redirects the user to a different URL.
     *
     * @param string $url The URL to redirect to.
     * @return void
     */
    public static function redirect(string $url)
    {
        if(self::$mock){
            self::$mockRedirectedTo = $url;
            return;
        }
        header("Location: " . $url);
        exit;
    }

    /**
     * Sends a JSON response.
     *
     * @param array $data The data to be sent as JSON.
     * @param int $statusCode The HTTP status code (default is 200).
     * @return void
    */
    public static function json(array $data, int $statusCode = 200): void
    {
        header('Content-Type: application/json');
        http_response_code($statusCode);
        echo json_encode($data);
        exit;
    }
}