<?php

use App\Http\Controllers\ProfileController;
use App\Modules\Catalog\Controllers\ProductController;
use App\Modules\Transactions\Controllers\TransactionController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return redirect()->route('login');
});

Route::get('/onboarding1', function () {
    return Inertia::render('Onboarding/Onboarding1');
})->name('onboarding1');

Route::get('/onboarding2', function () {
    return Inertia::render('Onboarding/Onboarding2');
})->name('onboarding2');

Route::get('/dashboard', function () {
    return Inertia::render('Dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::get('/products', function () {
    return Inertia::render('Catalog/Products');
})->middleware(['auth', 'verified'])->name('products');

Route::get('/products/wishlist', [ProductController::class, 'showWishlist'] )->middleware(['auth', 'verified'])->name('product.wishlist');

Route::get('/cart', function () {
    return Inertia::render('Transaction/Cart');
})->middleware(['auth', 'verified'])->name('cart');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::get('/transaction/history', [TransactionController::class, 'History'])->name('transaction.history');
});

Route::get('/transaction/{id}/checkout', function ($id) {
    return Inertia::render('Transaction/Checkout', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('transaction');

Route::get('/transaction/{id}/status', [TransactionController::class, 'getTransactionStatus'])->middleware(['auth', 'verified'])->name('transaction.status');

Route::get('/transaction/validation', function () {
    return Inertia::render('Transaction/Validation');
})->middleware(['auth', 'verified'])->name('validation');

Route::get('/transaction/history', function () {
    return Inertia::render('Transaction/TransactionHistory');
})->middleware(['auth', 'verified'])->name('transaction.history');

Route::get('/products/{id}', [ProductController::class, 'showProductPage'])->middleware(['auth', 'verified'])->name('products.detail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});


require __DIR__.'/auth.php';
