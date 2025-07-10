<?php
/**
 * @file UserController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Controlador para gestionar la cuenta del usuario (perfil, contraseña).
 */

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use PDO;

/**
 * Class UserController
 * Gestiona las acciones relacionadas con la cuenta del usuario, como la edición
 * de su información personal y la actualización de su contraseña.
 * Hereda de AuthenticatedController, por lo que todas sus acciones requieren que el usuario esté logueado.
 */
class UserController extends AuthenticatedController
{
    /**
     * Muestra el formulario para editar los datos de la cuenta del usuario.
     *
     * @return void
     */
    public function edit(): void
    {
        // El constructor padre ya ha verificado que el usuario está autenticado.
        // Obtenemos los datos del usuario actual para rellenar el formulario.
        $userModel = new User($this->pdo);
        $user = $userModel->find(Auth::id());

        // Si por alguna razón no se encuentra el usuario (muy improbable), cerramos sesión.
        if (!$user) {
            Auth::logout();
            Response::redirect('/login');
            return;
        }

        // Renderiza la vista del formulario de edición de cuenta.
        View::render('users/edit', [
            'title' => 'Configuración de mi Cuenta',
            'user' => $user
        ]);
    }

    /**
     * Procesa la actualización de los datos del perfil del usuario (email, username).
     * NO actualiza la contraseña.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function update(Request $request): void
    {
        $data = $request->getBody();
        $userId = Auth::id();

        // --- Validación (Aquí iría la lógica de una clase UserRequest) ---
        // 1. Validar que el email es un email válido.
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            // Manejar error de validación
            Response::redirect('/user/edit', ['error' => 'El formato del email no es válido.']);
            return;
        }
        // 2. Validar que el nuevo email o username no estén ya en uso por OTRO usuario.
        $userModel = new User($this->pdo);
        $existingUser = $userModel->findByEmail($data['email']);
        if ($existingUser && $existingUser['id'] !== $userId) {
            Response::redirect('/user/edit', ['error' => 'El email ya está en uso por otra cuenta.']);
            return;
        }
        // (Harías lo mismo para el username)

        // --- Actualización ---
        $userToUpdate = new User($this->pdo);
        $currentUserData = $userModel->find($userId);

        $userToUpdate->id = $userId;
        $userToUpdate->username = $data['username'] ?? $currentUserData['username'];
        $userToUpdate->email = $data['email'] ?? $currentUserData['email'];
        // Mantenemos la contraseña existente, ya que no se actualiza aquí.
        $userToUpdate->password = $currentUserData['password'];

        if ($userToUpdate->save()) {
            Response::redirect('/user/edit', ['success' => 'Tus datos han sido actualizados con éxito.']);
        } else {
            Response::redirect('/user/edit', ['error' => 'Hubo un error al actualizar tus datos.']);
        }
    }

    /**
     * Procesa la actualización de la contraseña del usuario.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function updatePassword(Request $request): void
    {
        $data = $request->getBody();
        $userId = Auth::id();

        // --- Validación ---
        if (empty($data['current_password']) || empty($data['new_password']) || empty($data['password_confirmation'])) {
            Response::redirect('/user/edit', ['error_password' => 'Todos los campos de contraseña son requeridos.']);
            return;
        }

        if ($data['new_password'] !== $data['password_confirmation']) {
            Response::redirect('/user/edit', ['error_password' => 'La nueva contraseña y su confirmación no coinciden.']);
            return;
        }

        // (Opcional) Añadir reglas de fortaleza para la nueva contraseña (ej. longitud mínima).
        if (strlen($data['new_password']) < 8) {
             Response::redirect('/user/edit', ['error_password' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
            return;
        }

        // --- Verificación de la contraseña actual ---
        $userModel = new User($this->pdo);
        $currentUserData = $userModel->find($userId);

        if (!password_verify($data['current_password'], $currentUserData['password'])) {
            Response::redirect('/user/edit', ['error_password' => 'La contraseña actual es incorrecta.']);
            return;
        }

        // --- Actualización ---
        $userToUpdate = new User($this->pdo);
        $userToUpdate->id = $userId;
        $userToUpdate->username = $currentUserData['username'];
        $userToUpdate->email = $currentUserData['email'];
        // Hasheamos la nueva contraseña antes de guardarla.
        $userToUpdate->password = password_hash($data['new_password'], PASSWORD_DEFAULT);

        if ($userToUpdate->save()) {
            Response::redirect('/user/edit', ['success_password' => 'Tu contraseña ha sido cambiada con éxito.']);
        } else {
            Response::redirect('/user/edit', ['error_password' => 'Hubo un error al cambiar tu contraseña.']);
        }
    }
}