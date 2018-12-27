<?php

namespace App;

use App\Transformers\TokenTransformer;
use App\Transformers\UserTransformer;
use Symfony\Component\HttpKernel\Exception\UnauthorizedHttpException;

class Auth
{
    /**
     * Authenticate a user by emailand password
     *
     * @param string $email
     * @param string $password
     *
     * @return array
     */
    public function authenticateByEmailAndPassword(string $email, string $password): array
    {
        if (!$token = app('auth')->attempt(compact('email', 'password'))) {
            throw new UnauthorizedHttpException();
        }

        return fractal($token, new TokenTransformer())->toArray();
    }

    /**
     * Get the current authenticated user.
     *
     * @return array
     */
    public function getAuthenticatedUser(): array
    {
        $user = app('auth')->user();

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Refresh current authentication token.
     *
     * @return array
     */
    public function refreshAuthenticationToken()
    {
        $token = app('auth')->refresh();

        return fractal($token, new TokenTransformer())->toArray();
    }

    /**
     * Invalidate current authentication token.
     *
     * @return bool
     */
    public function invalidateAuthenticationToken(): bool
    {
        if (!app('auth')->logout()) {
            return false;
        }

        return true;
    }
}