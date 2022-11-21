<?php

use App\Http\Controllers\API\AuthController;
use App\Http\Controllers\API\HomeController;
use App\Http\Controllers\API\ProductController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::controller(AuthController::class)->group(function () {
    Route::post('login', 'login');
    Route::post('register', 'register');
    Route::post('verify', 'verify');
    Route::post('social-login', 'socialLogin');
    Route::post('reset-password/{user_id}', 'resetPassword');
});
Route::middleware('auth:api')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('user', 'getUserDetails');
        Route::get('logout', 'logout');
        Route::post('update-profile/{user_id}', 'updateProfile');
    });
});
