<?php

namespace App\Core;

/**
 * Class View
 * Handles rendering views and provides mocking capabilities for testing.
 */
class View
{
    /**
     * @var bool Flag indicating whether the class is in mock mode (for testing).
     */
    public static bool $mock = false;

    /**
     * @var string|null Stores the name of the rendered view when in mock mode.
     */
    public static ?string $mockRenderedView = null;

    /**
     * @var array|null Stores the data passed to the rendered view when in mock mode.
     */
    public static ?array $mockRenderedData = null;

    /**
     * Renders a view with the given data, or stores the view name and data in mock mode.
     *
     * @param string $view The name of the view file (without the .php extension).
     * @param array $data An associative array of data to pass to the view.
     * @return void
     */
    public static function render(string $view, array $data = [])
    {
        if (self::$mock) {
            self::$mockRenderedView = $view;
            self::$mockRenderedData = $data;
            return;
        }
        extract($data); // Make data variables available in the view

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewPath)) {
            include $viewPath;
        } else {
            echo "View not found: " . $viewPath;
        }
    }
}