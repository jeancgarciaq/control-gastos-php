<?php
/**
 * @file BalanceService.php
 * @package App\Services
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Servicio para calcular balances de perfiles y totales globales del usuario.
 */

namespace App\Services;

use PDO;

/**
 * @class BalanceService
 * @brief Proporciona métodos para calcular balances, tanto para perfiles individuales
 * como para el total de un usuario.
 */
class BalanceService
{
    /**
     * @var PDO La instancia de la conexión a la base de datos.
     */
    private PDO $pdo;

    /**
     * Constructor del BalanceService.
     * @param PDO $pdo La conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Calcula el total de ingresos de todos los perfiles de un usuario.
     *
     * @param int $userId El ID del usuario.
     * @return float El total de ingresos.
     */
    public function getGlobalTotalIncome(int $userId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(i.amount), 0) 
             FROM income i
             JOIN profile p ON i.profile_id = p.id
             WHERE p.user_id = :user_id"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Calcula el total de gastos de todos los perfiles de un usuario.
     *
     * @param int $userId El ID del usuario.
     * @return float El total de gastos.
     */
    public function getGlobalTotalExpenses(int $userId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(e.amount), 0) 
             FROM expenses e
             JOIN profile p ON e.profile_id = p.id
             WHERE p.user_id = :user_id"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        return (float) $stmt->fetchColumn();
    }

    /**
     * Calcula el balance global de un usuario.
     * Suma los saldos iniciales de todos sus perfiles, suma todos los ingresos
     * y resta todos los gastos.
     *
     * @param int $userId El ID del usuario.
     * @return float El balance global calculado.
     */
    public function getGlobalBalance(int $userId): float
    {
        $stmt = $this->pdo->prepare(
            "SELECT COALESCE(SUM(initial_balance), 0) 
             FROM profile 
             WHERE user_id = :user_id"
        );
        $stmt->bindValue(':user_id', $userId, PDO::PARAM_INT);
        $stmt->execute();
        $totalInitialBalance = (float) $stmt->fetchColumn();

        $totalIncome = $this->getGlobalTotalIncome($userId);
        $totalExpenses = $this->getGlobalTotalExpenses($userId);

        return $totalInitialBalance + $totalIncome - $totalExpenses;
    }

    /**
     * Calculates the current balance for a profile.
     *
     * @param int $profileId The ID of the profile.
     * @return float The calculated balance.
     */
    public function calculateBalance(int $profileId): float
    {
        
        // Get the initial balance
        $stmt = $this->pdo->prepare("SELECT initial_balance FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $profileId, PDO::PARAM_INT);
        $stmt->execute();
        $initialBalance = (float) $stmt->fetchColumn();

        // Calculate total income
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM income WHERE profile_id = :profile_id");
        $stmt->bindValue(':profile_id', $profileId, PDO::PARAM_INT);
        $stmt->execute();
        $totalIncome = (float) $stmt->fetchColumn();

        // Calculate total expenses
        $stmt = $this->pdo->prepare("SELECT COALESCE(SUM(amount), 0) FROM expenses WHERE profile_id = :profile_id");
        $stmt->bindValue(':profile_id', $profileId, PDO::PARAM_INT);
        $stmt->execute();
        $totalExpenses = (float) $stmt->fetchColumn();

        return $initialBalance + $totalIncome - $totalExpenses;
    }

    /**
     * Updates the `assets` field in the profile table with the current balance.
     *
     * @param int $profileId The ID of the profile to update.
     * @return bool True on success, false on failure.
     */
    public function updateProfileAssets(int $profileId): bool
    {
        $balance = $this->calculateBalance($profileId);

        $stmt = $this->pdo->prepare("UPDATE profile SET assets = :assets WHERE id = :id");
        $stmt->bindValue(':assets', $balance);
        $stmt->bindValue(':id', $profileId, PDO::PARAM_INT);

        return $stmt->execute();
    }
}