<?php

namespace App\Models;

use PDO;

/**
 * Class User
 * Representa y gestiona los datos de un usuario en la base de datos.
 */
class User
{
    private PDO $pdo;
    public ?int $id = null;
    public string $username;
    public string $email;
    public string $password;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Guarda el usuario actual en la base de datos.
     * Si el usuario no tiene ID, lo crea. Si ya tiene ID, lo actualiza.
     *
     * @return bool True en éxito, false en fallo.
     */
    public function save(): bool
    {
        if ($this->id === null) {
            // Lógica de creación
            $stmt = $this->pdo->prepare(
                "INSERT INTO users (username, email, password) VALUES (:username, :email, :password)"
            );
        } else {
            // Lógica de actualización
            $stmt = $this->pdo->prepare(
                "UPDATE users SET username = :username, email = :email, password = :password WHERE id = :id"
            );
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', $this->password);

        $result = $stmt->execute();

        if ($result && $this->id === null) {
            $this->id = (int)$this->pdo->lastInsertId();
        }

        return $result;
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
     * Busca un usuario por su ID.
     *
     * @param int $id El ID del usuario.
     * @return array|false Un array con los datos del usuario, o false si no se encuentra.
     */
    public function find(int $id): array|false
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