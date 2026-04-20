<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\ProductController;
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


Route::prefix('/v1')->group(function () {
    Route::get('/categories', [ProductController::class, 'indexCategory']);
    Route::get('/categories/{id}', [ProductController::class, 'showCategory']);
    Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory']);
    Route::get('/products', [ProductController::class, 'indexProducts']);
    Route::get('/products/{id}', [ProductController::class, 'showProduct']);
    Route::post('/payments/webhook', [TransactionController::class, 'handleNotification']);
     
   
    Route::middleware('auth:sanctum')->group(function () {
        Route::put('/categories/{id}/update', [ProductController::class, 'updateCategory']);
        Route::post('/categories', [ProductController::class, 'storeCategory']);
        Route::post('/products', [ProductController::class, 'storeProduct']);
        Route::put('/products/{id}/update', [ProductController::class, 'updateProduct']);
        Route::delete('/products/{id}/delete', [ProductController::class, 'destroyProduct']);
        Route::post('/transactions', [TransactionController::class, 'checkout']);
        Route::post('/transactions/validate', [TransactionController::class, 'validationPaymentDetails']);
        Route::get('/transactions/{id}/status', [TransactionController::class, 'getTransactionStatus']);
        Route::put('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);

    });
});


    