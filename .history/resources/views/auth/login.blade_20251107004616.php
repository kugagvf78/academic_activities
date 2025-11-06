<!DOCTYPE html>
<html lang="vi">
<head>
    <meta charset="UTF-8">
    <title>Đăng nhập</title>
</head>
<body>
    <h2>Đăng nhập</h2>
    @if ($errors->any())
        <div style="color:red;">{{ $errors->first('login') }}</div>
    @endif

    <form method="POST" action="/login">
        @csrf
        <label>Tên đăng nhập:</label>
        <input type="text" name="TenDangNhap" required><br>
        <label>Mật khẩu:</label>
        <input type="password" name="MatKhau" required><br>
        <button type="submit">Đăng nhập</button>
    </form>
</body>
</html>
