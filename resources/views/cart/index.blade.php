@extends('layouts.app')

@section('title', 'Keranjang Belanja - ' . config('app.name'))

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
    }
    .cart-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .cart-card:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -5px rgba(0, 0, 0, 0.1), 0 8px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .text-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
    .btn-primary-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        transition: all 0.3s ease;
    }
    .btn-primary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(59, 130, 246, 0.3);
    }
    .btn-secondary-gradient {
        background: linear-gradient(135deg, #10b981 0%, #3b82f6 100%);
        transition: all 0.3s ease;
    }
    .btn-secondary-gradient:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 20px rgba(16, 185, 129, 0.3);
    }
    .accent-blue {
        color: #3b82f6;
    }
    .accent-green {
        color: #10b981;
    }
    .bg-blue-gradient {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .bg-green-gradient {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .quantity-btn {
        transition: all 0.2s ease;
    }
    .quantity-btn:hover:not(:disabled) {
        transform: scale(1.1);
    }
    .summary-gradient {
        background: linear-gradient(135deg, #f8fafc 0%, #f1f5f9 100%);
    }
</style>
@endpush

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Simple Header -->
    <div class="text-center mb-8">
        <h1 class="text-2xl md:text-3xl font-bold text-gray-900 mb-2">
            Keranjang <span class="text-gradient">Belanja</span>
        </h1>
        <p class="text-gray-600">
            Review produk pilihan Anda dan lanjutkan ke pembayaran
        </p>
    </div>
    @if($cartItems->count() > 0)
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="cart-card bg-white rounded-2xl shadow-xl overflow-hidden">
                    <div class="bg-blue-gradient p-4 border-b">
                        <div class="flex items-center justify-between">
                            <div>
                                <h2 class="text-lg font-bold text-gray-900">Produk dalam Keranjang</h2>
                                <p class="text-sm text-gray-600 mt-1">{{ $cartItems->count() }} item dipilih</p>
                            </div>
                            <div class="w-10 h-10 bg-white rounded-full flex items-center justify-center shadow-lg">
                                <svg class="w-5 h-5 accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                                </svg>
                            </div>
                        </div>
                    </div>
                    
                    <div class="divide-y divide-gray-100">
                        @foreach($cartItems as $item)
                            <div class="p-4 hover:bg-gray-50 transition-all duration-300" id="cart-item-{{ $item->id }}">
                                <div class="flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gradient-to-br from-gray-100 to-gray-200 rounded-xl overflow-hidden shadow-md">
                                        @if($item->product->images->count() > 0)
                                            <img src="{{ $item->product->images->first()->url }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-base font-bold text-gray-900 mb-1">
                                            {{ $item->product->name }}
                                        </h3>
                                        <div class="flex items-center gap-2 mb-2">
                                            <span class="text-xs font-medium accent-blue uppercase tracking-wide bg-blue-gradient px-2 py-1 rounded-full">
                                                {{ $item->product->category->name ?? 'Eau de Parfum' }}
                                            </span>
                                        </div>
                                        <div class="accent-green font-bold text-base">
                                            Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <button onclick="decreaseQuantity({{ $item->id }})"
                                                class="quantity-btn w-8 h-8 rounded-full bg-blue-gradient hover:bg-blue-500 flex items-center justify-center shadow-lg {{ $item->quantity <= 1 ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        
                                        <div class="bg-gray-50 rounded-lg px-3 py-1 min-w-[2.5rem] text-center">
                                            <span class="font-bold text-gray-900 text-sm" id="quantity-{{ $item->id }}">
                                                {{ $item->quantity }}
                                            </span>
                                        </div>
                                        
                                        <button onclick="increaseQuantity({{ $item->id }})"
                                                class="quantity-btn w-8 h-8 rounded-full bg-green-gradient hover:bg-green-500 flex items-center justify-center shadow-lg {{ $item->quantity >= $item->product->stock ? 'opacity-50 cursor-not-allowed' : '' }}"
                                                {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="3" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Item Total & Actions -->
                                    <div class="text-right min-w-0 w-28">
                                        <div class="text-base font-bold accent-blue mb-2" id="item-total-{{ $item->id }}">
                                            Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}
                                        </div>
                                        <button onclick="removeItem({{ $item->id }})"
                                                class="inline-flex items-center gap-1 text-red-500 hover:text-red-700 text-xs font-medium transition-colors duration-200 bg-red-50 hover:bg-red-100 px-2 py-1 rounded-full">
                                            <svg class="w-3 h-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                                
                                @if($item->product->stock < $item->quantity)
                                    <div class="mt-3 p-3 bg-red-50 border-l-4 border-red-500 rounded-r-lg">
                                        <div class="flex items-center">
                                            <svg class="w-4 h-4 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span class="text-xs font-medium text-red-700">
                                                Stok tidak mencukupi! Hanya tersisa {{ $item->product->stock }} item.
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Cart Actions -->
                    <div class="p-4 bg-green-gradient border-t">
                        <div class="flex flex-col sm:flex-row gap-3 justify-between items-center">
                            <a href="{{ route('products.catalog') }}" 
                               class="inline-flex items-center gap-2 px-5 py-2 bg-white text-gray-700 rounded-xl font-medium hover:bg-gray-50 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 16l-4-4m0 0l4-4m-4 4h18"></path>
                                </svg>
                                Lanjut Belanja
                            </a>
                            <button onclick="clearCart()" 
                                    class="inline-flex items-center gap-2 px-5 py-2 bg-red-500 text-white rounded-xl font-medium hover:bg-red-600 transition-all duration-300 shadow-lg hover:shadow-xl">
                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                    </svg>
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="cart-card bg-white rounded-2xl shadow-xl p-6 sticky top-24">
                    <div class="text-center mb-6">
                        <div class="w-12 h-12 mx-auto bg-blue-gradient rounded-full flex items-center justify-center mb-3">
                            <svg class="w-6 h-6 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <h2 class="text-xl font-bold text-gray-900">Ringkasan Pesanan</h2>
                        <p class="text-sm text-gray-600 mt-1">Detail pembayaran Anda</p>
                    </div>
                    
                    <div class="space-y-3 mb-6">
                        <div class="summary-gradient rounded-xl p-3">
                            <div class="flex justify-between items-center">
                                <span class="text-gray-700 font-medium text-sm">Subtotal</span>
                                <span class="font-bold text-gray-900 text-sm" id="subtotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                            </div>
                        </div>
 
                        <div class="border-t-2 border-gray-100 pt-3">
                            <div class="flex justify-between items-center">
                                <span class="font-bold text-gray-900 text-base">Total</span>
                                <span class="font-bold accent-green text-lg" id="final-total">Rp {{ number_format($total , 0, ',', '.') }}</span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <button onclick="checkout()" 
                                class="w-full btn-primary-gradient px-5 py-3 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                            <div class="flex items-center justify-center gap-2">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"></path>
                                </svg>
                                <span>Checkout Sekarang</span>
                            </div>
                        </button>
                        
                        <div class="bg-blue-gradient rounded-xl p-3 text-center">
                            <div class="flex items-center justify-center gap-2 mb-1">
                                <svg class="w-4 h-4 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m5.618-4.016A11.955 11.955 0 0112 2.944a11.955 11.955 0 01-8.618 3.04A12.02 12.02 0 003 9c0 5.591 3.824 10.29 9 11.622 5.176-1.332 9-6.03 9-11.622 0-1.042-.133-2.052-.382-3.016z"></path>
                                </svg>
                                <span class="text-xs font-semibold text-blue-700">Pembayaran Aman</span>
                            </div>
                            <p class="text-xs text-gray-600">
                                Transaksi dilindungi SSL 256-bit
                            </p>
                        </div>
                        
                        <p class="text-xs text-gray-500 text-center leading-relaxed">
                            Dengan melanjutkan, Anda menyetujui syarat dan ketentuan kami
                        </p>
                    </div>
                </div>
            </div>
        </div>
    @else
        <!-- Empty Cart -->
        <div class="text-center py-16">
            <div class="max-w-md mx-auto">
                <div class="w-24 h-24 bg-blue-gradient rounded-full flex items-center justify-center mx-auto mb-6 shadow-xl">
                    <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 13h8"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-3">Keranjang Masih Kosong</h3>
                <p class="text-gray-600 mb-6 leading-relaxed">Sepertinya Anda belum menambahkan produk parfum favorit ke keranjang. Yuk, mulai jelajahi koleksi kami!</p>
                
                <div class="space-y-4">
                    <a href="{{ route('products.catalog') }}" 
                       class="inline-flex items-center gap-2 btn-primary-gradient px-6 py-3 text-white font-bold rounded-xl shadow-lg hover:shadow-xl transition-all duration-300">
                        <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                        </svg>
                        <span>Jelajahi Parfum</span>
                    </a>
                    
                    <div class="flex justify-center gap-6 text-xs text-gray-500 pt-3">
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            500+ Produk
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            Gratis Ongkir
                        </div>
                        <div class="flex items-center gap-1">
                            <svg class="w-3 h-3 accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7"></path>
                            </svg>
                            100% Original
                        </div>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Toast Notification -->
<div id="toast" class="hidden fixed top-4 right-4 z-50 max-w-sm w-full">
    <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p id="toast-message" class="text-sm font-medium text-gray-900"></p>
            </div>
            <button onclick="hideToast()" class="ml-auto text-gray-400 hover:text-gray-600">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    </div>
</div>

<!-- Loading Overlay -->
<div id="loading" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center">
    <div class="bg-white rounded-lg p-6">
        <div class="flex items-center space-x-3">
            <div class="animate-spin rounded-full h-6 w-6 border-2 border-blue-600 border-t-transparent"></div>
            <span class="text-gray-700">Memproses...</span>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
    // Toast notification functions
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
    
    function hideToast() {
        document.getElementById('toast').classList.add('hidden');
    }
    
    function showLoading() {
        const loading = document.getElementById('loading');
        loading.classList.remove('hidden');
        loading.classList.add('flex');
    }
    
    function hideLoading() {
        const loading = document.getElementById('loading');
        loading.classList.add('hidden');
        loading.classList.remove('flex');
    }
    
    // Cart functions
    async function increaseQuantity(cartId) {
        const currentQuantity = parseInt(document.getElementById(`quantity-${cartId}`).textContent);
        await updateQuantity(cartId, currentQuantity + 1);
    }
    
    async function decreaseQuantity(cartId) {
        const currentQuantity = parseInt(document.getElementById(`quantity-${cartId}`).textContent);
        if (currentQuantity > 1) {
            await updateQuantity(cartId, currentQuantity - 1);
        }
    }
    
    async function updateQuantity(cartId, newQuantity) {
        if (newQuantity < 1) return;
        
        showLoading();
        
        try {
            const formData = new FormData();
            formData.append('_method', 'PUT');
            formData.append('_token', '{{ csrf_token() }}');
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
                // Update quantity display
                document.getElementById(`quantity-${cartId}`).textContent = newQuantity;
                
                // Update button states
                updateButtonStates(cartId, newQuantity, data.data.product.stock);
                
                // Recalculate totals
                await recalculateTotals();
                
                showToast('Keranjang berhasil diperbarui!', 'success');
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'error');
            }
        } catch (error) {
            console.error('Error updating quantity:', error);
            showToast('Terjadi kesalahan jaringan', 'error');
        } finally {
            hideLoading();
        }
    }
    
    function updateButtonStates(cartId, quantity, stock) {
        const decreaseBtn = document.querySelector(`[onclick="decreaseQuantity(${cartId})"]`);
        const increaseBtn = document.querySelector(`[onclick="increaseQuantity(${cartId})"]`);
        
        // Update decrease button state
        if (quantity <= 1) {
            decreaseBtn.disabled = true;
            decreaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            decreaseBtn.disabled = false;
            decreaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
        
        // Update increase button state
        if (quantity >= stock) {
            increaseBtn.disabled = true;
            increaseBtn.classList.add('opacity-50', 'cursor-not-allowed');
        } else {
            increaseBtn.disabled = false;
            increaseBtn.classList.remove('opacity-50', 'cursor-not-allowed');
        }
    }
    
    async function removeItem(cartId) {
        if (!confirm('Apakah Anda yakin ingin menghapus item ini?')) return;
        
        showLoading();
        
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');

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
                // Remove item from DOM
                document.getElementById(`cart-item-${cartId}`).remove();
                
                // Recalculate totals
                await recalculateTotals();
                
                showToast('Item berhasil dihapus!', 'success');
                
                // Check if cart is empty and reload page
                const remainingItems = document.querySelectorAll('[id^="cart-item-"]');
                if (remainingItems.length === 0) {
                    location.reload();
                }
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan jaringan', 'error');
        } finally {
            hideLoading();
        }
    }
    
    async function clearCart() {
        if (!confirm('Apakah Anda yakin ingin mengosongkan keranjang?')) return;
        
        showLoading();
        
        try {
            const formData = new FormData();
            formData.append('_method', 'DELETE');
            formData.append('_token', '{{ csrf_token() }}');

            const response = await fetch('/cart', {
                method: 'POST',
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                },
                body: formData
            });
            
            if (response.ok) {
                location.reload();
            } else {
                showToast('Terjadi kesalahan', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan jaringan', 'error');
        } finally {
            hideLoading();
        }
    }
    
    async function recalculateTotals() {
        // This would typically fetch updated cart data from server
        // For now, we'll calculate from current DOM
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
    
    function checkout() {
        // Redirect to checkout page
        window.location.href = '{{ route("checkout.index") }}';
    }
</script>
@endpush
