<?php

use App\Http\Controllers\Api\V1\Admin\MenuApiController;
use App\Http\Controllers\Api\V1\Auth\RecoverPasswordApiController;
use App\Http\Controllers\Api\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\V1\Admin\RolesApiController;
use App\Http\Controllers\Api\V1\Admin\LookupsApiController;
use App\Http\Controllers\Api\V1\Auth\UserRegistrationApiController;

//registration
Route::post('register', [AuthController::class, 'register']);

//Login
Route::middleware('throttle:5,2')->post('/login', [AuthController::class, 'login']);

//Password Reset and Resend Otp
Route::middleware('throttle:5,2')->post('reset_password', [RecoverPasswordApiController::class, 'sendPasswordReset']);
Route::middleware('throttle:5,2')->post('/resetuserpassword', [ChangePasswordApiController::class, 'changePassword']);
Route::middleware('throttle:5,2')->post('resend_otp_validate', [RecoverPasswordApiController::class, 'sendRegistrationOtp']);
Route::middleware('throttle:5,2')->post('registration_otp_validate', [UserRegistrationApiController::class, 'validateRegistrationOtp']);
Route::middleware('throttle:5,2')->post('resetpassword', [RecoverPasswordApiController::class, 'validateOtp']);

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

    // Email Templates
    Route::resource('emailtemplates', 'App\Http\Controllers\Api\V1\Admin\EmailTemplateApiController');

    //LookupsApiController
    Route::get('lookupdata/{type}', [LookupsApiController::class, 'lookupdata']);
    Route::get('child_lookups_edit', [LookupsApiController::class, 'childLookupEdit']);
    Route::resource('lookups', 'App\Http\Controllers\Api\V1\Admin\LookupsApiController');
    Route::get('/fetchlookup', [LookupsApiController::class, 'fetchLookup']);
    Route::get('/fetch_lang_lookup', [LookupsApiController::class, 'fetchLangLookup']);
    Route::post('/update_lookups_status', [LookupsApiController::class, 'updateLookupStatus']);
    Route::post('/fetch_parent_lookup', [LookupsApiController::class, 'fetchParentLookup']);
    Route::post('/save_lookups', [LookupsApiController::class, 'store_lookups']);
    Route::post('/save_child_lookups', [LookupsApiController::class, 'store_child_lookups']);
    Route::post('/delete_lookup/{id}', [LookupsApiController::class, 'destroy']);


});
