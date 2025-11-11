<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use Illuminate\Pagination\LengthAwarePaginator;

/*
|--------------------------------------------------------------------------
| Auth Routes
|--------------------------------------------------------------------------
*/
// Thêm vào phần Auth Routes trong web.php
Route::controller(AuthController::class)->group(function () {
    Route::get('/login', 'showLogin')->name('login');
    Route::post('/login', 'webLogin')->name('login.post');
    
    // Routes đăng ký
    Route::get('/register', 'showRegister')->name('register');
    Route::post('/register', 'webRegister')->name('register.post');

    // Middleware jwt.web
    Route::middleware('jwt.web')->group(function () {
        Route::get('/change-password', 'showChangePassword')->name('password.change');
        Route::post('/change-password', 'changePassword')->name('password.update');
        Route::post('/logout', 'logout')->name('logout');
    });
});
/*
|--------------------------------------------------------------------------
| Public Routes
|--------------------------------------------------------------------------
*/

Route::view('/', 'client.home')->name('client.home');

Route::prefix('/hoi-thao')->name('client.events.')->group(function () {
    Route::get('/', function () {
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

        $perPage = 6;
        $page = request()->get('page', 1);
        $paged = $items->forPage($page, $perPage);
        $events = new LengthAwarePaginator($paged, $items->count(), $perPage, $page, [
            'path' => request()->url(),
            'query' => request()->query(),
        ]);

        return view('client.events.index', compact('events'));
    })->name('index');

    Route::get('/{slug}', function ($slug) {
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

    // Dùng middleware jwt.web
    Route::middleware('jwt.web')->group(function () {
        Route::get('/{slug}/dang-ky', function ($slug) {
            return view('client.events.register', compact('slug'));
        })->name('register');

        Route::get('/{slug}/co-vu', function ($slug) {
            return view('client.events.cheer', compact('slug'));
        })->name('cheer');

        Route::get('/{slug}/ho-tro', function ($slug) {
            return view('client.events.support', compact('slug'));
        })->name('support');

        Route::get('/phan-bo-co-vu', function () {
            return view('client.events.assign');
        })->name('assign');
    });
});

Route::prefix('/ket-qua')->name('client.results.')->group(function () {
    Route::get('/', function () {
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
                ['name' => 'Nguyễn Văn A', 'rank' => 'Giải Nhất', 'score' => 98, 'prize' => '1.000.000đ + Giấy khen'],
                ['name' => 'Trần Thị B', 'rank' => 'Giải Nhì', 'score' => 93, 'prize' => '700.000đ + Giấy khen'],
                ['name' => 'Team SQL Pro', 'rank' => 'Giải Ba', 'score' => 88, 'prize' => '500.000đ + Giấy khen'],
            ]
        ];
        return view('client.results.show', compact('result'));
    })->name('show');
});

Route::view('/tin-tuc', 'client.news.index')->name('client.news.index');
Route::view('/lien-he', 'client.contact')->name('client.contact');

/*
|--------------------------------------------------------------------------
| User Routes (JWT Auth)
|--------------------------------------------------------------------------
*/
Route::middleware('jwt.web')->group(function () {
    // Route::view('/hoi-thao-cua-toi', 'user.my-events')->name('user.myEvents');
    Route::view('/ho-so', 'client.profile')->name('client.profile');
});

/*
|--------------------------------------------------------------------------
| Test Route (có thể xóa sau khi test xong)
|--------------------------------------------------------------------------
*/
Route::get('/test-jwt', function () {
    return [
        'jwt_check' => jwt_check(),
        'jwt_user' => jwt_user(),
        'jwt_guest' => jwt_guest(),
    ];
});

Route::get('/quen-mat-khau', [AuthController::class, 'showForgotPassword'])
    ->name('password.request');

Route::post('/quen-mat-khau', [AuthController::class, 'sendResetLink'])
    ->name('password.email');

Route::get('/dat-lai-mat-khau/{token}', [AuthController::class, 'showResetPassword'])
    ->name('password.reset');

Route::post('/dat-lai-mat-khau', [AuthController::class, 'resetPassword'])
    ->name('password.update');