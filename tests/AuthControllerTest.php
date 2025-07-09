<?php

namespace Tests;

use App\Controllers\AuthController;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Models\User;
use PHPUnit\Framework\TestCase;

/**
 * Class AuthControllerTest
 * Tests the AuthController class.
 */
class AuthControllerTest extends TestCase
{
    /**
     * Tests the register() method of the AuthController.
     *
     * @return void
     */
    public function testRegister(): void
    {
        View::$mock = true;
        View::$mockRenderedView = null;
        $controller = new AuthController();
        $controller->register();

        $this->assertEquals('auth/register', View::$mockRenderedView);
        View::$mock = false;
    }

    /**
     * Tests the processRegister() method of the AuthController with valid data.
     *
     * @return void
     */
    public function testProcessRegister_withValidData(): void
    {
        // Arrange
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;

        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn([
            'username' => 'testuser',
            'email' => 'test@example.com',
            'password' => 'password123',
            'g-recaptcha-response' => 'valid_recaptcha_response', // Replace with a valid value
        ]);
        $controller = new AuthController();

        $mockUser = $this->createMock(User::class);
        $mockUser->method('create')->willReturn(true);
        $controller->user = $mockUser;

        View::$mock = true;
        View::$mockRenderedView = null;

        // Act
        $controller->processRegister($mockRequest);

        // Assert
        $this->assertEquals('/login', Response::$mockRedirectedTo); //Expects a redirection

        Auth::$mock = false;
        View::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the login() method of the AuthController.
     *
     * @return void
     */
    public function testLogin(): void
    {
        View::$mock = true;
        View::$mockRenderedView = null;
        $controller = new AuthController();
        $controller->login();

        $this->assertEquals('auth/login', View::$mockRenderedView);
        View::$mock = false;
    }

    /**
     * Tests the processLogin() method of the AuthController with valid data.
     *
     * @return void
     */
    public function testProcessLogin_withValidData(): void
    {
         // Arrange
        $mockRequest = $this->createMock(Request::class);
        $mockRequest->method('getBody')->willReturn([
            'username' => 'testuser',
            'password' => 'password123',
            'g-recaptcha-response' => 'valid_recaptcha_response', // Replace with a valid value
        ]);

        // Mock Auth class
        Auth::$mock = true;
        Auth::$mockAuthCheck = true;
        Auth::$mockAuthId = 1;

        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $controller = new AuthController();
        View::$mock = true;
        View::$mockRenderedView = null;

         // Act
        $controller->processLogin($mockRequest);

        // Assert
        $this->assertEquals('/dashboard', Response::$mockRedirectedTo); //expects a redirection to /dashboard

        // Revert to original values
        View::$mock = false;
        Auth::$mock = false;
        Response::$mock = false;
    }

    /**
     * Tests the logout() method of the AuthController.
     *
     * @return void
     */
    public function testLogout(): void
    {
        Auth::$mock = true;
        Auth::$mockAuthCheck = false;
        Auth::$mockAuthId = 1;
        Auth::$mockUser = null;

        Response::$mock = true;
        Response::$mockRedirectedTo = null;

        $controller = new AuthController();
        $controller->logout();

        // Assert
        $this->assertEquals('/', Response::$mockRedirectedTo);

        // Revert to original values
        Auth::$mock = false;
        Response::$mock = false;
    }
}