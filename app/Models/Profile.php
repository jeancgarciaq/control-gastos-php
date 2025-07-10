<?php
/**
 * @file Profile.php
 * @package App\Models
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Modelo para representar y gestionar los perfiles financieros de los usuarios.
 */

namespace App\Models;

use PDO;

/**
 * Class Profile
 * Representa y gestiona los datos de un perfil financiero en la base de datos.
 */
class Profile
{
    private PDO $pdo;

    public ?int $id = null;
    public string $name;
    public string $phone;
    public int $user_id;
    public string $position_or_company;
    public string $marital_status;
    public int $children;
    public float $assets;
    public float $initial_balance;

    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Guarda el perfil actual en la base de datos.
     * Si el perfil no tiene ID, lo crea (INSERT). Si ya tiene ID, lo actualiza (UPDATE).
     *
     * @return bool True en éxito, false en fallo.
     */
    public function save(): bool
    {
        $sql = $this->id === null
            ? "INSERT INTO profile (name, phone, position_or_company, marital_status, children, assets, initial_balance, user_id) VALUES (:name, :phone, :position_or_company, :marital_status, :children, :assets, :initial_balance, :user_id)"
            : "UPDATE profile SET name = :name, phone = :phone, position_or_company = :position_or_company, marital_status = :marital_status, children = :children, assets = :assets, initial_balance = :initial_balance, user_id = :user_id WHERE id = :id";
        
        $stmt = $this->pdo->prepare($sql);

        if ($this->id !== null) {
            $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        }

        $stmt->bindValue(':name', $this->name);
        $stmt->bindValue(':phone', $this->phone);
        $stmt->bindValue(':position_or_company', $this->position_or_company);
        $stmt->bindValue(':marital_status', $this->marital_status);
        $stmt->bindValue(':children', $this->children, PDO::PARAM_INT);
        $stmt->bindValue(':assets', $this->assets);
        $stmt->bindValue(':initial_balance', $this->initial_balance);
        $stmt->bindValue(':user_id', $this->user_id, PDO::PARAM_INT);

        $result = $stmt->execute();

        if ($result && $this->id === null) {
            $this->id = (int)$this->pdo->lastInsertId();
        }

        return $result;
    }
    
    /**
     * Elimina el perfil actual de la base de datos.
     * Solo funciona si el objeto tiene un ID.
     *
     * @return bool True en éxito, false en fallo.
     */
    public function delete(): bool
    {
        if ($this->id === null) {
            return false; // No se puede borrar un perfil que no existe en la BD.
        }
        $stmt = $this->pdo->prepare("DELETE FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $this->id, PDO::PARAM_INT);
        return $stmt->execute();
    }

    /**
     * Busca un perfil por su ID.
     *
     * @param int $id El ID del perfil.
     * @return array|false Un array con los datos del perfil, o false si no se encuentra.
    */
    public function find(int $id): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Recupera todos los perfiles asociados a un usuario.
     *
     * @param int $userId El ID del usuario.
     * @return array Un array de perfiles.
     */
    public function getAllForUser(int $userId): array
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id ORDER BY name ASC");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Comprueba si un perfil pertenece a un usuario específico.
     *
     * @param int $profileId El ID del perfil.
     * @param int $userId El ID del usuario.
     * @return bool True si el perfil pertenece al usuario, false en caso contrario.
     */
    public function isOwnedByUser(int $profileId, int $userId): bool
    {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) FROM profile WHERE id = :id AND user_id = :user_id");
        $stmt->bindValue(':id', $profileId, PDO::PARAM_INT);
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (bool) $stmt->fetchColumn();
    }

    /**
     * @brief Encuentra un perfil basado en el ID del usuario propietario.
     * @param int $userId El ID del usuario.
     * @return array|false Los datos del perfil o false si no se encuentra.
     */
    public function findByUserId(int $userId): array|false
    {
        $stmt = $this->pdo->prepare("SELECT * FROM profile WHERE user_id = :user_id LIMIT 1");
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}