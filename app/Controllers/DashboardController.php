<?php
/**
 * @file DashboardController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.1
 * @date 2025-07-10
 * @brief Controlador para gestionar la vista dashboard.
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Response;
use App\Core\View;
use App\Models\User;

/**
 * @class DashboardController
 * @brief Gestiona la lógica del panel principal del usuario.
 * Hereda de AuthenticatedController, por lo que todas sus rutas están protegidas.
 */
class DashboardController extends AuthenticatedController
{
    /**
     * Muestra la página principal del panel de control.
     *
     * @return void
     */
    public function index(): void
    {
        // Obtiene de forma segura los datos del usuario autenticado,
        // pasándole la conexión a la base de datos que este controlador ya tiene.
        $user = Auth::user($this->pdo);

        // Si por alguna razón el usuario no se encuentra en la BD (muy raro),
        // es buena práctica cerrar la sesión y redirigir.
        if (!$user) {
            Auth::logout();
            Response::redirect('/login');
            return;
        }

        // Y renderizar la vista, pasándole los datos que necesite.
        View::render('dashboard', [
            'title' => 'Dashboard',
            'user' => $user // Pasa el objeto de usuario a la vista
        ]);
    }
}