<?php

use App\Http\Controllers\Api\Auth\AuthController;
use App\Http\Controllers\Api\MovieController;
use App\Http\Controllers\Api\RatingController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::post('register', [AuthController::class, 'register']);
Route::post('login', [AuthController::class, 'login']);

Route::middleware('auth:api')->group(function () {
    Route::get('/logout', [AuthController::class, 'logout']);
});

Route::prefix('v1')->group(function () {
    Route::apiResource('movies', MovieController::class)->only(['index', 'show']);
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('movies', MovieController::class)->only(['store', 'update', 'destroy']);
    });

    Route::apiResource('ratings', RatingController::class)->only(['index', 'show']);
    Route::middleware('auth:api')->group(function () {
        Route::apiResource('ratings', RatingController::class)->only(['store', 'update', 'destroy']);
    });
});



Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');
