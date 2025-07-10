<?php

namespace App\Requests;

use App\Core\Validator;

/**
 * Class RegisterRequest
 * Handles validation for registration data.
 */
class RegisterRequest
{
    private Validator $validator;
    private array $errors = [];

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Validates the given registration data.
     *
     * @param array $data The registration data to validate.
     * @return bool True if the data is valid, false otherwise.
     */
    public function validate(array $data): bool
    {
        $rules = [
            'username' => 'required|min:3',
            'email' => 'required|email',
            'password' => 'required|min:6',
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