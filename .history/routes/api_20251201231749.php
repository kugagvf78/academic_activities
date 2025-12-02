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


Route::middleware('auth:api')->prefix('profile')->group(function () {
    
    // Lấy toàn bộ thông tin hồ sơ (user + profile + activities + certificates + điểm rèn luyện + đăng ký)
    Route::get('/', [ProfileApiController::class, 'index']);
    
    // Cập nhật ảnh đại diện
    Route::post('/avatar', [ProfileApiController::class, 'updateAvatar']);
    
    // Cập nhật thông tin cá nhân
    Route::put('/info', [ProfileApiController::class, 'updateInfo']);
    
    Route::delete('/activities/{madangkyhoatdong}', [ProfileApiController::class, 'cancelActivityRegistration']);
    
    Route::delete('/competitions/{id}', [ProfileApiController::class, 'cancelCompetitionRegistration']);
    
    Route::get('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'showSubmitExam']);

    Route::post('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'submitExam']);
});