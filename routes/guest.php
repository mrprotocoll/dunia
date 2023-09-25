<?php

use App\Http\Controllers\Api\GeoController;
use App\Http\Controllers\Api\V1\Admin\AgeRangeController;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\GoogleAuthController;
use App\Http\Controllers\Api\V1\Auth\NewPasswordController;
use App\Http\Controllers\Api\V1\Auth\PasswordResetLinkController;
use App\Http\Controllers\Api\V1\Auth\RegisteredUserController;
use App\Http\Controllers\Api\V1\ProductController;
use Illuminate\Support\Facades\Route;

Route::post('/register', [RegisteredUserController::class, 'store'])
    ->middleware('guest')
    ->name('register');

Route::post('/login', [AuthenticatedSessionController::class, 'store'])
    ->middleware('guest')
    ->name('login');

Route::post('/auth/oauth', GoogleAuthController::class)
    ->middleware('guest')
    ->name('auth.google');

Route::post('/forgot-password', [PasswordResetLinkController::class, 'store'])
    ->middleware('guest')
    ->name('password.email');

Route::post('/reset-password', [NewPasswordController::class, 'store'])
    ->middleware('guest')
    ->name('password.store');

//products
Route::apiResource('/products', ProductController::class)
    ->only(['index', 'show']);
Route::get('/products/{category}', [ProductController::class, 'categories']);
Route::post('/products/filter', [ProductController::class, 'filter']);

// GeoLocation
Route::get('/countries', [GeoController::class, 'countries']);
Route::get('/states/{country}', [GeoController::class, 'states']);
Route::get('/cities/{state}', [GeoController::class, 'cities']);

// age ranges
Route::get('/ages', [AgeRangeController::class, 'index']);

//chat
Route::get('/chat', [App\Http\Controllers\Api\V1\ChatGPTController::class, 'askChatGpt']);

