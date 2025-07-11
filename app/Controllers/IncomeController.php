<?php
/**
 * @file IncomeController.php
 * @package App\Controllers
 * @author jeancgarciaq
 * @version 1.0
 * @date 2025-07-10
 * @brief Controlador para gestionar los Ingresos de los usuarios.
 */

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
 * @class IncomeController
 * @brief Gestiona las peticiones relacionadas con los ingresos (CRUD).
 * Hereda de AuthenticatedController, por lo que todas sus acciones requieren autenticación.
 */
class IncomeController extends AuthenticatedController
{
    /**
     * @var BalanceService El servicio para calcular balances.
     */
    private BalanceService $balanceService;

    /**
     * Constructor de IncomeController.
     *
     * @param PDO $pdo La instancia de conexión a la base de datos.
     */
    public function __construct(PDO $pdo)
    {
        parent::__construct($pdo);
        $this->balanceService = new BalanceService($this->pdo);
    }

    /**
     * Muestra una lista de todos los ingresos del usuario.
     *
     * @return void
     */
    public function index(): void
    {
        $incomeModel = new Income($this->pdo);
        $incomes = $incomeModel->getAllForUser(Auth::id());
        
        $this->view('incomes/index', ['title' => 'Mis Ingresos', 'incomes' => $incomes]);
    }

    /**
     * Muestra el formulario para crear un nuevo ingreso.
     *
     * @return void
     */
    public function create(): void
    {
        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        $this->view('incomes/create', ['title' => 'Crear Ingreso', 'profiles' => $profiles]);
    }

    /**
     * Almacena un nuevo ingreso en la base de datos.
     *
     * @param Request $request La petición HTTP con los datos del formulario.
     * @return void
     */
    public function store(Request $request): void
    {
        $data = $request->getBody();
        $incomeRequest = new IncomeRequest();

        if (!$incomeRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('incomes/create', ['title' => 'Crear Ingreso', 'errors' => $incomeRequest->errors(), 'data' => $data, 'profiles' => $profiles]);
            return;
        }

        $income = new Income($this->pdo);
        $income->date = $data['date'];
        $income->description = $data['description'];
        $income->amount = (float) $data['amount'];
        $income->type = $data['type']; // Campo 'type' añadido para consistencia
        $income->profile_id = (int) $data['profile_id'];

        if ($income->save()) {
            $this->balanceService->updateProfileAssets($income->profile_id);
            Response::redirect('/incomes');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('incomes/create', ['title' => 'Crear Ingreso', 'errors' => ['general' => ['Error al crear el ingreso.']], 'data' => $data, 'profiles' => $profiles]);
        }
    }

    /**
     * Muestra el formulario para editar un ingreso.
     *
     * @param Request $request La petición HTTP, usada para obtener el ID de la ruta.
     * @return void
     */
    public function edit(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find($id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/incomes', ['error' => 'Ingreso no encontrado o sin permisos.']);
            return;
        }

        $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
        $this->view('incomes/edit', ['title' => 'Editar Ingreso', 'income' => $income, 'profiles' => $profiles]);
    }

    /**
     * Actualiza un ingreso existente en la base de datos.
     *
     * @param Request $request La petición HTTP con los datos y el ID de la ruta.
     * @return void
     */
    public function update(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $data = $request->getBody();
        $incomeModel = new Income($this->pdo);
        $originalIncome = $incomeModel->find($id);

        if (!$originalIncome || !(new Profile($this->pdo))->isOwnedByUser($originalIncome['profile_id'], Auth::id())) {
            Response::redirect('/incomess', ['error' => 'Ingreso no encontrado o sin permisos.']);
            return;
        }

        $incomeRequest = new IncomeRequest();
        if (!$incomeRequest->validate($data)) {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $data['id'] = $id;
            $this->view('incomes/edit', ['title' => 'Editar Ingreso', 'errors' => $incomeRequest->errors(), 'income' => $data, 'profiles' => $profiles]);
            return;
        }
        
        $incomeToUpdate = new Income($this->pdo);
        $incomeToUpdate->id = $id;
        $incomeToUpdate->date = $data['date'];
        $incomeToUpdate->description = $data['description'];
        $incomeToUpdate->amount = (float) $data['amount'];
        $incomeToUpdate->type = $data['type']; // Campo 'type' añadido para consistencia
        $incomeToUpdate->profile_id = (int) $data['profile_id'];

        if ($incomeToUpdate->save()) {
            $this->balanceService->updateProfileAssets($incomeToUpdate->profile_id);
            Response::redirect('/incomess');
        } else {
            $profiles = (new Profile($this->pdo))->getAllForUser(Auth::id());
            $this->view('incomes/edit', ['title' => 'Editar Ingreso', 'errors' => ['general' => ['Error al actualizar el ingreso.']], 'income' => $originalIncome, 'profiles' => $profiles]);
        }
    }

    /**
     * Elimina un ingreso de la base de datos.
     *
     * @param Request $request La petición HTTP, usada para obtener el ID de la ruta.
     * @return void
     */
    public function destroy(Request $request): void
    {
        $id = (int)$request->getRouteParam('id');
        $incomeModel = new Income($this->pdo);
        $income = $incomeModel->find($id);

        if (!$income || !(new Profile($this->pdo))->isOwnedByUser($income['profile_id'], Auth::id())) {
            Response::redirect('/incomes', ['error' => 'Ingreso no encontrado o sin permisos.']);
            return;
        }

        $incomeToDelete = new Income($this->pdo);
        $incomeToDelete->id = $id;

        if ($incomeToDelete->delete()) {
            $this->balanceService->updateProfileAssets($income['profile_id']);
            Response::redirect('/incomes', ['success' => 'Ingreso eliminado con éxito.']);
        } else {
            Response::redirect('/incomes', ['error' => 'No se pudo eliminar el ingreso.']);
        }
    }
}