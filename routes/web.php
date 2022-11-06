<?php

use App\Http\Controllers\Admin\AdminDashboard;
use App\Http\Controllers\Admin\BrandController;
use App\Http\Controllers\Admin\CategoryController;
use App\Http\Controllers\Admin\UserController;
use App\Http\Controllers\Admin\CmsController;
use App\Http\Controllers\Admin\FaqController;
use App\Http\Controllers\Admin\ProductController;
use App\Http\Controllers\Admin\VendorController;
use App\Http\Controllers\Frontend\HomeController;
use App\Http\Controllers\Vendor\VendorDashboard;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/

//Frontend Route
Route::get('/', [HomeController::class, 'home']);
Route::get('user-login', [HomeController::class, 'login'])->name('login.get');
Route::get('user-register', [HomeController::class, 'register'])->name('register.get');

Route::group(['middleware' => 'auth'], function () {
    Route::controller(HomeController::class)->group(function () {
        Route::get('/dashboard', 'getDashboard')->name('dashboard');
        Route::get('/profile', 'getProfile')->name('profile');
        Route::get('/logout', 'logout')->name('user.logout');
    });
});

//Admin Routes
Route::redirect('/admin', 'admin/dashboard');
Route::group(['prefix' => 'admin'], function () {
    Route::get('login', [AdminDashboard::class, 'login'])->name('admin.login');
    Route::group(['middleware' => ['auth', 'checkAdmin']], function () {
        Route::controller(AdminDashboard::class)->group(function () {
            Route::get('/dashboard', 'getDashboard')->name('admin.dashboard');
            Route::get('/profile', 'getProfile')->name('admin.profile');
            Route::get('/logout', 'logout')->name('admin.logout');
        });
        Route::resource('users', UserController::class);
        Route::resource('vendors', VendorController::class);
        Route::resource('category', CategoryController::class);
        Route::resource('brand', BrandController::class);
        Route::resource('product', ProductController::class);
        Route::resource('cms', CmsController::class);
        Route::resource('faq', FaqController::class);
    });
});

//Vendor Routes
Route::redirect('/vendor', 'vendor/dashboard');
Route::group(['prefix' => 'vendor'], function () {
    Route::get('login', [VendorDashboard::class, 'login'])->name('vendor.login');
    Route::post('login', [VendorDashboard::class, 'authenticate'])->name('vendor.login.post');
    Route::group(['middleware' => ['auth', 'checkVendor']], function () {
        Route::controller(VendorDashboard::class)->group(function () {
            Route::get('/dashboard', 'getDashboard')->name('vendor.dashboard');
            Route::get('/profile', 'getProfile')->name('vendor.profile');
            Route::get('/logout', 'logout')->name('vendor.logout');
        });
    });
});

//
include('artisan.php');
