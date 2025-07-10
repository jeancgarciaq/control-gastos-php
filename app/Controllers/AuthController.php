<?php
/**
 * @file AuthController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.0
 * @brief Controlador para gestionar la autenticación de usuarios.
 */

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
 * @class AuthController
 * @brief Maneja las peticiones relacionadas con la autenticación: registro, login y logout.
 */
class AuthController
{
    /**
     * La instancia de conexión a la base de datos.
     * @var PDO
     */
    private PDO $pdo;

    /**
     * Constructor de AuthController.
     *
     * @param PDO $pdo La instancia de conexión a la base de datos, inyectada por el Router.
     */
    public function __construct(PDO $pdo)
    {
        $this->pdo = $pdo;
    }

    /**
     * Muestra el formulario de registro.
     * Pasa la Site Key de reCAPTCHA a la vista.
     *
     * @return void
     */
    public function register(): void
    {
        View::render('auth/register', [
            'title'   => 'Register',
            'siteKey' => ReCaptcha::getSiteKey(),
        ]);
    }

    /**
     * Procesa el envío del formulario de registro.
     * Valida los datos, verifica el reCAPTCHA y crea el nuevo usuario.
     * Maneja tanto peticiones estándar como AJAX.
     *
     * @param Request $request El objeto que encapsula la petición HTTP.
     * @return void
     */
    public function processRegister(Request $request): void
    {
        $data = $request->getBody();
        $registerRequest = new RegisterRequest();

        // 1. Validar los datos del formulario (username, email, password)
        if (!$registerRequest->validate($data)) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $registerRequest->errors()], 422);
            } else {
                View::render('auth/register', ['title' => 'Register', 'errors' => $registerRequest->errors(), 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }
        
        // 2. Validar el token de reCAPTCHA
        if (!ReCaptcha::verify($data['g-recaptcha-response'] ?? '')) {
            $error = ['recaptcha' => ['reCAPTCHA verification failed. Please try again.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 403);
            } else {
                View::render('auth/register', ['title' => 'Register', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }

        $user = new User($this->pdo);

        // 3. Comprobar si el usuario o email ya existen
        if ($user->findByUsername($data['username'])) {
            $error = ['username' => ['Username already taken.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 409);
            } else {
                View::render('auth/register', ['title' => 'Register', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }
        
        if ($user->findByEmail($data['email'])) {
            $error = ['email' => ['Email already in use.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 409);
            } else {
                View::render('auth/register', ['title' => 'Register', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }
        
        // 4. Crear el usuario
        $user->username = $data['username'];
        $user->email = $data['email'];
        $user->password = password_hash($data['password'], PASSWORD_DEFAULT);

        if ($user->create()) {
            if ($request->isAjax()) {
                Response::json(['success' => true, 'redirect' => '/login']);
            } else {
                Response::redirect('/login');
            }
        } else {
            $error = ['general' => ['Registration failed. Please try again.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 500);
            } else {
                View::render('auth/register', ['title' => 'Register', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
        }
    }

    /**
     * Muestra el formulario de inicio de sesión.
     *
     * @return void
     */
    public function login(): void
    {
        View::render('auth/login', ['title' => 'Login', 'siteKey' => ReCaptcha::getSiteKey()]);
    }

    /**
     * Procesa el envío del formulario de inicio de sesión.
     *
     * @param Request $request El objeto que encapsula la petición HTTP.
     * @return void
     */
    public function processLogin(Request $request): void
    {
        $data = $request->getBody();
        $loginRequest = new LoginRequest();

        if (!$loginRequest->validate($data)) {
            View::render('auth/login', ['title' => 'Login', 'errors' => $loginRequest->errors(), 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            return;
        }
        
        if (!ReCaptcha::verify($data['g-recaptcha-response'] ?? '')) {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['recaptcha' => ['reCAPTCHA verification failed.']], 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            return;
        }

        if (Auth::attempt($data['username'], $data['password'], $this->pdo)) {
            Response::redirect('/dashboard');
        } else {
            View::render('auth/login', ['title' => 'Login', 'errors' => ['general' => ['Invalid username or password.']], 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
        }
    }

    /**
     * Cierra la sesión del usuario actualmente autenticado.
     *
     * @return void
     */
    public function logout(): void
    {
        Auth::logout();
        Response::redirect('/');
    }
}