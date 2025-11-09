<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

/*
|--------------------------------------------------------------------------
| Public Routes (ai cũng xem được)
|--------------------------------------------------------------------------
*/
Route::view('/', 'client.home')->name('client.home');        // Trang chủ
Route::get('/hoi-thao', function () {
    // Fake dữ liệu 18 cuộc thi
    $items = collect(range(1, 18))->map(function ($i) {
        return (object) [
            'id' => $i,
            'title' => "Cuộc thi học thuật CNTT #$i",
            'description' => "Khám phá công nghệ mới và rèn luyện kỹ năng sáng tạo thông qua cuộc thi số $i.",
            'image' => "https://source.unsplash.com/600x400/?coding,innovation,$i",
            'status' => ['Đang mở', 'Sắp diễn ra', 'Đã kết thúc'][array_rand([0,1,2])],
        ];
    });

    // Tạo paginator thủ công
    $perPage = 6;
    $page = request()->get('page', 1);
    $paged = $items->forPage($page, $perPage);
    $events = new LengthAwarePaginator($paged, $items->count(), $perPage, $page, [
        'path' => request()->url(),
        'query' => request()->query(),
    ]);

    return view('client.events', compact('events'));
})->name('client.events');
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

