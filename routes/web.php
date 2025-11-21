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
use App\Http\Controllers\Web\GiangVien\GiangVienCuocThiController;
use App\Http\Controllers\Web\GiangVien\GiangVienDeThiController;

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
        
        // Hủy đăng ký hoạt động (cổ vũ/hỗ trợ)
        Route::delete('/activity/cancel/{madangkyhoatdong}', 'cancelActivityRegistration')
            ->name('activity.cancel');
        
        // ✅ FIXED: Hủy đăng ký dự thi
        Route::delete('/competition/cancel/{id}', 'cancelCompetitionRegistration')
            ->name('competition.cancel');
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


/*
|--------------------------------------------------------------------------
| SINH VIÊN Profile Routes
|--------------------------------------------------------------------------
*/
Route::middleware(['jwt.web', 'role:SinhVien'])
    ->prefix('ho-so')
    ->name('profile.')
    ->controller(ProfileController::class)
    ->group(function () {
        Route::get('/', 'index')->name('index');
        Route::post('/avatar', 'updateAvatar')->name('avatar.update');
        Route::put('/update', 'updateInfo')->name('update');
        Route::get('/diem-ren-luyen/export', 'exportDiemRenLuyenPDF')->name('diem.export');
        Route::delete('/activity/cancel/{madangkyhoatdong}', 'cancelActivityRegistration')->name('activity.cancel');
        Route::delete('/competition/cancel/{id}', 'cancelCompetitionRegistration')->name('competition.cancel');
    
        Route::get('/competition/{id}/{loaidangky}/submit', 'showSubmitExam')->name('competition.submit.form');
        Route::post('/competition/{id}/{loaidangky}/submit', 'submitExam')->name('competition.submit');
    });

/*
| GIẢNG VIÊN Routes (đã có middleware jwt.web + role:GiangVien)
*/
Route::middleware(['jwt.web', 'role:GiangVien'])
    ->prefix('giang-vien')
    ->name('giangvien.')
    ->group(function () {

        // Hồ sơ giảng viên
        Route::prefix('ho-so')->name('profile.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienProfileController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::put('/update', 'updateInfo')->name('update');
            Route::post('/avatar', 'updateAvatar')->name('avatar.update');
            Route::get('/ke-hoach', 'danhSachKeHoach')->name('kehoach.index');
            Route::get('/de-thi', 'danhSachDeThi')->name('dethi.index');
            Route::get('/cham-diem', 'danhSachBaiCanCham')->name('chamdiem.index');
            Route::get('/phan-cong', 'danhSachPhanCong')->name('phancong.index');
            Route::get('/chi-phi', 'danhSachChiPhi')->name('chiphi.index');
            Route::get('/quyet-toan', 'danhSachQuyetToan')->name('quyettoan.index');
            Route::get('/tin-tuc', 'danhSachTinTuc')->name('tintuc.index');
        });

        // Quản lý cuộc thi - GỘP VÀO ĐÂY
        Route::prefix('cuoc-thi')->name('cuocthi.')->controller(GiangVienCuocThiController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tao-moi', 'create')->name('create');
            Route::post('/tao-moi', 'store')->name('store');
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            Route::get('/{id}/sua', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
        });

        // Quản lý đề thi
        Route::prefix('de-thi')->name('dethi.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienDeThiController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tao-moi', 'create')->name('create');
            Route::post('/tao-moi', 'store')->name('store');
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            Route::get('/{id}/sua', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');

            // Route::get('/{id}/view-file', 'viewFile')->name('view-file');
            Route::get('/{id}/download-file', 'downloadFile')->name('download-file');

            // THÊM 2 ROUTES MỚI
            Route::get('/{id}/bai-thi/{baithiId}/download', 'downloadBaiThi')->name('download-baithi');
            Route::post('/{id}/download-multiple', 'downloadMultipleBaiThi')->name('download-multiple');
            
            // API lấy vòng thi
            Route::get('/api/vongthi/{macuocthi}', 'getVongThi')->name('api.vongthi');
        });


        Route::prefix('cham-diem')->name('chamdiem.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienChamDiemController::class)->group(function () {
            // Danh sách bài cần chấm
            Route::get('/', 'index')->name('index');
            
            // Xem chi tiết để chấm điểm
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            
            // Cập nhật điểm
            Route::put('/{id}', 'update')->name('update');
            
            // Xóa điểm (để chấm lại)
            Route::delete('/{id}', 'destroy')->name('destroy');
            
            // Download bài làm
            Route::get('/{id}/download-bai-lam', 'downloadBaiLam')->name('download-bailam');
            
            // API lấy danh sách cuộc thi (cho filter)
            Route::get('/api/cuocthi', 'getCuocThi')->name('api.cuocthi');
            
            // Chấm hàng loạt (optional)
            Route::post('/bulk-update', 'bulkUpdate')->name('bulk-update');
        });

        Route::prefix('phan-cong')
            ->name('phancong.')
            ->controller(\App\Http\Controllers\Web\GiangVien\GiangVienPhanCongController::class)
            ->group(function () {
                // Danh sách phân công
                Route::get('/', 'index')->name('index');
                
                // Chi tiết phân công
                Route::get('/{id}/chi-tiet', 'show')->name('show');
                
                // API thống kê
                Route::get('/api/statistics', 'statistics')->name('api.statistics');
                
                // Export Excel
                Route::get('/export', 'export')->name('export');
            });

    });