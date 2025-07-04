<?php

namespace App\Requests;

use App\Core\Validator;

/**
 * Class ProfileRequest
 * Handles validation for profile data.
 */
class ProfileRequest
{
    private Validator $validator;
    private array $errors = [];

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Validates the given profile data.
     *
     * @param array $data The profile data to validate.
     * @return bool True if the data is valid, false otherwise.
     */
    public function validate(array $data): bool
    {
        $rules = [
            'name' => 'required',
            'phone' => 'required',
            'position_or_company' => 'required',
            'marital_status' => 'required',
            'children' => 'required',
            'assets' => 'required',
            'initial_balance' => 'required'
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