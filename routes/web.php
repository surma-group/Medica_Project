<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\CheckoutController;

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

Route::get('/about', [HomeController::class, 'about'])->name('about');
Route::get('/vision', [HomeController::class, 'vision'])->name('vision');
Route::get('/mission', [HomeController::class, 'mission'])->name('mission');
Route::get('/core-values', [HomeController::class, 'coreValues'])->name('core-values');
Route::get('/career', [HomeController::class, 'career'])->name('career');
Route::get('/terms-and-conditions', [HomeController::class, 'terms'])->name('terms');

// Authentication Routes (Website)
Route::get('/login', [LoginController::class, 'showLoginForm'])->name('login');
Route::post('/login', [LoginController::class, 'login'])->name('login.perform');
Route::get('/logout', [LoginController::class, 'logout'])->name('logout');