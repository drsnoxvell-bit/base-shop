<?php

use App\Http\Controllers\Auth\LoginController;
use App\Http\Controllers\Auth\RegisterController;
use App\Http\Controllers\Auth\SocialAuthController;
use App\Http\Controllers\Shop\AccountController;
use App\Http\Controllers\Shop\CartController;
use App\Http\Controllers\Shop\CatalogController;
use App\Http\Controllers\Shop\CheckoutController;
use App\Http\Controllers\SpaController;
use App\Support\ShopStack;
use Illuminate\Support\Facades\Route;

Route::get('/auth/{provider}/redirect', [SocialAuthController::class, 'redirect'])
    ->name('auth.social.redirect');
Route::get('/auth/{provider}/callback', [SocialAuthController::class, 'callback'])
    ->name('auth.social.callback');

Route::post('/logout', [LoginController::class, 'destroy'])->name('logout')->middleware('auth');

if (ShopStack::isSpa()) {
    Route::post('/login', [LoginController::class, 'store']);
    Route::post('/register', [RegisterController::class, 'store']);

    Route::get('/', SpaController::class)->name('shop.home');
    Route::get('/catalog', SpaController::class)->name('shop.catalog');
    Route::get('/category/{slug}', SpaController::class)->name('shop.category');
    Route::get('/product/{slug}', SpaController::class)->name('shop.product');
    Route::get('/cart', SpaController::class)->name('shop.cart');
    Route::get('/checkout', SpaController::class)->name('shop.checkout');
    Route::get('/checkout/success/{order}', SpaController::class)->name('shop.checkout.success');
    Route::get('/login', SpaController::class)->name('login');
    Route::get('/register', SpaController::class)->name('register');
    Route::get('/account', SpaController::class)->name('shop.account');

    Route::get('/{any}', SpaController::class)
        ->where('any', '^(?!admin|api|sanctum|auth|storage|vendor|up).*$');

    return;
}

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

Route::middleware('guest')->group(function () {
    Route::get('/login', [LoginController::class, 'create'])->name('login');
    Route::post('/login', [LoginController::class, 'store']);
    Route::get('/register', [RegisterController::class, 'create'])->name('register');
    Route::post('/register', [RegisterController::class, 'store']);
});

Route::middleware('auth')->group(function () {
    Route::get('/account', [AccountController::class, 'show'])->name('shop.account');
    Route::put('/account', [AccountController::class, 'update'])->name('shop.account.update');
});
