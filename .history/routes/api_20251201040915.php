<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Client\EventController;

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});


Route::get('/cuocthi', [EventController::class, 'apiIndex']);
Route::prefix('events')->group(function () {
    Route::get('/', [EventApiController::class, 'index']);          // GET /api/events
    Route::get('/categories', [EventApiController::class, 'categories']);
    Route::get('/statistics', [EventApiController::class, 'statistics']);
    Route::get('/{macuocthi}', [EventApiController::class, 'show']); // GET /api/events/123
});