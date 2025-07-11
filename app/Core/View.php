<?php
/**
 * @file View.php
 * @package App\Core
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Clase para renderizar vistas, ahora con soporte para plantillas (layouts).
*/

namespace App\Core;

/**
 * Class View
 * Handles rendering views within a main layout and provides mocking for testing.
 */
class View
{
    public static bool $mock = false;
    public static ?string $mockRenderedView = null;
    public static ?array $mockRenderedData = null;

    /**
     * Renders a view, embedding it within a main layout by default.
     *
     * @param string $view The name of the content view file (e.g., 'profiles/index').
     * @param array $data An associative array of data to pass to the view and layout.
     * @param string|null $layout The name of the layout file. If null, renders the view directly.
     * @return void
     */
    public static function render(string $view, array $data = [], ?string $layout = 'layout')
    {
        if (self::$mock) {
            self::$mockRenderedView = $view;
            self::$mockRenderedData = $data;
            return;
        }

        // Hacer que las variables de datos estén disponibles en el ámbito actual.
        extract($data);

        $viewPath = __DIR__ . '/../Views/' . $view . '.php';

        if (file_exists($viewPath)) {
            // Si se especifica un layout, renderiza la vista dentro de él.
            if ($layout) {
                $layoutPath = __DIR__ . '/../Views/' . $layout . '.php';
                if (file_exists($layoutPath)) {
                    // Captura el contenido de la vista principal en una variable.
                    ob_start();
                    include $viewPath;
                    $content = ob_get_clean();
                    
                    // Ahora, incluye el layout. Tanto las variables de $data (como $nav)
                    // como la variable $content estarán disponibles dentro del layout.
                    include $layoutPath;
                } else {
                    echo "Layout not found: " . $layoutPath;
                }
            } else {
                // Si no hay layout, renderiza la vista directamente (comportamiento anterior).
                include $viewPath;
            }
        } else {
            echo "View not found: " . $viewPath;
        }
    }
}