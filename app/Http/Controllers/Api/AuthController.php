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
            'status' => 'S',
            'token' => $access_token,
            'user' => $user,
        ]);
    }

public function login(Request $request)
    {
        $allowedCountries = ['GB', 'IM', 'JE', 'GG', 'GI'];
        $countryCode = null;
        $countryName = null;
 
        /**
         * 1️⃣ Try Cloudflare country header (BEST for production)
         */
        if ($request->hasHeader('CF-IPCountry')) {
            $countryCode = $request->header('CF-IPCountry');
            $countryName = $countryCode;
        }
 
        /**
         * 2️⃣ Localhost handling (dev environment)
         */
        if (!$countryCode && app()->environment('local')) {
            $countryCode = 'IN'; // allow localhost testing
            $countryName = 'INDIA';
        }
 
        /**
         * 3️⃣ IP-based lookup fallback (non-local, non-cloudflare)
         */
        if (!$countryCode) {
            $ip = $request->ip();
            $location = Location::get($ip);
 
            if ($location && $location->countryCode) {
                $countryCode = $location->countryCode;
                $countryName = $location->countryName;
            }
        }
 
        /**
         * 4️⃣ Block if still unable to detect
         */
        if (!$countryCode) {
            return response()->json([
                'status' => 'E',
                'message' => 'Unable to detect location',
            ], 403);
        }
 
        /**
         * 5️⃣ Block if country not allowed
         */
        if (!in_array($countryCode, $allowedCountries)) {
            return response()->json([
                'status' => 'E',
                'message' => 'Login not allowed from your country',
                'country' => $countryName,
            ], 403);
        }
 
        /**
         * 6️⃣ Authenticate user
         */
        if (!Auth::attempt($request->only('email', 'password'))) {
            return response()->json([
                'status' => 'E',
                'message' => 'Invalid credentials',
            ], 401);
        }
 
        $user = Auth::user();
 
        /**
         * 7️⃣ Revoke old tokens
         */
        $user->tokens()->delete();
 
        /**
         * 8️⃣ Create new token
         */
        $tokenResult = $user->createToken('API Token');
        $accessToken = $tokenResult->accessToken;
        $tokenId = $tokenResult->token->id;
 
        /**
         * 9️⃣ Save token id only
         */
        $user->token_id = $tokenId;
        $user->save();
 
        return response()->json([
            'status' => 'S',
            'token' => $accessToken,
            'user' => $user,
            'country' => $countryName,
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
            'message' => 'Logged out successfully'
        ]);
    }
}