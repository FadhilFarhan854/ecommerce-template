@extends('layouts.app')

@section('title', 'Keranjang Belanja - ' . config('app.name'))

@section('content')
<!-- Header -->
<div class="bg-gray-50 py-10 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 text-center mb-2">Keranjang Belanja</h1>
        <p class="text-gray-500 text-center max-w-xl mx-auto">
            Kelola produk yang ingin Anda beli
        </p>
    </div>
</div>

<div class="container mx-auto px-4 md:px-10 mb-8">
    @if($cartItems->count() > 0)
        <div class="grid lg:grid-cols-3 gap-8">
            <!-- Cart Items -->
            <div class="lg:col-span-2">
                <div class="bg-white rounded-lg shadow overflow-hidden">
                    <div class="p-6 border-b">
                        <h2 class="text-xl font-semibold text-gray-800">Produk dalam Keranjang</h2>
                        <p class="text-gray-500 text-sm mt-1">{{ $cartItems->count() }} item</p>
                    </div>
                    
                    <div class="divide-y divide-gray-200">
                        @foreach($cartItems as $item)
                            <div class="p-6 hover:bg-gray-50 transition" id="cart-item-{{ $item->id }}">
                                <div class="flex items-center space-x-4">
                                    <!-- Product Image -->
                                    <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden">
                                        @if($item->product->images->count() > 0)
                                            <img src="{{ $item->product->images->first()->url }}" 
                                                 alt="{{ $item->product->name }}" 
                                                 class="w-full h-full object-cover">
                                        @else
                                            <div class="w-full h-full flex items-center justify-center bg-gray-200">
                                                <svg class="w-8 h-8 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 16m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                                </svg>
                                            </div>
                                        @endif
                                    </div>
                                    
                                    <!-- Product Details -->
                                    <div class="flex-grow min-w-0">
                                        <h3 class="text-lg font-medium text-gray-900 truncate">
                                            {{ $item->product->name }}
                                        </h3>
                                        <p class="text-sm text-gray-500 mt-1">
                                            {{ $item->product->category->name ?? 'Tanpa Kategori' }}
                                        </p>
                                        <div class="text-green-600 font-semibold mt-2">
                                            Rp {{ number_format($item->product->price, 0, ',', '.') }}
                                        </div>
                                    </div>
                                    
                                    <!-- Quantity Controls -->
                                    <div class="flex items-center space-x-2">
                                        <button onclick="decreaseQuantity({{ $item->id }})"
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition"
                                                {{ $item->quantity <= 1 ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        
                                        <span class="w-12 text-center font-medium text-gray-900" id="quantity-{{ $item->id }}">
                                            {{ $item->quantity }}
                                        </span>
                                        
                                        <button onclick="increaseQuantity({{ $item->id }})"
                                                class="w-8 h-8 rounded-full bg-gray-200 hover:bg-gray-300 flex items-center justify-center transition"
                                                {{ $item->quantity >= $item->product->stock ? 'disabled' : '' }}>
                                            <svg class="w-4 h-4 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                    
                                    <!-- Item Total -->
                                    <div class="text-right min-w-0 w-24">
                                        <div class="text-lg font-semibold text-gray-900" id="item-total-{{ $item->id }}">
                                            Rp {{ number_format($item->quantity * $item->product->price, 0, ',', '.') }}
                                        </div>
                                        <button onclick="removeItem({{ $item->id }})"
                                                class="text-red-500 hover:text-red-700 text-sm mt-1 transition">
                                            Hapus
                                        </button>
                                    </div>
                                </div>
                                
                                @if($item->product->stock < $item->quantity)
                                    <div class="mt-4 p-3 bg-red-50 border border-red-200 rounded-lg">
                                        <div class="flex items-center">
                                            <svg class="w-5 h-5 text-red-500 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L4.35 16.5c-.77.833.192 2.5 1.732 2.5z"></path>
                                            </svg>
                                            <span class="text-sm text-red-700">
                                                Stok tidak mencukupi! Hanya tersisa {{ $item->product->stock }} item.
                                            </span>
                                        </div>
                                    </div>
                                @endif
                            </div>
                        @endforeach
                    </div>
                    
                    <!-- Cart Actions -->
                    <div class="p-6 bg-gray-50 border-t">
                        <div class="flex justify-between items-center">
                            <a href="{{ route('products.catalog') }}" 
                               class="px-6 py-2 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition">
                                Lanjut Belanja
                            </a>
                            <button onclick="clearCart()" 
                                    class="px-6 py-2 bg-red-600 text-white rounded-lg hover:bg-red-700 transition">
                                Kosongkan Keranjang
                            </button>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Order Summary -->
            <div class="lg:col-span-1">
                <div class="bg-white rounded-lg shadow p-6 sticky top-24">
                    <h2 class="text-xl font-semibold text-gray-800 mb-6">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-4 mb-6">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium" id="subtotal">Rp {{ number_format($total, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pajak (11%)</span>
                            <span class="font-medium" id="tax">Rp {{ number_format($total * 0.11, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Rp 15.000</span>
                        </div>
                        <hr class="border-gray-200">
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-gray-800">Total</span>
                            <span class="text-green-600" id="final-total">Rp {{ number_format($total + ($total * 0.11) + 15000, 0, ',', '.') }}</span>
                        </div>
                    </div>
                    
                    <div class="space-y-3">
                        <button onclick="checkout()" 
                                class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                            Checkout
                        </button>
                        <p class="text-xs text-gray-500 text-center">
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
                <div class="w-24 h-24 bg-gray-100 rounded-full flex items-center justify-center mx-auto mb-6">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 13h8"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-semibold text-gray-700 mb-2">Keranjang Kosong</h3>
                <p class="text-gray-500 mb-6">Sepertinya Anda belum menambahkan produk apapun ke keranjang.</p>
                <a href="{{ route('products.catalog') }}" 
                   class="inline-block px-8 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Mulai Belanja
                </a>
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
