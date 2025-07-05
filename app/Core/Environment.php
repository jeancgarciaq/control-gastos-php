<?php

namespace App\Core;

use Dotenv\Dotenv;

/**
 * Class Environment
 * Handles loading and accessing environment variables from a .env file.
 */
class Environment
{
    /**
     * Environment constructor.
     * Loads environment variables from the specified path.
     *
     * @param string $path The path to the directory containing the .env file.
     */
    public function __construct(string $path)
    {
        // createImmutable previene que se sobrescriban variables de entorno ya existentes
        // en el servidor, lo cual es más seguro.
        $dotenv = Dotenv::createImmutable($path);
        $dotenv->load();
    }

    /**
     * Gets the value of an environment variable.
     *
     * @param string $key The name of the variable.
     * @param mixed|null $default The value to return if the variable does not exist.
     * @return mixed
     */
    public function get(string $key, $default = null)
    {
        // La librería vlucas/phpdotenv carga las variables en $_ENV y $_SERVER
        return $_ENV[$key] ?? $_SERVER[$key] ?? $default;
    }

    /**
     * Sets an environment variable for the current request.
     * Note: This does not write to the .env file.
     *
     * @param string $key The name of the variable.
     * @param string $value The value of the variable.
     * @return void
     */
    public function set(string $key, string $value): void
    {
        $_ENV[$key] = $value;
        $_SERVER[$key] = $value;
    }

    /**
     * Checks if an environment variable exists.
     *
     * @param string $key The name of the variable.
     * @return bool
     */
    public function has(string $key): bool
    {
        return isset($_ENV[$key]) || isset($_SERVER[$key]);
    }
}