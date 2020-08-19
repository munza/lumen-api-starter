<?php

namespace App;

use App\Events\UserCreated;
use App\Events\UserUpdated;
use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class Accounts
{
    /**
     * Get list of paginated users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function getUsers(Request $request): array
    {
        $users = User::filter($request)->paginate($request->get('per_page', 20));

        return fractal($users, new UserTransformer())->toArray();
    }

    /**
     * Get a user by ID.
     *
     * @param  int  $id
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function getUserById(int $id): array
    {
        $user = User::findOrFail($id);

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Store a new user.
     *
     * @param  array  $attrs
     * @return array
     *
     * @throws \Illuminate\Validation\ValidationException
     */
    public function storeUser(array $attrs): array
    {
        $user = new User($attrs);

        if (!$user->isValidFor('CREATE')) {
            throw new ValidationException($user->validator());
        }

        $user->save();

        event(new UserCreated($user));

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Update a user by ID.
     *
     * @param  int  $id
     * @param  array  $attrs
     * @return array
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     */
    public function updateUserById(int $id, array $attrs): array
    {
        $user = User::findOrFail($id);
        $user->fill($attrs);

        if (!$user->isValidFor('UPDATE')) {
            throw new ValidationException($user->validator());
        }

        $changes = $user->getDirty();
        $user->save();

        event(new UserUpdated($user, $changes));

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Delete a user by ID.
     *
     * @param  int  $id
     * @return bool
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     */
    public function deleteUserById(int $id): bool
    {
        $user = User::findOrFail($id);

        return (bool) $user->delete();
    }
}
