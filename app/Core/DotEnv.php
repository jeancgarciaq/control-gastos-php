<?php

namespace App\Core;

use Dotenv\Dotenv;

/**
 * Class DotEnv
 * Loads environment variables from a .env file.
 */
class DotEnv
{
    /**
     * @var string The path to the .env file.
     */
    protected string $path;

    /**
     * DotEnv constructor.
     *
     * @param string $path The path to the .env file.
     */
    public function __construct(string $path)
    {
        $this->path = $path;
    }

    /**
     * Loads the environment variables.
     *
     * @return void
     */
    public function load(): void
    {
        $dotenv = Dotenv::createImmutable(dirname($this->path));
        $dotenv->load();
    }
}