# 🛒 Dokumentasi Fitur Keranjang Belanja

## Daftar Isi
1. [Overview](#overview)
2. [Fitur yang Tersedia](#fitur-yang-tersedia)
3. [Perbaikan yang Dilakukan](#perbaikan-yang-dilakukan)
4. [Struktur File](#struktur-file)
5. [API Endpoints](#api-endpoints)
6. [Frontend Components](#frontend-components)
7. [Cara Penggunaan](#cara-penggunaan)
8. [Testing](#testing)
9. [Troubleshooting](#troubleshooting)

## Overview

Sistem keranjang belanja ini adalah implementasi lengkap e-commerce cart dengan fitur AJAX, real-time updates, dan user experience yang modern menggunakan Laravel dan Tailwind CSS.

### Teknologi yang Digunakan
- **Backend**: Laravel 11
- **Frontend**: Blade Templates + Vanilla JavaScript
- **Styling**: Tailwind CSS
- **Database**: SQLite/MySQL
- **Authentication**: Laravel Sanctum

## Fitur yang Tersedia

### ✅ Fitur Utama Cart
1. **Add to Cart** - Tambah produk ke keranjang
2. **Update Quantity** - Ubah jumlah produk
3. **Remove Item** - Hapus item tertentu
4. **Clear Cart** - Kosongkan seluruh keranjang
5. **Cart Count** - Counter real-time di header
6. **Stock Validation** - Validasi stok produk
7. **AJAX Operations** - Operasi tanpa reload halaman

### 🎨 UI/UX Features
1. **Modal Popup** - Detail produk dengan styling modern
2. **Toast Notifications** - Feedback sukses/error
3. **Loading States** - Indikator proses loading
4. **Responsive Design** - Tampilan optimal di semua device
5. **Real-time Updates** - Update counter dan total otomatis

### 🔒 Security Features
1. **CSRF Protection** - Perlindungan dari serangan CSRF
2. **Authentication Required** - Hanya user login yang bisa akses cart
3. **User Ownership** - User hanya bisa akses cart sendiri
4. **Input Validation** - Validasi data input

## Perbaikan yang Dilakukan

### 🐛 Bug Fixes
1. **Modal Popup Issues**
   - ❌ Modal tidak bisa ditutup dengan benar
   - ✅ Modal dengan event handlers yang proper
   - ✅ Close on outside click
   - ✅ Proper body scroll handling

2. **Network Errors saat Add to Cart**
   - ❌ CSRF token issues
   - ❌ JSON vs Form data conflicts
   - ✅ Proper FormData usage
   - ✅ Consistent header handling

3. **Method Not Allowed untuk Update Quantity**
   - ❌ PUT method tidak didukung browser
   - ✅ Method spoofing dengan FormData
   - ✅ Dual response handling (JSON/Redirect)

### 🚀 Improvements
1. **Code Structure**
   - Separated concerns (API vs Web routes)
   - Consistent error handling
   - Better validation messages

2. **User Experience**
   - Real-time cart counter
   - Smooth animations
   - Better feedback messages

## Struktur File

```
ecommerce-template/
├── app/
│   ├── Http/Controllers/
│   │   └── CartController.php          # Controller utama cart
│   └── Models/
│       ├── Cart.php                    # Model cart
│       ├── Product.php                 # Model product
│       └── User.php                    # Model user
├── resources/views/
│   ├── cart/
│   │   └── index.blade.php             # Halaman cart utama
│   ├── products/
│   │   └── catalog.blade.php           # Katalog dengan cart features
│   ├── layouts/
│   │   └── app.blade.php               # Layout utama dengan cart icon
│   └── test-cart.blade.php             # Halaman testing
├── routes/
│   ├── web.php                         # Web routes
│   └── api.php                         # API routes
└── database/
    ├── migrations/                     # Database migrations
    └── seeders/                        # Database seeders
```

## API Endpoints

### Web Routes (dengan CSRF Protection)
```php
// Cart Management
GET    /cart                    # Tampilkan halaman cart
POST   /cart                    # Tambah item ke cart
PUT    /cart/{cart}             # Update quantity item
DELETE /cart/{cart}             # Hapus item dari cart
DELETE /cart                    # Kosongkan cart
GET    /cart/count              # Get cart count (JSON)

// Public Routes
GET    /catalog                 # Halaman katalog produk
GET    /test-cart               # Halaman testing cart
GET    /debug-cart              # Debug cart data (authenticated)
```

### API Routes (Minimal - Future Extension)
```php
// Health check dan user info
GET    /api/health              # Health check endpoint
GET    /api/user                # Get authenticated user info
```

**Note**: Template ini menggunakan web-based authentication dengan session. API routes telah dibersihkan karena tidak digunakan. Untuk pengembangan mobile app atau third-party integration di masa depan, API routes dapat di-enable kembali sesuai kebutuhan.

## Frontend Components

### 1. Cart Icon Component (Header)
**File**: `resources/views/layouts/app.blade.php`

```html
<!-- Cart Icon dengan Counter -->
<div class="relative">
    <a href="{{ route('cart.index') }}" class="p-2 rounded-full hover:bg-gray-100">
        <svg class="w-6 h-6 text-gray-600"><!-- Cart SVG --></svg>
        <span id="cart-count" class="cart-counter">0</span>
    </a>
</div>
```

**JavaScript Functions**:
- `loadCartCount()` - Load counter saat page load
- `updateCartCount()` - Update counter setelah operasi cart

### 2. Product Modal Component
**File**: `resources/views/products/catalog.blade.php`

**Features**:
- Modal detail produk dengan gambar besar
- Quantity selector
- Add to cart dengan validation stock
- Responsive design

**JavaScript Functions**:
- `openModal(id)` - Buka modal produk
- `closeModal(id)` - Tutup modal
- `addToCartFromModal(productId)` - Add to cart dari modal

### 3. Cart Page Component
**File**: `resources/views/cart/index.blade.php`

**Features**:
- Daftar item dengan gambar dan detail
- Quantity controls (+/- buttons)
- Remove item buttons
- Order summary dengan pajak dan ongkir
- Empty cart state

**JavaScript Functions**:
- `updateQuantity(cartId, newQuantity)` - Update jumlah item
- `removeItem(cartId)` - Hapus item
- `clearCart()` - Kosongkan cart
- `recalculateTotals()` - Recalculate total harga

### 4. Toast Notification Component
**Features**:
- Success/Error notifications
- Auto-hide after 3 seconds
- Smooth animations

**JavaScript Functions**:
- `showToast(message, type)` - Tampilkan toast
- `hideToast()` - Sembunyikan toast

## Cara Penggunaan

### Untuk Developer

#### 1. Setup Database
```bash
# Jalankan migrations
php artisan migrate

# Seed data categories dan products
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

#### 2. Start Development Server
```bash
php artisan serve
```

#### 3. Testing Cart Features
1. Buka `http://127.0.0.1:8000/catalog`
2. Login terlebih dahulu
3. Test add to cart dari katalog
4. Test modal popup dan add to cart dari modal
5. Buka halaman cart dan test update/remove items

### Untuk User

#### 1. Browse Products
- Kunjungi halaman katalog: `/catalog`
- Gunakan filter pencarian dan kategori
- Klik "Lihat Detail" untuk modal popup

#### 2. Add to Cart
- **Dari Katalog**: Klik tombol hijau "+ Keranjang"
- **Dari Modal**: Pilih quantity, klik "Tambah ke Keranjang"
- Lihat cart counter di header bertambah

#### 3. Manage Cart
- Klik icon cart di header atau menu dropdown
- Update quantity dengan tombol +/-
- Hapus item dengan tombol "Hapus"
- Kosongkan cart dengan "Kosongkan Keranjang"

#### 4. Checkout (Coming Soon)
- Review order summary
- Klik "Checkout" (akan diarahkan ke payment gateway)

## Testing

### 1. Manual Testing
```bash
# Akses halaman test
http://127.0.0.1:8000/test-cart
```

### 2. Debug Cart Data
```bash
# Check cart data (harus login)
http://127.0.0.1:8000/debug-cart
```

### 3. API Testing dengan cURL
```bash
# Get cart count
curl -X GET "http://127.0.0.1:8000/cart/count" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest"

# Add to cart
curl -X POST "http://127.0.0.1:8000/cart" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -F "_token=CSRF_TOKEN" \
  -F "product_id=1" \
  -F "quantity=2"
```

## Troubleshooting

### Common Issues

#### 1. "Terjadi kesalahan jaringan"
**Penyebab**: CSRF token issues atau server tidak running
**Solusi**:
- Pastikan server running: `php artisan serve`
- Clear cache: `php artisan cache:clear`
- Check CSRF token di browser developer tools

#### 2. "Method Not Allowed"
**Penyebab**: HTTP method tidak sesuai
**Solusi**: 
- Sudah diperbaiki dengan method spoofing
- Pastikan menggunakan FormData untuk requests

#### 3. "Unauthorized action"
**Penyebab**: User mencoba akses cart orang lain
**Solusi**: 
- Login dengan user yang benar
- Check authentication middleware

#### 4. "Insufficient stock available"
**Penyebab**: Stock produk tidak mencukupi
**Solusi**:
- Check stock di database
- Update stock produk jika perlu

### Debug Commands

```bash
# Check routes
php artisan route:list --path=cart

# Check database tables
php artisan tinker
>>> \App\Models\Product::count()
>>> \App\Models\Cart::with('product')->get()

# Clear all caches
php artisan optimize:clear
```

### Log Files
Check log files untuk error details:
- `storage/logs/laravel.log`

## Browser Compatibility

✅ **Supported Browsers**:
- Chrome 90+
- Firefox 88+
- Safari 14+
- Edge 90+

⚠️ **Features yang memerlukan modern browser**:
- Fetch API
- ES6 Arrow Functions
- CSS Grid/Flexbox

## Performance Notes

- Cart counter di-load asynchronous untuk performa yang lebih baik
- AJAX requests mengurangi page reloads
- Optimistic UI updates untuk UX yang smooth
- Lazy loading untuk modal content

## Security Considerations

1. **CSRF Protection**: Semua POST/PUT/DELETE requests dilindungi CSRF
2. **Input Validation**: Semua input divalidasi di server-side
3. **Authentication**: Cart operations memerlukan login
4. **Authorization**: User hanya bisa akses cart sendiri
5. **SQL Injection Prevention**: Menggunakan Eloquent ORM

---

**Version**: 1.0
**Last Updated**: 22 Agustus 2025
**Maintainer**: E-commerce Development Team
