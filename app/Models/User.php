<?php
/**
 * @file User.php
 * @package App\Models
 * @author jeancgarciaq
 * @version 1.0
 * @date 2025-07-10
 * @brief Modelo que representa la entidad de un usuario y gestiona su persistencia.
*/

namespace App\Models;

use PDO;

/**
 * @class User
 * @brief Representa a un usuario de la aplicación.
 * Contiene las propiedades del usuario y los métodos para interactuar
 * con la tabla 'users' en la base de datos.
 */
class User
{
    /**
     * @var PDO La conexión a la base de datos para realizar consultas.
    */
    private PDO $pdo;

    /**
     * @var int|null El identificador único del usuario. Es null si el usuario no ha sido guardado.
    */
    public ?int $id = null;

    /**
     * @var string El nombre de usuario, debe ser único.
    */
    public string $username;

    /**
     * @var string El correo electrónico del usuario, debe ser único.
    */
    public string $email;

    /**
     * @var string La contraseña del usuario, almacenada de forma segura (hasheada).
    */
    public string $password;

    /**
     * @brief Constructor del modelo User.
     * @param PDO $pdo La instancia de la conexión a la base de datos (Inyección de Dependencias).
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * @brief Actualiza el nombre de usuario y el email de un usuario.
     * @param int $id El ID del usuario.
     * @param string $username El nuevo nombre de usuario.
     * @param string $email El nuevo correo electrónico.
     * @return bool True si la actualización fue exitosa, false en caso contrario.
    */
    public function updateDetails(int $id, string $username, string $email): bool
    {
        $stmt = $this->pdo->prepare("UPDATE users SET username = :username, email = :email WHERE id = :id");
        $stmt->bindValue(':username', $username);
        $stmt->bindValue(':email', $email);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * @brief Actualiza la contraseña de un usuario.
     * @param int $id El ID del usuario.
     * @param string $newPassword La nueva contraseña (sin hashear).
     * @return bool True si la actualización fue exitosa, false en caso contrario.
    */
    public function updatePassword(int $id, string $newPassword): bool
    {
        $hashedPassword = password_hash($newPassword, PASSWORD_BCRYPT);
        $stmt = $this->pdo->prepare("UPDATE users SET password = :password WHERE id = :id");
        $stmt->bindValue(':password', $hashedPassword);
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Elimina el usuario actual de la base de datos.
     * Solo funciona si el objeto tiene un ID.
     *
     * @return bool True en éxito, false en fallo.
    */
    public function delete(): bool
    {
        if ($this->id === null) {
            return false; // No se puede borrar un usuario que no existe en la BD.
        }
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Encuentra un usuario por su ID.
     * @param int $id El ID del usuario.
     * @return array|false Los datos del usuario o false si no se encuentra.
     */
    public function findById(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por su nombre de usuario.
     *
     * @param string $username El nombre de usuario.
     * @return array|false Un array con los datos del usuario, o false si no se encuentra.
     */
    public function findByUsername(string $username): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Busca un usuario por su email.
     *
     * @param string $email El email.
     * @return array|false Un array con los datos del usuario, o false si no se encuentra.
     */
    public function findByEmail(string $email): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}