<?php

namespace App\Traits;

use Illuminate\Validation\Validator;

trait ModelValidatable
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
    public function isValid(string $event = null): bool
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
        $event = strtoupper($event);

        switch (true) {
            case !method_exists($this, 'rules'):
                return [];

            case $event === 'UPDATE':
            case $this->getKey() !== null:
                return $this->mergeRuleWithDefault('UPDATE');

            case $event === null:
            case $event === 'CREATE':
            default:
                return $this->mergeRuleWithDefault('CREATE');
        }

        return [];
    }

    /**
     * Merge validation rules wiht the default rules "*"
     *
     * @param string|null $event
     *
     * @return array
     */
    private function mergeRuleWithDefault(string $event = null): array
    {
        switch (true) {
            case $event === null:
                return isset($this->rules()['*'])
                    ? $this->rules()['*']
                    : $this->rules();

            case !isset($this->rules()[$event]):
                return $this->mergeRuleWithDefault();

            case isset($this->rules()['*']):
                return array_merge($this->rules()['*'], $this->rules()[$event]);

            default:
                return $this->rules()[$event];
        }
    }
}
