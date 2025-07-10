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
use App\Models\Profile; // Importamos el modelo Profile

/**
 * @class UserController
 * @brief Gestiona las acciones relacionadas con la cuenta del usuario.
 */
class UserController extends AuthenticatedController
{
    // El método edit() puede permanecer como está si planeas tener una página /user/edit separada.

    /**
     * Procesa la actualización de los datos del perfil del usuario (email, username).
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function update(Request $request): void
    {
        $userId = Auth::id();
        $userModel = new User($this->pdo);
        $profileModel = new Profile($this->pdo); // Instanciamos el modelo Profile

        // Determinar la URL de redirección correcta
        $profile = $profileModel->findByUserId($userId);
        $redirectUrl = $profile ? "/profiles/{$profile['id']}" : "/dashboard";

        $body = $request->getBody();
        $username = trim($body['username'] ?? '');
        $email = trim($body['email'] ?? '');

        // --- Validación ---
        if (empty($username) || empty($email)) {
            Response::redirect($redirectUrl, ['error' => 'El nombre de usuario y el correo no pueden estar vacíos.']);
            return;
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            Response::redirect($redirectUrl, ['error' => 'El formato del correo electrónico no es válido.']);
            return;
        }

        // --- Actualización ---
        if ($userModel->updateDetails($userId, $username, $email)) {
            Response::redirect($redirectUrl, ['success' => 'Tus datos han sido actualizados con éxito.']);
        } else {
            Response::redirect($redirectUrl, ['error' => 'Hubo un error al actualizar tus datos.']);
        }
    }

    /**
     * Procesa la actualización de la contraseña del usuario.
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function updatePassword(Request $request): void
    {
        $userId = Auth::id();
        $userModel = new User($this->pdo);
        $profileModel = new Profile($this->pdo); // Instanciamos el modelo Profile

        // Determinar la URL de redirección correcta
        $profile = $profileModel->findByUserId($userId);
        $redirectUrl = $profile ? "/profiles/{$profile['id']}" : "/dashboard";

        $body = $request->getBody();
        $currentPassword = $body['current_password'] ?? '';
        $newPassword = $body['new_password'] ?? '';
        $passwordConfirmation = $body['password_confirmation'] ?? '';

        // --- Validación ---
        $currentUser = $userModel->findById($userId);
        if (!$currentUser || !password_verify($currentPassword, $currentUser['password'])) {
            Response::redirect($redirectUrl, ['error' => 'La contraseña actual es incorrecta.']);
            return;
        }
        if (strlen($newPassword) < 8) {
             Response::redirect($redirectUrl, ['error' => 'La nueva contraseña debe tener al menos 8 caracteres.']);
            return;
        }
        if ($newPassword !== $passwordConfirmation) {
            Response::redirect($redirectUrl, ['error' => 'La nueva contraseña y su confirmación no coinciden.']);
            return;
        }

        // --- Actualización ---
        if ($userModel->updatePassword($userId, $newPassword)) {
            Response::redirect($redirectUrl, ['success' => 'Tu contraseña ha sido cambiada con éxito.']);
        } else {
            Response::redirect($redirectUrl, ['error' => 'Hubo un error al cambiar tu contraseña.']);
        }
    }
}