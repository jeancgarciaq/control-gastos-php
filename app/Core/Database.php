<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Provides a singleton instance of a PDO database connection using environment variables.
 */
class Database
{
    /**
     * @var PDO|null The singleton PDO instance.
     */
    private static ?PDO $instance = null;

    /**
     * Gets the singleton instance of the PDO database connection.
     *
     * This method now reads credentials directly from the $_ENV superglobal,
     * which should be populated by Dotenv in the application's entry point.
     *
     * @return PDO The PDO instance.
     * @throws PDOException If the connection fails or environment variables are not set.
     */
    public static function connect(): PDO
    {
        if (self::$instance === null) {
            // Check if the required environment variables are set
            $required_keys = ['DB_HOST', 'DB_DATABASE', 'DB_USERNAME', 'DB_PASSWORD'];
            foreach ($required_keys as $key) {
                if (empty($_ENV[$key])) {
                    // It's better to fail loudly if the configuration is incomplete.
                    throw new PDOException("La variable de entorno '$key' no está definida. Revisa tu archivo .env.");
                }
            }

            $dsn = "mysql:host=" . $_ENV['DB_HOST'] . ";dbname=" . $_ENV['DB_DATABASE'] . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $_ENV['DB_USERNAME'], $_ENV['DB_PASSWORD'], $options);
            } catch (PDOException $e) {
                // In a real production environment, you should log this error instead of exposing details.
                // For development, re-throwing the exception is fine as it gives a clear error message.
                throw new PDOException("Error de conexión a la base de datos: " . $e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * The constructor is private to prevent direct instantiation.
     * Use connect()
     */
    private function __construct() {}

    /**
     * Private clone method to prevent cloning of the instance.
     */
    private function __clone() {}

    /**
     * Private unserialize method to prevent unserializing of the instance.
     */
    public function __wakeup() {}
}