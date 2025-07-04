<?php

namespace App\Core;

class Auth
{
    public static $mock = false;
    public static $mockAuthCheck = false;
    public static $mockAuthId = null;
    public static $mockUser = null;

    /**
     * Attempts to authenticate a user with the given username and password.
     *
     * @param string $username The username.
     * @param string $password The password.
     * @return bool True if authentication is successful, false otherwise.
     */
    public static function attempt(string $username, string $password): bool
    {
        $user = (new \App\Models\User())->findByUsername($username);

        if ($user && password_verify($password, $user['password'])) {
            // Generate a token (simplified for now)
            $token = bin2hex(random_bytes(32));
            $_SESSION['auth_token'] = $token;
            $_SESSION['user_id'] = $user['id']; // Store user ID in session

            return true;
        }

        return false;
    }

    /**
     * Checks if a user is currently authenticated.
     *
     * @return bool True if the user is authenticated, false otherwise.
     */
    public static function check(): bool
    {
        if(self::$mock){
            return self::$mockAuthCheck;
        }
        return isset($_SESSION['auth_token']);
    }

    /**
     * Gets the ID of the currently authenticated user.
     *
     * @return int|null The user ID, or null if the user is not authenticated.
     */
    public static function id(): ?int
    {
        if(self::$mock){
            return self::$mockAuthId;
        }
        return $_SESSION['user_id'] ?? null;
    }

    /**
     * Logs out the currently authenticated user.
     *
     * @return void
     */
    public static function logout(): void
    {
        unset($_SESSION['auth_token']);
        unset($_SESSION['user_id']);
        session_destroy();
    }

      /**
     * Checks if the current user is a guest (not logged in).
     *
     * @return bool True if the user is a guest, false otherwise.
     */
    public static function guest(): bool
    {
      return !self::check();
    }

    /**
     * Gets the currently authenticated user's data.
     *
     * @return array|null An associative array of user data, or null if the user is not authenticated.
     */
    public static function user(): ?array
    {
        if(self::$mock){
            return self::$mockUser;
        }
        $userId = self::id();
        if ($userId) {
            return (new \App\Models\User())->find($userId);
        }
        return null;
    }
}