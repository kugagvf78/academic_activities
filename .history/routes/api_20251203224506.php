<?php

use App\Http\Controllers\Api\ContestRegistrationApiController;
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


Route::prefix('events')->group(function () {

    Route::get('/', [EventApiController::class, 'index']);           // Danh sách cuộc thi
    Route::get('/categories', [EventApiController::class, 'categories']);
    Route::get('/statistics', [EventApiController::class, 'statistics']);
    Route::get('/{macuocthi}', [EventApiController::class, 'show']); // Chi tiết cuộc thi

    Route::middleware('auth:api')->group(function () {

        Route::post('/register', [EventApiController::class, 'register']);

        Route::post('/support', [EventApiController::class, 'support']);

        Route::post('/cheer', [EventApiController::class, 'cheer']);
    });
});
Route::get('/results', [ResultApiController::class, 'index']);
Route::get('/results/{id}', [ResultApiController::class, 'show']);
Route::get('/results/{id}/pdf', [ResultApiController::class, 'exportPDF']);

Route::get('/news', [NewsApiController::class, 'index']);
Route::get('/news/{slug}', [NewsApiController::class, 'show']);


Route::middleware('auth:api')->prefix('profile')->group(function () {

    Route::get('/', [ProfileApiController::class, 'index']);

    Route::post('/avatar', [ProfileApiController::class, 'updateAvatar']);

    Route::put('/info', [ProfileApiController::class, 'updateInfo']);

    Route::delete('/activities/{madangkyhoatdong}', [ProfileApiController::class, 'cancelActivityRegistration']);

    Route::delete('/competitions/{id}', [ProfileApiController::class, 'cancelCompetitionRegistration']);

    Route::get('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'showSubmitExam']);

    Route::post('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'submitExam']);
});

Route::middleware('auth:api')->prefix('events')->group(function () {
    // Hiển thị form đăng ký cuộc thi
    Route::get('{slug}/register', [ContestRegistrationApiController::class, 'showRegistrationForm']);
    
    // Xử lý đăng ký cuộc thi
    Route::post('{slug}/register', [ContestRegistrationApiController::class, 'register']);
    
    // Kiểm tra mã sinh viên
    Route::post('check-student-code', [ContestRegistrationApiController::class, 'checkStudentCode']);

    Route::get('{slug}/cheer', [CheerRegistrationApiController::class, 'getCheerActivities']);

    // Đăng ký cổ vũ
    Route::post('{slug}/cheer', [CheerRegistrationApiController::class, 'registerCheer']);
});