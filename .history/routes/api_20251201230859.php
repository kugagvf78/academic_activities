<?php

use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\ResultApiController;
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


Route::get('/events', [EventApiController::class, 'index']);
Route::get('/events/{macuocthi}', [EventApiController::class, 'show']);
Route::get('/events/categories', [EventApiController::class, 'categories']);

Route::get('/results', [ResultApiController::class, 'index']);
Route::get('/results/{id}', [ResultApiController::class, 'show']);
Route::get('/results/{id}/pdf', [ResultApiController::class, 'exportPDF']);

Route::get('/news', [NewsApiController::class, 'index']);
Route::get('/news/{slug}', [NewsApiController::class, 'show']);


Route::middleware('auth:api')->group(function () {
    Route::get('/profile', [ProfileApiController::class, 'getProfile']);

    Route::post('/profile/update', [ProfileController::class, 'updateInfo']);
    Route::post('/profile/avatar', [ProfileController::class, 'updateAvatar']);

    Route::post('/profile/cancel-activity/{id}', [ProfileController::class, 'cancelActivity']);
    Route::post('/profile/cancel-competition/{id}', [ProfileController::class, 'cancelCompetition']);

    Route::post('/profile/submit-exam/{id}/{loaidangky}', [ProfileController::class, 'submitExam']);
});