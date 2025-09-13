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

            <div style="background: #fef3c7; padding: 15px; border-radius: 6px; margin: 20px 0;">
                <p style="margin: 0; font-size: 14px; color: #92400e;">
                    <strong>🔑 Instruksi Reset Password:</strong><br>
                    1. Hubungi admin melalui WhatsApp/Email<br>
                    2. Berikan informasi akun Anda (nama & email)<br>
                    3. Admin akan membantu reset password Anda
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

            <div style="text-align: center; margin: 30px 0;">
                <div style="background: #e5e7eb; padding: 20px; border-radius: 8px;">
                    <h3 style="color: #1f2937; margin-bottom: 15px;">📞 Hubungi Admin</h3>
                    
                    <a href="https://wa.me/{{ str_replace(['+', '-', ' '], '', config('landing.contact.whatsapp', '6281234567890')) }}?text=Halo,%20saya%20ingin%20reset%20password%20akun%20{{ $user->email }}" 
                       style="display: inline-block; background: #10b981; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 5px;">
                        💬 WhatsApp Admin
                    </a>
                    
                    <a href="mailto:{{ config('landing.contact.email', 'admin@ramaperfume.com') }}?subject=Reset Password Request&body=Halo, saya ingin reset password untuk akun: {{ $user->email }}" 
                       style="display: inline-block; background: #3b82f6; color: white; padding: 12px 20px; text-decoration: none; border-radius: 6px; font-weight: 600; margin: 5px;">
                        📧 Email Admin
                    </a>
                </div>
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
