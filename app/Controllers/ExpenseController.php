<?php
/**
 * @file ExpenseController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @date 2025-07-10
 * @brief Controlador para gestionar los gastos de usuarios.
 */

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
 * @class ExpenseController
 * @brief Gestiona las peticiones relacionadas con los gastos (CRUD).
 * Hereda de AuthenticatedController, por lo que todas sus acciones requieren autenticación.
 */
class ExpenseController extends AuthenticatedController
{
    /**
     * @var BalanceService El servicio para calcular balances.
     */
    private BalanceService $balanceService;

    /**
     * Constructor de ExpenseController.
     *
     * @param PDO $pdo La instancia de conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->balanceService = new BalanceService($this->pdo);
    }

    /**
     * Muestra una lista de todos los gastos del usuario.
     *
     * @return void
     */
    public function index(): void
    {
        $expenseModel = new Expense($this->pdo);
        $expenses = $expenseModel->getAllForUser(Auth::id());
        
        $this->view('expenses/index', [
            'title' => 'Mis Gastos', 
            'expenses' => $expenses
        ]);
    }

    /**
     * Muestra el formulario para crear un nuevo gasto.
     *
     * @return void
     */
    public function create(): void
    {
        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        $this->view('expenses/create', [
            'title' => 'Crear Gasto', 
            'profiles' => $profiles
        ]);
    }

    /**
     * Almacena un nuevo gasto en la base de datos.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function store(Request $request): void
    {
        $data = $request->getBody();
        $expenseRequest = new ExpenseRequest(); // Asumiendo que esta clase valida los datos

        if (!$expenseRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('expenses/create', ['title' => 'Crear Gasto', 'errors' => $expenseRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $expense = new Expense($this->pdo);
        $expense->date = $data['date'];
        $expense->description = $data['description'];
        $expense->amount = (float) $data['amount'];
        $expense->type = $data['type'];
        $expense->profile_id = (int) $data['profile_id'];

        if ($expense->save()) {
            $this->balanceService->updateProfileAssets($expense->profile_id);
            Response::redirect('/expenses');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('expenses/create', ['title' => 'Crear Gasto', 'errors' => ['general' => ['Error al crear el gasto.']], 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Muestra el formulario para editar un gasto.
     *
     * @param Request $request La petición HTTP, usada para obtener el ID de la ruta.
     * @return void
     */
    public function edit(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find($id);

        if (!$expense || !(new Profile($this->pdo))->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses', ['error' => 'Gasto no encontrado o sin permisos.']);
            return;
        }

        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        $this->view('expenses/edit', ['title' => 'Editar Gasto', 'expense' => $expense, 'profiles' => $profiles]);
    }

    /**
     * Actualiza un gasto existente en la base de datos.
     *
     * @param Request $request La petición HTTP con los datos y el ID de la ruta.
     * @return void
     */
    public function update(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $data = $request->getBody();
        $expenseModel = new Expense($this->pdo);
        $originalExpense = $expenseModel->find($id);

        if (!$originalExpense || !(new Profile($this->pdo))->isOwnedByUser($originalExpense['profile_id'], Auth::id())) {
            Response::redirect('/expenses', ['error' => 'Gasto no encontrado o sin permisos.']);
            return;
        }

        $expenseRequest = new ExpenseRequest();
        if (!$expenseRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $data['id'] = $id;
            $this->view('expenses/edit', ['title' => 'Editar Gasto', 'errors' => $expenseRequest->errors(), 'expense' => $data, 'profiles' => $profiles]);
            return;
        }
        
        $expenseToUpdate = new Expense($this->pdo);
        $expenseToUpdate->id = $id;
        $expenseToUpdate->date = $data['date'];
        $expenseToUpdate->description = $data['description'];
        $expenseToUpdate->amount = (float) $data['amount'];
        $expenseToUpdate->type = $data['type'];
        $expenseToUpdate->profile_id = (int) $data['profile_id'];

        if ($expenseToUpdate->save()) {
            $this->balanceService->updateProfileAssets($expenseToUpdate->profile_id);
            Response::redirect('/expenses');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('expenses/edit', ['title' => 'Editar Gasto', 'errors' => ['general' => ['Error al actualizar el gasto.']], 'expense' => $originalExpense, 'profiles' => $profiles]);
        }
    }

    /**
     * Elimina un gasto de la base de datos.
     *
     * @param Request $request La petición HTTP, usada para obtener el ID de la ruta.
     * @return void
     */
    public function destroy(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $expenseModel = new Expense($this->pdo);
        $expense = $expenseModel->find($id);

        if (!$expense || !(new Profile($this->pdo))->isOwnedByUser($expense['profile_id'], Auth::id())) {
            Response::redirect('/expenses', ['error' => 'Gasto no encontrado o sin permisos.']);
            return;
        }

        $expenseToDelete = new Expense($this->pdo);
        $expenseToDelete->id = $id;

        if ($expenseToDelete->delete()) {
            // Usamos el profile_id del gasto que recuperamos antes de borrarlo.
            $this->balanceService->updateProfileAssets($expense['profile_id']);
            Response::redirect('/expenses', ['success' => 'Gasto eliminado con éxito.']);
        } else {
            Response::redirect('/expenses', ['error' => 'No se pudo eliminar el gasto.']);
        }
    }
}