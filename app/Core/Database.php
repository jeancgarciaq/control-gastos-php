<?php

namespace App\Core;

use PDO;
use PDOException;

/**
 * Class Database
 * Provides a singleton instance of a PDO database connection.
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
     * @return PDO The PDO instance.
     * @throws PDOException If the connection fails.
     */
    public static function getInstance(): PDO
    {
        if (self::$instance === null) {
            $dsn = "mysql:host=" . getenv('DB_HOST') . ";dbname=" . getenv('DB_DATABASE') . ";charset=utf8mb4";
            $options = [
                PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
                PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
                PDO::ATTR_EMULATE_PREPARES => false,
            ];
            try {
                self::$instance = new PDO($dsn, getenv('DB_USERNAME'), getenv('DB_PASSWORD'), $options);
            } catch (PDOException $e) {
                throw new PDOException($e->getMessage(), (int)$e->getCode());
            }
        }

        return self::$instance;
    }

    /**
     * Calls a method on the PDO instance.
     *
     * @param string $method The name of the method to call.
     * @param array $args The arguments to pass to the method.
     * @return mixed The result of the method call.
     */
    public static function __callStatic($method, $args)
    {
        return call_user_func_array([self::getInstance(), $method], $args);
    }
}