<?php
/**
 * @file Auth.php
 * @package App\Core
 * @author Jean Carlo Garcia
 * @version 1.1
 * @brief Gestiona la autenticación de usuarios (login, logout, estado).
 */

namespace App\Core;

use App\Models\User;
use PDO;

/**
 * @class Auth
 * @brief Proporciona métodos estáticos para manejar el ciclo de vida de la autenticación.
 */
class Auth
{
    /**
     * Intenta autenticar a un usuario con su nombre de usuario y contraseña.
     *
     * @param string $username El nombre de usuario.
     * @param string $password La contraseña en texto plano.
     * @param PDO $pdo La instancia de conexión a la base de datos.
     * @return bool Devuelve true si la autenticación es exitosa, de lo contrario false.
     */
    public static function attempt(string $username, string $password, PDO $pdo): bool
    {
        $userModel = new User($pdo);
        $user = $userModel->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Regenera el ID de sesión para prevenir ataques de fijación de sesión.
            session_regenerate_id(true);
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            return true;
        }

        return false;
    }

    /**
     * Verifica si hay un usuario autenticado.
     *
     * @return bool True si el usuario está logueado, false en caso contrario.
     */
    public static function check(): bool
    {
        return isset($_SESSION['user_id']);
    }

    /**
     * Cierra la sesión del usuario.
     *
     * @return void
     */
    public static function logout(): void
    {
        // Limpia todas las variables de sesión.
        $_SESSION = [];

        // Destruye la sesión.
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }

        session_destroy();
    }

    /**
     * Obtiene el ID del usuario autenticado.
     *
     * @return int|null El ID del usuario o null si no está logueado.
     */
    public static function id(): ?int
    {
        return $_SESSION['user_id'] ?? null;
    }
}