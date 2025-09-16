<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - {{ config('landing.site.name', 'Rama Perfume') }}</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6; color: #333; max-width: 600px; margin: 0 auto; padding: 20px;">
    <div style="background: #f8f9fa; padding: 30px; border-radius: 10px;">
        <div style="text-align: center; margin-bottom: 30px;">
            <h1 style="color: #1f2937; margin-bottom: 10px;">{{ config('landing.site.name', 'Rama Perfume') }}</h1>
            <p style="color: #6b7280; font-size: 16px;">🔐 Reset Password Akun Anda</p>
        </div>

        <div style="background: white; padding: 30px; border-radius: 8px; margin-bottom: 20px;">
            <h2 style="color: #1f2937; margin-bottom: 20px;">Halo {{ $user->name }}!</h2>
            
            <p style="margin-bottom: 20px;">
                Anda menerima email ini karena ada permintaan reset password untuk akun Anda.
            </p>

            <div style="text-align: center; margin: 30px 0;">
                <a href="{{ $resetUrl }}" 
                   style="display: inline-block; background: #3b82f6; color: white; padding: 15px 30px; text-decoration: none; border-radius: 8px; font-weight: 600; font-size: 16px;">
                    🔐 Reset Password Sekarang
                </a>
            </div>

            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px; color: #92400e;">
                    <strong>⏰ Penting:</strong> Link ini hanya berlaku selama 1 jam. Jika sudah expired, silakan lakukan request reset password baru.
                </p>
            </div>

            <div style="background: #f3f4f6; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px; color: #6b7280;">
                    <strong>📧 Info Akun:</strong><br>
                    Nama: {{ $user->name }}<br>
                    Email: {{ $user->email }}<br>
                    Waktu Permintaan: {{ now()->format('d M Y H:i') }} WIB
                </p>
            </div>

            <div style="background: #f9fafb; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 12px; color: #6b7280;">
                    <strong>� Tidak bisa klik tombol?</strong><br>
                    Salin dan paste link berikut ke browser Anda:<br>
                    <code style="background: #e5e7eb; padding: 2px 4px; border-radius: 3px; font-family: monospace;">{{ $resetUrl }}</code>
                </p>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

            <p style="margin-bottom: 20px; font-size: 14px; color: #6b7280;">
                <strong>⚠️ Keamanan:</strong> Jika Anda tidak meminta reset password, silakan abaikan email ini dan segera hubungi admin jika ada aktivitas mencurigakan.
            </p>
        </div>

        <div style="text-align: center; color: #6b7280; font-size: 14px;">
            <p>Salam,<br><strong>Tim {{ config('landing.site.name', 'Rama Perfume') }}</strong></p>
            <p style="margin-top: 20px; font-size: 12px;">
                Email otomatis - Harap tidak membalas email ini.<br>
                Untuk bantuan, hubungi: {{ config('landing.contact.email', 'admin@ramaperfume.com') }}
            </p>
        </div>
    </div>
</body>
</html>
