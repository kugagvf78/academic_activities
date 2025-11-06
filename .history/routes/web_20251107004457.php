<?php

use App\Http\Controllers\AuthController;

Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
Route::post('/login', [AuthController::class, 'login']);
Route::post('/logout', [AuthController::class, 'logout'])->name('logout');

Route::get('/doi-mat-khau', [AuthController::class, 'showChangePassword'])->middleware('auth');
Route::post('/doi-mat-khau', [AuthController::class, 'changePassword'])->middleware('auth');

Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware('auth')->name('dashboard');
