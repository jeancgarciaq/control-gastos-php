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
use App\Controllers\ExpenseController;
use App\Controllers\HomeController;
use App\Controllers\IncomeController;
use App\Controllers\ProfileController;

// NOTA: La variable $router ya existe, fue creada en public/index.php.
// Aquí solo la utilizamos para definir las rutas.

// --- Rutas Principales y de Autenticación ---
$router->get('/', [HomeController::class, 'index']);
$router->get('/register', [AuthController::class, 'register']);
$router->post('/register', [AuthController::class, 'processRegister']);
$router->get('/login', [AuthController::class, 'login']);
$router->post('/login', [AuthController::class, 'processLogin']);
$router->get('/logout', [AuthController::class, 'logout']);


// --- Rutas de Perfil (Profile) ---
$router->get('/profile/create', [ProfileController::class, 'create']);
$router->post('/profile/create', [ProfileController::class, 'store']);
$router->get('/profile/{id}/edit', [ProfileController::class, 'edit']);
$router->post('/profile/{id}/edit', [ProfileController::class, 'update']);
$router->get('/profile/{id}', [ProfileController::class, 'show']);
$router->post('/profile/{id}/delete', [ProfileController::class, 'destroy']); // Simula la petición DELETE


// --- Rutas de Gastos (Expense) ---
$router->get('/expenses', [ExpenseController::class, 'index']);
$router->get('/expenses/create', [ExpenseController::class, 'create']);
$router->post('/expenses/create', [ExpenseController::class, 'store']);
$router->get('/expenses/{id}/edit', [ExpenseController::class, 'edit']);
$router->post('/expenses/{id}/edit', [ExpenseController::class, 'update']);
$router->get('/expenses/{id}', [ExpenseController::class, 'show']);
$router->post('/expenses/{id}/delete', [ExpenseController::class, 'destroy']); // Simula la petición DELETE


// --- Rutas de Ingresos (Income) ---
$router->get('/income', [IncomeController::class, 'index']);
$router->get('/income/create', [IncomeController::class, 'create']);
$router->post('/income/create', [IncomeController::class, 'store']);
$router->get('/income/{id}/edit', [IncomeController::class, 'edit']);
$router->post('/income/{id}/edit', [IncomeController::class, 'update']);
$router->get('/income/{id}', [IncomeController::class, 'show']);
$router->post('/income/{id}/delete', [IncomeController::class, 'destroy']); // Simula la petición DELETE