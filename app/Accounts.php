<?php

namespace App;

use App\Models\User;
use App\Transformers\UserTransformer;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Spatie\Fractal\Fractal;

class Accounts
{
    /**
     * Get list of paginated users.
     *
     * @param  \Illuminate\Http\Request  $request
     *
     * @return array
     */
    public function getUsersWithPagination(Request $request): array
    {
        $users = User::filter($request)->paginate();

        return fractal($users, new UserTransformer())->toArray();
    }

    /**
     * Get a user by ID.
     *
     * @param  int  $id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return array
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
     *
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return array
     */
    public function storeUser(array $attrs): array
    {
        $user = new User($attrs);

        app('validator')->validate($attrs, [
            'email' => 'required|unique:users',
            'name' => 'required|min:4',
            'password' => 'required|min:6',
        ]);

        $user->save();

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Update a user by ID.
     *
     * @param  int  $id
     * @param  array  $attrs
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     * @throws \Illuminate\Validation\ValidationException
     *
     * @return array
     */
    public function updateUserById(int $id, array $attrs): array
    {
        $user = User::findOrFail($id);

        app('validator')->validate($attrs, [
            'email' => 'required|unique:users,email,' . $id,
            'name' => 'required|min:4',
            'password' => 'sometimes|min:6',
        ]);

        $user->fill($attrs);
        $user->save();

        return fractal($user, new UserTransformer())->toArray();
    }

    /**
     * Delete a user by ID.
     *
     * @param  int  $id
     *
     * @throws \Illuminate\Database\Eloquent\ModelNotFoundException
     *
     * @return array
     */
    public function deleteUserById(int $id): array
    {
        $user = User::findOrFail($id);

        $user->delete();

        return fractal($user, new UserTransformer())->toArray();
    }
}
