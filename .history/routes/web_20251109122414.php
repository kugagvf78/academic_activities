<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ✅ Trang mặc định: chuyển đến login
Route::get('/', fn() => redirect()->route('login'));

// ✅ Nhóm Auth
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');

    Route::get('/change-password', 'showChangePassword')->middleware('auth')->name('password.change');
    Route::post('/change-password', 'changePassword')->middleware('auth')->name('password.update');

    Route::post('/logout', 'logout')->middleware('auth')->name('logout');
});
