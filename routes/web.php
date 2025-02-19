<?php

use App\Http\Controllers\ProfileController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\CartController;
use Illuminate\Support\Facades\Route;


Route::get('/', [ProductController::class, 'index'])->name('home');
// Products route with optional 'type' filter
Route::get('/products', [ProductController::class, 'index'])->name('product.index');
//create product
Route::post('/product', [ProductController::class, 'store'])->name('product.store');
// Edit Product
Route::get('/product/{id}/edit', [ProductController::class, 'edit'])->name('product.edit');
Route::put('/product/{id}', [ProductController::class, 'update'])->name('product.update');
// Delete Product
Route::delete('/product/{id}', [ProductController::class, 'destroy'])->name('product.destroy');
//search Product
Route::get('/products/search', [ProductController::class, 'search'])->name('product.search');



//cart add
Route::post('/cart/add', [CartController::class, 'addToCart'])->name('cart.add');
//cart updated
Route::post('/cart/update', [CartController::class, 'updateQuantity'])->name('cart.update');
//cart remove
Route::post('/cart/remove', [CartController::class, 'removeItem'])->name('cart.remove');
//cart pay
Route::post('/cart/pay', [CartController::class, 'processPayment'])->name('cart.pay');
//cart discount
Route::post('/apply-discount', [CartController::class, 'applyDiscount'])->name('apply.discount');


//profile and login
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
