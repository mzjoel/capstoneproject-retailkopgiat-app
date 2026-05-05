<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Modules\Transactions\Controllers\TransactionController;


Route::middleware(['web', 'auth'])->group(function () {
    Route::get('/transactions/history', [TransactionController::class, 'History']);
    Route::post('/transactions', [TransactionController::class, 'checkout']);
    Route::post('/transactions/validate', [TransactionController::class, 'validationPaymentDetails']);
    Route::get('/transactions/{id}/status', [TransactionController::class, 'getTransactionStatus']);
    Route::patch('/transactions/{id}/status', [TransactionController::class, 'updateStatus']);
});

    