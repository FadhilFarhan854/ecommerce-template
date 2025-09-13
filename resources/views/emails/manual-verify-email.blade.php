<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ config('landing.site.name', 'Rama Perfume') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 30px; border-radius: 10px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1f2937; margin-bottom: 10px;">{{ config('landing.site.name', 'Rama Perfume') }}</h1>
            <p style="color: #6b7280; font-size: 16px;">Verifikasi Alamat Email Anda</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #1f2937; margin-bottom: 20px;">Halo {{ $user->name }}!</h2>
            
            <p style="margin-bottom: 20px;">
                Terima kasih telah mendaftar di {{ config('landing.site.name', 'Rama Perfume') }}! 
            </p>

            <p style="margin-bottom: 20px;">
                Untuk mengaktifkan akun Anda, silakan klik tombol verifikasi di bawah ini:
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $verificationUrl }}" 
                   style="background: #10b981; color: white; padding: 15px 30px; text-decoration: none; border-radius: 6px; font-weight: 600; display: inline-block;">
                    ✅ Verifikasi Email Sekarang
                </a>
            </div>

            <div style="background: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px; color: #6b7280;">
                    <strong>Info Verifikasi:</strong><br>
                    Email: {{ $user->email }}<br>
                    Token: {{ substr($token, 0, 8) }}...
                </p>
            </div>

            <p style="margin-bottom: 20px; font-size: 14px; color: #6b7280;">
                Jika tombol tidak berfungsi, copy dan paste link berikut ke browser Anda:
            </p>
            <p style="font-size: 12px; color: #3b82f6; word-break: break-all; background: #f8f9fa; padding: 10px; border-radius: 4px;">
                {{ $verificationUrl }}
            </p>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

            <p style="margin-bottom: 20px;">
                Setelah verifikasi, Anda dapat:
            </p>
            <ul style="color: #6b7280;">
                <li>Mengakses dashboard pribadi</li>
                <li>Melakukan pemesanan</li>
                <li>Melihat riwayat transaksi</li>
                <li>Mendapat update promo terbaru</li>
            </ul>

            <p style="margin-bottom: 20px; font-size: 14px; color: #ef4444;">
                <strong>Penting:</strong> Jika Anda tidak mendaftar di website kami, silakan abaikan email ini.
            </p>
        </div>

        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>Selamat berbelanja,<br><strong>Tim {{ config('landing.site.name', 'Rama Perfume') }}</strong></p>
            <p style="margin-top: 20px; font-size: 12px;">
                Email otomatis - Harap tidak membalas email ini.<br>
                Ada pertanyaan? Hubungi kami di {{ config('landing.contact.email', 'info@ramaperfume.com') }}
            </p>
        </div>
    </div>
</body>
</html>
