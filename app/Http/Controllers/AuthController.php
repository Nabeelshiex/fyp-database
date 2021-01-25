<?php

namespace App\Http\Controllers;

header('Access-Control-Allow-Origin: *');

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use App\User;

class AuthController extends Controller
{
    /**
     * Create a new AuthController instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth:api', ['except' => ['login']]);
    }

    /**
     * Get a JWT via given credentials.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function login()
    {
        $credentials = request(['email', 'password']);

        if (!$token = auth()->attempt($credentials)) {
            return response()->json(['message' => 'Invalid email or password'], 401);
        }

        return $this->respondWithToken($token, $credentials, null);
    }

    /**
     * Get the authenticated User.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function me()
    {
        return response()->json(auth()->user());
    }

    /**
     * Log the user out (Invalidate the token).
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function logout(Request $request)
    {
        auth()->logout();

        $user = User::where('remember_token', $request->token)->first();
        $user->remember_token = null;
        $user->save();

        return response()->json(['message' => 'Successfully logged out']);
    }

    /**
     * Refresh a token.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function refresh(Request $request)
    {
        $oldToken = $request->token;

        return $this->respondWithToken(auth()->refresh(), null, $oldToken);
    }

    /**
     * Get the token array structure.
     *
     * @param  string $token
     *
     * @return \Illuminate\Http\JsonResponse
     */
    protected function respondWithToken($token, $credentials, $oldToken)
    {
        if ($credentials) {
            $user = User::where('email', $credentials['email'])->first();
            $user->remember_token = $token;
            $user->save();
        }

        if ($oldToken) {
            $user = User::where('remember_token', $oldToken)->first();
            $user->remember_token = $token;
            $user->save();
        }

        return response()->json([
            'token' => $token
        ]);
    }
}
