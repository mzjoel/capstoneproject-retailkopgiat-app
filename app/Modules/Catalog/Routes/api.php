<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Catalog\Controllers\ProductController;

Route::get('/categories', [ProductController::class, 'indexCategory']);
    Route::get('/categories/{id}', [ProductController::class, 'showCategory']);
    Route::delete('/categories/{id}', [ProductController::class, 'destroyCategory']);
    Route::get('/products', [ProductController::class, 'indexProducts']);
    Route::get('/products/{id}', [ProductController::class, 'showProduct']);
     
   
Route::middleware('auth:sanctum')->group(function () {
        Route::put('/categories/{id}/update', [ProductController::class, 'updateCategory']);
        Route::post('/categories', [ProductController::class, 'storeCategory']);
        Route::post('/products', [ProductController::class, 'storeProduct']);
        Route::put('/products/{id}/update', [ProductController::class, 'updateProduct']);
        Route::delete('/products/{id}/delete', [ProductController::class, 'destroyProduct']);
});

    