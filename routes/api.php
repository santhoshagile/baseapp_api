<?php

use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('test', function () {
    return response()->json(['status' => 'API working']);
});

Route::middleware('auth:api')->group(function () {
    Route::post('menutree', [MenuApiController::class, 'menutree']);
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);
});
