<?php
/**
 * @file show.php
 * @package App\Views
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Vista ver de Expense.
 * 
 * @var string $title El título de la página.
 * @var array $user Un array que contiene los datos del usuario autenticado.
 * @var App\Services\NavigationService $nav El servicio de navegación inyectado.
 */
?>

<!-- Contenido específico del Expenses Show -->

    <div class="container mx-auto py-8">
        <h1 class="text-3xl font-bold text-gray-800 mb-4">Expense Details</h1>

        <div class="bg-white shadow-md rounded px-8 pt-6 pb-8 mb-4">
            <div class="mb-4">
                <strong class="font-bold">Date:</strong>
                <span><?= htmlspecialchars($expense['date']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Description:</strong>
                <span><?= htmlspecialchars($expense['description']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Amount:</strong>
                <span><?= htmlspecialchars($expense['amount']) ?></span>
            </div>
            <div class="mb-4">
                <strong class="font-bold">Type:</strong>
                <span><?= htmlspecialchars($expense['type']) ?></span>
            </div>

            <div class="flex items-center justify-between">
                <a href="/expenses" class="bg-gray-500 hover:bg-gray-700 text-white font-bold py-2 px-4 rounded focus:outline-none focus:shadow-outline">
                    Back to Expenses
                </a>
                <div>
                    <a href="/expenses/<?= htmlspecialchars($expense['id']) ?>/edit" class="bg-green-500 hover:bg-green-700 text-white font-bold py-2 px-4 rounded mr-2">
                        Edit
                    </a>
                    <form action="/expenses/<?= htmlspecialchars($expense['id']) ?>/delete" method="post" class="inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="bg-red-500 hover:bg-red-700 text-white font-bold py-2 px-4 rounded" onclick="return confirm('Are you sure you want to delete this expense?')">
                            Delete
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>