<?php
/**
 * @file routes.php
 * @package App
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Define todas las rutas de la aplicación.
 *
 * Este archivo es incluido por public/index.php y utiliza la variable $router
 * que fue instanciada allí para registrar todos los endpoints de la aplicación.
 */

namespace App;

use App\Controllers\AuthController;
use App\Controllers\DashboardController;
use App\Controllers\ExpenseController;
use App\Controllers\HomeController;
use App\Controllers\IncomeController;
use App\Controllers\ProfileController;
use App\Controllers\UserController;

// NOTA: La variable $router ya existe, fue creada en public/index.php.
// Aquí solo la utilizamos para definir las rutas.

// --- Rutas Principales y de Autenticación ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/dashboard', [DashboardController::class, 'index']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'processRegister']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);


// --- Rutas de Perfil (Profile) ---
$router->get('/profiles', [ProfileController::class, 'index']);
$router->get('/profiles/create', [ProfileController::class, 'create']);
$router->post('/profiles/create', [ProfileController::class, 'store']);
$router->get('/profiles/{id}/edit', [ProfileController::class, 'edit']);
$router->post('/profiles/{id}/edit', [ProfileController::class, 'update']);
$router->get('/profiles/{id}', [ProfileController::class, 'show']);
$router->post('/profiles/{id}/delete', [ProfileController::class, 'destroy']); // Simula la petición DELETE

// Rutas para la gestión de la cuenta de usuario (desde la vista de perfil)
$router->post('/user/update', [UserController::class, 'update']);
$router->post('/user/password/update', [UserController::class, 'updatePassword']);

// --- Rutas de Gastos (Expense) ---
$router->get('/expenses', [ExpenseController::class, 'index']);
$router->get('/expenses/create', [ExpenseController::class, 'create']);
$router->post('/expenses/create', [ExpenseController::class, 'store']);
$router->get('/expenses/{id}/edit', [ExpenseController::class, 'edit']);
$router->post('/expenses/{id}/edit', [ExpenseController::class, 'update']);
$router->get('/expenses/{id}', [ExpenseController::class, 'show']);
$router->post('/expenses/{id}/delete', [ExpenseController::class, 'destroy']); // Simula la petición DELETE


// --- Rutas de Ingresos (Income) ---
$router->get('/incomes', [IncomeController::class, 'index']);
$router->get('/incomes/create', [IncomeController::class, 'create']);
$router->post('/incomes/create', [IncomeController::class, 'store']);
$router->get('/incomes/{id}/edit', [IncomeController::class, 'edit']);
$router->post('/incomes/{id}/edit', [IncomeController::class, 'update']);
$router->get('/incomes/{id}', [IncomeController::class, 'show']);
$router->post('/incomes/{id}/delete', [IncomeController::class, 'destroy']); // Simula la petición DELETE