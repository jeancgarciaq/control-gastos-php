<?php
/**
 * @file sidebar.php
 * @package App\Views\partials
 * @brief Barra lateral de navegación de la aplicación.
 *
 * Es dinámica y muestra acciones contextuales si el servicio de navegación está disponible.
 * @var App\Services\NavigationService|null $nav El servicio de navegación (puede no estar definido).
 */

// Comprobamos si la variable $nav existe antes de usarla.
$navContext = isset($nav) ? $nav->getContext() : null;
?>

<!--
Contenedor de la barra lateral.
CAMBIOS CLAVE:
1. Se ha añadido 'flex flex-col' para convertirlo en un contenedor Flexbox vertical.
2. Se ha eliminado 'min-h-screen' de aquí, ya que el layout principal lo controla.
-->
<aside
    id="sidebar"
    class="bg-gray-800 text-white w-64 p-4 z-30 flex flex-col fixed top-0 left-0 h-full transform -translate-x-full transition-transform duration-300 ease-in-out md:relative md:translate-x-0"
>
    <!--
    Contenedor superior.
    CAMBIO CLAVE:
    1. Se ha añadido 'flex-grow' para que este div ocupe todo el espacio vertical
       disponible, empujando el botón de logout hacia abajo.
    -->
    <div class="flex-grow">
        <!-- Logo -->
        <div class="mb-8 text-center">
            <a href="/dashboard" class="text-2xl font-bold text-white hover:text-cyan-400">Control Gastos</a>
        </div>

        <!-- Navegación Principal -->
        <nav>
            <ul>
                <li class="mb-2">
                    <a href="/dashboard" class="flex items-center p-2 rounded hover:bg-gray-700 <?= (isset($nav) && $nav->isActive('/dashboard')) ? 'bg-cyan-600' : '' ?>">
                        <i class="fa-solid fa-house fa-fw mr-3"></i> Dashboard
                    </a>
                </li>
                <li class="mb-2">
                    <a href="/profiles" class="flex items-center p-2 rounded hover:bg-gray-700 <?= (isset($nav) && $nav->isActive('/profiles')) ? 'bg-cyan-600' : '' ?>">
                        <i class="fa-solid fa-user fa-fw mr-3"></i> Perfiles
                    </a>
                </li>
                <li class="mb-2">
                    <a href="/incomes" class="flex items-center p-2 rounded hover:bg-gray-700 <?= (isset($nav) && $nav->isActive('/incomes')) ? 'bg-cyan-600' : '' ?>">
                        <i class="fa-solid fa-sack-dollar fa-fw mr-3"></i> Ingresos
                    </a>
                </li>
                <li class="mb-2">
                    <a href="/expenses" class="flex items-center p-2 rounded hover:bg-gray-700 <?= (isset($nav) && $nav->isActive('/expenses')) ? 'bg-cyan-600' : '' ?>">
                        <i class="fa-solid fa-money-bill-trend-up fa-fw mr-3"></i> Gastos
                    </a>
                </li>
            </ul>
        </nav>

        <!-- Sección de Acciones Contextuales -->
        <?php if ($navContext && $navContext->entity): ?>
            <div class="mt-6 pt-4 border-t border-gray-700">
                <h3 class="text-sm font-semibold text-gray-400 uppercase mb-2">Acciones de <?= ucfirst($navContext->entity) ?></h3>
                <ul>
                    <li class="mb-2"><a href="/<?= $navContext->entity ?>" class="flex items-center p-2 rounded text-sm hover:bg-gray-700"><i class="fa-solid fa-list fa-fw mr-3"></i> Ver Listado</a></li>
                    <li class="mb-2"><a href="/<?= $navContext->entity ?>/create" class="flex items-center p-2 rounded text-sm hover:bg-gray-700"><i class="fa-solid fa-plus fa-fw mr-3"></i> Crear Nuevo</a></li>

                    <?php if ($navContext->id): ?>
                        <li class="mb-2"><a href="/<?= $navContext->entity ?>/<?= $navContext->id ?>" class="flex items-center p-2 rounded text-sm hover:bg-gray-700"><i class="fa-solid fa-eye fa-fw mr-3"></i> Ver <?= $navContext->entityNameSingular ?></a></li>
                        <li class="mb-2"><a href="/<?= $navContext->entity ?>/<?= $navContext->id ?>/edit" class="flex items-center p-2 rounded text-sm hover:bg-gray-700"><i class="fa-solid fa-pencil fa-fw mr-3"></i> Editar <?= $navContext->entityNameSingular ?></a></li>
                        <li class="mb-2">
                            <form action="/<?= $navContext->entity ?>/<?= $navContext->id ?>/delete" method="POST" onsubmit="return confirm('¿Estás seguro?');">
                                <input type="hidden" name="_method" value="DELETE">
                                <button type="submit" class="w-full flex items-center p-2 rounded text-sm text-red-400 hover:bg-red-900 hover:text-white text-left"><i class="fa-solid fa-trash fa-fw mr-3"></i> Eliminar <?= $navContext->entityNameSingular ?></button>
                            </form>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        <?php endif; ?>
    </div>

    <!-- Sección de Logout -->
    <div>
        <a href="/logout">
            <button type="submit" class="w-full flex items-center p-2 rounded hover:bg-red-700 bg-red-600 text-left">
                <i class="fa-solid fa-door-open fa-fw mr-3"></i> Cerrar Sesión
            </button>
        </a>
    </div>
</aside>