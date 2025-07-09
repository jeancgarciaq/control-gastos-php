<?php

namespace App\Core;

/**
 * Class Validator
 * Handles data validation based on defined rules.
 */
class Validator
{
    /**
     * @var array An array of validation errors.
     */
    private array $errors = [];

    /**
     * Validates the given data against the given rules.
     *
     * @param array $data The data to validate.
     * @param array $rules An associative array of validation rules (field => rules string).
     * @return bool True if the data is valid, false otherwise.
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArray = explode('|', $ruleString);
            foreach ($rulesArray as $rule) {
                $ruleParts = explode(':', $rule);
                $ruleName = $ruleParts[0];
                $ruleParams = $ruleParts[1] ?? null;

                $methodName = 'validate' . ucfirst($ruleName);

                if (method_exists($this, $methodName)) {
                    $this->$methodName($field, $data[$field] ?? null, $ruleParams);
                } else {
                    // Handle invalid rule
                    $this->errors[$field][] = "Invalid rule: " . $ruleName;
                }
            }
        }

        return empty($this->errors);
    }

    /**
     * Validates that a field is required.
     *
     * @param string $field The name of the field.
     * @param mixed $value The value of the field.
     * @return void
     */
    private function validateRequired(string $field, mixed $value): void
    {
        if (empty($value)) {
            $this->errors[$field][] = "The " . $field . " field is required.";
        }
    }

    /**
     * Validates that a field is a valid email address.
     *
     * @param string $field The name of the field.
     * @param mixed $value The value of the field.
     * @return void
     */
    private function validateEmail(string $field, mixed $value): void
    {
        if (!filter_var($value, FILTER_VALIDATE_EMAIL)) {
            $this->errors[$field][] = "The " . $field . " field must be a valid email address.";
        }
    }

    /**
     * Validates that a field has a minimum length.
     *
     * @param string $field The name of the field.
     * @param mixed $value The value of the field.
     * @param string $minLength The minimum length.
     * @return void
     */
    private function validateMin(string $field, mixed $value, string $minLength): void
    {
        if (strlen($value) < (int)$minLength) {
            $this->errors[$field][] = "The " . $field . " field must be at least " . $minLength . " characters.";
        }
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