<?php

namespace App\Core;

/**
 * Class View
 * Handles rendering views.
 */
class View
{
    /**
     * Renders a view with the given data.
     *
     * @param string $view The name of the view file (without the .php extension).
     * @param array $data An associative array of data to pass to the view.
     * @return void
     */
    public static function render(string $view, array $data = [])
    {
        extract($data); // Make data variables available in the view

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: " . $viewPath;
        }
    }
}