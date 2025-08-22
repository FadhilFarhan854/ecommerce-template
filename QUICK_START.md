# 🚀 Quick Start Guide - Cart Features

## Instalasi & Setup

### 1. Prerequisites
- PHP 8.1+
- Composer
- Node.js (untuk asset compilation)
- SQLite/MySQL

### 2. Setup Database
```bash
# Jalankan migrations
php artisan migrate

# Seed data sample
php artisan db:seed --class=CategorySeeder
php artisan db:seed --class=ProductSeeder
```

### 3. Start Server
```bash
php artisan serve
```
Server akan berjalan di: `http://127.0.0.1:8000`

## Testing Cart Features

### 🛒 Test Add to Cart
1. Buka: `http://127.0.0.1:8000/catalog`
2. Login terlebih dahulu
3. Klik "+ Keranjang" pada produk
4. Lihat counter cart di header

### 📱 Test Modal Popup
1. Di halaman katalog, klik "Lihat Detail"
2. Modal akan terbuka dengan detail produk
3. Pilih quantity dan klik "Tambah ke Keranjang"
4. Modal bisa ditutup dengan klik X atau area luar modal

### 🛍️ Test Cart Management
1. Klik icon cart di header
2. Test update quantity dengan tombol +/-
3. Test hapus item dengan tombol "Hapus"
4. Test kosongkan cart

### 🧪 Test Page
Akses halaman testing: `http://127.0.0.1:8000/test-cart`

## File Structure

```
resources/views/
├── cart/index.blade.php           # Halaman cart utama
├── products/catalog.blade.php     # Katalog dengan modal
├── layouts/app.blade.php          # Header dengan cart icon
└── test-cart.blade.php           # Halaman testing

app/Http/Controllers/
└── CartController.php            # Controller cart dengan dual response

routes/
├── web.php                       # Web routes dengan CSRF
└── api.php                       # API routes dengan Sanctum
```

## Key Features Implemented

### ✅ Frontend Features
- **Modal Popup**: Detail produk dengan responsive design
- **Cart Icon**: Counter real-time di header
- **Toast Notifications**: Feedback sukses/error
- **Loading States**: UX yang smooth
- **AJAX Operations**: Tanpa page reload

### ✅ Backend Features
- **Dual Response**: JSON untuk AJAX, Redirect untuk form
- **Method Spoofing**: Support PUT/DELETE via FormData
- **Stock Validation**: Cek stok sebelum add to cart
- **User Authorization**: User hanya akses cart sendiri

### ✅ Security
- **CSRF Protection**: Semua state-changing operations
- **Input Validation**: Server-side validation
- **Authentication**: Login required untuk cart operations

## Common Operations

### Add to Cart (JavaScript)
```javascript
async function addToCart(productId, quantity = 1) {
    const formData = new FormData();
    formData.append('_token', csrfToken);
    formData.append('product_id', productId);
    formData.append('quantity', quantity);

    const response = await fetch('/cart', {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    });
}
```

### Update Quantity (JavaScript)
```javascript
async function updateQuantity(cartId, newQuantity) {
    const formData = new FormData();
    formData.append('_method', 'PUT');
    formData.append('_token', csrfToken);
    formData.append('quantity', newQuantity);

    const response = await fetch(`/cart/${cartId}`, {
        method: 'POST',
        body: formData
    });
}
```

## Troubleshooting

### ❌ "Terjadi kesalahan jaringan"
**Fix**: Check CSRF token dan pastikan server running

### ❌ "Method Not Allowed"
**Fix**: Sudah diperbaiki dengan method spoofing

### ❌ Cart counter tidak update
**Fix**: Pastikan fungsi `updateCartCount()` dipanggil setelah operasi cart

### ❌ Modal tidak bisa ditutup
**Fix**: Sudah diperbaiki dengan event handlers yang proper

## Next Steps

1. **Payment Integration**: Tambah payment gateway
2. **Inventory Management**: Real-time stock updates
3. **Order Management**: Checkout flow
4. **Email Notifications**: Order confirmations
5. **Admin Panel**: Manage products dan orders

---

**Happy Coding! 🎉**
