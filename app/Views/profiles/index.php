<?php
/**
 * @file index.php
 * @package App\Views
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-11
 * @brief Vista principal de Profile.
 * 
 * @var string $title El título de la página.
 * @var array $user Un array que contiene los datos del usuario autenticado.
 * @var App\Services\NavigationService $nav El servicio de navegación inyectado.
 */
?>

<!-- Contenido específico del Profiles Index -->
<h1 class="text-3xl font-bold text-gray-800 mb-4">Mis Perfiles</h1>

<div class="mb-4">
    <a href="/profiles/create" class="bg-blue-500 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
        Crear Nuevo Perfil
    </a>
</div>

<?php if (empty($profiles)): ?>
    <p class="text-gray-700">No se encontraron perfiles.</p>
<?php else: ?>
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-4">
        <?php foreach ($profiles as $profile): ?>
            <div class="bg-white shadow-md rounded-lg p-4">
                <h2 class="text-xl font-semibold mb-2"><?= htmlspecialchars($profile['name']) ?></h2>
                <p class="text-gray-600 mb-4">
                    <?= htmlspecialchars($profile['position_or_company']) ?>
                </p>
                <div class="flex items-center justify-start space-x-4">
                    <a href="/profiles/<?= htmlspecialchars($profile['id']) ?>" class="text-blue-500 hover:text-blue-700">
                        <i class="fa-solid fa-eye mr-3"></i>Ver
                    </a>
                    <a href="/profiles/<?= htmlspecialchars($profile['id']) ?>/edit" class="text-green-500 hover:text-green-700">
                        <i class="fa-solid fa-pencil mr-3"></i>Editar
                    </a>
                    <form action="/profiles/<?= htmlspecialchars($profile['id']) ?>/delete" method="post" class="inline">
                        <input type="hidden" name="_method" value="DELETE">
                        <button type="submit" class="text-red-500 hover:text-red-700" onclick="return confirm('¿Estás seguro?')">
                            <i class="fa-solid fa-trash mr-3"></i>Eliminar
                        </button>
                    </form>
                </div>
            </div>
        <?php endforeach; ?>
    </div>
<?php endif; ?>