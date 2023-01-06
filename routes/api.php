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

    Route::controller(ArtistController::class)->group(function () {

        //Update Artist
        Route::post('update-artist-business-profile', 'updateArtistBusinessProfile');

        // Upload Artist Photo Album
        Route::get('photo-album', 'photoAlbum');
        Route::post('artist-photo-upload', 'artistPhotoUpload');
        Route::delete('artist-photo-delete/{photo_id}', 'artistPhotoDelete');

        //Upload Artist Pricing
        Route::get('pricing-service',  'priceService');
        Route::post('add-artist-pricing',  'storeArtistPrice');
        Route::post('edit-artist-pricing/{price_id}',  'updateArtistPrice');
        Route::delete('delete-artist-pricing/{price_id}',  'deleteArtistPrice');

        //Upload Artist Payment Policy
        Route::post('add-artist-payment-policy',  'storePaymentPolicy');
        Route::post('edit-artist-payment-policy/{payment_policy_id}',  'updatePaymentPolicy');
        Route::delete('delete-artist-payment-policy/{payment_policy_id}',  'deletePaymentPolicy');

        //Update Cancellation Policy

        Route::post('add-artist-cancellation-policy',  'storeCancellationPolicy');
        Route::post('edit-artist-cancellation-policy/{payment_cancellation_id}',  'updateCancellationPolicy');
        Route::delete('delete-artist-cancellation-policy/{payment_cancellation_id}',  'deleteCancellationPolicy');
    });
});

Route::get('artist-details/{artist_id}', [HomeController::class, 'artistDetails']);
