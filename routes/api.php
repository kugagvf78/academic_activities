<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\Api\EventApiController;
use App\Http\Controllers\Api\NewsApiController;
use App\Http\Controllers\Admin\AdminUserController;
use App\Http\Controllers\Api\AdminEventController;
use App\Http\Controllers\Web\Client\ProfileController;
use App\Http\Controllers\Api\CheerRegistrationApiController;
use App\Http\Controllers\Api\ContestRegistrationApiController;
use App\Http\Controllers\Api\ProfileApiController;
use App\Http\Controllers\Api\ResultApiController;
use App\Http\Controllers\Api\SupportApiController;
use App\Http\Controllers\Web\Client\EventController;
use App\Http\Middleware\CorsMiddleware;

/*
|--------------------------------------------------------------------------
| Authentication Routes
|--------------------------------------------------------------------------
*/

Route::group(['prefix' => 'auth'], function () {
    Route::post('register', [AuthController::class, 'register']);
    Route::post('login', [AuthController::class, 'login']);
    Route::post('logout', [AuthController::class, 'logout'])->middleware('auth:api');
    Route::post('refresh', [AuthController::class, 'refresh'])->middleware('auth:api');
    Route::get('me', [AuthController::class, 'me'])->middleware('auth:api');
});

/*
|--------------------------------------------------------------------------
| Event API Routes (Public - Không cần authentication)
|--------------------------------------------------------------------------
*/
Route::prefix('events')->group(function () {

    Route::get('/', [EventApiController::class, 'index']);
    Route::get('/categories', [EventApiController::class, 'categories']);
    Route::get('/statistics', [EventApiController::class, 'statistics']);
    Route::get('/{macuocthi}', [EventApiController::class, 'show']);

    Route::middleware('auth:api')->group(function () {

        Route::post('/register', [EventApiController::class, 'register']);
        Route::post('/support', [EventApiController::class, 'support']);
        Route::post('/cheer', [EventApiController::class, 'cheer']);

        // Các route đăng ký theo slug
        Route::get('{slug}/register', [ContestRegistrationApiController::class, 'showRegistrationForm']);
        Route::post('{slug}/register', [ContestRegistrationApiController::class, 'register']);

        Route::get('{slug}/cheer', [CheerRegistrationApiController::class, 'getCheerActivities']);
        Route::post('{slug}/cheer', [CheerRegistrationApiController::class, 'registerCheer']);

        Route::post('check-student-code', [ContestRegistrationApiController::class, 'checkStudentCode']);

        Route::get('{slug}/support', [SupportApiController::class, 'getSupportActivities']);

        // Đăng ký hỗ trợ
        Route::post('support', [SupportApiController::class, 'registerSupport']);

        // Kiểm tra MSSV
        Route::post('support/check-student', [SupportApiController::class, 'checkStudent']);
    });
});

/*
|--------------------------------------------------------------------------
| News API Routes (Public - Không cần authentication)
| URL: /api/news/*
| Route names: api.news.*
|--------------------------------------------------------------------------
*/
Route::prefix('news')->name('api.news.')->controller(NewsApiController::class)->group(function () {
    Route::get('/', 'index')->name('index');           // GET /api/news
    Route::get('/{slug}', 'show')->name('show');       // GET /api/news/{slug}
});


Route::get('/results', [ResultApiController::class, 'index']);
Route::get('/results/{id}', [ResultApiController::class, 'show']);
Route::get('/results/{id}/pdf', [ResultApiController::class, 'exportPDF']);



Route::middleware('auth:api')->prefix('profile')->group(function () {

    Route::get('/', [ProfileApiController::class, 'index']);

    Route::post('/avatar', [ProfileApiController::class, 'updateAvatar']);

    Route::put('/info', [ProfileApiController::class, 'updateInfo']);

    Route::delete('/activities/{madangkyhoatdong}', [ProfileApiController::class, 'cancelActivityRegistration']);

    Route::delete('/competitions/{id}', [ProfileApiController::class, 'cancelCompetitionRegistration']);

    Route::get('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'showSubmitExam']);

    Route::post('/submit-exam/{id}/{loaidangky}', [ProfileApiController::class, 'submitExam']);
});


/*
|--------------------------------------------------------------------------
| Admin User Management API Routes (For Mobile App)
| URL: /api/admin/users/*
| Route names: api.admin.users.*
|--------------------------------------------------------------------------
*/
Route::prefix('admin')
    ->middleware(['auth:api', 'admin'])
    ->name('api.admin.')
    ->group(function () {

        // Quản lý người dùng
        Route::prefix('users')->name('users.')->controller(AdminUserController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/statistics', 'statistics')->name('statistics');
            Route::get('/lops', 'getLops')->name('lops');
            Route::get('/bomons', 'getBoMons')->name('bomons');

            Route::post('/generate-code', 'generateCode')->name('generate-code');

            Route::get('/{id}', 'show')->name('show');
            Route::post('/', 'store')->name('store');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/reset-password', 'resetPassword')->name('reset-password');
            Route::patch('/{id}/toggle-status', 'toggleStatus')->name('toggle-status');
            Route::post('/import', 'import')->name('import');
            Route::get('/export/excel', 'exportExcel')->name('export.excel');
            Route::get('/export/pdf', 'exportPdf')->name('export.pdf');
            Route::post('/bulk-delete', 'bulkDelete')->name('bulk-delete');
            Route::post('/bulk-activate', 'bulkActivate')->name('bulk-activate');
        });
    });

/*
|--------------------------------------------------------------------------
| Student Profile API Routes
| URL: /api/student/profile
|--------------------------------------------------------------------------
*/
Route::prefix('student')
    ->middleware(['auth:api'])
    ->name('api.student.')
    ->group(function () {
        Route::get('/profile', [ProfileController::class, 'index'])->name('profile');
        Route::post('/profile', [ProfileController::class, 'update'])->name('profile.update');

        Route::post('/avatar', [ProfileApiController::class, 'updateAvatar'])->name('avatar.update');
    });
