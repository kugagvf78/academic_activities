<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
*/

// Mặc định chuyển đến login
Route::get('/', fn() => redirect()->route('login'));

// Auth Routes
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');

    Route::get('/change-password', 'showChangePassword')->middleware('auth')->name('password.change');
    Route::post('/change-password', 'changePassword')->middleware('auth')->name('password.update');

    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});

// Client Routes (giao diện người dùng)
Route::middleware('auth')->group(function () {
    Route::get('/home', fn() => view('client.home'))->name('client.home');
    Route::get('/gioi-thieu', fn() => view('client.about'))->name('client.about');
    Route::get('/lien-he', fn() => view('client.contact'))->name('client.contact');
});
