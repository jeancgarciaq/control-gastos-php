<?php

namespace App\Controllers;

use App\Core\Auth;
use App\Core\Request;
use App\Core\Response;
use App\Core\View;
use App\Helpers\ReCaptcha;
use App\Models\User;
use App\Requests\LoginRequest;
use App\Requests\RegisterRequest;
use PDO;

/**
 * Class AuthController
 * Handles user authentication-related requests (registration, login, logout).
 */
class AuthController
{
    /**
     * @var PDO The database connection object.
     */
    private PDO $pdo;

    /**
     * AuthController constructor.
     *
     * @param PDO $pdo The database connection object.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Displays the registration form.
     *
     * @return void
     */
    public function register()
    {
        // Extraemos la siteKey y la pasamos a la vista
        $siteKey = ReCaptcha::getSiteKey();

        View::render('auth/register', [
            'title'   => 'Register',
            'siteKey' => $siteKey,
        ]);
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

        // Verificamos que estemos recibiendo datos
        if (!$registerRequest->validate($data)) {
            
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $registerRequest->errors()]);
                return;
            }

            View::render('auth/register', [
                'title'   => 'Register',
                'errors'  => $registerRequest->errors(),
                'data'    => $data,
                'siteKey' => ReCaptcha::getSiteKey(),
            ]);
            return;
        }
        
        // Usamos el helper para validar el captcha
        if (!ReCaptcha::verify(
            $data['g-recaptcha-response'],
            getenv('RECAPTCHA_SECRET_KEY')
        )) {
            
            if ($request->isAjax()) {
                Response::json([
                    'success' => false, 
                    'errors' => ['recaptcha' => ['reCAPTCHA verification failed.']]
                ]);
                return;
            }

            View::render('auth/register', [
                'title'   => 'Register',
                'errors'  => ['recaptcha' => ['reCAPTCHA verification failed.']],
                'data'    => $data,
                'siteKey' => ReCaptcha::getSiteKey(),
            ]);
            return;
        }

        // Instanciamos el modelo User, pasándole la conexión PDO
        $user = new User($this->pdo);

        // Comprobamos si el usuario o email ya existen
        if ($user->findByUsername($data['username'])) {

            if ($request->isAjax()) {
                Response::json([
                    'success' => false, 
                    'errors' => ['username' => ['Username already taken.']]
                ]);
                return;
            }

            View::render('auth/register', [
                'title'   => 'Register',
                'errors'  => ['username' => ['Username already taken.']],
                'data'    => $data,
                'siteKey' => ReCaptcha::getSiteKey(),
            ]);
            return;
        }
        
        if ($user->findByEmail($data['email'])) {

            if ($request->isAjax()) {
                Response::json([
                    'success' => false, 
                    'errors' => ['email' => ['Email already in use.']]]
                );
                return;
            }

            View::render('auth/register', [
                'title'   => 'Register',
                'errors'  => ['email' => ['Email already in use.']],
                'data'    => $data,
                'siteKey' => ReCaptcha::getSiteKey(),
            ]);
            return;
        }
        
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($user->create()) {
            
            if ($request->isAjax()) {
                Response::json(['success' => true]);
                return;
            }
            Response::redirect('/login');
        } else {
            if ($request->isAjax()) {
                Response::json([
                    'success' => false, 
                    'errors' => ['general' => ['Registration failed. Please try again.']]
                ]);
                return;
            }
            
            View::render('auth/register', [
                'title'   => 'Register',
                'errors'  => ['general' => ['Registration failed. Please try again.']],
                'data'    => $data,
                'siteKey' => ReCaptcha::getSiteKey(),
            ]);
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
        
        if (!ReCaptcha::verify(
            $data['g-recaptcha-response'],
            getenv('RECAPTCHA_SECRET_KEY')
        )) {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['recaptcha' => ['reCAPTCHA verification failed.']], 'data' => $data]);
            return;
        }

        // Auth::attempt también necesitará la conexión PDO
        if (Auth::attempt($data['username'], $data['password'], $this->pdo)) {
            Response::redirect('/dashboard');
        } else {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['general' => ['Invalid username or password.']], 'data' => $data]);
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