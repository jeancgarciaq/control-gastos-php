<?php
/**
 * @var string $title El título de la página.
 * @var string $content El contenido principal de la vista específica.
 */
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <!-- Usamos una variable para el título, que cada vista definirá -->
    <title><?= isset($title) ? htmlspecialchars($title) : 'Control de Gastos' ?></title>
    <!-- Asegúrate de que esta ruta a tu CSS de Tailwind es correcta -->
    <link href="/output.css?v=<?= time() ?>" rel="stylesheet">
    <!-- Font Awesome for icons -->
    <script src="https://kit.fontawesome.com/394d785d07.js" crossorigin="anonymous"></script>
</head>
<body class="bg-gray-100">

    <div class="flex">
        <!-- 1. Incluimos la barra lateral -->
        <?php require_once __DIR__ . '/partials/sidebar.php'; ?>

        <!-- Contenedor principal que se adapta al lado de la barra lateral -->
        <div class="flex-1 flex flex-col">
            
            <!-- Barra superior con botón de menú para móvil -->
            <header class="bg-white shadow-md p-4 flex justify-between items-center md:hidden">
                <a href="/home" class="text-xl font-bold text-gray-800">Control Gastos</a>
                <!-- Botón Hamburguesa -->
                <button id="menu-button" class="text-gray-800 focus:outline-none">
                    <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16m-7 6h7"></path>
                    </svg>
                </button>
            </header>

            <!-- 2. Área de contenido principal -->
            <main class="flex-1 p-4 md:p-8">
                <!-- Aquí se inyectará el contenido de cada vista -->
                <?= $content ?>
            </main>
        </div>
    </div>

    <!-- Overlay para cerrar el menú en móvil al hacer clic fuera -->
    <div id="sidebar-overlay" class="fixed inset-0 bg-black opacity-50 z-20 hidden md:hidden"></div>

    <!-- 3. JavaScript para la interactividad -->
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            const menuButton = document.getElementById('menu-button');
            const sidebar = document.getElementById('sidebar');
            const overlay = document.getElementById('sidebar-overlay');

            function toggleSidebar() {
                sidebar.classList.toggle('-translate-x-full');
                overlay.classList.toggle('hidden');
            }

            menuButton.addEventListener('click', toggleSidebar);
            overlay.addEventListener('click', toggleSidebar);
        });
    </script>
</body>
</html>