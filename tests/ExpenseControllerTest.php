<?php

namespace Tests;

use App\Controllers\ExpenseController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\Expense;
use App\Models\Profile;
use App\Services\BalanceService;
use PHPUnit\Framework\TestCase;

/**
 * Class ExpenseControllerTest
 * Tests the ExpenseController class.
 */
class ExpenseControllerTest extends TestCase
{
    private function createMockRequest(array $data): Request
    {
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn($data);
        return $mockRequest;
    }

    /**
     * Tests the index() method of the ExpenseController.
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

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('getAllForUser')->willReturn([]);

        $controller = new ExpenseController();
        $controller->index();

        $this->assertEquals('expenses/index', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('expenses', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the create() method of the ExpenseController.
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

        $controller = new ExpenseController();
        $controller->create();

        $this->assertEquals('expenses/create', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('profiles', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the store() method of the ExpenseController with valid data.
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
            'description' => 'Test Expense',
            'amount' => 100,
            'type' => 'Test',
            'profile_id' => 1
        ]);

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('create')->willReturn(true);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new ExpenseController();
        $controller->deleteExpense = function (){
            return true;
        };

        $controller->store($mockRequest);

        $this->assertEquals('/expenses', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the edit() method of the ExpenseController.
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

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);
        $mockProfileModel->method('getAllForUser')->willReturn([]);

        $controller = new ExpenseController();

        $controller->edit($this->createMock(Request::class), 1);

        $this->assertEquals('expenses/edit', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('expense', View::$mockRenderedData);
        $this->assertArrayHasKey('profiles', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the update() method of the ExpenseController with valid data.
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
            'description' => 'Updated Test Expense',
            'amount' => 150,
            'type' => 'Updated',
            'profile_id' => 1
        ]);

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);
        $mockExpenseModel->method('update')->willReturn(true);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);
        $controller = new ExpenseController();

        $controller->update($mockRequest, 1);

        $this->assertEquals('/expenses', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the show() method of the ExpenseController.
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

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new ExpenseController();

        $controller->show(1);

        $this->assertEquals('expenses/show', View::$mockRenderedView);
        $this->assertArrayHasKey('title', View::$mockRenderedData);
        $this->assertArrayHasKey('expense', View::$mockRenderedData);

        Auth::$mock = false;
        View::$mock = false;
    }

    /**
     * Tests the destroy() method of the ExpenseController.
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

        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);
        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $controller = new ExpenseController();
        $controller->deleteExpense = function (){
            return true;
        };
        $controller->destroy($this->createMock(Request::class), 1);

        $this->assertEquals('/expenses', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Additional test: Test that destroy() method updates profile assets after deleting expense
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
        $mockExpenseModel = $this->createMock(Expense::class);
        $mockExpenseModel->method('find')->willReturn(['id' => 1, 'profile_id' => 1]);

        $mockProfileModel = $this->createMock(Profile::class);
        $mockProfileModel->method('isOwnedByUser')->willReturn(true);

        $mockBalanceService = $this->createMock(BalanceService::class);
        $mockBalanceService->expects($this->once())
            ->method('updateProfileAssets')
            ->with(1);

        $controller = new ExpenseController($mockBalanceService);
        $controller->deleteExpense = function (){
            return true;
        };
        $controller->destroy($mockRequest, 1);

        $this->assertEquals('/expenses', Response::$mockRedirectedTo);

        Auth::$mock = false;
        Response::$mock = false;
    }
}