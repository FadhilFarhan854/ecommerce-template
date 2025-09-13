# 📧 Panduan Setup Email untuk Forgot Password & Email Verification

## 🎯 Pilihan Email Provider

### **1. Mailtrap (Recommended untuk Development)**

**Kelebihan:**
- ✅ Gratis untuk testing
- ✅ Email tidak benar-benar terkirim (safe untuk testing)
- ✅ Interface web untuk melihat email
- ✅ Easy setup

**Langkah Setup:**

1. **Daftar di Mailtrap:**
   ```
   https://mailtrap.io
   ```

2. **Buat Inbox Baru:**
   - Login → Add Inbox
   - Pilih "Email Testing"
   - Nama: "Rama Perfume Dev"

3. **Copy Credentials:**
   - Klik inbox → SMTP Settings
   - Pilih "Laravel 9+"
   - Copy username & password

4. **Update .env:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=sandbox.smtp.mailtrap.io
   MAIL_PORT=2525
   MAIL_USERNAME=your_mailtrap_username_here
   MAIL_PASSWORD=your_mailtrap_password_here
   MAIL_FROM_ADDRESS="noreply@ramaperfume.com"
   MAIL_FROM_NAME="Rama Perfume"
   ```

---

### **2. Gmail SMTP (Untuk Production/Real Emails)**

**Kelebihan:**
- ✅ Email benar-benar terkirim
- ✅ Reliable
- ✅ Gratis (dengan batasan)

**Langkah Setup:**

1. **Enable 2-Factor Authentication di Gmail**

2. **Generate App Password:**
   - Google Account → Security
   - 2-Step Verification → App passwords
   - Generate password untuk "Mail"

3. **Update .env:**
   ```env
   MAIL_MAILER=smtp
   MAIL_HOST=smtp.gmail.com
   MAIL_PORT=587
   MAIL_USERNAME=your_gmail@gmail.com
   MAIL_PASSWORD=your_app_password_here
   MAIL_ENCRYPTION=tls
   MAIL_FROM_ADDRESS="noreply@ramaperfume.com"
   MAIL_FROM_NAME="Rama Perfume"
   ```

---

### **3. Log Driver (Untuk Testing Lokal)**

**Kelebihan:**
- ✅ Tidak perlu setup eksternal
- ✅ Email tersimpan di log file
- ✅ Cepat untuk testing

**Setup:**
```env
MAIL_MAILER=log
MAIL_FROM_ADDRESS="noreply@ramaperfume.com"
MAIL_FROM_NAME="Rama Perfume"
```

Email akan tersimpan di: `storage/logs/laravel.log`

---

## 🚀 Langkah Setelah Setup Email

### **1. Clear Cache:**
```bash
php artisan config:clear
php artisan cache:clear
```

### **2. Test Email:**
Buka: `http://127.0.0.1:8000/test-auth-features.html`

### **3. Test Forgot Password:**
1. Buka `/forgot-password`
2. Masukkan email yang sudah terdaftar
3. Cek email di Mailtrap/Gmail/Log

### **4. Test Email Verification:**
1. Register user baru di `/register`
2. Cek email verifikasi
3. Klik link verifikasi

---

## 🔧 Troubleshooting

### **Error: "Connection refused"**
- ✅ Periksa MAIL_HOST dan MAIL_PORT
- ✅ Pastikan credentials benar
- ✅ Coba `php artisan config:clear`

### **Error: "Authentication failed"**
- ✅ Username/password salah
- ✅ Untuk Gmail: pastikan app password, bukan password biasa

### **Email tidak muncul di Mailtrap**
- ✅ Periksa inbox yang benar
- ✅ Refresh halaman Mailtrap
- ✅ Periksa log Laravel: `storage/logs/laravel.log`

### **Template email rusak**
- ✅ Periksa file di `resources/views/emails/`
- ✅ Pastikan syntax Blade benar

---

## 📝 Next Steps

Setelah email working:

1. **Test semua fitur** dengan email provider pilihan
2. **Customize email templates** sesuai brand
3. **Setup email untuk production** (jika diperlukan)
4. **Add email queueing** untuk performa better
5. **Setup email notifications** untuk order confirmations

---

## 💡 Tips

- **Development:** Gunakan Mailtrap atau Log driver
- **Staging:** Gunakan real email provider tapi dengan prefix [STAGING]
- **Production:** Gunakan professional email service (SendGrid, AWS SES, etc.)

## 🔗 Links Berguna

- **Mailtrap:** https://mailtrap.io
- **Laravel Mail Docs:** https://laravel.com/docs/10.x/mail
- **Email Testing:** http://127.0.0.1:8000/test-auth-features.html
