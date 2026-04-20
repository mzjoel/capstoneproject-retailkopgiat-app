<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\UserController;

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

Route::prefix('/v1/auth')->group(function () {
    Route::post('/signup', [UserController::class, 'register']);
    Route::post('/signin', [UserController::class, 'login']);

  
    Route::middleware('auth:sanctum')->group(function () {
        Route::post('/signout', [UserController::class, 'logout']);
    });
});