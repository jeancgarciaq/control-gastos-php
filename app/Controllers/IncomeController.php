<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Income;
use App\Models\Profile;
use App\Requests\IncomeRequest;
use App\Services\BalanceService;
use PDO;

/**
 * Class IncomeController
 * Handles income-related requests.
*/
class IncomeController
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
     * IncomeController constructor.
     *
     * @param PDO $pdo The database connection object.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        $this->balanceService = new BalanceService($this->pdo);
    }

    /**
     * Displays a list of all income for the user.
     *
     * @return void
    */
    public function index()
    {
        if (Auth::guest()) Response::redirect('/login');

        $incomeModel = new Income($this->pdo);
        $incomes = $incomeModel->getAllForUser(Auth::id());
        
        View::render('income/index', ['title' => 'My Income', 'income' => $incomes]);
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

        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
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
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('income/create', ['title' => 'Create Income', 'errors' => $incomeRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $income = new Income($this->pdo);
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
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
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

        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find((int)$id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); 
        }
        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        View::render('income/edit', ['title' => 'Edit Income', 'income' => $income, 'profiles' => $profiles]);
    }

    /**
     * Processes the income editing form submission.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the income entry to update.
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

        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find((int)$id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income');
        }

        $data = $request->getBody();
        $incomeRequest = new IncomeRequest();

        if (!$incomeRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('income/edit', ['title' => 'Edit Income', 'errors' => $incomeRequest->errors(), 'income' => $income, 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $income = new Income($this->pdo);
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
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            View::render('income/edit', ['title' => 'Edit Income', 'errors' => ['general' => ['Failed to update income.']], 'income' => $income, 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Displays the details of a specific income entry.
     *
     * @param int $id The ID of the income entry to show.
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

        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find((int)$id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income');
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

        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find((int)$id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/income'); // or error
        }

        //Delete the income
        $income = new Income($this->pdo);
        $income->id = $id;
        $profile_id = $income->profile_id; //Get the profile_id to update the balance
        if ($this->deleteIncome($id)) {
            //Update the profile assets with the new balance
            $this->balanceService->updateProfileAssets($profile_id);
            Response::redirect('/income');
        } else {
            //Handle the case where the delete fails
            echo "Failed to delete income.";
        }
    }

    /**
     * Delete income from database
     * @param int $id
     * @return bool
     */
    public function deleteIncome(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM income WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}