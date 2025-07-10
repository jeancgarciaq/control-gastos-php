<?php
/**
 * @file ProfileController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Controlador para gestionar los perfiles financieros de los usuarios.
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Profile;
use App\Models\User;

/**
 * Class ProfileController
 * Gestiona el CRUD de los perfiles financieros.
 * Hereda de AuthenticatedController, por lo que todas sus acciones requieren autenticación.
 */
class ProfileController extends AuthenticatedController
{
    // No necesita un constructor propio, hereda el de AuthenticatedController.

    /**
     * Muestra una lista de todos los perfiles del usuario.
     * (Esta podría ser la misma página que el dashboard o una página separada)
     */
    public function index(): void
    {
        $profileModel = new Profile($this->pdo);
        $profiles = $profileModel->getAllForUser(Auth::id());

        View::render('profiles/index', [
            'title' => 'Mis Perfiles Financieros',
            'profiles' => $profiles
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo perfil.
     */
    public function create(): void
    {
        View::render('profiles/create', ['title' => 'Crear Nuevo Perfil']);
    }

    /**
     * Almacena un nuevo perfil en la base de datos.
     */
    public function store(Request $request): void
    {
        $data = $request->getBody();
        // Aquí iría la validación de datos con una clase ProfileRequest

        $profile = new Profile($this->pdo);
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = (int) $data['children'];
        $profile->initial_balance = (float) $data['initial_balance'];
        $profile->assets = (float) $data['initial_balance']; // Al crear, los activos son iguales al balance inicial
        $profile->user_id = Auth::id();

        if ($profile->save()) {
            Response::redirect('/dashboard'); // O a la lista de perfiles
        } else {
            // Manejar error
            View::render('profiles/create', ['title' => 'Crear Nuevo Perfil', 'error' => 'No se pudo crear el perfil.']);
        }
    }

    /**
     * Muestra el formulario para editar un perfil existente.
     */
    public function edit(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $profileModel = new Profile($this->pdo);

        if (!$id || !$profileModel->isOwnedByUser($id, Auth::id())) {
            Response::redirect('/dashboard', ['error' => 'Perfil no encontrado o sin permisos.']);
            return;
        }

        $profile = $profileModel->find($id);
        View::render('profiles/edit', ['title' => 'Editar Perfil', 'profile' => $profile]);
    }

    /**
     * Actualiza un perfil existente en la base de datos.
     */
    public function update(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $data = $request->getBody();
        $profileModel = new Profile($this->pdo);

        if (!$id || !$profileModel->isOwnedByUser($id, Auth::id())) {
            Response::redirect('/dashboard', ['error' => 'Perfil no encontrado o sin permisos.']);
            return;
        }
        
        // Aquí iría la validación de datos

        $profile = new Profile($this->pdo);
        $profile->id = $id;
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = (int) $data['children'];
        $profile->initial_balance = (float) $data['initial_balance'];
        $profile->assets = (float) $data['assets']; // En la edición, los activos pueden ser diferentes
        $profile->user_id = Auth::id();

        if ($profile->save()) {
            Response::redirect('/dashboard');
        } else {
            // Manejar error
            $profileData = (array) $profile;
            View::render('profiles/edit', ['title' => 'Editar Perfil', 'profile' => $profileData, 'error' => 'No se pudo actualizar el perfil.']);
        }
    }


    /**
     * Muestra un perfil financiero específico y la configuración de la cuenta del usuario.
     *
     * @param Request $request La petición HTTP, usada para obtener el ID de la ruta.
     * @return void
     */
    public function show(Request $request): void
    {
        // Ahora getRouteParam('id') devolverá el valor correcto (ej: 1)
        $id = (int)$request->getRouteParam('id');
        $profileModel = new Profile($this->pdo);

        // find($id) ahora buscará el perfil correcto.
        $profile = $profileModel->find($id);

        // La condición de seguridad ahora funcionará como se espera.
        // Se usa (int) por seguridad, para asegurar la comparación de tipos.
        if (!$profile || (int)$profile['user_id'] !== Auth::id()) {
            Response::redirect('/profiles', ['error' => 'Acceso denegado. No tienes permiso para ver este perfil.']);
            return;
        }

        // Si la autorización es exitosa, se renderiza la vista.
        $user = Auth::user($this->pdo);

        View::render('profiles/show', [
            'title' => 'Detalles del Perfil: ' . htmlspecialchars($profile['name']),
            'profile' => $profile,
            'user' => $user
        ]);
    }

    /**
     * Elimina un perfil.
     */
    public function destroy(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $profileModel = new Profile($this->pdo);

        if (!$id || !$profileModel->isOwnedByUser($id, Auth::id())) {
            Response::redirect('/dashboard', ['error' => 'Perfil no encontrado o sin permisos.']);
            return;
        }

        // Cargar el modelo para poder usar el método delete de instancia
        $profileToDelete = new Profile($this->pdo);
        $profileToDelete->id = $id;

        // Aquí deberías añadir lógica para borrar gastos/ingresos asociados o reasignarlos
        
        if ($profileToDelete->delete()) {
            Response::redirect('/dashboard');
        } else {
            Response::redirect('/dashboard', ['error' => 'No se pudo eliminar el perfil.']);
        }
    }
}