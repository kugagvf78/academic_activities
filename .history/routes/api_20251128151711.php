<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EventApiController;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/
Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

/*
|--------------------------------------------------------------------------
| Event API Routes (Public - Không cần authentication)
|--------------------------------------------------------------------------
*/
Route::prefix('events')->name('api.events.')->controller(EventApiController::class)->group(function () {
    // Lấy danh sách cuộc thi (có filter, search, pagination)
    Route::get('/', 'index')->name('index');
    
    // Lấy thống kê tổng quan
    Route::get('/statistics', 'statistics')->name('statistics');
    
    // Lấy danh sách loại cuộc thi
    Route::get('/categories', 'categories')->name('categories');
    
    // Lấy chi tiết cuộc thi theo mã
    Route::get('/{macuocthi}', 'show')->name('show');
});