<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Requests\IncomeRequest;
use App\Models\Income;
use App\Models\Profile;
use App\Services\BalanceService;

/**
 * Class IncomeController
 * Handles income-related requests (listing, creation, editing, showing).
 */
class IncomeController
{
    /**
     * @var BalanceService The balance calculation service.
     */
    private BalanceService $balanceService;

    /**
     * IncomeController constructor.
     *
     * @param BalanceService|null $balanceService Optional balance service. If null, a new instance will be created.
     */
    public function __construct(BalanceService $balanceService = null)
    {
        $this->balanceService = $balanceService ?? new BalanceService();
    }

    /**
     * Displays a list of income entries for the current user.
     *
     * @return void
     */
    public function index()
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $income = (new Income())->getAllForUser(Auth::id());
        View::render('income/index', ['title' => 'Income', 'income' => $income]);
    }

    /**
     * Displays the income creation form.
     *
     * @return void
     */
    public function create()
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profiles = (new Profile())->getAllForUser(Auth::id());
        View::render('income/create', ['title' => 'Create Income', 'profiles' => $profiles]);
    }

    /**
     * Processes the income creation form submission.
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
        $incomeRequest = new IncomeRequest();

        if (!$incomeRequest->validate($data)) {
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('income/create', ['title' => 'Create Income', 'errors' => $incomeRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $income = new Income();
        $income->date = $data['date'];
        $income->description = $data['description'];
        $income->amount = $data['amount'];
        $income->type = $data['type'];
        $income->profile_id = $data['profile_id'];

        if ($income->create()) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($income->profile_id);
            Response::redirect('/income');
        } else {
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('income/create', ['title' => 'Create Income', 'errors' => ['general' => ['Failed to create income.']], 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the income editing form.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the income entry to edit.
     * @return void
     */
    public function edit(Request $request, int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $income = (new Income())->find($id);
        if (!$income || !(new Profile())->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); // or error
        }
        $profiles = (new Profile())->getAllForUser(Auth::id());
        View::render('income/edit', ['title' => 'Edit Income', 'income' => $income, 'profiles' => $profiles]);
    }

    /**
     * Processes the income editing form submission.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the income entry to update.
     * @return void
     */
    public function update(Request $request, int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        $income = (new Income())->find($id);

        if (!$income || !(new Profile())->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); // or error
        }

        $data = $request->getBody();
        $incomeRequest = new IncomeRequest();

        if (!$incomeRequest->validate($data)) {
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('income/edit', ['title' => 'Edit Income', 'errors' => $incomeRequest->errors(), 'income' => $income, 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $income = new Income();
        $income->id = $id;
        $income->date = $data['date'];
        $income->description = $data['description'];
        $income->amount = $data['amount'];
        $income->type = $data['type'];
        $income->profile_id = $data['profile_id'];

        if ($income->update()) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($income->profile_id);
            Response::redirect('/income');
        } else {
            $profiles = (new Profile())->getAllForUser(Auth::id());
            View::render('income/edit', ['title' => 'Edit Income', 'errors' => ['general' => ['Failed to update income.']], 'income' => $income, 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the details of a specific income entry.
     *
     * @param int $id The ID of the income entry to show.
     * @return void
     */
    public function show(int $id)
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $income = (new Income())->find($id);
        if (!$income || !(new Profile())->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); // or error
        }

        View::render('income/show', ['title' => 'Income Details', 'income' => $income]);
    }

    /**
     * Deletes an income.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the income to delete.
     * @return void
     */
    public function destroy(Request $request, int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $income = (new Income())->find($id);
        if (!$income || !(new Profile())->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); // or error
        }

        //Delete the income
        $income = new Income();
        $income->id = $id;
        $profile_id = $income->profile_id; //Get the profile_id to update the balance
        if ($this->deleteIncome($id)) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($profile_id);
            Response::redirect('/income'); //Redirect to income page or another appropriate page
        } else {
            //Handle the case where the delete fails
            echo "Failed to delete income."; //Or show an error message to the user
        }
    }

    /**
     * Delete income from database
     * @param int $id
     * @return bool
     */
    public function deleteIncome(int $id): bool
    {
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM income WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}