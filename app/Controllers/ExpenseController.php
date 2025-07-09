<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Expense;
use App\Models\Profile;
use App\Requests\ExpenseRequest;
use App\Services\BalanceService;
use PDO;

/**
 * Class ExpenseController
 * Handles expense-related requests.
*/
class ExpenseController
{
    /**
     * @var PDO The database connection object.
    */
    private PDO $pdo;

    /**
     * @var BalanceService The service for calculating balances.
    */
    private BalanceService $balanceService;

    /**
     * ExpenseController constructor.
     *
     * @param PDO $pdo The database connection object.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->balanceService = new BalanceService($this->pdo);
    }

    /**
     * Displays a list of all expenses for the user.
     *
     * @return void
    */
    public function index()
    {
        if (Auth::guest()) Response::redirect('/login');

        $expenseModel = new Expense($this->pdo);
        $expenses = $expenseModel->getAllForUser(Auth::id());
        
        View::render('expenses/index', ['title' => 'My Expenses', 'expenses' => $expenses]);
    }

    /**
     * Displays the expense creation form.
     *
     * @return void
     */
    public function create()
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        View::render('expenses/create', ['title' => 'Create Expense', 'profiles' => $profiles]);
    }

    /**
     * Processes the expense creation form submission.
     *
     * @param Request $request The request object.
     * @return void
     */
    public function store(Request $request)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $data = $request->getBody();
        $expenseRequest = new ExpenseRequest();

        if (!$expenseRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('expenses/create', ['title' => 'Create Expense', 'errors' => $expenseRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $expense = new Expense($this->pdo);
        $expense->date = $data['date'];
        $expense->description = $data['description'];
        $expense->amount = $data['amount'];
        $expense->type = $data['type'];
        $expense->profile_id = $data['profile_id'];

        if ($expense->create()) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($expense->profile_id);
            Response::redirect('/expenses');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('expenses/create', ['title' => 'Create Expense', 'errors' => ['general' => ['Failed to create expense.']], 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the expense editing form.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the expense to edit.
     * @return void
     */
    public function edit(Request $request)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        // Obtiene el 'id' de la ruta usando el nuevo método
        $id = $request->getRouteParam('id');

        if (!$id) {
            // Manejar error si no hay ID
            Response::redirect('/dashboard');
        }

        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find((int)$id);

        if (!$expense || !(new Profile())->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses');
        }
        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        View::render('expenses/edit', ['title' => 'Edit Expense', 'expense' => $expense, 'profiles' => $profiles]);
    }

    /**
     * Processes the expense editing form submission.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the expense to update.
     * @return void
     */
    public function update(Request $request)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        
        // Obtiene el 'id' de la ruta usando el nuevo método
        $id = $request->getRouteParam('id');

        if (!$id) {
            // Manejar error si no hay ID
            Response::redirect('/dashboard');
        }

        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find((int)$id);

        if (!$expense || !(new Profile($this->pdo))->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses');
        }

        $data = $request->getBody();
        $expenseRequest = new ExpenseRequest();

        if (!$expenseRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('expenses/edit', ['title' => 'Edit Expense', 'errors' => $expenseRequest->errors(), 'expense' => $expense, 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $expense = new Expense($this->pdo);
        $expense->id = $id;
        $expense->date = $data['date'];
        $expense->description = $data['description'];
        $expense->amount = $data['amount'];
        $expense->type = $data['type'];
        $expense->profile_id = $data['profile_id'];

        if ($expense->update()) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($expense->profile_id);
            Response::redirect('/expenses');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('expenses/edit', ['title' => 'Edit Expense', 'errors' => ['general' => ['Failed to update expense.']], 'expense' => $expense, 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the details of a specific expense.
     *
     * @param int $id The ID of the expense to show.
     * @return void
     */
    public function show(Request $request)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        // Obtiene el 'id' de la ruta usando el nuevo método
        $id = $request->getRouteParam('id');

        if (!$id) {
            // Manejar error si no hay ID
            Response::redirect('/dashboard');
        }

        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find((int)$id);

        if (!$expense || !(new Profile($this->pdo))->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses'); // or error
        }

        View::render('expenses/show', ['title' => 'Expense Details', 'expense' => $expense]);
    }

    /**
     * Deletes an expense.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the expense to delete.
     * @return void
     */
    public function destroy(Request $request): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        
        // Obtiene el 'id' de la ruta usando el nuevo método
        $id = $request->getRouteParam('id');

        if (!$id) {
            // Manejar error si no hay ID
            Response::redirect('/dashboard');
        }

        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find((int)$id);

        if (!$expense || !(new Profile($this->pdo))->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses'); // or error
        }

        //Delete the expense
        $expense = new Expense($this->pdo);
        $expense->id = $id;
        $profile_id = $expense->profile_id;
        if ($this->deleteExpense($id)) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($profile_id);
            Response::redirect('/expenses'); //Redirect to expenses page
        } else {
            //Handle the case where the delete fails
            echo "Failed to delete expense.";
        }
    }

    /**
     * Delete expense from database
     * @param int $id
     * @return bool
     */
    public function deleteExpense(int $id): bool
    {
        $stmt = $$this->pdo->prepare("DELETE FROM expenses WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}