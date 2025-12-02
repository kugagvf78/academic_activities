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
    
    // Hủy đăng ký hoạt động hỗ trợ (Cổ vũ, Tổ chức, Hỗ trợ kỹ thuật)
    Route::delete('/activities/{madangkyhoatdong}', [ProfileApiController::class, 'cancelActivityRegistration']);
    
    // Hủy đăng ký dự thi (cá nhân hoặc đội nhóm)
    Route::delete('/competitions/{id}', [ProfileApiController::class, 'cancelCompetitionRegistration']);
    
    // Lấy form nộp bài thi (kiểm tra điều kiện)
    Route::get('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'showSubmitExam']);
    
    // Xử lý nộp bài thi
    Route::po