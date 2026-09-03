<?php

use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::get('/', [CatalogController::class, 'home'])->name('shop.home');
Route::get('/catalog', [CatalogController::class, 'catalog'])->name('shop.catalog');
Route::get('/category/{slug}', [CatalogController::class, 'category'])->name('shop.category');
Route::get('/product/{slug}', [CatalogController::class, 'product'])->name('shop.product');

Route::get('/cart', [CartController::class, 'show'])->name('shop.cart');
Route::post('/cart/add/{product}', [CartController::class, 'add'])->name('shop.cart.add');
Route::patch('/cart/{product}', [CartController::class, 'update'])->name('shop.cart.update');
Route::delete('/cart/{product}', [CartController::class, 'remove'])->name('shop.cart.remove');
Route::post('/cart/recalculate', [CartController::class, 'recalculate'])->name('shop.cart.recalculate');

Route::get('/checkout', [CheckoutController::class, 'create'])->name('shop.checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('shop.checkout.store');
Route::get('/checkout/success/{order}', [CheckoutController::class, 'success'])->name('shop.checkout.success');
