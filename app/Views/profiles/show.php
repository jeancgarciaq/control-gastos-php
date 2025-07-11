<?php
/**
 * @file show.php
 * @package App\Views
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Vista ver de Profile.
 * 
 * @var string $title El título de la página.
 * @var array $user Un array que contiene los datos del usuario autenticado.
 * @var App\Services\NavigationService $nav El servicio de navegación inyectado.
 */
?>

<!-- Contenido específico del Profiles Show -->

    <div class="container mx-auto p-4 md:p-8 max-w-4xl">

        <!-- INICIO: Bloque para mostrar mensajes de éxito/error -->
        <?php if (isset($_SESSION['success'])): ?>
            <div class="bg-green-100 border-l-4 border-green-500 text-green-700 p-4 mb-6 rounded-md" role="alert">
                <p class="font-bold">Éxito</p>
                <p><?= htmlspecialchars($_SESSION['success']) ?></p>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="bg-red-100 border-l-4 border-red-500 text-red-700 p-4 mb-6 rounded-md" role="alert">
                <p class="font-bold">Error</p>
                <p><?= htmlspecialchars($_SESSION['error']) ?></p>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
        <!-- FIN: Bloque de mensajes -->

        <h1 class="text-3xl font-bold text-gray-800 mb-8"><?= htmlspecialchars($title) ?></h1>

        <!-- Sección 1: Detalles del Perfil Financiero -->
        <div class="bg-white p-6 rounded-lg shadow-md mb-8">
            <div class="flex justify-between items-center mb-4">
                <h2 class="text-2xl font-semibold text-gray-700">Información Financiera</h2>
                <a href="/profiles/<?= htmlspecialchars($profile['id']) ?>/edit" class="text-sm font-medium text-blue-600 hover:text-blue-800 transition-colors">Editar Perfil Financiero</a>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-4">
                <div>
                    <strong class="text-gray-600">Nombre Completo:</strong>
                    <p class="text-gray-800 text-lg"><?= htmlspecialchars($profile['name']) ?></p>
                </div>
                <div>
                    <strong class="text-gray-600">Teléfono:</strong>
                    <p class="text-gray-800 text-lg"><?= htmlspecialchars($profile['phone']) ?></p>
                </div>
                <div>
                    <strong class="text-gray-600">Cargo o Empresa:</strong>
                    <p class="text-gray-800 text-lg"><?= htmlspecialchars($profile['position_or_company']) ?></p>
                </div>
                <div>
                    <strong class="text-gray-600">Estado Civil:</strong>
                    <p class="text-gray-800 text-lg"><?= htmlspecialchars($profile['marital_status']) ?></p>
                </div>
                <div>
                    <strong class="text-gray-600">Número de Hijos:</strong>
                    <p class="text-gray-800 text-lg"><?= htmlspecialchars($profile['children']) ?></p>
                </div>
                <div>
                    <strong class="text-gray-600">Balance (Activos):</strong>
                    <p class="text-gray-800 text-lg font-mono">$<?= number_format($profile['assets'], 2) ?></p>
                </div>
            </div>
        </div>

        <!-- Sección 2: Configuración de la Cuenta de Usuario -->
        <div class="bg-white p-6 rounded-lg shadow-md">
            <h2 class="text-2xl font-semibold text-gray-700 mb-6">Configuración de la Cuenta</h2>

            <!-- Formulario para actualizar Email y Username -->
            <form action="/user/update" method="POST" class="mb-8 border-b pb-8">
                <h3 class="text-xl font-semibold text-gray-600 mb-4">Datos de Usuario</h3>
                <div class="mb-4">
                    <label for="username" class="block text-sm font-medium text-gray-700">Nombre de Usuario</label>
                    <input type="text" name="username" id="username" value="<?= htmlspecialchars($user['username']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <div class="mb-4">
                    <label for="email" class="block text-sm font-medium text-gray-700">Correo Electrónico</label>
                    <input type="email" name="email" id="email" value="<?= htmlspecialchars($user['email']) ?>" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm">
                </div>
                <button type="submit" class="px-4 py-2 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500">Actualizar Datos</button>
            </form>

            <!-- Formulario para cambiar la Contraseña -->
            <form action="/user/password/update" method="POST">
                <h3 class="text-xl font-semibold text-gray-600 mb-4">Cambiar Contraseña</h3>
                <div class="mb-4">
                    <label for="current_password" class="block text-sm font-medium text-gray-700">Contraseña Actual</label>
                    <input type="password" name="current_password" id="current_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="new_password" class="block text-sm font-medium text-gray-700">Nueva Contraseña</label>
                    <input type="password" name="new_password" id="new_password" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <div class="mb-4">
                    <label for="password_confirmation" class="block text-sm font-medium text-gray-700">Confirmar Nueva Contraseña</label>
                    <input type="password" name="password_confirmation" id="password_confirmation" class="mt-1 block w-full rounded-md border-gray-300 shadow-sm focus:border-indigo-500 focus:ring-indigo-500 sm:text-sm" required>
                </div>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white font-semibold rounded-md hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-red-500">Cambiar Contraseña</button>
            </form>
        </div>
    </div>