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
}