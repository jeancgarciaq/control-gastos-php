<?php

namespace Tests;

use App\Controllers\IncomeController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Income;
use App\Models\Profile;
use App\Services\BalanceService;
use PHPUnit\Framework\TestCase;

/**
 * Class IncomeControllerTest
 * Tests the IncomeController class.
 */
class IncomeControllerTest extends TestCase
{
    private function createMockRequest(array $data): Request
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn($data);
        return $mockRequest;
    }

    /**
     * Tests the index() method of the IncomeController.
     *
     * @return void
     */
    public function testIndex(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        View::$mock = true;
        View::$mockRenderedView = null;
        View::$mockRenderedData = null;

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('getAllForUser')->willReturn([]);

        $controller = new IncomeController();
        $controller->index();

        $this->assertEquals('income/index', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('income', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the create() method of the IncomeController.
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
        View::$mockRenderedData = null;

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('getAllForUser')->willReturn([]);

        $controller = new IncomeController();
        $controller->create();

        $this->assertEquals('income/create', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('profiles', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the store() method of the IncomeController with valid data.
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
            'date' => '2024-01-01',
            'description' => 'Test Income',
            'amount' => 100,
            'type' => 'Test',
            'profile_id' => 1
        ]);

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('create')->willReturn(true);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new IncomeController();
        $controller->deleteIncome = function (){
            return true;
        };

        $controller->store($mockRequest);

        $this->assertEquals('/income', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the edit() method of the IncomeController.
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
        View::$mockRenderedData = null;

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);
        $mockProfileModel->method('getAllForUser')->willReturn([]);

        $controller = new IncomeController();

        $controller->edit($this->createMock(Request::class), 1);

        $this->assertEquals('income/edit', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('income', View::$mockRenderedData);
        $this->assertArrayHasKey('profiles', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the update() method of the IncomeController with valid data.
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
            'date' => '2024-01-01',
            'description' => 'Updated Test Income',
            'amount' => 150,
            'type' => 'Updated',
            'profile_id' => 1
        ]);

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);
        $mockIncomeModel->method('update')->willReturn(true);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new IncomeController();

        $controller->update($mockRequest, 1);

        $this->assertEquals('/income', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the show() method of the IncomeController.
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
        View::$mockRenderedData = null;

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new IncomeController();

        $controller->show(1);

        $this->assertEquals('income/show', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('income', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the destroy() method of the IncomeController.
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

        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new IncomeController();
        $controller->deleteIncome = function (){
            return true;
        };
        $controller->destroy($this->createMock(Request::class), 1);

        $this->assertEquals('/income', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Additional test: Test that destroy() method updates profile assets after deleting income
     * @return void
     */
    public function testDestroy_updatesProfileAssets(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;
        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockRequest = $this->createMock(Request::class);
        $mockIncomeModel = $this->createMock(Income::class);
        $mockIncomeModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $mockBalanceService = $this->createMock(BalanceService::class);
        $mockBalanceService->expects($this->once())
            ->method('updateProfileAssets')
            ->with(1);

        $controller = new IncomeController($mockBalanceService);
        $controller->deleteIncome = function (){
            return true;
        };

        $controller->destroy($mockRequest, 1);

        $this->assertEquals('/income', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }
}