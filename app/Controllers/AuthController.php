<?php
/**
 * @file AuthController.php
 * @package App\Controllers
 * @author Jean Carlo Garcia
 * @version 1.1
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
class AuthController extends Controller
{

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
        ], null);
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
        View::render('auth/login', ['title' => 'Login', 'siteKey' => ReCaptcha::getSiteKey()], null);
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

        // 1. Validar los datos del formulario
        if (!$loginRequest->validate($data)) {
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $loginRequest->errors()], 422);
            } else {
                View::render('auth/login', ['title' => 'Login', 'errors' => $loginRequest->errors(), 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }
        
        // 2. Validar el token de reCAPTCHA
        if (!ReCaptcha::verify($data['g-recaptcha-response'] ?? '')) {
            $error = ['recaptcha' => ['reCAPTCHA verification failed. Please try again.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 403);
            } else {
                View::render('auth/login', ['title' => 'Login', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
            return;
        }

        // 3. Intentar autenticar al usuario
        if (Auth::attempt($data['username'], $data['password'], $this->pdo)) {
            if ($request->isAjax()) {
                Response::json(['success' => true, 'redirect' => '/dashboard']);
            } else {
                Response::redirect('/dashboard');
            }
        } else {
            $error = ['general' => ['Invalid username or password.']];
            if ($request->isAjax()) {
                Response::json(['success' => false, 'errors' => $error], 401);
            } else {
                View::render('auth/login', ['title' => 'Login', 'errors' => $error, 'data' => $data, 'siteKey' => ReCaptcha::getSiteKey()]);
            }
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