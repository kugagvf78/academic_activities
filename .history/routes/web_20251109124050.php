<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Mặc định chuyển đến login
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes
Route::middleware('auth')->group(function () {
    Route::view('/home', 'client.home')->name('client.home');
    Route::view('/hoi-thao', 'client.events')->name('client.events');
});
// Client Routes (giao diện người dùng)
Route::middleware('auth')->group(function () {
    Route::view('/home', 'client.home')->name('client.home');
    Route::view('/gioi-thieu', 'client.about')->name('client.about');
    Route::view('/hoi-thao', 'client.events')->name('client.events');
    Route::view('/lien-he', 'client.contact')->name('client.contact');
});
