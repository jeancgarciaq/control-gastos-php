<?php

namespace App\Requests;

use App\Core\Validator;

/**
 * Class IncomeRequest
 * Handles validation for income data.
 */
class IncomeRequest
{
    private Validator $validator;
    private array $errors = [];

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Validates the given income data.
     *
     * @param array $data The income data to validate.
     * @return bool True if the data is valid, false otherwise.
     */
    public function validate(array $data): bool
    {
        $rules = [
            'date' => 'required',
            'description' => 'required',
            'amount' => 'required',
            'type' => 'required',
            'profile_id' => 'required'
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