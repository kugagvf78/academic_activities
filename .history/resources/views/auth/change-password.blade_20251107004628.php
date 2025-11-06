<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đổi mật khẩu</title>
</head>
<body>
    <h2>Đổi mật khẩu</h2>

    @if(session('success'))
        <div style="color:green;">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div style="color:red;">{{ implode(', ', $errors->all()) }}</div>
    @endif

    <form method="POST" action="/doi-mat-khau">
        @csrf
        <label>Mật khẩu cũ:</label>
        <input type="password" name="MatKhauCu" required><br>
        <label>Mật khẩu mới:</label>
        <input type="password" name="MatKhauMoi" required><br>
        <label>Nhập lại mật khẩu mới:</label>
        <input type="password" name="MatKhauMoi_confirmation" required><br>
        <button type="submit">Đổi mật khẩu</button>
    </form>
</body>
</html>
