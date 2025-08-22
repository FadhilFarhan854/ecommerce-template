# Category CRUD - Laravel Monolith vs API

Proyek ini mendemonstrasikan bagaimana Laravel dapat digunakan untuk membuat aplikasi **Monolith** dan **API** dalam satu controller yang sama.

## Perbedaan Pendekatan

### 1. API (Application Programming Interface)
- **Tujuan**: Menyediakan endpoints untuk aplikasi frontend terpisah (React, Vue, Mobile Apps, dll)
- **Response**: JSON format
- **Routes**: Menggunakan `routes/api.php`
- **Authentication**: Menggunakan Sanctum tokens
- **Frontend**: Terpisah dari backend

### 2. Monolith 
- **Tujuan**: Aplikasi web tradisional dengan server-side rendering
- **Response**: HTML views (Blade templates)
- **Routes**: Menggunakan `routes/web.php`
- **Authentication**: Menggunakan session-based auth
- **Frontend**: Terintegrasi dengan backend (Blade templates)

## Implementasi dalam CategoryController

Controller yang dibuat menggunakan deteksi otomatis untuk menentukan jenis response:

```php
// Deteksi jenis request
if ($request->wantsJson() || $request->is('api/*')) {
    // Response JSON untuk API
    return response()->json(['data' => $data]);
} else {
    // Response HTML untuk web (monolith)
    return view('categories.index', compact('data'));
}
```

## Struktur Routes

### API Routes (`routes/api.php`)
```php
// Public routes
Route::prefix('categories')->group(function () {
    Route::get('/', [CategoryController::class, 'index']);
    Route::get('/{category}', [CategoryController::class, 'show']);
});

// Protected routes (memerlukan authentication)
Route::middleware('auth:sanctum')->prefix('categories')->group(function () {
    Route::post('/', [CategoryController::class, 'store']);
    Route::put('/{category}', [CategoryController::class, 'update']);
    Route::delete('/{category}', [CategoryController::class, 'destroy']);
});
```

### Web Routes (`routes/web.php`)
```php
// Menggunakan resource routing untuk CRUD lengkap
Route::resource('categories', CategoryController::class);
```

## Contoh Penggunaan

### 1. Sebagai API
```bash
# GET semua kategori
GET /api/categories
Accept: application/json

# POST kategori baru
POST /api/categories
Content-Type: application/json
Authorization: Bearer {token}
{
    "name": "Electronics",
    "slug": "electronics"
}
```

### 2. Sebagai Monolith
```bash
# Mengakses halaman web
GET /categories           -> Menampilkan daftar kategori
GET /categories/create    -> Form membuat kategori
POST /categories          -> Menyimpan kategori baru
GET /categories/1         -> Detail kategori
GET /categories/1/edit    -> Form edit kategori
PUT /categories/1         -> Update kategori
DELETE /categories/1      -> Hapus kategori
```

## File Structure

```
app/
├── Http/
│   ├── Controllers/
│   │   └── CategoryController.php    # Controller hybrid (API + Web)
│   └── Requests/
│       └── CategoryRequest.php       # Validation request
├── Models/
│   └── Category.php                  # Model dengan factory
database/
├── factories/
│   └── CategoryFactory.php          # Factory untuk testing
└── migrations/
    └── category_table.php            # Database schema
resources/
└── views/
    └── categories/                   # Blade templates untuk web
        ├── index.blade.php
        ├── create.blade.php
        ├── edit.blade.php
        └── show.blade.php
routes/
├── api.php                           # Routes untuk API
└── web.php                           # Routes untuk web
tests/
└── Feature/
    └── CategoryTest.php              # Comprehensive tests
```

## Testing

Semua tests telah dibuat untuk memastikan kedua pendekatan berfungsi:

```bash
php artisan test --filter=CategoryTest
```

Tests mencakup:
- ✅ CRUD operations via API
- ✅ Authentication & Authorization  
- ✅ Validation rules
- ✅ Error handling
- ✅ Relationship constraints
- ✅ Factory & Model tests

## Kapan Menggunakan Mana?

### Gunakan API ketika:
- Frontend dan backend terpisah
- Aplikasi mobile/SPA
- Microservices architecture
- Multiple clients (web, mobile, desktop)

### Gunakan Monolith ketika:
- Aplikasi web tradisional
- Server-side rendering (SEO friendly)
- Rapid prototyping
- Tim kecil dengan satu aplikasi

## Keunggulan Laravel

Laravel memungkinkan fleksibilitas untuk:
1. **Memulai sebagai monolith** lalu migrasi ke API
2. **Menggunakan kedua pendekatan** dalam satu aplikasi
3. **Sharing logic** antara web dan API routes
4. **Konsisten dalam validation** dan business logic

Controller yang dibuat di proyek ini mendemonstrasikan bagaimana satu controller dapat melayani kedua kebutuhan dengan efisien.
