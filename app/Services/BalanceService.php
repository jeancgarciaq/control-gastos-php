<?php

namespace App\Services;

use App\Models\Profile;
use App\Models\Income;
use App\Models\Expense;
use App\Core\Database;
use PDO;

/**
 * Class BalanceService
 * Provides methods for calculating and updating profile balances.
 */
class BalanceService
{
    /**
        * @var PDO The database connection object.
    */
        private PDO $pdo;

    /**
     * BalanceService constructor.
     *
     * @param PDO $pdo The database connection object.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
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