<?php
/**
 * @file AuthenticatedController.php
 * @package App\Core
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Controlador base para acciones que requieren autenticación.
*/

namespace App\Controllers;

use PDO;
use App\Core\Auth;
use App\Core\Response;

/**
 * @class AuthenticatedController
 * @brief Hereda de Controller y añade una capa de verificación de autenticación.
*/
abstract class AuthenticatedController extends Controller 
{
    /**
     * @var PDO La instancia de la conexión a la base de datos.
     */
    
    public function __construct(PDO $pdo)
    {
        // Llamar al constructor del padre (Controller)
        parent::__construct($pdo);

        // La lógica de autenticación
        if (!Auth::check()) {
            Response::redirect('/login', ['error' => 'Debes iniciar sesión para acceder a esta página.']);
            exit();
        }
    }
}

