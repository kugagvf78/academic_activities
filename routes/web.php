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

        // Quản lý cuộc thi
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

            Route::get('/{id}/download-file', 'downloadFile')->name('download-file');
            Route::get('/{id}/bai-thi/{baithiId}/download', 'downloadBaiThi')->name('download-baithi');
            Route::post('/{id}/download-multiple', 'downloadMultipleBaiThi')->name('download-multiple');
            
            // API lấy vòng thi
            Route::get('/api/vongthi/{macuocthi}', 'getVongThi')->name('api.vongthi');
        });

        // Chấm điểm
        Route::prefix('cham-diem')->name('chamdiem.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienChamDiemController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/cuoc-thi/{macuocthi}', 'showCuocThi')->name('show-cuocthi');
            Route::get('/cuoc-thi/{macuocthi}/xep-hang', 'showRankings')->name('rankings');
            Route::get('/api/cuoc-thi/{macuocthi}/rankings', 'getRankings')->name('api.rankings');
            Route::get('/cuoc-thi/{macuocthi}/export-template', 'exportTemplate')->name('export-template');
            Route::post('/cuoc-thi/{macuocthi}/import', 'importDiem')->name('import');
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            Route::put('/{id}', 'update')->name('update');
        });

        Route::prefix('phan-cong')
        ->name('phancong.')
        ->controller(\App\Http\Controllers\Web\GiangVien\GiangVienPhanCongController::class)
        ->group(function () {
            // Tất cả giảng viên
            Route::get('/', 'index')->name('index');
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            Route::get('/export', 'export')->name('export');
            Route::get('/api/statistics', 'statistics')->name('api.statistics');
            
            // API endpoints
            Route::get('/api/ban/{macuocthi}', 'getBanByCuocThi')->name('api.ban');
            Route::get('/api/congviec/{macuocthi}', 'getCongViecByCuocThi')->name('api.congviec');
            Route::get('/api/ban-detail/{maban}', 'getBanDetail')->name('api.ban-detail');
            
            // Chỉ trưởng bộ môn
            Route::get('/tao-moi', 'create')->name('create');
            Route::post('/tao-moi', 'store')->name('store');
            Route::get('/{id}/sua', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            
            // Quản lý ban
            Route::get('/quan-ly-ban', 'quanLyBan')->name('quan-ly-ban');
            Route::get('/ban/{maban}', 'chiTietBan')->name('chi-tiet-ban');
            Route::post('/phan-cong-nhieu', 'phanCongNhieuGiangVien')->name('phan-cong-nhieu');


            // CRUD Ban
            Route::get('/ban/create/{macuocthi}', 'createBan')->name('ban.create');
            Route::post('/ban/store', 'storeBan')->name('ban.store');
            Route::get('/ban/edit/{maban}', 'editBan')->name('ban.edit');
            Route::put('/ban/update/{maban}', 'updateBan')->name('ban.update');
            Route::delete('/ban/destroy/{maban}', 'destroyBan')->name('ban.destroy');
        });

        // Quản lý Kế hoạch Cuộc thi
        Route::prefix('ke-hoach')->name('kehoach.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienKeHoachController::class)->group(function () {
            Route::get('/', 'index')->name('index');
            Route::get('/tao-moi', 'create')->name('create');
            Route::post('/tao-moi', 'store')->name('store');
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            Route::get('/{id}/sua', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            Route::delete('/{id}', 'destroy')->name('destroy');
            Route::post('/{id}/gui-lai', 'resubmit')->name('resubmit');
            Route::get('/{id}/export', 'export')->name('export');
            Route::get('/api/statistics', 'statistics')->name('statistics');

            // THÊM MỚI: Routes duyệt/từ chối kế hoạch (chỉ trưởng bộ môn)
            Route::post('/{id}/duyet', 'approve')->name('approve');
            Route::post('/{id}/tu-choi', 'reject')->name('reject');
        });

        // ✨ THÊM MỚI: Quản lý Hoạt động Hỗ trợ
        Route::prefix('hoat-dong')->name('hoatdong.')->controller(\App\Http\Controllers\Web\GiangVien\GiangVienHoatDongController::class)->group(function () {
            // Danh sách hoạt động
            Route::get('/', 'index')->name('index');
            
            // Tạo hoạt động mới
            Route::get('/tao-moi', 'create')->name('create');
            Route::post('/tao-moi', 'store')->name('store');
            
            // Chi tiết hoạt động
            Route::get('/{id}/chi-tiet', 'show')->name('show');
            
            // Chỉnh sửa hoạt động
            Route::get('/{id}/sua', 'edit')->name('edit');
            Route::put('/{id}', 'update')->name('update');
            
            // Xóa hoạt động
            Route::delete('/{id}', 'destroy')->name('destroy');
            
            // Tạo mã QR điểm danh
            Route::get('/{id}/tao-qr', 'generateQR')->name('generate-qr');
            
            // Export danh sách điểm danh
            Route::get('/{id}/export', 'exportAttendance')->name('export-attendance');

            Route::post('/{id}/import-google-form', 'importFromGoogleForm')->name('import-google-form');
        });
    });

/*
|--------------------------------------------------------------------------
| ⚠️ QUAN TRỌNG: Routes CÔNG KHAI cho Điểm danh QR (KHÔNG middleware)
| Đặt NGOÀI group middleware để sinh viên quét QR có thể truy cập
|--------------------------------------------------------------------------
*/
Route::prefix('hoat-dong')->name('hoatdong.')->group(function () {
    // Form điểm danh (public - sinh viên quét QR)
    Route::get('/{id}/diem-danh', [\App\Http\Controllers\Web\GiangVien\GiangVienHoatDongController::class, 'scanQR'])
        ->name('scan-qr');
    
    // Xử lý điểm danh (public)
    Route::post('/{id}/diem-danh', [\App\Http\Controllers\Web\GiangVien\GiangVienHoatDongController::class, 'checkIn'])
        ->name('check-in');
});






// Admin Routes
Route::prefix('admin')->middleware(['jwt.web', 'admin'])->group(function () {
    Route::get('/dashboard', [App\Http\Controllers\Admin\AdminController::class, 'dashboard'])
        ->name('admin.dashboard');
    
    // Thêm các route admin khác ở đây
    // Route::get('/users', ...)->name('admin.users');
    // Route::get('/departments', ...)->name('admin.departments');
    // ...
});