<?php
/**
 * @file index.php
 * @package App
 * @author Jean Carlo Garcia
 * @version 1.1
 * @brief Punto de entrada principal de la aplicación.
 */

// Inicia la sesión en cada petición. ESTA ES LA LÍNEA CLAVE.
session_start();

// Muestra todos los errores para facilitar la depuración.
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Carga el autoloader de Composer.
require_once __DIR__ . '/../vendor/autoload.php';

use App\Core\Database;
use App\Core\Router;

// Carga las variables de entorno desde el archivo .env.
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

// --- Conexión a la Base de Datos ---
// Se encapsula en un bloque try-catch para manejar errores de conexión.
try {
    $pdo = Database::connect();
} catch (\PDOException $e) {
    // Si la conexión falla, muestra un mensaje de error y detiene la ejecución.
    // En un entorno de producción, esto debería registrarse en un archivo de log
    // y mostrar una página de error genérica al usuario.
    error_log("Error de conexión a la base de datos: " . $e->getMessage());
    die("Error de conexión a la base de datos. Por favor, revisa la configuración y asegúrate de que el servicio de base de datos esté en ejecución.");
}


// --- Enrutamiento ---
// Obtiene la URI y el método de la petición actual.
$uri = $_SERVER['REQUEST_URI'];
$method = $_SERVER['REQUEST_METHOD'];

// Crea una instancia del Router.
$router = new Router();

// Carga las rutas definidas en el archivo de rutas.
require_once __DIR__ . '/../app/routes.php';

// Resuelve la ruta actual y ejecuta el controlador correspondiente,
// inyectando la conexión a la base de datos.
$router->resolve($pdo);