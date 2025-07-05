<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Provides a singleton instance of a PDO database connection.
 * Note: While the connection is a singleton, it now depends on an Environment object.
 */
class Database
{
    /**
     * @var PDO|null The PDO instance.
    */
    private static ?PDO $instance = null;

    /**
     * Gets the singleton instance of the PDO database connection.
     *
     * @param Environment $env The application's environment configuration object.
     * @return PDO The PDO instance.
     * @throws PDOException If the connection fails.
    */
    public static function getInstance(Environment $env): PDO
    {
        if (self::$instance === null) {
            // Use the Environment object to get credentials instead of getenv()
            $dsn = "mysql:host=" . $env->get('DB_HOST') . ";dbname=" . $env->get('DB_DATABASE') . ";charset=utf8mb4";
            
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];

            try {
                self::$instance = new PDO($dsn, $env->get('DB_USERNAME'), $env->get('DB_PASSWORD'), $options);
            } catch (PDOException $e) {
                // For security, don't echo detailed errors in production. Log them instead.
                // For now, we re-throw the exception.
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * This class should not be instantiated directly.
     * Use getInstance(Environment $env)
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