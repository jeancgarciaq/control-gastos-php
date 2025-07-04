<?php

namespace App\Models;

use App\Core\Database;
use PDO;

/**
 * Class Expense
 * Represents an expense entry.
 */
class Expense
{
    /** @var int|null The expense ID. Null if the expense hasn't been saved to the database yet. */
    public ?int $id = null;

    /** @var string The date of the expense. */
    public string $date;

    /** @var string The description of the expense. */
    public string $description;

    /** @var float The amount of the expense. */
    public float $amount;

    /** @var string The type of the expense. */
    public string $type;

    /** @var int The ID of the profile associated with the expense. */
    public int $profile_id;

    /**
     * Creates a new expense in the database.
     * Sets the ID of the expense object if creation is successful.
     *
     * @return bool True on success, false on failure.
     */
    public function create(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("INSERT INTO expenses (date, description, amount, type, profile_id) VALUES (:date, :description, :amount, :type, :profile_id)");
        $stmt->bindValue(':date', $this->date);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':amount', $this->amount);
        $stmt->bindValue(':type', $this->type);
        $stmt->bindValue(':profile_id', $this->profile_id, PDO::PARAM_INT);

        $result = $stmt->execute();

        if ($result) {
            $this->id = (int)$db->lastInsertId(); //Set the ID after successful creation
        }

        return $result;
    }

    /**
     * Finds an expense by its ID.
     *
     * @param int $id The expense ID.
     * @return mixed An array containing the expense data, or false if not found.
     */
    public function find(int $id): mixed
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT * FROM expenses WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetch();
    }

    /**
     * Updates an existing expense in the database.
     *
     * @return bool True on success, false on failure.
     */
    public function update(): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("UPDATE expenses SET date = :date, description = :description, amount = :amount, type = :type, profile_id = :profile_id WHERE id = :id");
        $stmt->bindValue(':date', $this->date);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':amount', $this->amount);
        $stmt->bindValue(':type', $this->type);
        $stmt->bindValue(':profile_id', $this->profile_id, PDO::PARAM_INT);
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);

        return $stmt->execute();
    }

    /**
     * Retrieves all expenses associated with a given user.
     *
     * @param int $userId The ID of the user.
     * @return array An array containing the user's expenses.
     */
    public function getAllForUser(int $userId): array
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("SELECT expenses.* FROM expenses INNER JOIN profile ON expenses.profile_id = profile.id WHERE profile.user_id = :user_id");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();

        return $stmt->fetchAll();
    }

    /**
     * Delete expense from database
     * @param int $id
     * @return bool
     */
    public function deleteExpense(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}