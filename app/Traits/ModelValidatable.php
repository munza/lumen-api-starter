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
     * @param  string  $action
     * @return bool
     */
    public function isValidFor(string $action = ''): bool
    {
        $this->validator = app('validator')->make($this->attributes, $this->getRulesFor($action));

        return (bool) $this->validator()->passes();
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
     * Get rules for an action combined with common rules.
     *
     * @param  string  $action
     * @return array
     */
    private function getRulesFor(string $action): array
    {
        if (!method_exists($this, 'rules')) {
            return [];
        }

        $commonRules = $this->getRuleByAction('*');
        $actionRules = $this->getRuleByAction($action);

        return array_merge($commonRules, $actionRules);
    }

    /**
     * Get rules by action.
     *
     * @param  string  $action
     * @return array
     */
    private function getRuleByAction(string $action): array
    {
        if (!array_key_exists($action, $this->rules())) {
            return [];
        }

        return $this->rules()[$action];
    }
}
