<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\Admin\ProductImageController;
use App\Http\Controllers\Api\V1\OrderController;
use Illuminate\Support\Facades\Route;

Route::prefix('admin')->group(function (){
    Route::middleware(['auth:sanctum', 'role:admin'])->group(function () {
        Route::apiResource('authors', Admin\AuthorController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('tags', Admin\TagController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('categories', Admin\CategoryController::class)
            ->only(['index', 'show', 'store', 'update']);

        Route::apiResource('ages', Admin\AgeRangeController::class)
            ->only(['destroy', 'show', 'store', 'update']);

        Route::apiResource('products', Admin\ProductController::class)
            ->only(['store', 'update']);

        Route::post('products/{product}/images', [ProductImageController::class, 'store']);
        Route::delete('products/{product}/images/{productImage}', [Admin\ProductImageController::class, 'destroy']);

        Route::get('orders', [OrderController::class, 'index']);
        Route::get('orders/{order}', [OrderController::class, 'show']);

        Route::apiResource('users', Admin\ProductController::class)
            ->only(['index', 'show']);
    });

    // admin Authentication
    Route::post('register', Admin\Auth\RegisterController::class);
    Route::post('login', Admin\Auth\LoginController::class);
});
