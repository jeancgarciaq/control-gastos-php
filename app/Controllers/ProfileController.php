<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Profile;
use App\Requests\ProfileRequest;
use App\Services\BalanceService;
use PDO;

/**
 * Class ProfileController
 * Handles profile-related requests (creation, editing, showing, deleting).
 */
class ProfileController
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
     * ProfileController constructor.
     *
     * @param PDO $pdo The database connection object.
    */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
        // Inyectamos la conexión al servicio también.
        $this->balanceService = new BalanceService($this->pdo);
    }

    /**
     * Displays the profile creation form.
     *
     * @return void
    */
    public function create()
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        View::render('profile/create', ['title' => 'Create Profile']);
    }

    /**
     * Processes the profile creation form submission.
     *
     * @param Request $request The request object.
     * @return void
    */
    public function store(Request $request)
    {
        if (Auth::guest()) Response::redirect('/login');

        $data = $request->getBody();
        $profileRequest = new ProfileRequest();

        if (!$profileRequest->validate($data)) {
            View::render('profile/create', ['title' => 'Create Profile', 'errors' => $profileRequest->errors(), 'data' => $data]);
            return;
        }

        $profile = new Profile($this->pdo);
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = (int)$data['children'];
        $profile->assets = (float)$data['assets'];
        $profile->initial_balance = (float)$data['initial_balance'];
        $profile->user_id = Auth::id();

        if ($profile->create()) {
            // Actualizamos los assets después de crear el perfil.
            $this->balanceService->updateProfileAssets($profile->id);
            Response::redirect('/profile/' . $profile->id);
        } else {
            View::render('profile/create', ['title' => 'Create Profile', 'errors' => ['general' => ['Failed to create profile.']], 'data' => $data]);
        }
    }

    /**
     * Displays a specific profile.
     *
     * @param Request $request The request object (contiene parámetros de la URL).
     * @return void
    */
    public function show(Request $request)
    {
        if (Auth::guest()) Response::redirect('/login');
        
        // Obtiene el 'id' de la ruta usando el nuevo método
        $id = $request->getRouteParam('id');

        if (!$id) {
            // Manejar error si no hay ID
            Response::redirect('/dashboard');
        }

        $profileModel = new Profile($this->pdo);
        $profile = $profileModel->find((int)$id);


        if (!$profile || $profile['user_id'] !== Auth::id()) {
            Response::redirect('/dashboard');
        }

        $balance = $this->balanceService->calculateBalance($id);

        View::render('profile/show', ['title' => 'Profile Details', 'profile' => $profile, 'balance' => $balance]);
    }

    /**
     * Displays the profile editing form.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the profile to edit.
     * @return void
    */
    public function edit(Request $request): void
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

        $profileModel = new Profile($this->pdo);
        $profile = $profileModel->find((int)$id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        View::render('profile/edit', ['title' => 'Edit Profile', 'profile' => $profile]);
    }

    /**
     * Processes the profile editing form submission.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the profile to update.
     * @return void
    */
    public function update(Request $request): void
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

        $profileModel = new Profile($this->pdo);
        $profile = $profileModel->find((int)$id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        $data = $request->getBody();
        $profileRequest = new ProfileRequest($this->pdo);

        if (!$profileRequest->validate($data)) {
            View::render('profile/edit', ['title' => 'Edit Profile', 'errors' => $profileRequest->errors(), 'profile' => $profile, 'data' => $data]);
            return;
        }

        $profile = new Profile($this->pdo);
        $profile->id = $id;
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = $data['children'];
        $profile->assets = $data['assets'];
        $profile->initial_balance = $data['initial_balance'];
        $profile->user_id = Auth::id();

        if ($profile->update()) {
            // Update assets after profile is updated.
            $this->balanceService->updateProfileAssets($profile->id);
            Response::redirect('/profile/' . $id);
        } else {
            View::render('profile/edit', ['title' => 'Edit Profile', 'errors' => ['general' => ['Failed to update profile.']], 'profile' => $profile, 'data' => $data]);
        }
    }

    /**
     * Displays the details of a specific profile. Also displays the calculated balance.
     *
     * @param int $id The ID of the profile to show.
     * @return void
    */
    public function show(Request $request): void
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

        $profileModel = new Profile($this->pdo);
        $profile = $profileModel->find((int)$id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        //Calculate the balance
        $balance = $this->balanceService->calculateBalance($id);

        View::render('profile/show', ['title' => 'Profile Details', 'profile' => $profile, 'balance' => $balance]);
    }

    /**
     * Deletes a profile.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the profile to delete.
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

        $profileModel = new Profile($this->pdo);
        $profile = $profileModel->find((int)$id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        //Delete the profile
        $profile = new Profile($this->pdo);
        $profile->id = $id;
        if ($this->deleteProfile($id)) {
            Response::redirect('/dashboard'); //Redirect to dashboard or another appropriate page
        } else {
            //Handle the case where the delete fails
            echo "Failed to delete profile."; //Or show an error message to the user
        }
    }

    /**
     * Delete profile from database
     * @param int $id
     * @return bool
    */
    public function deleteProfile(int $id): bool
    {
        $stmt = $this->pdo->prepare("DELETE FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}