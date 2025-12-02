import 'nguoidung.dart';
import 'sinhvien.dart';
import 'giangvien.dart';

class AuthResponse {
  final String accessToken;
  final NguoiDung user;
  final dynamic detail; // SinhVien hoặc GiangVien

  AuthResponse({
    required this.accessToken,
    required this.user,
    this.detail,
  });

  factory AuthResponse.fromJson(Map<String, dynamic> json) {
    return AuthResponse(
      accessToken: json["access_token"],
      user: NguoiDung.fromJson(json["user"]),
      detail: json["detail"] == null
          ? null
          : (json["user"]["vaitro"] == "SinhVien"
              ? SinhVien.fromJson(json["detail"])
              : GiangVien.fromJson(json["detail"])),
    );
  }
}
