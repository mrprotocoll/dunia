<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Auth\AuthenticatedSessionController;
use App\Http\Controllers\Api\V1\Auth\EmailVerificationNotificationController;
use App\Http\Controllers\Api\V1\Auth\LoginController;
use App\Http\Controllers\Api\V1\Auth\LogoutController;
use App\Http\Controllers\Api\V1\Auth\RegisterController;
use App\Http\Controllers\Api\V1\Auth\VerifyEmailController;
use App\Http\Controllers\Api\V1\OrderController;
use App\Http\Controllers\Api\V1\ProductController;
use App\Http\Controllers\Api\V1\ReviewController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::prefix('admin')->group(function (){
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::apiResource('authors', Admin\AuthorController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('tags', Admin\TagController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('categories', Admin\CategoryController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('products', Admin\ProductController::class)
            ->only(['store', 'update']);

        Route::post('products/{product}/images', [ProductImageController::class, 'store']);
        Route::delete('products/{product}/images/{productImage}', [Admin\ProductImageController::class, 'destroy']);
    });

    // admin Authentication
    Route::post('register', Admin\Auth\RegisterController::class);
    Route::post('login', Admin\Auth\LoginController::class);
});

// Customer protected routes
Route::middleware(['auth:sanctum'])->group(function (){
    Route::middleware(['role:customer'])->group(function () {
        Route::post('order', [OrderController::class, 'store']);
    });

    Route::post('products/{product}/reviews', [ReviewController::class, 'store']);
});

// Authentications
Route::post('/logout', [AuthenticatedSessionController::class, 'destroy'])
    ->middleware('auth:sanctum')
    ->name('logout');

Route::get('/verify-email/{id}/{hash}', VerifyEmailController::class)
    ->middleware(['auth', 'signed', 'throttle:6,1'])
    ->name('verification.verify');

Route::post('/email/verification-notification', [EmailVerificationNotificationController::class, 'store'])
    ->middleware(['auth', 'throttle:6,1'])
    ->name('verification.send');
