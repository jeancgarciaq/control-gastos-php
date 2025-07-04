<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Requests\ProfileRequest;
use App\Models\Profile;
use App\Services\BalanceService;

/**
 * Class ProfileController
 * Handles profile-related requests (creation, editing, showing).
 */
class ProfileController
{
    /**
     * @var BalanceService The balance calculation service.
     */
    private BalanceService $balanceService;

    /**
     * ProfileController constructor.
     *
     * @param BalanceService|null $balanceService Optional balance service. If null, a new instance will be created.
     */
    public function __construct(BalanceService $balanceService = null)
    {
        $this->balanceService = $balanceService ?? new BalanceService();
    }
    /**
     * Displays the profile creation form.
     *
     * @return void
     */
    public function create(): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }
        View::render('profile/create', ['title' => 'Create Profile']);
    }

    /**
     * Processes the profile creation form submission.
     *
     * @param Request $request The request object containing the form data.
     * @return void
     */
    public function store(Request $request): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $data = $request->getBody();
        $profileRequest = new ProfileRequest();

        if (!$profileRequest->validate($data)) {
            View::render('profile/create', ['title' => 'Create Profile', 'errors' => $profileRequest->errors(), 'data' => $data]);
            return;
        }

        $profile = new Profile();
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = $data['children'];
        $profile->assets = $data['assets'];
        $profile->initial_balance = $data['initial_balance'];
        $profile->user_id = Auth::id(); // Link to the logged-in user!

        if ($profile->create()) {
            // Update the assets with the initial balance
            $this->balanceService->updateProfileAssets($profile->id);

            Response::redirect('/profile/' . $profile->id);
        } else {
            View::render('profile/create', ['title' => 'Create Profile', 'errors' => ['general' => ['Failed to create profile.']], 'data' => $data]);
        }
    }

    /**
     * Displays the profile editing form.
     *
     * @param Request $request The request object.
     * @param int $id The ID of the profile to edit.
     * @return void
     */
    public function edit(Request $request, int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profile = (new Profile())->find($id);

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
    public function update(Request $request, int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profile = (new Profile())->find($id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        $data = $request->getBody();
        $profileRequest = new ProfileRequest();

        if (!$profileRequest->validate($data)) {
            View::render('profile/edit', ['title' => 'Edit Profile', 'errors' => $profileRequest->errors(), 'profile' => $profile, 'data' => $data]);
            return;
        }

        $profile = new Profile();
        $profile->id = $id; // Important for updating the existing record
        $profile->name = $data['name'];
        $profile->phone = $data['phone'];
        $profile->position_or_company = $data['position_or_company'];
        $profile->marital_status = $data['marital_status'];
        $profile->children = $data['children'];
        $profile->assets = $data['assets'];
        $profile->initial_balance = $data['initial_balance'];
        $profile->user_id = Auth::id(); // Ensure user_id isn't changed (security!)

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
    public function show(int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profile = (new Profile())->find($id);

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
    public function destroy(Request $request, int $id): void
    {
        if (Auth::guest()) {
            Response::redirect('/login');
        }

        $profile = (new Profile())->find($id);

        if (!$profile || $profile['user_id'] !== Auth::id()) {
            // Profile not found or doesn't belong to the user
            Response::redirect('/dashboard');
        }

        //Delete the profile
        $profile = new Profile();
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
        $db = Database::getInstance();
        $stmt = $db->prepare("DELETE FROM profile WHERE id = :id");
        $stmt->bindValue(':id', $id, PDO::PARAM_INT);

        return $stmt->execute();
    }
}