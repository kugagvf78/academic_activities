<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


// Mặc định chuyển đến login
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes
// Giao diện public
Route::controller(ClientController::class)->group(function () {
    Route::get('/', 'home')->name('client.home');
    Route::get('/hoi-thao', 'events')->name('client.events');
    Route::get('/hoi-thao/{slug}', 'eventDetail')->name('client.event.detail');
    Route::get('/tin-tuc', 'news')->name('client.news');
    Route::get('/lien-he', 'contact')->name('client.contact');
});


// Client Routes (giao diện người dùng)
Route::middleware('auth')->group(function () {
    Route::view('/hoi-thao', 'client.events')->name('client.events');
});
