<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\V1\Admin\MenuApiController;
use App\Http\Controllers\Api\V1\Admin\RolesApiController;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);
Route::post('test', function () {
    return response()->json(['status' => 'API working']);
});

Route::middleware('auth:api')->group(function () {
    Route::get('profile', [AuthController::class, 'profile']);
    Route::post('logout', [AuthController::class, 'logout']);

    // RolesApiController
    Route::resource('roles', RolesApiController::class);

    //MenuApiController
    Route::post('menutree', [MenuApiController::class, 'menutree']);
    Route::resource('menu', MenuApiController::class);
    Route::get('rolemenu', [MenuApiController::class, 'rolemenu']);
    Route::get('getmenuaccess/{roleid}', [MenuApiController::class, 'getmenuaccess']);
    Route::post('storemenuaccess', [MenuApiController::class, 'storemenuaccess']);
    Route::get('parentmenus', [MenuApiController::class, 'parentMenus']);

});
