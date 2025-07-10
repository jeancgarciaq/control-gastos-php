<?php
/**
 * @file AuthenticatedController.php
 * @package App\Core
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Controlador base para acciones que requieren autenticación.
 */

namespace App\Controllers;

use PDO;
use App\Core\Auth;

/**
 * @class AuthenticatedController
 * @brief Controlador base que verifica la autenticación del usuario.
 * Cualquier controlador que herede de esta clase protegerá todas sus rutas,
 * redirigiendo a la página de login si el usuario no está autenticado.
 */
abstract class AuthenticatedController
{
    /**
     * @var PDO La instancia de la conexión a la base de datos.
     */
    protected PDO $pdo;

    /**
     * Constructor del AuthenticatedController.
     * Su única función es verificar si el usuario ha iniciado sesión.
     * Si no lo ha hecho, lo redirige a la página de login.
     *
     * @param PDO $pdo La instancia de conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;

        if (!Auth::check()) {
            Response::redirect('/login', ['error' => 'Debes iniciar sesión para acceder a esta página.']);
            exit(); // Detiene la ejecución para prevenir que se procese el resto del controlador.
        }
    }
}