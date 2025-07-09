<?php

namespace Tests;

use App\Controllers\ProfileController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Profile;
use App\Services\BalanceService;
use PHPUnit\Framework\TestCase;

/**
 * Class ProfileControllerTest
 * Tests the ProfileController class.
 */
class ProfileControllerTest extends TestCase
{
    private function createMockRequest(array $data): Request
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn($data);
        return $mockRequest;
    }

    /**
     * Tests the create() method of the ProfileController.
     *
     * @return void
     */
    public function testCreate(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        View::$mock = true;
        View::$mockRenderedView = null;

        $controller = new ProfileController();
        $controller->create();

        $this->assertEquals('profile/create', View::$mockRenderedView);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the store() method of the ProfileController with valid data.
     *
     * @return void
     */
    public function testStore_withValidData(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockRequest = $this->createMockRequest([
            'name' => 'Test Profile',
            'phone' => '123-456-7890',
            'position_or_company' => 'Test Company',
            'marital_status' => 'Single',
            'children' => 0,
            'assets' => 10000,
            'initial_balance' => 500
        ]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('create')->willReturn(true);
        $mockProfileModel->id = 1;

        $mockBalanceService = $this->createMock(BalanceService::class);
        $mockBalanceService->expects($this->once())
             ->method('updateProfileAssets')
             ->with(1);

        $controller = new ProfileController($mockBalanceService);

        $controller->profile = $mockProfileModel;
        $mockProfileModel->id = 1;

        $controller->store($mockRequest);

        $this->assertEquals('/profile/1', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the edit() method of the ProfileController.
     *
     * @return void
     */
    public function testEdit(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        View::$mock = true;
        View::$mockRenderedView = null;

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('find')->willReturn(['id' => 1, 'user_id' => 1]);

        $controller = new ProfileController();
        $controller->profile = $mockProfileModel;
        $controller->edit($this->createMock(Request::class), 1);

        $this->assertEquals('profile/edit', View::$mockRenderedView);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the update() method of the ProfileController with valid data.
     *
     * @return void
     */
    public function testUpdate_withValidData(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockRequest = $this->createMockRequest([
            'name' => 'Updated Test Profile',
            'phone' => '098-765-4321',
            'position_or_company' => 'Updated Company',
            'marital_status' => 'Married',
            'children' => 2,
            'assets' => 20000,
            'initial_balance' => 1000
        ]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('find')->willReturn(['id' => 1, 'user_id' => 1]);
        $mockProfileModel->method('update')->willReturn(true);

        $mockBalanceService = $this->createMock(BalanceService::class);
         $mockBalanceService->expects($this->once())
             ->method('updateProfileAssets')
             ->with(1);

        $controller = new ProfileController($mockBalanceService);

        $controller->update($mockRequest, 1);

        $this->assertEquals('/profile/1', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the show() method of the ProfileController.
     *
     * @return void
     */
    public function testShow(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        View::$mock = true;
        View::$mockRenderedView = null;

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('find')->willReturn(['id' => 1, 'user_id' => 1]);

        $controller = new ProfileController();
        $controller->profile = $mockProfileModel;
        $controller->show(1);

        $this->assertEquals('profile/show', View::$mockRenderedView);

        Auth::$mock = false;
        View::$mock = false;
    }

     /**
     * Tests the destroy() method of the ProfileController.
     *
     * @return void
     */
    public function testDestroy(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('find')->willReturn(['id' => 1, 'user_id' => 1]);

        $controller = new ProfileController();
        $controller->deleteProfile = function (){
            return true;
        };
        $controller->destroy($this->createMock(Request::class), 1);

        $this->assertEquals('/dashboard', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

     /**
     * Additional test: Test that edit() method redirects to dashboard if profile doesn't belong to user.
     *
     * @return void
     */
    public function testEdit_redirectsToDashboardIfProfileNotOwned(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 2;  // Different user ID
        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('find')->willReturn(['id' => 1, 'user_id' => 1]);  // Profile belongs to user 1

        $controller = new ProfileController();
        $controller->profile = $mockProfileModel;
        $controller->edit($this->createMock(Request::class), 1);

        $this->assertEquals('/dashboard', Response::$mockRedirectedTo);
        Auth::$mock = false;
        Response::$mock = false;
    }
}