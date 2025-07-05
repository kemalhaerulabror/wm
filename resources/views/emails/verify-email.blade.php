<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email</title>
    <style>
        /* Base */
        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
            color: #333333;
            line-height: 1.6;
            background-color: #f8f8f8;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .email-wrapper {
            background-color: #ffffff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            margin-bottom: 20px;
        }
        .email-header {
            background-color: #1f2937; /* bg-gray-800 */
            color: #ffffff;
            padding: 20px;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
            text-align: center;
        }
        .logo-container {
            display: flex;
            align-items: center;
            justify-content: center;
            margin-bottom: 10px;
        }
        .logo-icon {
            font-size: 2rem;
            margin-right: 10px;
        }
        .logo-text {
            font-size: 1.5rem;
            font-weight: bold;
        }
        .email-body {
            padding: 30px;
            color: #333333;
        }
        .greeting {
            font-size: 1.2rem;
            font-weight: bold;
            margin-bottom: 20px;
        }
        .content {
            margin-bottom: 30px;
        }
        .button {
            display: inline-block;
            background-color: #1f2937; /* bg-gray-800 */
            color: #ffffff !important;
            text-decoration: none;
            padding: 12px 24px;
            border-radius: 5px;
            font-weight: bold;
            margin: 15px 0;
            text-align: center;
        }
        .button:hover {
            background-color: #374151; /* bg-gray-700 */
        }
        .email-footer {
            padding: 15px;
            text-align: center;
            font-size: 0.8rem;
            color: #6b7280; /* text-gray-500 */
            border-top: 1px solid #e5e7eb; /* border-gray-200 */
        }
        .note {
            font-size: 0.9rem;
            color: #6b7280; /* text-gray-500 */
            margin-top: 20px;
        }
        .motorcycle-icon {
            width: 40px;
            height: 40px;
            background-color: #ffffff;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin-right: 10px;
            font-size: 24px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="email-wrapper">
            <div class="email-header">
                <div class="logo-container">
                    <span class="logo-text">Wipa Motor</span>
                </div>
            </div>
            
            <div class="email-body">
                <div class="greeting">Halo {{ $user->name }},</div>
                
                <div class="content">
                    <p>Terima kasih telah mendaftar di Wipa Motor.</p>
                    
                    <p>Silakan klik tombol di bawah untuk memverifikasi alamat email Anda:</p>
                    
                    <div style="text-align: center;">
                        <a href="{{ $verificationUrl }}" class="button">Verifikasi Email</a>
                    </div>
                    
                    <p>Jika Anda tidak dapat mengklik tombol di atas, salin dan tempel URL di bawah ini ke browser web Anda:</p>
                    
                    <p style="word-break: break-all;">{{ $verificationUrl }}</p>
                </div>
                
                <div class="note">
                    <p>Jika Anda tidak membuat akun ini, abaikan email ini.</p>
                </div>
            </div>
            
            <div class="email-footer">
                <p>&copy; {{ date('Y') }} Wipa Motor. Semua hak dilindungi.</p>
            </div>
        </div>
    </div>
</body>
</html> 