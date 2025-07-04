<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Requests\ExpenseRequest;
use App\Models\Expense;
use App\Models\Profile;
use App\Services\BalanceService;

/**
 * Class ExpenseController
 * Handles expense-related requests (listing, creation, editing, showing).
 */
class ExpenseController
{
    /**
     * @var BalanceService The balance calculation service.
     */
    private BalanceService $balanceService;

    /**
     * ExpenseController constructor.
     *
     * @param BalanceService|null $balanceService Optional balance service. If null, a new instance will be created.
     */
    public function __construct(BalanceService $balanceService = null)
    {
        $this->balanceService = $balanceService ?? new BalanceService();
    }

    /**
     * Displays a list of expenses for the current user.
     *
     * @return void
     */
    public function index()
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $expenses = (new Expense())->getAllForUser(Auth::id());
        View::render('expenses/index', ['title' => 'Expenses', 'expenses' => $expenses]);
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

        $profiles = (new Profile())->getAllForUser(Auth::id());
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
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('expenses/create', ['title' => 'Create Expense', 'errors' => $expenseRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $expense = new Expense();
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
            $profiles = (new Profile())->getAllForUser(Auth::id());
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
    public function edit(Request $request, int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $expense = (new Expense())->find($id);
        if (!$expense || !(new Profile())->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses'); // or error
        }
        $profiles = (new Profile())->getAllForUser(Auth::id());
        View::render('expenses/edit', ['title' => 'Edit Expense', 'expense' => $expense, 'profiles' => $profiles]);
    }

    /**
     * Processes the expense editing form submission.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the expense to update.
     * @return void
     */
    public function update(Request $request, int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        $expense = (new Expense())->find($id);

        if (!$expense || !(new Profile())->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses'); // or error
        }

        $data = $request->getBody();
        $expenseRequest = new ExpenseRequest();

        if (!$expenseRequest->validate($data)) {
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('expenses/edit', ['title' => 'Edit Expense', 'errors' => $expenseRequest->errors(), 'expense' => $expense, 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $expense = new Expense();
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
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('expenses/edit', ['title' => 'Edit Expense', 'errors' => ['general' => ['Failed to update expense.']], 'expense' => $expense, 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the details of a specific expense.
     *
     * @param int $id The ID of the expense to show.
     * @return void
     */
    public function show(int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $expense = (new Expense())->find($id);
        if (!$expense || !(new Profile())->isOwnedByUser($expense['profile_id'], Auth::id())) {
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
    public function destroy(Request $request, int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $expense = (new Expense())->find($id);
        if (!$expense || !(new Profile())->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses'); // or error
        }

        //Delete the expense
        $expense = new Expense();
        $expense->id = $id;
        $profile_id = $expense->profile_id; //Get the profile_id to update the balance
        if ($this->deleteExpense($id)) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($profile_id);
            Response::redirect('/expenses'); //Redirect to expenses page or another appropriate page
        } else {
            //Handle the case where the delete fails
            echo "Failed to delete expense."; //Or show an error message to the user
        }
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