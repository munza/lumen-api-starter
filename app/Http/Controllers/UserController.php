<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class UserController extends Controller
{
    /**
     * Get all the users.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        return response()->json([
            'data' => User::all()->toArray(),
        ], Response::HTTP_OK);
    }

    /**
     * Store a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $validated = $this->validate($request, [
            'email'    => 'required|unique:users',
            'name'     => 'required|min:4',
            'password' => 'required|min:6',
        ]);

        return response()->json([
            'data' => User::create($validated),
        ], Response::HTTP_CREATED);
    }

    /**
     * Get a user.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(string $id): JsonResponse
    {
        $user = User::findOrFail($id);

        return response()->json($user);
    }

    /**
     * Update a user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  mixed  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(Request $request, $id): JsonResponse
    {
        $user = User::findOrFail($id);

        $validated = $this->validate($request, [
            'email'    => 'required|unique:users,email,' . $id,
            'name'     => 'required|min:4',
            'password' => 'sometimes|min:6',
        ]);

        $user->fill($validated);
        $user->save();

        return response()->json([
            'data' => $user->toArray(),
        ], Response::HTTP_ACCEPTED);
    }

    /**
     * Delete a user.
     *
     * @param  mixed  $id
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy($id): JsonResponse
    {
        $user = User::findOrFail($id);

        $user->delete();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
