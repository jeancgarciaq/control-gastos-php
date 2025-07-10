<?php

namespace App\Models;

use PDO;

/**
 * Class User
 * Represents a user.
 */
class User
{
     /**
     * @var PDO The database connection object.
     */
    private PDO $pdo;

    /** @var int|null The user ID. Null if the user hasn't been saved to the database yet. */
    public ?int $id = null;

    /** @var string The username. */
    public string $username;

    /** @var string The email address. */
    public string $email;

    /** @var string The password (hashed). */
    public string $password;

     /**
     * Income constructor.
     *
     * @param PDO $pdo The database connection object.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new user in the database.
     * Sets the ID of the user object if creation is successful.
     *
     * @return bool True on success, false on failure.
     */
    public function create(): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO users (username, email, password) VALUES (:username, :email, :password)");
        $stmt->bindValue(':username', $this->username);
        $stmt->bindValue(':email', $this->email);
        $stmt->bindValue(':password', $this->password);

        $result = $stmt->execute();

        if ($result) {
            $this->id = (int)$this->pdo->lastInsertId();
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
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE id = :id");
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
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE username = :username");
        $stmt->bindValue(':username', $username);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Finds a user by its email.
     *
     * @param string $email The email.
     * @return mixed An array containing the user data, or false if not found.
     */
    public function findByEmail(string $email): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM users WHERE email = :email");
        $stmt->bindValue(':email', $email);
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
        $stmt = $this->pdo->prepare("DELETE FROM users WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}