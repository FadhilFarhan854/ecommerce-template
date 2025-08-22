@extends('layouts.app')

@section('title', 'Test Cart - ' . config('app.name'))

@section('content')
<div class="container mx-auto px-4 py-8">
    <div class="max-w-2xl mx-auto">
        <h1 class="text-3xl font-bold text-gray-800 mb-8">Test Fitur Keranjang</h1>
        
        @if(auth()->check())
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Test Add to Cart</h2>
                <p class="text-gray-600 mb-4">Klik tombol di bawah untuk menguji fungsi tambah ke keranjang:</p>
                
                <!-- Simulate product -->
                <div class="border rounded-lg p-4 mb-4">
                    <h3 class="font-medium">Produk Test</h3>
                    <p class="text-gray-500">ID: 1</p>
                    <div class="text-green-600 font-semibold">Rp 50.000</div>
                </div>
                
                <button onclick="testAddToCart()" 
                        class="w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Test Add to Cart
                </button>
            </div>
            
            <div class="bg-white rounded-lg shadow p-6 mb-6">
                <h2 class="text-xl font-semibold mb-4">Navigasi Cepat</h2>
                <div class="space-y-2">
                    <a href="{{ route('products.catalog') }}" 
                       class="block w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition text-center">
                        Lihat Katalog Produk
                    </a>
                    <a href="{{ route('cart.index') }}" 
                       class="block w-full px-6 py-3 bg-purple-600 text-white font-medium rounded-lg hover:bg-purple-700 transition text-center">
                        Lihat Keranjang
                    </a>
                </div>
            </div>
        @else
            <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6">
                <h2 class="text-xl font-semibold text-yellow-800 mb-2">Login Diperlukan</h2>
                <p class="text-yellow-700 mb-4">Anda harus login untuk menguji fitur keranjang.</p>
                <a href="{{ route('login') }}" 
                   class="inline-block px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition">
                    Login Sekarang
                </a>
            </div>
        @endif
    </div>
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
    
    // Test function
    async function testAddToCart() {
        try {
            const response = await fetch('{{ route("cart.store") }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}',
                    'Accept': 'application/json'
                },
                body: JSON.stringify({
                    product_id: 1,
                    quantity: 1
                })
            });
            
            const data = await response.json();
            
            if (response.ok) {
                showToast('Test berhasil! Produk ditambahkan ke keranjang.', 'success');
                if (window.updateCartCount) {
                    window.updateCartCount();
                }
            } else {
                showToast(data.message || 'Test gagal', 'error');
            }
        } catch (error) {
            showToast('Terjadi kesalahan jaringan', 'error');
        }
    }
</script>
@endpush
