<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\RegisterController;
use App\Http\Controllers\ProductRequestController;
use App\Http\Controllers\ProfileController;
use App\Http\Middleware\RedirectIfNotCustomer;

// Public Website Routes
Route::get('/', [HomeController::class, 'index'])->name('home');
Route::get('/contact', [HomeController::class, 'contact'])->name('contact');
Route::get('/shop', [HomeController::class, 'shop'])->name('shop');
Route::get('/order-request', [HomeController::class, 'orderRequest'])->name('order.request');
Route::post('/order-request/store', [OrderController::class, 'store'])->name('order.request.store');
Route::post('/cart/add/{id}', [CartController::class, 'add'])->name('cart.add');
Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
Route::get('/cart/remove/{id}', [CartController::class, 'remove'])->name('cart.remove');
Route::get('/cart/update/{id}/{type}', [CartController::class, 'updateCart'])->name('cart.update');
Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout');
Route::post('/checkout', [CheckoutController::class, 'store'])->name('checkout.store');
// frontend (customer)
Route::get('/product/request', [ProductRequestController::class, 'create'])->name('product.request');
Route::post('/product/request', [ProductRequestController::class, 'store'])->name('product.request.store');

Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/vision', [HomeController::class, 'vision'])->name('vision');
Route::get('/mission', [HomeController::class, 'mission'])->name('mission');
Route::get('/core-values', [HomeController::class, 'coreValues'])->name('core-values');
Route::get('/career', [HomeController::class, 'career'])->name('career');
Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms');

// Authentication Routes (Website)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::get('/register', [RegisterController::class, 'showRegisterForm'])->name('register');
Route::post('/register', [RegisterController::class, 'register'])->name('register.store');

Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::post('/logout', [LoginController::class, 'logout'])->name('logout');

Route::middleware(['auth', RedirectIfNotCustomer::class])->group(function () {
    Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
    Route::get('/profile/edit', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::post('/profile/update', [ProfileController::class, 'update'])->name('profile.update');

    Route::get('/profile/password', [ProfileController::class, 'password'])->name('profile.password');
    Route::post('/profile/password', [ProfileController::class, 'updatePassword'])->name('profile.password.update');

    Route::get('/profile/orders', [ProfileController::class, 'orders'])->name('profile.orders');
    Route::get('/profile/orders/{id}', [ProfileController::class, 'showOrder'])->name('profile.order.show');
});

Route::get('/shozollinkstorage', function () {
    Artisan::call('storage:link');
    return "Storage link created!";
});
