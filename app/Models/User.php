<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Class User
 * Represents a user.
 */
class User
{
    /** @var int|null The user ID. Null if the user hasn't been saved to the database yet. */
    public ?int $id = null;

    /** @var string The username. */
    public string $username;

    /** @var string The email address. */
    public string $email;

    /** @var string The password (hashed). */
    public string $password;

    /**
     * Creates a new user in the database.
     * Sets the ID of the user object if creation is successful.
     *
     * @return bool True on success, false on failure.
     */
    public function create(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO user (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', $this->password);

        $result = $stmt->execute();

        if ($result) {
            $this->id = (int)$db->lastInsertId(); //Set the ID after successful creation
        }

        return $result;
    }

    /**
     * Finds a user by its ID.
     *
     * @param int $id The user ID.
     * @return mixed An array containing the user data, or false if not found.
     */
    public function find(int $id): mixed
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM user WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Finds a user by its username.
     *
     * @param string $username The username.
     * @return mixed An array containing the user data, or false if not found.
     */
    public function findByUsername(string $username): mixed
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM user WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    //You might add update() and delete() methods here if needed

    /**
     * Delete income from database
     * @param int $id
     * @return bool
     */
    public function deleteUser(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM user WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}