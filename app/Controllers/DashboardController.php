<?php
/**
 * @file DashboardController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador para la página principal (dashboard).
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Services\BalanceService;

/**
 * @class DashboardController
 * @brief Gestiona la lógica y la vista del dashboard principal.
 *        Hereda de AuthenticatedController para asegurar que el usuario ha iniciado sesión.
 */
class DashboardController extends AuthenticatedController
{
    /**
     * Muestra el dashboard con un resumen financiero del usuario.
     *
     * @return void
     */
    public function index(): void
    {
        // 1. Obtener los datos del usuario autenticado.
        $user = Auth::user($this->pdo);

        // Si por alguna razón no se encuentra el usuario (aunque Auth::check() ya lo validó),
        if (!$user) {
            // Redirigir al login o mostrar un error.
            Response::redirect('/logout');
            exit();
        }

        // 2. Crear una instancia del servicio de balance, pasándole la conexión PDO.
        $balanceService = new BalanceService($this->pdo);

        // 3. Obtener los totales globales para el usuario actual.
        $totalIncome = $balanceService->getGlobalTotalIncome($user['id']);
        $totalExpenses = $balanceService->getGlobalTotalExpenses($user['id']);
        $currentBalance = $balanceService->getGlobalBalance($user['id']);

        // 4. Pasar TODOS los datos requeridos a la vista.
        $this->view('dashboard', [
            'title' => 'Dashboard',
            'user' => $user,
            'totalIncome' => $totalIncome,
            'totalExpenses' => $totalExpenses,
            'currentBalance' => $currentBalance
        ]);
    }
}