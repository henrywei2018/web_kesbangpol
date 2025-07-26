<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Kode Verifikasi - {{ $appName }}</title>
    <style>
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
            background-color: #f8f9fa;
        }
        .container {
            background: white;
            padding: 30px;
            border-radius: 10px;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 30px;
        }
        .logo {
            font-size: 24px;
            font-weight: bold;
            color: #2563eb;
        }
        .otp-code {
            background: #2563eb;
            color: white;
            font-size: 32px;
            font-weight: bold;
            text-align: center;
            padding: 20px;
            border-radius: 8px;
            letter-spacing: 5px;
            margin: 20px 0;
        }
        .info-box {
            background: #f0f9ff;
            border-left: 4px solid #2563eb;
            padding: 15px;
            margin: 20px 0;
        }
        .warning-box {
            background: #fef3c7;
            border-left: 4px solid #f59e0b;
            padding: 15px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #6b7280;
            font-size: 14px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <div class="logo">{{ $appName }}</div>
            <h1>{{ 
                $type === 'registration' ? 'Verifikasi Registrasi' : 
                ($type === 'password_reset' ? 'Reset Password' : 'Verifikasi Email') 
            }}</h1>
        </div>

        <p>Halo,</p>

        <p>
            @if($type === 'registration')
                Terima kasih telah mendaftar di {{ $appName }}. Untuk menyelesaikan proses registrasi, silakan gunakan kode verifikasi berikut:
            @elseif($type === 'password_reset')
                Anda telah meminta reset password. Gunakan kode berikut untuk melanjutkan:
            @else
                Silakan gunakan kode verifikasi berikut:
            @endif
        </p>

        <div class="otp-code">
            {{ $otp }}
        </div>

        <div class="info-box">
            <strong>Informasi Penting:</strong>
            <ul>
                <li>Kode ini akan kedaluarsa dalam <strong>{{ $expiryMinutes }} menit</strong></li>
                <li>Jangan bagikan kode ini kepada siapa pun</li>
                <li>Kode hanya dapat digunakan sekali</li>
            </ul>
        </div>

        @if($type === 'registration')
        <div class="warning-box">
            <strong>Keamanan:</strong> Jika Anda tidak melakukan registrasi, abaikan email ini. Akun tidak akan dibuat tanpa verifikasi kode.
        </div>
        @endif

        <p>
            Jika Anda mengalami masalah atau tidak meminta kode ini, silakan hubungi tim support kami.
        </p>

        <div class="footer">
            <p>Email ini dikirim secara otomatis. Mohon tidak membalas email ini.</p>
            <p>&copy; {{ date('Y') }} {{ $appName }}. All rights reserved.</p>
        </div>
    </div>
</body>
</html>