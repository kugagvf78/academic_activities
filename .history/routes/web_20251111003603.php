<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Pagination\LengthAwarePaginator;
use Illuminate\Support\Collection;

/*
|--------------------------------------------------------------------------
| Public Routes (ai cũng xem được)
|--------------------------------------------------------------------------
*/

// 🏠 Trang chủ
Route::view('/', 'client.home')->name('client.home');

// 🎓 Danh sách & Chi tiết hội thảo / cuộc thi
Route::prefix('/hoi-thao')->name('client.events.')->group(function () {

    // 👉 Trang danh sách cuộc thi
    Route::get('/', function () {
        // Fake dữ liệu 18 cuộc thi
        $items = collect(range(1, 18))->map(function ($i) {
            return (object) [
                'id' => $i,
                'slug' => "database-design-challenge-$i",
                'title' => "Cuộc thi học thuật CNTT #$i",
                'description' => "Khám phá công nghệ mới và rèn luyện kỹ năng sáng tạo thông qua cuộc thi số $i.",
                'image' => "https://source.unsplash.com/600x400/?database,technology,$i",
                'status' => ['Đang mở', 'Sắp diễn ra', 'Đã kết thúc'][array_rand([0, 1, 2])],
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

        return view('client.events.index', compact('events'));
    })->name('index');

    // 👉 Trang chi tiết cuộc thi
    Route::get('/{slug}', function ($slug) {
        // Giả lập dữ liệu 1 cuộc thi (sẽ thay bằng DB sau)
        $event = (object) [
            'title' => 'Database Design Challenge 2025',
            'slug' => $slug,
            'description' => 'Cuộc thi học thuật về thiết kế cơ sở dữ liệu dành cho sinh viên Khoa CNTT HUIT.',
            'image' => 'https://source.unsplash.com/1200x600/?database,design,challenge',
            'date' => '07/12/2025',
            'time' => '7h45 - 16h30',
            'location' => 'Khu A & B - Đại học Công Thương TP.HCM',
            'status' => 'Đang mở đăng ký',
        ];

        return view('client.events.show', compact('event'));
    })->name('show');
});

Route::prefix('/ket-qua')->name('client.results.')->group(function () {
    // Danh sách kết quả
    Route::get('/', function () {
        // Fake dữ liệu
        $results = collect(range(1, 9))->map(fn($i) => (object) [
            'id' => $i,
            'title' => "Database Design Challenge #$i",
            'date' => '07/12/2025',
            'participants' => rand(50, 200),
            'winner' => 'Nguyễn Văn A',
            'image' => "https://source.unsplash.com/600x400/?trophy,competition,$i"
        ]);
        return view('client.results.index', compact('results'));
    })->name('index');

    // Chi tiết kết quả
    Route::get('/{id}', function ($id) {
        $result = (object) [
            'id' => $id,
            'title' => "Database Design Challenge 2025",
            'date' => '07/12/2025',
            'rounds' => [
                ['name' => 'Vòng Sơ khảo', 'winner' => 'Nguyễn Văn A'],
                ['name' => 'Vòng Chung kết', 'winner' => 'Team SQL Pro'],
            ],
            'top3' => [
                [
                    'name' => 'Nguyễn Văn A',
                    'rank' => 'Giải Nhất',
                    'score' => 98,
                    'prize' => '1.000.000đ + Giấy khen'
                ],
                [
                    'name' => 'Trần Thị B',
                    'rank' => 'Giải Nhì',
                    'score' => 93,
                    'prize' => '700.000đ + Giấy khen'
                ],
                [
                    'name' => 'Team SQL Pro',
                    'rank' => 'Giải Ba',
                    'score' => 88,
                    'prize' => '500.000đ + Giấy khen'
                ],
            ]
        ];
        return view('client.results.show', compact('result'));
    })->name('show');

    Route::get('/dang-ky-hoi-thao/{id}', function ($id) {
    return view('client.events.register', [
        'id' => $id,
        'event' => 'Database Design Challenge 2025'
    ]);
})->name('client.events.register');
});


// 📰 Tin tức
Route::view('/tin-tuc', 'client.news')->name('client.news');

// 📞 Liên hệ
Route::view('/lien-he', 'client.contact')->name('client.contact');


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
*/
Route::middleware('auth')->group(function () {
    Route::get('/dang-ky-hoi-thao/{id}', function ($id) {
        // Tạm thời hiển thị trang đăng ký hội thảo (sẽ làm controller sau)
        return "Trang đăng ký hội thảo ID: {$id}";
    })->name('user.register.event');

    Route::view('/hoi-thao-cua-toi', 'user.my-events')->name('user.myEvents');
});
