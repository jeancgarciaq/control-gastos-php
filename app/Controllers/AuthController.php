<?php

namespace App\Controllers;

use App\Core\View;
use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Requests\RegisterRequest;
use App\Requests\LoginRequest;
use App\Models\User;
use App\Helpers\ReCaptcha; // Add this line

/**
 * Class AuthController
 * Handles user authentication-related requests (registration, login, logout).
 */
class AuthController
{
    /**
     * Displays the registration form.
     *
     * @return void
     */
    public function register()
    {
        View::render('auth/register', ['title' => 'Register']);
    }

    /**
     * Processes the registration form submission.
     *
     * @param Request $request The request object.
     * @return void
     */
    public function processRegister(Request $request)
    {
        $data = $request->getBody();
        $registerRequest = new RegisterRequest();

        if (!$registerRequest->validate($data)) {
            View::render('auth/register', ['title' => 'Register', 'errors' => $registerRequest->errors(), 'data' => $data]);
            return;
        }

        // Verify reCAPTCHA
        $recaptchaResponse = $data['g-recaptcha-response'] ?? '';
        $recaptchaSecretKey = getenv('RECAPTCHA_SECRET_KEY');

        if (!ReCaptcha::verify($recaptchaResponse, $recaptchaSecretKey)) {
            View::render('auth/register', ['title' => 'Register', 'errors' => ['general' => ['reCAPTCHA verification failed.']], 'data' => $data]);
            return;
        }

        // Hash the password
        $hashedPassword = password_hash($data['password'], PASSWORD_DEFAULT);

        // Create the user in the database
        $user = new User();
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = $hashedPassword;

        if ($user->create()) {
            Response::redirect('/login');
        } else {
            View::render('auth/register', ['title' => 'Register', 'errors' => ['general' => ['Registration failed.']], 'data' => $data]);
        }
    }

    /**
     * Displays the login form.
     *
     * @return void
     */
    public function login()
    {
        View::render('auth/login', ['title' => 'Login']);
    }

    /**
     * Processes the login form submission.
     *
     * @param Request $request The request object.
     * @return void
     */
    public function processLogin(Request $request)
    {
        $data = $request->getBody();
        $loginRequest = new LoginRequest();

        if (!$loginRequest->validate($data)) {
            View::render('auth/login', ['title' => 'Login', 'errors' => $loginRequest->errors(), 'data' => $data]);
            return;
        }

        // Verify reCAPTCHA
        $recaptchaResponse = $data['g-recaptcha-response'] ?? '';
        $recaptchaSecretKey = getenv('RECAPTCHA_SECRET_KEY');

        if (!ReCaptcha::verify($recaptchaResponse, $recaptchaSecretKey)) {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['general' => ['reCAPTCHA verification failed.']], 'data' => $data]);
            return;
        }

        if (Auth::attempt($data['username'], $data['password'])) {
            Response::redirect('/dashboard');
        } else {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['general' => ['Invalid credentials.']], 'data' => $data]);
        }
    }

    /**
     * Logs out the currently authenticated user.
     *
     * @return void
     */
    public function logout()
    {
        Auth::logout();
        Response::redirect('/');
    }
}