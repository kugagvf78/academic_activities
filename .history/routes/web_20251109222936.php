<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (ai cũng xem được)
|--------------------------------------------------------------------------
*/
Route::view('/', 'client.home')->name('client.home');        // Trang chủ
Route::view('/hoi-thao', 'client.events')->name('client.events'); // Danh sách hội thảo
Route::view('/lien-he', 'client.contact')->name('client.contact'); // Liên hệ
Route::view('/tin-tuc', 'client.news')->name('client.news');       // Tin tức

/*
|--------------------------------------------------------------------------
| Auth Routes (đăng nhập / đổi mật khẩu / đăng xuất)
|--------------------------------------------------------------------------
*/
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'login')->name('login.post');

    Route::middleware('auth')->group(function () {
        Route::get('/change-password', 'showChangePassword')->name('password.change');
        Route::post('/change-password', 'changePassword')->name('password.update');
        Route::post('/logout', 'logout')->name('logout');
    });
});

/*
|--------------------------------------------------------------------------
| User Routes (yêu cầu đăng nhập)
|--------------------------------------------------------------------------
|
| Chỉ khi người dùng đã đăng nhập mới được phép đăng ký tham gia hội thảo,
| xem danh sách hội thảo đã tham dự, hoặc chỉnh sửa thông tin cá nhân.
|
*/
Route::middleware('auth')->group(function () {
    Route::get('/dang-ky-hoi-thao/{id}', function ($id) {
        // Tạm thời hiển thị trang đăng ký hội thảo (sẽ làm controller sau)
        return "Trang đăng ký hội thảo ID: {$id}";
    })->name('user.register.event');

    Route::view('/hoi-thao-cua-toi', 'user.my-events')->name('user.myEvents');
});

