<?php

namespace App\Traits;

use Illuminate\Validation\Validator;

trait ModelValidable
{
    /**
     * The model validator instance.
     *
     * @var \Illuminate\Validation\Validator
     */
    public $validator;

    /**
     * Check if the model is valid for it's attributes.
     *
     * @param string $event
     *
     * @return bool
     */
    public function isValid(string $event = null)
    {
        $this->validator = app('validator')->make($this->attributes, $this->getValidationRules($event));

        if ($this->validator()->passes()) {
            return true;
        }

        return false;
    }

    /**
     * Get the model validator.
     *
     * @return \Illuminate\Validation\Validator
     */
    public function validator(): Validator
    {
        return $this->validator;
    }

    /**
     * Get the validation rules.
     *
     * @param string $event
     *
     * @return array
     */
    private function getValidationRules(string $event = null): array
    {
        switch (true) {
            case $event === 'UPDATE':
            case $this->getKey() !== null:
                return $this->rules()['UPDATE'];

            case $event === null:
            case $event === 'CREATE':
            default:
                return $this->rules()['CREATE'];
        }

        return [];
    }
}
