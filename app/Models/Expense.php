<?php
/**
 * @file Expense.php
 * @package App\Models
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Modelo para representar y gestionar los gastos.
 */

namespace App\Models;

use PDO;

/**
 * Class Expense
 * Representa y gestiona los datos de un gasto en la base de datos.
 */
class Expense
{
    private PDO $pdo;

    public ?int $id = null;
    public string $date;
    public string $description;
    public float $amount;
    public string $type;
    public int $profile_id;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Guarda el gasto actual en la base de datos.
     * Si el gasto no tiene ID, lo crea (INSERT). Si ya tiene ID, lo actualiza (UPDATE).
     *
     * @return bool True en éxito, false en fallo.
     */
    public function save(): bool
    {
        $sql = $this->id === null
            ? "INSERT INTO expenses (date, description, amount, type, profile_id) VALUES (:date, :description, :amount, :type, :profile_id)"
            : "UPDATE expenses SET date = :date, description = :description, amount = :amount, type = :type, profile_id = :profile_id WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);

        if ($this->id !== null) {
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':date', $this->date);
        $stmt->bindValue(':description', $this->description);
        $stmt->bindValue(':amount', $this->amount);
        $stmt->bindValue(':type', $this->type);
        $stmt->bindValue(':profile_id', $this->profile_id, PDO::PARAM_INT);

        $result = $stmt->execute();

        if ($result && $this->id === null) {
            $this->id = (int)$this->pdo->lastInsertId();
        }

        return $result;
    }

    /**
     * Elimina el gasto actual de la base de datos.
     * Solo funciona si el objeto tiene un ID.
     *
     * @return bool True en éxito, false en fallo.
     */
    public function delete(): bool
    {
        if ($this->id === null) {
            return false; // No se puede borrar un gasto que no existe en la BD.
        }
        $stmt = $this->pdo->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Busca un gasto por su ID.
     *
     * @param int $id El ID del gasto.
     * @return array|false Un array con los datos del gasto, o false si no se encuentra.
     */
    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM expenses WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera todos los gastos asociados a un usuario a través de sus perfiles.
     *
     * @param int $userId El ID del usuario.
     * @return array Un array de gastos.
     */
    public function getAllForUser(int $userId): array
    {
        // La consulta con JOIN es correcta y eficiente.
        $stmt = $this->pdo->prepare("SELECT expenses.* FROM expenses INNER JOIN profile ON expenses.profile_id = profile.id WHERE profile.user_id = :user_id ORDER BY expenses.date DESC");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}