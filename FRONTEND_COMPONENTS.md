# 🎨 Frontend Components Documentation

## Overview
Dokumentasi lengkap untuk semua komponen frontend yang digunakan dalam sistem cart.

## Table of Contents
1. [Modal Component](#modal-component)
2. [Cart Icon Component](#cart-icon-component)
3. [Toast Notification Component](#toast-notification-component)
4. [Cart Page Component](#cart-page-component)
5. [Loading Component](#loading-component)
6. [JavaScript Utilities](#javascript-utilities)

---

## Modal Component

### 📍 Location
`resources/views/products/catalog.blade.php`

### 🎯 Purpose
Menampilkan detail produk dalam popup modal yang responsive dan user-friendly.

### 🏗️ Structure
```html
<div id="modal-{product_id}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
    <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
        <!-- Modal Header -->
        <div class="flex items-center justify-between p-6 border-b">
            <h2 class="text-2xl font-bold text-gray-800">Product Name</h2>
            <button onclick="closeModal(id)" class="text-gray-400 hover:text-gray-600">×</button>
        </div>
        
        <!-- Modal Body -->
        <div class="p-6">
            <div class="grid md:grid-cols-2 gap-6">
                <!-- Product Image -->
                <div class="h-80 bg-gray-100 rounded-lg overflow-hidden">
                    <img src="product_image" alt="product_name" class="w-full h-full object-cover">
                </div>
                
                <!-- Product Details -->
                <div class="space-y-4">
                    <!-- Category, Price, Stock, Description -->
                    <!-- Quantity Selector -->
                    <!-- Add to Cart Button -->
                </div>
            </div>
        </div>
    </div>
</div>
```

### 🎛️ JavaScript Functions

#### `openModal(id)`
**Purpose**: Membuka modal produk dengan ID tertentu

```javascript
function openModal(id) {
    const modal = document.getElementById('modal-' + id);
    modal.classList.remove('hidden');
    modal.classList.add('flex');
    document.body.style.overflow = 'hidden'; // Prevent background scroll
}
```

#### `closeModal(id)`
**Purpose**: Menutup modal produk

```javascript
function closeModal(id) {
    const modal = document.getElementById('modal-' + id);
    modal.classList.add('hidden');
    modal.classList.remove('flex');
    document.body.style.overflow = 'auto'; // Restore background scroll
}
```

#### `addToCartFromModal(productId)`
**Purpose**: Menambah produk ke cart dari modal dengan quantity yang dipilih

```javascript
function addToCartFromModal(productId) {
    const quantityInput = document.getElementById('quantity-' + productId);
    const quantity = parseInt(quantityInput.value) || 1;
    
    addToCart(productId, quantity).then(() => {
        closeModal(productId);
    });
}
```

### 🎨 CSS Classes (Tailwind)
- `hidden` / `flex`: Toggle modal visibility
- `fixed inset-0`: Full screen overlay
- `bg-black bg-opacity-50`: Semi-transparent backdrop
- `z-50`: High z-index for layering
- `max-w-2xl w-full`: Responsive width
- `max-h-[90vh] overflow-y-auto`: Scrollable content

### 📱 Responsive Features
- Grid layout adapts on mobile (`md:grid-cols-2`)
- Modal width adjusts to screen size
- Scrollable content for long descriptions
- Touch-friendly buttons

---

## Cart Icon Component

### 📍 Location
`resources/views/layouts/app.blade.php`

### 🎯 Purpose
Menampilkan icon cart di header dengan counter real-time untuk jumlah item.

### 🏗️ Structure
```html
<div class="relative">
    <a href="{{ route('cart.index') }}" class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 relative">
        <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 13h8"></path>
        </svg>
        <span id="cart-count" class="absolute -top-1 -right-1 bg-red-500 text-white text-xs rounded-full h-5 w-5 flex items-center justify-center font-medium">
            0
        </span>
    </a>
</div>
```

### 🎛️ JavaScript Functions

#### `loadCartCount()`
**Purpose**: Load cart count saat halaman dimuat

```javascript
async function loadCartCount() {
    try {
        const response = await fetch('/cart/count', {
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest',
                'X-CSRF-TOKEN': csrfToken
            },
            credentials: 'same-origin'
        });
        
        if (response.ok) {
            const data = await response.json();
            const cartCountElement = document.getElementById('cart-count');
            if (cartCountElement) {
                const count = data.count || 0;
                cartCountElement.textContent = count;
                cartCountElement.style.display = count > 0 ? 'flex' : 'none';
            }
        }
    } catch (error) {
        console.log('Could not load cart count');
    }
}
```

#### `updateCartCount()`
**Purpose**: Update cart count setelah operasi cart (global function)

```javascript
window.updateCartCount = loadCartCount;
```

### 🎨 Styling Features
- **Badge Position**: `absolute -top-1 -right-1` untuk posisi badge
- **Badge Visibility**: Hidden saat count = 0, visible saat count > 0
- **Hover Effects**: `hover:bg-gray-100` untuk feedback visual
- **Responsive**: Icon size adapts untuk mobile

### 🔄 Auto-Update Triggers
Cart count otomatis update setelah:
- Add to cart
- Update quantity
- Remove item
- Clear cart

---

## Toast Notification Component

### 📍 Location
Multiple files (catalog.blade.php, cart/index.blade.php, test-cart.blade.php)

### 🎯 Purpose
Memberikan feedback visual kepada user untuk operasi yang berhasil atau gagal.

### 🏗️ Structure
```html
<div id="toast" class="hidden fixed top-4 right-4 z-50 max-w-sm w-full">
    <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p id="toast-message" class="text-sm font-medium text-gray-900">Message here</p>
            </div>
            <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
```

### 🎛️ JavaScript Functions

#### `showToast(message, type)`
**Purpose**: Menampilkan toast notification

```javascript
function showToast(message, type = 'success') {
    const toast = document.getElementById('toast');
    const toastMessage = document.getElementById('toast-message');
    const icon = toast.querySelector('svg');
    const border = toast.querySelector('div > div');
    
    toastMessage.textContent = message;
    
    if (type === 'success') {
        border.className = 'bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4';
        icon.className = 'w-6 h-6 text-green-500';
    } else if (type === 'error') {
        border.className = 'bg-white border-l-4 border-red-500 rounded-lg shadow-lg p-4';
        icon.className = 'w-6 h-6 text-red-500';
    }
    
    toast.classList.remove('hidden');
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        hideToast();
    }, 3000);
}
```

#### `hideToast()`
**Purpose**: Menyembunyikan toast notification

```javascript
function hideToast() {
    document.getElementById('toast').classList.add('hidden');
}
```

### 🎨 Toast Types
1. **Success Toast**: Green border & icon
2. **Error Toast**: Red border & icon

### ⏱️ Auto-Hide Feature
- Toast otomatis hilang setelah 3 detik
- User bisa manual close dengan tombol X

---

## Cart Page Component

### 📍 Location
`resources/views/cart/index.blade.php`

### 🎯 Purpose
Halaman utama cart dengan fitur lengkap untuk manage cart items.

### 🏗️ Main Sections

#### 1. Cart Items List
```html
<div class="divide-y divide-gray-200">
    @foreach($cartItems as $item)
        <div class="p-6 hover:bg-gray-50 transition" id="cart-item-{{ $item->id }}">
            <!-- Product Image -->
            <!-- Product Details -->
            <!-- Quantity Controls -->
            <!-- Item Total -->
        </div>
    @endforeach
</div>
```

#### 2. Quantity Controls
```html
<div class="flex items-center space-x-2">
    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity - 1 }})"
            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300">
        <!-- Minus Icon -->
    </button>
    
    <span class="w-12 text-center font-medium" id="quantity-{{ $item->id }}">
        {{ $item->quantity }}
    </span>
    
    <button onclick="updateQuantity({{ $item->id }}, {{ $item->quantity + 1 }})"
            class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300">
        <!-- Plus Icon -->
    </button>
</div>
```

#### 3. Order Summary
```html
<div class="bg-white rounded-lg shadow p-6 sticky top-24">
    <h2 class="text-xl font-semibold text-gray-800 mb-6">Ringkasan Pesanan</h2>
    
    <div class="space-y-4 mb-6">
        <div class="flex justify-between">
            <span class="text-gray-600">Subtotal</span>
            <span class="font-medium" id="subtotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
        </div>
        <!-- Tax, Shipping, Total -->
    </div>
    
    <button onclick="checkout()" class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
        Checkout
    </button>
</div>
```

### 🎛️ JavaScript Functions

#### `updateQuantity(cartId, newQuantity)`
**Purpose**: Update jumlah item dalam cart

```javascript
async function updateQuantity(cartId, newQuantity) {
    if (newQuantity < 1) return;
    
    showLoading();
    
    try {
        const formData = new FormData();
        formData.append('_method', 'PUT');
        formData.append('_token', csrfToken);
        formData.append('quantity', newQuantity);

        const response = await fetch(`/cart/${cartId}`, {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            // Update UI
            document.getElementById(`quantity-${cartId}`).textContent = newQuantity;
            await recalculateTotals();
            showToast('Keranjang berhasil diperbarui!', 'success');
        } else {
            showToast(data.message || 'Terjadi kesalahan', 'error');
        }
    } catch (error) {
        showToast('Terjadi kesalahan jaringan', 'error');
    } finally {
        hideLoading();
    }
}
```

#### `removeItem(cartId)`
**Purpose**: Hapus item dari cart

#### `clearCart()`
**Purpose**: Kosongkan seluruh cart

#### `recalculateTotals()`
**Purpose**: Recalculate total harga setelah perubahan

```javascript
async function recalculateTotals() {
    let subtotal = 0;
    
    document.querySelectorAll('[id^="cart-item-"]').forEach(item => {
        const cartId = item.id.replace('cart-item-', '');
        const quantity = parseInt(document.getElementById(`quantity-${cartId}`).textContent);
        const priceText = item.querySelector('.text-green-600').textContent;
        const price = parseInt(priceText.replace(/[^\d]/g, ''));
        const itemTotal = quantity * price;
        
        // Update item total display
        document.getElementById(`item-total-${cartId}`).textContent = 
            `Rp ${itemTotal.toLocaleString('id-ID')}`;
        
        subtotal += itemTotal;
    });
    
    const tax = subtotal * 0.11;
    const shipping = 15000;
    const total = subtotal + tax + shipping;
    
    // Update summary
    document.getElementById('subtotal').textContent = `Rp ${subtotal.toLocaleString('id-ID')}`;
    document.getElementById('tax').textContent = `Rp ${tax.toLocaleString('id-ID')}`;
    document.getElementById('final-total').textContent = `Rp ${total.toLocaleString('id-ID')}`;
}
```

### 🔄 Real-time Updates
- Quantity changes instantly reflect in UI
- Totals recalculate automatically
- Cart count updates in header

---

## Loading Component

### 📍 Location
`resources/views/cart/index.blade.php`

### 🎯 Purpose
Menampilkan loading state saat proses AJAX berjalan.

### 🏗️ Structure
```html
<div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-600 border-t-transparent"></div>
            <span class="text-gray-700">Memproses...</span>
        </div>
    </div>
</div>
```

### 🎛️ JavaScript Functions

#### `showLoading()`
```javascript
function showLoading() {
    const loading = document.getElementById('loading');
    loading.classList.remove('hidden');
    loading.classList.add('flex');
}
```

#### `hideLoading()`
```javascript
function hideLoading() {
    const loading = document.getElementById('loading');
    loading.classList.add('hidden');
    loading.classList.remove('flex');
}
```

### 🎨 Animation
- `animate-spin` untuk rotating spinner
- Smooth fade in/out effects

---

## JavaScript Utilities

### 🔧 Common Patterns

#### FormData untuk Laravel
```javascript
const formData = new FormData();
formData.append('_token', csrfToken);
formData.append('_method', 'PUT'); // Method spoofing
formData.append('field', value);
```

#### Fetch dengan Error Handling
```javascript
try {
    const response = await fetch(url, {
        method: 'POST',
        headers: {
            'Accept': 'application/json',
            'X-Requested-With': 'XMLHttpRequest'
        },
        body: formData
    });
    
    const data = await response.json();
    
    if (response.ok) {
        // Success
    } else {
        // Error
    }
} catch (error) {
    // Network error
}
```

#### DOM Manipulation
```javascript
// Update text content
document.getElementById('element-id').textContent = newValue;

// Toggle classes
element.classList.add('class-name');
element.classList.remove('class-name');
element.classList.toggle('class-name');

// Style properties
element.style.display = 'none';
element.style.overflow = 'hidden';
```

### 🎯 Best Practices

1. **Error Handling**: Selalu wrap fetch dalam try-catch
2. **Loading States**: Tunjukkan loading saat proses async
3. **User Feedback**: Berikan feedback dengan toast notifications
4. **Responsive**: Gunakan classes responsive Tailwind
5. **Accessibility**: Tambah proper ARIA labels
6. **Performance**: Debounce untuk input yang frequent

---

**Version**: 1.0  
**Last Updated**: 22 Agustus 2025
