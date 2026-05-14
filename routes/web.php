<?php

use App\Http\Controllers\ProfileController;
use App\Modules\Catalog\Controllers\ProductController;
use Illuminate\Foundation\Application;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

Route::get('/', function () {
    return Inertia::render('Welcome', [
        'canLogin' => Route::has('login'),
        'canRegister' => Route::has('register'),
        'laravelVersion' => Application::VERSION,
        'phpVersion' => PHP_VERSION,
    ]);
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

Route::get('/wishlist', function () {
    return Inertia::render('Catalog/Wishlist');
})->middleware(['auth', 'verified'])->name('wishlist');

Route::get('/cart', function () {
    return Inertia::render('Transaction/Cart');
})->middleware(['auth', 'verified'])->name('cart');

Route::get('/transaction/{id}/history', function ($id) {
    return Inertia::render('Transaction/Checkout', ['id' => $id]);
})->middleware(['auth', 'verified'])->name('transaction');

Route::get('/transaction/validation', function () {
    return Inertia::render('Transaction/Validation');
})->middleware(['auth', 'verified'])->name('validation');

Route::get('/products/{id}', [ProductController::class, 'showProductPage'])->middleware(['auth', 'verified'])->name('products.detail');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
