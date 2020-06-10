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
     * @param string $action
     *
     * @return bool
     */
    public function isValidFor(string $action = ''): bool
    {
        $this->validator = app('validator')->make($this->attributes, $this->mergeRules('*', $action));

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
     * Get rules by action.
     *
     * @param string $action
     *
     * @return array
     */
    private function getRuleByAction(string $action): array
    {
        switch (true) {
            case '' === $action:
            case !method_exists($this, 'rules'):
            case !key_exists($action, $this->rules()):
                return [];

            default:
                return $this->rules()[$action];
        }
    }

    /**
     * Merge two rules together.
     * The first rules will be overwritten by the second one.
     *
     * @param string $first
     * @param string $second
     *
     * @return array
     */
    private function mergeRules(string $first, string $second): array
    {
        $firstRules = $this->getRuleByAction($first);
        $secondRules = $this->getRuleByAction($second);

        return array_merge($firstRules, $secondRules);
    }
}
