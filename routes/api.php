<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Admin\AdminUserController;

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
    Route::get('/', 'index')->name('index');
    Route::get('/statistics', 'statistics')->name('statistics');
    Route::get('/categories', 'categories')->name('categories');
    Route::get('/{macuocthi}', 'show')->name('show');
});

/*
|--------------------------------------------------------------------------
| Admin User Management API Routes (For Mobile App)
| URL: /api/admin/users/*
| Route names: api.admin.users.*  ← ✅ ĐỔI PREFIX
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:api', 'admin'])
    ->name('api.admin.')  // ← ✅ THÊM 'api.' vào đầu
    ->group(function () {
        
        // Quản lý người dùng
        Route::prefix('users')->name('users.')->controller(AdminUserController::class)->group(function () {
            Route::get('/', 'index')->name('index');  // api.admin.users.index
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/lops', 'getLops')->name('lops');
            Route::get('/bomons', 'getBoMons')->name('bomons');

            Route::post('/generate-code', 'generateCode')->name('generate-code');
            
            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/reset-password', 'resetPassword')->name('reset-password');
            Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::post('/import', 'import')->name('import');
            Route::get('/export/excel', 'exportExcel')->name('export.excel');
            Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
            Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/bulk-activate', 'bulkActivate')->name('bulk-activate');
        });
    });