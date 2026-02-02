<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Log;

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
            'status' => true,
            'token' => $access_token,
            'user' => $user,
        ]);
    }

    public function login(Request $request)
    {
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => false,
                'message' => 'Invalid credentials'
            ], 401);
        }

        $userdata = Auth::user();

        // ✅ Revoke old tokens (good security practice)
        $userdata->tokens()->delete();

        // ✅ Create new token
        $tokenResult = $userdata->createToken('API Token');

        $access_token = $tokenResult->accessToken;
        $tokenId     = $tokenResult->token->id;

        // ✅ Save only small token id (NOT full JWT)
        $userdata->token_id = $tokenId;
        $userdata->save();

        return response()->json([
            'status' => true,
            'token' => $access_token,
            'user' => $userdata,
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
            'status' => true,
            'message' => 'Logged out successfully'
        ]);
    }
}