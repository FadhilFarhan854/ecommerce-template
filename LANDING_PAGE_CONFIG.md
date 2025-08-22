# Landing Page Configuration Guide

Template ini menggunakan static controller untuk mengatur konten landing page secara dinamis. Ini memudahkan Anda untuk mengubah konten tanpa perlu mengedit langsung file view.

## Struktur Controller

### LandingPageController
File: `app/Http/Controllers/LandingPageController.php`

Controller ini menangani halaman utama (landing page) dan mengatur semua konten yang ditampilkan.

## File Konfigurasi

### config/landing.php
File ini berisi semua konfigurasi konten untuk landing page:

- **Site Information**: Nama situs, deskripsi, tagline, logo
- **Hero Section**: Slider dengan slides yang dapat dikustomisasi
- **About Section**: Informasi tentang perusahaan
- **Contact Information**: Alamat, telepon, email, WhatsApp
- **Navigation Menu**: Menu navigasi utama
- **Footer Links**: Link-link di footer
- **Sample Products**: Produk contoh jika database kosong

## Environment Variables

Beberapa konfigurasi dapat diatur melalui file `.env`:

```bash
# Site Configuration
SITE_NAME="TokoKu Store"
SITE_DESCRIPTION="Platform e-commerce terpercaya dengan produk berkualitas dan pelayanan terbaik di Indonesia."
SITE_TAGLINE="Belanja Smart, Hidup Berkualitas"
SITE_LOGO="/images/logo.png"
SITE_FAVICON="/favicon.ico"

# Contact Information
CONTACT_ADDRESS_STREET="Jl. Contoh No. 123"
CONTACT_ADDRESS_CITY="Jakarta Pusat"
CONTACT_ADDRESS_POSTAL="10110"
CONTACT_PHONE="+62 21 1234 5678"
CONTACT_EMAIL="info@tokoku.com"
CONTACT_WHATSAPP="+62 812 3456 7890"
```

## Cara Menggunakan

### 1. Mengubah Informasi Situs
Edit file `config/landing.php` atau file `.env`:

```php
// Di config/landing.php
'site' => [
    'name' => env('SITE_NAME', 'Nama Toko Anda'),
    'description' => env('SITE_DESCRIPTION', 'Deskripsi toko Anda'),
    // ...
],

// Atau di .env
SITE_NAME="Nama Toko Anda"
SITE_DESCRIPTION="Deskripsi toko Anda"
```

### 2. Mengubah Hero Slider
Edit bagian `hero.slides` di `config/landing.php`:

```php
'hero' => [
    'slides' => [
        [
            'title' => 'Judul Slide 1',
            'subtitle' => 'Subtitle slide 1',
            'button_text' => 'Tombol',
            'button_link' => '#link',
            'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
        ],
        // Tambahkan slide lainnya...
    ],
],
```

### 3. Mengubah Informasi Kontak
Edit bagian `contact` di `config/landing.php` atau file `.env`:

```php
// Di config/landing.php
'contact' => [
    'address' => [
        'street' => 'Alamat Jalan',
        'city' => 'Nama Kota',
        'postal_code' => 'Kode Pos',
    ],
    'phone' => '+62 xxx xxxx xxxx',
    'email' => 'email@domain.com',
    // ...
],
```

### 4. Mengubah Menu Navigasi
Edit bagian `navigation.main_menu` di `config/landing.php`:

```php
'navigation' => [
    'main_menu' => [
        ['text' => 'Beranda', 'url' => '/', 'anchor' => '#home'],
        ['text' => 'Tentang', 'url' => '#about', 'anchor' => '#about'],
        ['text' => 'Produk', 'url' => '/products', 'anchor' => '#products'],
        ['text' => 'Kontak', 'url' => '#contact', 'anchor' => '#contact'],
    ],
],
```

### 5. Mengubah Footer Links
Edit bagian `footer.links` di `config/landing.php`:

```php
'footer' => [
    'links' => [
        'services' => [
            'title' => 'Layanan',
            'items' => [
                ['text' => 'Bantuan', 'url' => '/help'],
                ['text' => 'FAQ', 'url' => '/faq'],
                // ...
            ],
        ],
        // ...
    ],
],
```

### 6. Mengubah Produk Sample
Edit bagian `sample_products` di `config/landing.php`:

```php
'sample_products' => [
    [
        'name' => 'Nama Produk',
        'description' => 'Deskripsi produk...',
        'price' => 1000000,
        'image' => '/images/product.jpg', // opsional
    ],
    // ...
],
```

## Tips Penggunaan

1. **Cache Configuration**: Setelah mengubah konfigurasi, jalankan `php artisan config:cache` pada production.

2. **Environment Variables**: Gunakan environment variables untuk pengaturan yang sering berubah atau berbeda antar environment.

3. **Produk Database**: Controller akan otomatis menggunakan produk dari database jika tersedia, dan fallback ke sample products jika tidak ada.

4. **Layout Template**: Semua konten menggunakan layout `layouts.app` yang juga dinamis berdasarkan konfigurasi.

## Struktur File

```
├── app/Http/Controllers/
│   └── LandingPageController.php
├── config/
│   └── landing.php
├── resources/views/
│   ├── layouts/
│   │   └── app.blade.php
│   └── welcome.blade.php
└── routes/
    └── web.php
```

## Keuntungan Pendekatan Ini

1. **Maintainability**: Mudah mengubah konten tanpa edit view
2. **Environment Specific**: Konten bisa berbeda per environment
3. **Separation of Concerns**: Logic terpisah dari presentation
4. **Reusable**: Konfigurasi bisa digunakan di controller/view lain
5. **Dynamic**: Otomatis menggunakan data database jika tersedia

Dengan pendekatan ini, Anda dapat dengan mudah mengkustomisasi seluruh konten landing page hanya dengan mengubah file konfigurasi dan environment variables.
