<?php

namespace App\Http\Controllers;

use App\Accounts;
use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Http\Response;

class AuthController extends Controller
{
    /**
     * Controller constructor.
     *
     * @param  \App\Accounts  $accounts
     */
    public function __construct(Accounts $accounts)
    {
        $this->accounts = $accounts;
    }

    /**
     * Get a JWT via given credentials.
     *
     * @param  Illuminate\Http\Request  $request
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(Request $request): JsonResponse
    {
        $token = $this->accounts->authenticateByEmailAndPassword(
            $request->input('email'),
            $request->input('password')
        );

        return response()->json($token, Response::HTTP_OK);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(): JsonResponse
    {
        $user = $this->accounts->getAuthenticatedUser();

        return response()->json($user, Response::HTTP_OK);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(): JsonResponse
    {
        $token = $this->accounts->refreshAuthenticationToken();

        return response()->json($token, Response::HTTP_OK);
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(): JsonResponse
    {
        $this->accounts->invalidateAuthenticationToken();

        return response()->json(null, Response::HTTP_NO_CONTENT);
    }
}
