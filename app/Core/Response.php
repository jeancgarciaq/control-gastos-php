<?php

namespace App\Core;

/**
 * Class Response
 * Handles HTTP responses.
 */
class Response
{
    /**
     * Redirects the user to a different URL.
     *
     * @param string $url The URL to redirect to.
     * @return void
     */
    public static function redirect(string $url)
    {
        header("Location: " . $url);
        exit;
    }
}