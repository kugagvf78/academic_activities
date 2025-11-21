<div class="card shadow-sm">
    <div class="card-header bg-primary text-white">
        <h5 class="mb-0"><i class="fas fa-bars"></i> Menu Giảng Viên</h5>
    </div>
    <div class="list-group list-group-flush">
        <a href="{{ route('giangvien.profile.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.index') ? 'active' : '' }}">
            <i class="fas fa-home me-2"></i> Trang Chủ
        </a>
        <a href="{{ route('giangvien.profile.kehoach.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.kehoach.*') ? 'active' : '' }}">
            <i class="fas fa-calendar-alt me-2"></i> Kế Hoạch Cuộc Thi
        </a>
        <a href="{{ route('giangvien.profile.dethi.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.dethi.*') ? 'active' : '' }}">
            <i class="fas fa-file-alt me-2"></i> Đề Thi
        </a>
        <a href="{{ route('giangvien.profile.chamdiem.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.chamdiem.*') ? 'active' : '' }}">
            <i class="fas fa-edit me-2"></i> Chấm Điểm
            @if(isset($stats['tong_bai_can_cham']) && $stats['tong_bai_can_cham'] > 0)
                <span class="badge bg-danger float-end">{{ $stats['tong_bai_can_cham'] }}</span>
            @endif
        </a>
        <a href="{{ route('giangvien.profile.phancong.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.phancong.*') ? 'active' : '' }}">
            <i class="fas fa-tasks me-2"></i> Phân Công
        </a>
        <a href="{{ route('giangvien.profile.chiphi.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.chiphi.*') ? 'active' : '' }}">
            <i class="fas fa-dollar-sign me-2"></i> Chi Phí
        </a>
        <a href="{{ route('giangvien.profile.quyettoan.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.quyettoan.*') ? 'active' : '' }}">
            <i class="fas fa-file-invoice-dollar me-2"></i> Quyết Toán
        </a>
        <a href="{{ route('giangvien.profile.tintuc.index') }}" 
           class="list-group-item list-group-item-action {{ request()->routeIs('giangvien.profile.tintuc.*') ? 'active' : '' }}">
            <i class="fas fa-newspaper me-2"></i> Tin Tức
        </a>
        <div class="list-group-item bg-light">
            <small class="text-muted">Tài khoản</small>
        </div>
        <a href="{{ route('password.change.view') }}" class="list-group-item list-group-item-action">
            <i class="fas fa-key me-2"></i> Đổi Mật Khẩu
        </a>
        <form action="{{ route('logout') }}" method="POST" class="m-0">
            @csrf
            <button type="submit" class="list-group-item list-group-item-action text-danger w-100 text-start border-0">
                <i class="fas fa-sign-out-alt me-2"></i> Đăng Xuất
            </button>
        </form>
    </div>
</div>