<?php
/**
 * @file home.php
 * @package App\Views
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Vista principal del dashboard.
 * 
 * @var string $title El título de la página.
 * @var array $user Un array que contiene los datos del usuario autenticado.
 * @var App\Services\NavigationService $nav El servicio de navegación inyectado.
 */
?>

<!-- Contenido específico del Dashboard -->
<div class="mb-6">
    <h1 class="text-3xl font-bold text-gray-800">¡Bienvenido, <?= htmlspecialchars($user['username']) ?>!</h1>
    <p class="text-gray-600">Aquí tienes un resumen general de tus finanzas.</p>
</div>

<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
    <div class="bg-white p-6 rounded-lg shadow-md ml-2 mr-2">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Total de Gastos</h3>
        <p class="text-3xl font-bold text-red-500">
            $<?= number_format($totalExpenses, 2) ?>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md ml-2 mr-2">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Total de Ingresos</h3>
        <p class="text-3xl font-bold text-green-500">
            $<?= number_format($totalIncome, 2) ?>
        </p>
    </div>
    <div class="bg-white p-6 rounded-lg shadow-md ml-2 mr-2">
        <h3 class="text-lg font-semibold text-gray-600 mb-2">Balance Actual</h3>
        <p class="text-3xl font-bold text-blue-500">
            $<?= number_format($currentBalance, 2) ?>
        </p>
    </div>
</div>

<!-- En el futuro, aquí podrías añadir gráficos o listas de transacciones recientes -->