<?php

use App\Http\Controllers\API\ArtistController;
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
    //Route::post('register', 'register');
    Route::post('get-otp', 'getOTP');
    Route::post('verify-otp', 'verifyOTP');
    Route::post('social-login', 'socialLogin');
    Route::post('reset-password/{user_id}', 'resetPassword');
});
Route::middleware('auth:sanctum')->group(function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('user', 'getUserDetails');
        Route::get('logout', 'logout');
        Route::post('update-profile/{user_id}', 'updateProfile');
        Route::get('banner-image', 'bannerImage');
        Route::get('category', 'category');
        Route::get('top-artist', 'topArtist');
        Route::get('post', 'post');
        // Route::get('artist-details/{artist_id}', 'artistDetails');

    });
    Route::post('artist-profile-update', [ArtistController::class, 'updateArtist']);
    Route::get('photo-album', [ArtistController::class, 'photoAlbum']);
    Route::post('artist-photo-upload', [ArtistController::class, 'artistPhotoUpload']);
    Route::get('pricing-service', [ArtistController::class, 'priceService']);
    Route::post('artist-pricing', [ArtistController::class, 'artistPrice']);
});

Route::get('artist-details/{artist_id}', [HomeController::class, 'artistDetails']);
