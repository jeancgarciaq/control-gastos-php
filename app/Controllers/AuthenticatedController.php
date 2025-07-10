<?php
/**
 * @file AuthenticatedController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador para guardar la autenticación de rutas.
*/

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use PDO;

/**
 * Class AuthenticatedController
 *
 * Un controlador base para todas las páginas que requieren que el usuario
 * esté autenticado. Extiende el controlador base y añade una comprobación
 * de seguridad en su constructor.
 *
 * @package App\Controllers
 */
abstract class AuthenticatedController extends Controller
{
    /**
     * Constructor del controlador autenticado.
     *
     * Primero, llama al constructor del controlador padre para inicializar la
     * conexión a la base de datos.
     *
     * Luego, ejecuta la lógica de seguridad: comprueba si el usuario actual
     * está autenticado. Si no lo está, detiene la ejecución y redirige
     * al usuario a la página de inicio de sesión.
     *
     * @param PDO $pdo La conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        // 1. Llama al constructor de la clase padre (Controller) para inyectar PDO.
        parent::__construct($pdo);

        // 2. Aplica la lógica de seguridad.
        if (!Auth::check()) {
            Response::redirect('/login');
            // Es crucial usar exit() después de una redirección para detener
            // la ejecución del script y prevenir cualquier procesamiento adicional.
            exit();
        }
    }
}