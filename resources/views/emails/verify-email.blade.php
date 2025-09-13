<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Email - {{ config('landing.site.name', 'Rama Perfume') }}</title>
</head>
<body style="margin: 0; padding: 0; background-color: #f9fafb; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;">
    <div style="max-width: 600px; margin: 40px auto; background-color: white; border-radius: 12px; box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); overflow: hidden;">
        
        <!-- Header -->
        <div style="background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); padding: 40px 30px; text-align: center;">
            <h1 style="color: white; margin: 0; font-size: 28px; font-weight: 700;">
                ✅ Verifikasi Email Anda
            </h1>
            <p style="color: #e0e7ff; margin: 10px 0 0 0; font-size: 16px;">
                {{ config('landing.site.name', 'Rama Perfume') }}
            </p>
        </div>

        <!-- Content -->
        <div style="padding: 40px 30px;">
            <p style="margin: 0 0 20px 0; font-size: 18px; color: #374151; line-height: 1.6;">
                Halo <strong>{{ $user->name }}</strong>! 👋
            </p>

            <p style="margin: 0 0 25px 0; font-size: 16px; color: #6b7280; line-height: 1.7;">
                Terima kasih telah mendaftar di <strong>{{ config('landing.site.name', 'Rama Perfume') }}</strong>! 
                Untuk melengkapi proses registrasi, silakan verifikasi email Anda dengan mengklik tombol di bawah ini:
            </p>

            <!-- Verification Button -->
            <div style="text-align: center; margin: 35px 0;">
                <a href="{{ $verificationUrl }}" 
                   style="display: inline-block; background: linear-gradient(135deg, #10b981 0%, #059669 100%); 
                          color: white; padding: 16px 32px; text-decoration: none; border-radius: 8px; 
                          font-weight: 600; font-size: 16px; box-shadow: 0 4px 12px rgba(16, 185, 129, 0.3);
                          transition: all 0.3s ease;">
                    🚀 Verifikasi Email Saya
                </a>
            </div>

            <div style="background-color: #f3f4f6; padding: 20px; border-radius: 8px; margin: 30px 0;">
                <p style="margin: 0 0 15px 0; font-size: 14px; color: #6b7280; font-weight: 600;">
                    💡 Link tidak bisa diklik? Copy dan paste URL berikut ke browser Anda:
                </p>
                <p style="margin: 0; font-size: 12px; color: #9ca3af; word-break: break-all; background: white; padding: 10px; border-radius: 4px; border: 1px solid #e5e7eb;">
                    {{ $verificationUrl }}
                </p>
            </div>

            <hr style="border: none; border-top: 1px solid #e5e7eb; margin: 30px 0;">

            <div style="background-color: #fef3c7; padding: 15px; border-radius: 6px; border-left: 4px solid #f59e0b;">
                <p style="margin: 0; font-size: 14px; color: #92400e;">
                    <strong>⚠️ Penting:</strong> Jika Anda tidak mendaftar di {{ config('landing.site.name', 'Rama Perfume') }}, 
                    abaikan email ini. Link verifikasi akan expire dalam 24 jam.
                </p>
            </div>

            <div style="margin-top: 30px; padding: 20px; background-color: #f9fafb; border-radius: 8px;">
                <h3 style="margin: 0 0 15px 0; font-size: 16px; color: #374151;">🎯 Setelah Verifikasi:</h3>
                <ul style="margin: 0; padding-left: 20px; color: #6b7280; font-size: 14px; line-height: 1.8;">
                    <li>✅ Akun Anda akan aktif sepenuhnya</li>
                    <li>🛒 Bisa berbelanja dan checkout</li>
                    <li>📧 Menerima notifikasi order dan promo</li>
                    <li>🎁 Akses ke member benefits</li>
                </ul>
            </div>
        </div>

        <!-- Footer -->
        <div style="background-color: #f9fafb; padding: 30px; text-align: center; border-top: 1px solid #e5e7eb;">
            <p style="margin: 0 0 10px 0; color: #6b7280; font-size: 14px;">
                Salam hangat,<br>
                <strong>Tim {{ config('landing.site.name', 'Rama Perfume') }}</strong> 💜
            </p>
            
            <div style="margin: 20px 0; font-size: 12px; color: #9ca3af;">
                <p style="margin: 5px 0;">📧 Email: {{ config('contact.email', 'info@ramaperfume.com') }}</p>
                <p style="margin: 5px 0;">📱 WhatsApp: {{ config('contact.whatsapp', '+62 819 3140 0047') }}</p>
                <p style="margin: 5px 0;">🌐 Website: {{ config('app.url', 'https://ramaperfume.com') }}</p>
            </div>

            <p style="margin: 15px 0 0 0; font-size: 11px; color: #9ca3af;">
                Email otomatis - Harap tidak membalas email ini.<br>
                © {{ date('Y') }} {{ config('landing.site.name', 'Rama Perfume') }}. All rights reserved.
            </p>
        </div>
    </div>
</body>
</html>
