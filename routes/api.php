<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use Illuminate\Support\Facades\Route;

Route::middleware('web')->group(function () {
    Route::get('/shop/bootstrap', [CatalogController::class, 'bootstrap'])->name('api.shop.bootstrap');
    Route::get('/shop/home', [CatalogController::class, 'home'])->name('api.shop.home');
    Route::get('/shop/catalog', [CatalogController::class, 'catalog'])->name('api.shop.catalog');
    Route::get('/shop/category/{slug}', [CatalogController::class, 'category'])->name('api.shop.category');
    Route::get('/shop/product/{slug}', [CatalogController::class, 'product'])->name('api.shop.product');

    Route::get('/shop/cart', [CartController::class, 'show'])->name('api.shop.cart');
    Route::post('/shop/cart/{product}', [CartController::class, 'add'])->name('api.shop.cart.add');
    Route::patch('/shop/cart/{product}', [CartController::class, 'update'])->name('api.shop.cart.update');
    Route::delete('/shop/cart/{product}', [CartController::class, 'remove'])->name('api.shop.cart.remove');

    Route::get('/shop/checkout', [CheckoutController::class, 'create'])->name('api.shop.checkout');
    Route::post('/shop/checkout', [CheckoutController::class, 'store'])->name('api.shop.checkout.store');
    Route::get('/shop/orders/{order}', [CheckoutController::class, 'success'])->name('api.shop.order');

    Route::post('/login', [LoginController::class, 'store'])->name('api.login');
    Route::post('/register', [RegisterController::class, 'store'])->name('api.register');
    Route::post('/logout', [LoginController::class, 'destroy'])->name('api.logout');

    Route::middleware('auth')->group(function () {
        Route::get('/account', [AccountController::class, 'show'])->name('api.account');
        Route::put('/account', [AccountController::class, 'update'])->name('api.account.update');
    });
});
