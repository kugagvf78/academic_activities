<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, 'Helvetica Neue', Arial, sans-serif;
            line-height: 1.6;
            color: #333333;
            margin: 0;
            padding: 0;
            background-color: #f4f7fa;
        }
        .email-wrapper {
            max-width: 600px;
            margin: 40px auto;
            background-color: #ffffff;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.07);
        }
        .header {
            background: linear-gradient(135deg, #1e40af 0%, #3b82f6 100%);
            padding: 40px 30px;
            text-align: center;
        }
        .header h1 {
            margin: 0;
            color: #ffffff;
            font-size: 28px;
            font-weight: 600;
            letter-spacing: -0.5px;
        }
        .header p {
            margin: 8px 0 0 0;
            color: rgba(255, 255, 255, 0.9);
            font-size: 14px;
        }
        .content {
            padding: 40px 30px;
        }
        .greeting {
            font-size: 18px;
            font-weight: 600;
            color: #1f2937;
            margin: 0 0 20px 0;
        }
        .message {
            color: #4b5563;
            font-size: 15px;
            line-height: 1.6;
            margin: 0 0 30px 0;
        }
        .otp-container {
            background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
            border: 2px solid #3b82f6;
            border-radius: 12px;
            padding: 30px;
            text-align: center;
            margin: 30px 0;
        }
        .otp-label {
            font-size: 13px;
            color: #6b7280;
            text-transform: uppercase;
            letter-spacing: 1px;
            font-weight: 600;
            margin-bottom: 15px;
        }
        .otp-code {
            font-size: 42px;
            font-weight: 700;
            letter-spacing: 12px;
            color: #1e40af;
            font-family: 'Courier New', monospace;
            margin: 0;
            user-select: all;
        }
        .otp-expire {
            font-size: 13px;
            color: #6b7280;
            margin-top: 15px;
        }
        .warning-box {
            background-color: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 20px;
            margin: 30px 0;
            border-radius: 6px;
        }
        .warning-box .warning-title {
            font-weight: 600;
            color: #92400e;
            font-size: 14px;
            margin: 0 0 12px 0;
            display: flex;
            align-items: center;
            gap: 8px;
        }
        .warning-box ul {
            margin: 0;
            padding-left: 20px;
            color: #78350f;
            font-size: 14px;
        }
        .warning-box li {
            margin: 8px 0;
        }
        .security-notice {
            background-color: #f3f4f6;
            border-radius: 8px;
            padding: 20px;
            margin: 30px 0;
            text-align: center;
        }
        .security-notice p {
            margin: 0;
            color: #6b7280;
            font-size: 13px;
            line-height: 1.5;
        }
        .footer {
            background-color: #f9fafb;
            padding: 30px;
            text-align: center;
            border-top: 1px solid #e5e7eb;
        }
        .footer-text {
            color: #6b7280;
            font-size: 13px;
            line-height: 1.6;
            margin: 0;
        }
        .signature {
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #e5e7eb;
        }
        .signature p {
            margin: 5px 0;
            color: #4b5563;
            font-size: 14px;
        }
        .signature strong {
            color: #1f2937;
        }
        @media only screen and (max-width: 600px) {
            .email-wrapper {
                margin: 20px;
                border-radius: 8px;
            }
            .header {
                padding: 30px 20px;
            }
            .header h1 {
                font-size: 24px;
            }
            .content {
                padding: 30px 20px;
            }
            .otp-code {
                font-size: 36px;
                letter-spacing: 8px;
            }
            .footer {
                padding: 20px;
            }
        }
    </style>
</head>
<body>
    <div class="email-wrapper">
        <!-- Header -->
        <div class="header">
            <h1>🔐 Xác Thực Tài Khoản</h1>
            <p>Mã xác thực OTP của bạn</p>
        </div>

        <!-- Content -->
        <div class="content">
            <p class="greeting">Xin chào{{ $userName ? ' ' . $userName : '' }}!</p>
            
            <p class="message">
                Chúng tôi nhận được yêu cầu đặt lại mật khẩu cho tài khoản của bạn. 
                Để tiếp tục, vui lòng sử dụng mã OTP bên dưới:
            </p>

            <!-- OTP Box -->
            <div class="otp-container">
                <div class="otp-label">Mã OTP của bạn</div>
                <div class="otp-code">{{ $otp }}</div>
                <div class="otp-expire">⏱️ Có hiệu lực trong <strong>5 phút</strong></div>
            </div>

            <!-- Warning -->
            <div class="warning-box">
                <div class="warning-title">
                    <span>⚠️</span>
                    <span>Lưu ý quan trọng</span>
                </div>
                <ul>
                    <li>Mã OTP này chỉ có hiệu lực trong <strong>5 phút</strong></li>
                    <li>Không chia sẻ mã này với bất kỳ ai, kể cả nhân viên hỗ trợ</li>
                    <li>Nếu bạn không thực hiện yêu cầu này, vui lòng bỏ qua email</li>
                </ul>
            </div>

            <!-- Security Notice -->
            <div class="security-notice">
                <p>
                    🛡️ Để bảo vệ tài khoản của bạn, chúng tôi không bao giờ yêu cầu mật khẩu 
                    hoặc thông tin cá nhân qua email.
                </p>
            </div>

            <!-- Signature -->
            <div class="signature">
                <p>Trân trọng,</p>
                <p><strong>Đội ngũ Hỗ trợ Khách hàng</strong></p>
            </div>
        </div>

        <!-- Footer -->
        <div class="footer">
            <p class="footer-text">
                Email này được gửi tự động, vui lòng không trả lời.<br>
                Nếu bạn cần hỗ trợ, vui lòng liên hệ bộ phận chăm sóc khách hàng.
            </p>
        </div>
    </div>
</body>
</html>