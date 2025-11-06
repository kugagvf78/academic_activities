<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

Route::get('/', function () {
    return redirect()->route('login'); // tự động chuyển đến trang đăng nhập
});

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login'])->name('login.post');

Route::get('/change-password', [AuthController::class, 'showChangePassword'])->name('password.change');
Route::post('/change-password', [AuthController::class, 'changePassword'])->name('password.update');

Route::get('/home', function () {
    return view('home');
})->middleware('auth')->name('dashboard');

Route::post('/logout', [AuthController::class, 'logout'])->middleware('auth')->name('logout');
