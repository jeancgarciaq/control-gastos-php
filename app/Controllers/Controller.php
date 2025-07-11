<?php
/**
 * @file Controller.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador base.
*/

namespace App\Controllers;

use PDO;
use App\Services\NavigationService;
use App\Core\View;

/**
 * Class Controller
 *
 * El controlador base del que todos los demás controladores de la aplicación heredan.
 * Proporciona una propiedad PDO para la interacción con la base de datos.
 *
 * @package App\Controllers
 */
abstract class Controller
{
    /**
     * @var PDO La instancia de la conexión a la base de datos.
     */
    protected PDO $pdo;

    /**
     * @var NavigationService El servicio que gestiona el contexto de navegación.
     */
    protected NavigationService $navigation;


    /**
     * Constructor del controlador.
     * Inyecta la conexión a la BD e inicializa el servicio de navegación.
     *
     * @param PDO $pdo La conexión a la base de datos.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->navigation = new NavigationService($_SERVER['REQUEST_URI']);
    }

    /**
     * Prepara los datos y delega el renderizado de una vista a la clase View.
     *
     * Este método actúa como un helper que enriquece los datos de la vista
     * con el servicio de navegación antes de pasarlos a View::render.
     * De esta forma, mantenemos la capacidad de mocking de la clase View.
     *
     * @param string $viewName El nombre del archivo de la vista (ej: 'profiles/index').
     * @param array $data Un array asociativo de datos para pasar a la vista.
     * @return void
    */
    protected function view(string $viewName, array $data = []): void
    {
        // Añadir el servicio de navegación a los datos que irán a la vista.
        $data['nav'] = $this->navigation;
        
        // Delegar el renderizado final a tu clase View.
        // El nombre de la vista no necesita la extensión .php porque View ya lo maneja.
        View::render($viewName, $data);
    }
}