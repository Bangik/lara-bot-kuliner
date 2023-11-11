<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Http\Requests\AuthLoginRequest;
use App\Http\Requests\AuthRegisterRequest;

class AuthController extends Controller
{
    // register and login functions using laravel passport
    public function register(AuthRegisterRequest $request)
    {
        // validate the request
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:8',
        ]);

        // create a new user
        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            // hash the password
            'password' => Hash::make($request->password),
        ]);

        // create a token for the user
        $token = $user->createToken('auth_token')->accessToken;

        // return the token
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function login(AuthLoginRequest $request)
    {
        // validate the request
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:8',
        ]);

        // find the user
        $user = User::where('email', $request->email)->first();

        // check if the user exists
        if (!$user || !Hash::check($request->password, $user->password)) {
            return response()->json([
                'status' => 'failed',
                'message' => 'Invalid credentials',
            ], 401);
        }

        // create a token for the user
        $token = $user->createToken('auth_token')->accessToken;

        // return the token
        return response()->json([
            'status' => 'success',
            'access_token' => $token,
            'token_type' => 'Bearer',
        ]);
    }

    public function logout(Request $request)
    {
        // revoke the token
        $request->user()->token()->revoke();

        // return a success message
        return response()->json([
            'status' => 'success',
            'message' => 'Successfully logged out',
        ]);
    }

    public function user(Request $request)
    {
        // return the authenticated user
        return response()->json($request->user());
    }
}
