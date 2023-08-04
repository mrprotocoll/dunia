<?php

use App\Http\Controllers\Api\V1\Admin;
use App\Http\Controllers\Api\V1\LoginController;
use App\Http\Controllers\Api\V1\RegisterController;
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

Route::prefix('admin')->middleware(['auth:sanctum'])->group(function (){
    Route::middleware(['role:1'])->group(function () {
        Route::apiResource('authors', Admin\AuthorController::class)
            ->only(['index', 'show', 'store', 'update']);
        Route::apiResource('tags', Admin\TagController::class)
            ->only(['index', 'show', 'store', 'update']);
    });
});

Route::post('register', RegisterController::class);
Route::post('login', LoginController::class);
