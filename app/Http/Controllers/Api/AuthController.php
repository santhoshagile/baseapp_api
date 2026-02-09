<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users',
            'password' => 'required|min:6',
        ]);

        $user = User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        $access_token = $user->createToken('API Token')->accessToken;

        return response()->json([
            'status' => 'S',
            'access_token' => $access_token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        /**
         * Authenticate user
         */
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'E',
                'message' => 'Invalid credentials',
            ], 401);
        }

        $user = Auth::user();

        /**
         * Revoke old tokens
         */
        $user->tokens()->delete();

        /**
         * Create new token
         */
        $tokenResult = $user->createToken('API Token');
        $accessToken = $tokenResult->accessToken;
        $tokenId = $tokenResult->token->id;

        /**
         * Save token id only
         */
        $user->token_id = $tokenId;
        $user->save();

        return response()->json([
            'status' => 'S',
            'access_token' => $accessToken,
            'user' => $user,
        ]);
    }

    public function profile(Request $request)
    {
        return response()->json(Auth::user());
    }

    public function logout(Request $request)
    {
        $request->user()->token()->revoke();

        return response()->json([
            'status' => 'S',
            'message' => 'Logged out successfully',
        ]);
    }
}
