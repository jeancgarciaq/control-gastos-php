<?php

namespace App\Models;

use PDO;

/**
 * Class Profile
 * Represents a user profile.
 */
class Profile
{
    /**
     * @var PDO The database connection object.
     */
    private PDO $pdo;

    /** @var int|null The profile ID. Null if the profile hasn't been saved to the database yet. */
    public ?int $id = null;

    /** @var string The profile name. */
    public string $name;

    /** @var string The profile phone number. */
    public string $phone;

    /** @var string The user_id */
    public int $user_id;

    /** @var string The profile position or company. */
    public string $position_or_company;

    /** @var string The profile marital status. */
    public string $marital_status;

    /** @var int The number of children. */
    public int $children;

    /** @var float The profile assets.  This is the *displayed* asset value; the actual balance is calculated. */
    public float $assets;

    /** @var float The profile initial balance. */
    public float $initial_balance;

    /**
     * Profile constructor.
     *
     * @param PDO $pdo The database connection object.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Creates a new profile in the database.
     *  Sets the ID of the profile object if creation is successful.
     * @return bool True on success, false on failure.
     */
    public function create(): bool
    {
        $stmt = $this->pdo->prepare("INSERT INTO profile (name, phone, position_or_company, marital_status, children, assets, initial_balance, user_id) VALUES (:name, :phone, :position_or_company, :marital_status, :children, :assets, :initial_balance, :user_id)");
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->bindValue(':position_or_company', $this->position_or_company);
        $stmt->bindValue(':marital_status', $this->marital_status);
        $stmt->bindValue(':children', $this->children, PDO::PARAM_INT);
        $stmt->bindValue(':assets', $this->assets);
        $stmt->bindValue(':initial_balance', $this->initial_balance);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);

        $result = $stmt->execute();

        if ($result) {
            $this->id = (int)$this->pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * Finds a profile by its ID.
     *
     * @param int $id The profile ID.
     * @return mixed An array containing the profile data, or false if not found.
     */
    public function find(int $id): mixed
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Updates an existing profile in the database.
     *
     * @return bool True on success, false on failure.
     */
    public function update(): bool
    {
        $stmt = $this->pdo->prepare("UPDATE profile SET name = :name, phone = :phone, position_or_company = :position_or_company, marital_status = :marital_status, children = :children, assets = :assets, initial_balance = :initial_balance, user_id = :user_id WHERE id = :id");
        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->bindValue(':position_or_company', $this->position_or_company);
        $stmt->bindValue(':marital_status', $this->marital_status);
        $stmt->bindValue(':children', $this->children, PDO::PARAM_INT);
        $stmt->bindValue(':assets', $this->assets);
        $stmt->bindValue(':initial_balance', $this->initial_balance);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Retrieves all profiles associated with a given user.
     *
     * @param int $userId The ID of the user.
     * @return array An array containing the user's profiles.
     */
    public function getAllForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Checks if a profile is owned by a specific user.
     *
     * @param int $profileId The ID of the profile.
     * @param int $userId The ID of the user.
     * @return bool True if the profile is owned by the user, false otherwise.
     */
    public function isOwnedByUser(int $profileId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM profile WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $profileId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return (bool) $stmt->fetchColumn();
    }
}