<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\DB;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Web\Client\ProfileController;
use App\Http\Controllers\Web\Client\EventController;
use App\Http\Controllers\Web\Client\ResultController;
use App\Http\Controllers\Web\Client\NewsController;
use App\Http\Controllers\Web\Client\ContestRegistrationController;
use App\Http\Controllers\Web\Client\CheerRegistrationController;
use App\Http\Controllers\Web\Client\SupportController;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'webLogin')->name('login.post');
    
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'webRegister')->name('register.post');

    Route::middleware('jwt.web')->group(function () {
        Route::get('/change-password', 'showChangePassword')->name('password.change.view');
        Route::post('/change-password', 'changePassword')->name('password.change');
        Route::post('/logout', 'logout')->name('logout');
    });
});

// Password Reset Routes
Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');
Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])
    ->name('password.email');
Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');
Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])
    ->name('password.update');

/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/
Route::view('/', 'client.home')->name('client.home');

/*
|--------------------------------------------------------------------------
| Event Routes (Cuộc thi)
|--------------------------------------------------------------------------
*/
Route::prefix('/hoi-thao')->name('client.events.')->group(function () {
    // Danh sách cuộc thi
    Route::get('/', [EventController::class, 'index'])->name('index');
    
    // Chi tiết cuộc thi (dùng slug)
    Route::get('/{slug}', [EventController::class, 'show'])->name('show');
    
    // Protected routes - Cần đăng nhập
    Route::middleware('jwt.web')->group(function () {
        // Đăng ký dự thi - FORM
        Route::get('/{slug}/dang-ky', [ContestRegistrationController::class, 'showRegistrationForm'])
            ->name('register');
        
        // Đăng ký dự thi - SUBMIT
        Route::post('/{slug}/dang-ky', [ContestRegistrationController::class, 'register'])
            ->name('register.submit');
        
        // Đăng ký cổ vũ - FORM (dùng slug)
        Route::get('/{slug}/co-vu', [CheerRegistrationController::class, 'showCheerForm'])
            ->name('cheer');
        
        // Đăng ký cổ vũ - SUBMIT (dùng slug)
        Route::post('/{slug}/co-vu', [CheerRegistrationController::class, 'registerCheer'])
            ->name('cheer.submit');
        
        // Đăng ký hỗ trợ Ban tổ chức - FORM
        Route::get('/{slug}/ho-tro', [SupportController::class, 'showSupportForm'])
            ->name('support');
        
        // Đăng ký hỗ trợ Ban tổ chức - SUBMIT
        Route::post('/{slug}/ho-tro', [SupportController::class, 'registerSupport'])
            ->name('support.submit');

    });
    
    // API kiểm tra mã sinh viên (không cần middleware vì chỉ check data)
    Route::post('/check-student-code', [ContestRegistrationController::class, 'checkStudentCode'])
        ->name('check.student');
});

/*
|--------------------------------------------------------------------------
| Result Routes (Kết quả)
|--------------------------------------------------------------------------
*/
Route::prefix('/ket-qua')->name('client.results.')->controller(ResultController::class)->group(function () {
    // Danh sách kết quả
    Route::get('/', 'index')->name('index');
    
    // Chi tiết kết quả
    Route::get('/{id}', 'show')->name('show');
    
    // Xuất PDF (protected)
    Route::middleware('jwt.web')->group(function () {
        Route::get('/{id}/export-pdf', 'exportPDF')->name('export');
    });
});

/*
|--------------------------------------------------------------------------
| News Routes (Tin tức)
|--------------------------------------------------------------------------
*/
Route::prefix('/tin-tuc')->name('client.news.')->controller(NewsController::class)->group(function () {
    // Danh sách tin tức
    Route::get('/', 'index')->name('index');
    
    // Chi tiết tin tức
    Route::get('/{slug}', 'show')->name('show');
});

Route::view('/lien-he', 'client.contact')->name('client.contact');

/*
|--------------------------------------------------------------------------
| User Profile Routes (JWT Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.web')->group(function () {
    Route::prefix('ho-so')->name('profile.')->controller(ProfileController::class)->group(function () {
        // Xem profile
        Route::get('/', 'index')->name('index');
        
        // Cập nhật avatar
        Route::post('/avatar', 'updateAvatar')->name('avatar.update');
        
        // Cập nhật thông tin
        Route::put('/update', 'updateInfo')->name('update');
        
        // Xuất điểm rèn luyện PDF
        Route::get('/diem-ren-luyen/export', 'exportDiemRenLuyenPDF')->name('diem.export');
        
        // **MỚI: Đăng ký cổ vũ**
        Route::get('/dang-ky-co-vu', 'myCheerRegistrations')->name('cheer.list');
        
        Route::delete('/activity/cancel/{madangkyhoatdong}', [ProfileController::class, 'cancelActivityRegistration'])
            ->name('activity.cancel'); 
    });
});

/*
|--------------------------------------------------------------------------
| Test Route
|--------------------------------------------------------------------------
*/
Route::get('/test-jwt', function () {
    return [
        'jwt_check' => jwt_check(),
        'jwt_user' => jwt_user(),
        'jwt_guest' => jwt_guest(),
    ];
});