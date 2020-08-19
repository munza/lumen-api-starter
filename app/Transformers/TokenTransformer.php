<?php

namespace App\Transformers;

use League\Fractal\TransformerAbstract;

class TokenTransformer extends TransformerAbstract
{
    /**
     * A Fractal transformer.
     *
     * @param  string  $token
     * @return array
     */
    public function transform(string $token): array
    {
        return [
            'access_token' => (string) $token,
            'token_type' => 'bearer',
            'expires_in' => (string) app('auth')->factory()->getTTL() * 60,
        ];
    }
}
