<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Test Production Email</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 30px; border-radius: 10px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #10b981; margin-bottom: 10px;">✅ Email Production Test</h1>
            <p style="color: #6b7280; font-size: 16px;">{{ config('landing.site.name', 'Rama Perfume') }}</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #1f2937; margin-bottom: 20px;">🎉 Email System Working!</h2>
            
            <p style="margin-bottom: 20px;">
                Selamat! Email production system Anda berhasil berfungsi dengan baik.
            </p>

            <div style="background: #f0f9ff; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px; color: #0369a1;">
                    <strong>📧 Test Details:</strong><br>
                    To: {{ $email }}<br>
                    Time: {{ $timestamp }}<br>
                    Provider: {{ config('mail.default') }}<br>
                    Host: {{ config('mail.mailers.smtp.host', 'Log Driver') }}
                </p>
            </div>

            <div style="background: #f0fdf4; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <h3 style="color: #15803d; margin-bottom: 10px;">✅ What's Working:</h3>
                <ul style="color: #166534; margin: 0; padding-left: 20px;">
                    <li>SMTP Connection ✅</li>
                    <li>Email Templates ✅</li>
                    <li>Authentication System ✅</li>
                    <li>Production Ready ✅</li>
                </ul>
            </div>

            <p style="margin-bottom: 20px;">
                Sistem email Anda siap untuk:
            </p>
            <ul style="color: #6b7280;">
                <li>📧 Email Verification saat registrasi</li>
                <li>🔐 Forgot Password instructions</li>
                <li>📬 Order confirmations</li>
                <li>🎁 Marketing campaigns</li>
            </ul>
        </div>

        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>Email dikirim melalui:<br><strong>{{ config('app.name') }} Email System</strong></p>
            <p style="margin-top: 20px; font-size: 12px;">
                Test email - Safe to delete
            </p>
        </div>
    </div>
</body>
</html>
