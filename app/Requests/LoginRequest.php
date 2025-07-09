<?php

namespace App\Requests;

use App\Core\Validator;

/**
 * Class LoginRequest
 * Handles validation for login data.
 */
class LoginRequest
{
    private Validator $validator;
    private array $errors = [];

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Validates the given login data.
     *
     * @param array $data The login data to validate.
     * @return bool True if the data is valid, false otherwise.
     */
    public function validate(array $data): bool
    {
        $rules = [
            'username' => 'required',
            'password' => 'required',
            'g-recaptcha-response' => 'required', // Add this line
        ];

        if (!$this->validator->validate($data, $rules)) {
            $this->errors = $this->validator->errors();
            return false;
        }

        return true;
    }

    /**
     * Gets the validation errors.
     *
     * @return array An associative array of validation errors.
     */
    public function errors(): array
    {
        return $this->errors;
    }
}