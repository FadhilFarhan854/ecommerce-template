@extends('layouts.app')

@section('title', 'Detail Pesanan #' . $order->id)

@section('content')
<!-- Custom Styles untuk komponen khusus -->
<style>
.rating-stars {
    direction: rtl;
    unicode-bidi: bidi-override;
}
.rating-stars input {
    display: none;
}
.rating-stars label {
    color: #ddd;
    cursor: pointer;
    font-size: 1.5rem;
    padding: 0 2px;
}
.rating-stars input:checked ~ label,
.rating-stars label:hover,
.rating-stars label:hover ~ label {
    color: #ffc107;
}
.rating-stars input:checked + label {
    color: #ffc107;
}
</style>

<div class="container mx-auto px-4 py-8">
    <!-- Header -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h1 class="text-2xl font-bold text-gray-900">Detail Pesanan #{{ $order->id }}</h1>
                <p class="text-gray-600 mt-1">Dibuat pada {{ $order->created_at->format('d M Y, H:i') }}</p>
            </div>
            <div class="text-right">
                <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium
                    @if($order->status === 'unpaid') bg-yellow-100 text-yellow-800
                    @elseif($order->status === 'paid') bg-blue-100 text-blue-800
                    @elseif($order->status === 'sending') bg-purple-100 text-purple-800
                    @elseif($order->status === 'finished') bg-green-100 text-green-800
                    @elseif($order->status === 'cancelled') bg-red-100 text-red-800
                    @else bg-gray-100 text-gray-800
                    @endif">
                    {{ $order->status_label }}
                </span>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Order Items -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow">
                <div class="p-6 border-b">
                    <h2 class="text-lg font-semibold text-gray-900">Produk yang Dipesan</h2>
                </div>
                
                <div class="divide-y divide-gray-200">
                    @foreach($order->items as $item)
                    <div class="p-6 mb-4 bg-gray-50 rounded-xl shadow-sm">
                        <div class="flex flex-col md:flex-row md:items-center md:space-x-6">
                            <!-- Product Image -->
                            <div class="flex-shrink-0 w-20 h-20 bg-gray-100 rounded-lg overflow-hidden mb-3 md:mb-0">
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
                            <div class="flex-grow">
                                <h3 class="font-semibold text-lg text-gray-900 mb-1">{{ $item->product->name }}</h3>
                                <p class="text-sm text-gray-500 mb-2">{{ $item->product->category->name ?? 'Tanpa Kategori' }}</p>
                                <div class="flex items-center space-x-2 mb-2">
                                    <span class="text-sm text-gray-600">Qty: <b>{{ $item->quantity }}</b></span>
                                    <span class="font-medium text-gray-900">@ Rp {{ number_format($item->price, 0, ',', '.') }}</span>
                                </div>
                                <div class="text-right md:text-left">
                                    <span class="font-bold text-green-700">Total: Rp {{ number_format($item->quantity * $item->price, 0, ',', '.') }}</span>
                                </div>
                            </div>
                        </div>
                        <!-- Form Review -->
                        @if($order->status === 'finished')
                        <div class="mt-6">
                            <form action="{{ route('reviews.store') }}" method="POST" class="bg-white p-4 rounded-lg border border-green-200 shadow-sm">
                                @csrf
                                <input type="hidden" name="order_id" value="{{ $order->id }}">
                                <input type="hidden" name="product_id" value="{{ $item->product->id }}">
                                <div class="mb-3">
                                    <label for="review_{{ $item->product->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Review Produk</label>
                                    <textarea name="review" id="review_{{ $item->product->id }}" rows="2" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-green-500 focus:border-green-500" required placeholder="Tulis ulasan Anda..."></textarea>
                                </div>
                                <div class="mb-3">
                                    <label for="rating_{{ $item->product->id }}" class="block text-sm font-semibold text-gray-700 mb-1">Rating</label>
                                    <select name="rating" id="rating_{{ $item->product->id }}" class="mt-1 block w-full border-gray-300 rounded-md focus:ring-yellow-500 focus:border-yellow-500" required>
                                        <option value="">Pilih rating</option>
                                        @for($i=1; $i<=5; $i++)
                                            <option value="{{ $i }}">{{ $i }} ⭐</option>
                                        @endfor
                                    </select>
                                </div>
                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-lg hover:bg-green-700 transition font-semibold w-full">Kirim Review</button>
                            </form>
                        </div>
                        @endif
                        <!-- End Form Review -->
                    </div>
                    @endforeach
                </div>
            </div>
        </div>

        <!-- Order Summary & Info -->
        <div class="space-y-6">
            <!-- Order Summary -->
            <div class="bg-white rounded-lg shadow p-6">
                <h2 class="text-lg font-semibold text-gray-900 mb-4">Ringkasan Pesanan</h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Subtotal</span>
                            <span class="font-medium">Rp {{ number_format($order->items->sum(function($item) { return $item->quantity * $item->price; }), 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Total Berat</span>
                            <span class="font-medium">{{ number_format($order->total_weight, 2) }} kg</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Pajak (11%)</span>
                            <span class="font-medium">Rp {{ number_format($order->items->sum(function($item) { return $item->quantity * $item->price; }) * 0.11, 0, ',', '.') }}</span>
                        </div>
                        <div class="flex justify-between">
                            <span class="text-gray-600">Ongkos Kirim</span>
                            <span class="font-medium">Rp 15.000</span>
                        </div>
                        <hr class="border-gray-200 my-3">
                        <div class="flex justify-between text-lg font-semibold">
                            <span class="text-gray-900">Total</span>
                            <span class="text-green-600">Rp {{ number_format($order->total_price, 0, ',', '.') }}</span>
                        </div>
                    </div>
                </div>

                <!-- Shipping Information -->
                <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-truck mr-2 text-purple-500"></i>
                        Informasi Pengiriman
                    </h2>
                    
                    <div class="text-sm text-gray-600 whitespace-pre-line bg-gray-50 p-4 rounded-lg">
                        <i class="fas fa-map-marker-alt mr-2 text-red-500"></i>
                        {{ $order->shipping_address }}
                    </div>
                </div>

                <!-- Payment Information -->
                <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-credit-card mr-2 text-indigo-500"></i>
                        Informasi Pembayaran
                    </h2>
                    
                    <div class="space-y-3">
                        <div class="flex justify-between">
                            <span class="text-gray-600">Metode Pembayaran</span>
                            <span class="font-medium">{{ ucfirst(str_replace('_', ' ', $order->payment_method)) }}</span>
                        </div>
                        <div class="flex justify-between items-center">
                            <span class="text-gray-600">Status Pembayaran</span>
                            <span class="font-medium
                                @if($order->payment_status === 'unpaid') text-yellow-600
                                @elseif($order->payment_status === 'paid') text-green-600
                                @elseif($order->payment_status === 'failed') text-red-600
                                @else text-gray-600
                                @endif">
                                @if($order->payment_status === 'unpaid')
                                    <i class="fas fa-clock mr-1"></i>
                                @elseif($order->payment_status === 'paid')
                                    <i class="fas fa-check-circle mr-1"></i>
                                @elseif($order->payment_status === 'failed')
                                    <i class="fas fa-times-circle mr-1"></i>
                                @endif
                                {{ $order->payment_status_label }}
                            </span>
                        </div>
                    </div>
                </div>

                <!-- Review Section (Only for finished orders) -->
                @if($order->canReviewProducts())
                <div class="bg-white rounded-xl shadow-sm p-6 fade-in">
                    <h2 class="text-lg font-semibold text-gray-900 mb-4 flex items-center">
                        <i class="fas fa-star mr-2 text-yellow-500"></i>
                        Review Products
                    </h2>
                    
                    <div class="space-y-4">
                        @foreach($order->items as $item)
                            @php
                                $existingReview = \App\Models\Review::where('user_id', Auth::id())
                                    ->where('product_id', $item->product_id)
                                    ->first();
                            @endphp
                            
                            <div class="border border-gray-200 rounded-lg p-4">
                                <div class="flex items-center space-x-4 mb-3">
                                    @if($item->product->images && count($item->product->images) > 0)
                                        <img src="{{ Storage::url($item->product->images[0]) }}" 
                                             alt="{{ $item->product->name }}" 
                                             class="w-16 h-16 object-cover rounded-lg">
                                    @else
                                        <div class="w-16 h-16 bg-gray-200 rounded-lg flex items-center justify-center">
                                            <i class="fas fa-image text-gray-400"></i>
                                        </div>
                                    @endif
                                    
                                    <div class="flex-1">
                                        <h3 class="font-medium text-gray-900">{{ $item->product->name }}</h3>
                                        <p class="text-sm text-gray-600">Qty: {{ $item->quantity }}</p>
                                    </div>
                                </div>
                                
                                @if($existingReview)
                                    <div class="bg-green-50 border border-green-200 rounded-lg p-3">
                                        <div class="flex items-center space-x-2 mb-2">
                                            <span class="text-sm font-medium text-green-800">Your Review:</span>
                                            <div class="flex">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $existingReview->rating ? 'text-yellow-400' : 'text-gray-300' }}" 
                                                         fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"/>
                                                    </svg>
                                                @endfor
                                            </div>
                                        </div>
                                        <p class="text-sm text-green-700">{{ $existingReview->review }}</p>
                                    </div>
                                @else
                                    <form action="{{ route('reviews.store') }}" method="POST" class="space-y-3">
                                        @csrf
                                        <input type="hidden" name="order_id" value="{{ $order->id }}">
                                        <input type="hidden" name="product_id" value="{{ $item->product_id }}">
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                            <div class="flex space-x-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <button type="button" 
                                                            onclick="setRating('{{ $item->product_id }}', {{ $i }})"
                                                            class="rating-star-{{ $item->product_id }} text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                                        ★
                                                    </button>
                                                @endfor
                                            </div>
                                            <input type="hidden" name="rating" id="rating-{{ $item->product_id }}" required>
                                        </div>
                                        
                                        <div>
                                            <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                            <textarea name="review" rows="3" required
                                                      class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                                      placeholder="Share your experience with this product..."></textarea>
                                        </div>
                                        
                                        <button type="submit" 
                                                class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition duration-200 text-sm font-medium">
                                            Submit Review
                                        </button>
                                    </form>
                                @endif
                            </div>
                        @endforeach
                    </div>
                </div>
                @endif
            </div>
            
            <!-- Sidebar -->
            <div class="lg:col-span-1">

                <!-- Actions -->
                <div class="space-y-3 sticky top-4">
                    <a href="{{ route('orders.history') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-gray-600 text-white rounded-lg hover:bg-gray-700 transition shadow-sm">
                        <i class="fas fa-list mr-2"></i> Lihat Semua Pesanan
                    </a>
                    
                    @if($order->canRetryPayment())
                        <button onclick="retryPayment('{{ $order->id }}')"
                                class="w-full px-4 py-3 bg-yellow-600 text-white rounded-lg hover:bg-yellow-700 transition shadow-sm flex items-center justify-center">
                            <i class="fas fa-credit-card mr-2"></i> Retry Payment
                        </button>
                    @elseif($order->canBeFinished())
                        <form action="{{ route('orders.mark-finished', $order) }}" method="POST">
                            @csrf
                            <button type="submit" 
                                    onclick="return confirm('Have you received this order?')"
                                    class="w-full px-4 py-3 bg-green-600 text-white rounded-lg hover:bg-green-700 transition shadow-sm flex items-center justify-center">
                                <i class="fas fa-check-circle mr-2"></i> Mark as Finished
                            </button>
                        </form>
                    @endif
                    
                    <a href="{{ route('products.catalog') }}" 
                       class="w-full inline-flex items-center justify-center px-4 py-3 bg-blue-600 text-white rounded-lg hover:bg-blue-700 transition shadow-sm">
                        <i class="fas fa-shopping-cart mr-2"></i> Lanjut Belanja
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Success Alert -->
@if(session('success'))
<div class="fixed top-4 right-4 z-50 max-w-sm w-full">
    <div class="bg-white border-l-4 border-green-500 rounded-lg shadow-lg p-4">
        <div class="flex items-center">
            <div class="flex-shrink-0">
                <svg class="w-6 h-6 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
            </div>
            <div class="ml-3">
                <p class="text-sm font-medium text-gray-900">{{ session('success') }}</p>
            </div>
        </div>
    </div>
</div>
@endif

@endsection

@section('scripts')
<script>
function cancelOrder(orderId) {
    if (!confirm('Apakah Anda yakin ingin membatalkan pesanan ini?')) {
        return;
    }
    
    fetch(`/orders/${orderId}`, {
        method: 'PUT',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({
            status: 'cancelled'
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert('Pesanan berhasil dibatalkan');
            location.reload();
        } else {
            alert('Gagal membatalkan pesanan: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan');
    });
}

// Retry payment function
async function retryPayment(orderId) {
    try {
        const response = await fetch(`/orders/${orderId}/retry-payment`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        });
        
        const data = await response.json();
        
        if (data.success && data.snap_token) {
            // Open Midtrans payment popup
            if (typeof window.snap !== 'undefined') {
                window.snap.pay(data.snap_token, {
                    onSuccess: function(result) {
                        console.log('Payment success:', result);
                        alert('Pembayaran berhasil!');
                        location.reload();
                    },
                    onPending: function(result) {
                        console.log('Payment pending:', result);
                        alert('Pembayaran pending. Silakan selesaikan pembayaran Anda.');
                        location.reload();
                    },
                    onError: function(result) {
                        console.log('Payment error:', result);
                        alert('Pembayaran gagal. Silakan coba lagi.');
                    },
                    onClose: function() {
                        console.log('Payment popup closed');
                        alert('Anda menutup popup pembayaran. Anda dapat mencoba lagi kapan saja.');
                    }
                });
            } else {
                console.error('Snap.js not loaded');
                alert('Payment gateway sedang loading. Silakan coba lagi dalam beberapa detik.');
                
                // Load Snap.js dynamically
                const script = document.createElement('script');
                script.src = 'https://app.sandbox.midtrans.com/snap/snap.js';
                script.setAttribute('data-client-key', '{{ config("midtrans.client_key") }}');
                script.onload = function() {
                    console.log('Snap.js loaded, retrying payment...');
                    retryPayment(orderId);
                };
                document.head.appendChild(script);
            }
        } else {
            alert(data.message || 'Gagal membuat token pembayaran');
        }
    } catch (error) {
        console.error('Retry payment error:', error);
        alert('Terjadi kesalahan. Silakan coba lagi.');
    }
}
</script>

{{-- Load Midtrans Snap.js --}}
<script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="{{ config('midtrans.client_key') }}"></script>
}

// Auto hide success alert
setTimeout(() => {
    const alert = document.querySelector('.fixed.top-4.right-4');
    if (alert) {
        alert.style.display = 'none';
    }
}, 5000);

// Rating system for reviews
function setRating(productId, rating) {
    // Update hidden input value
    document.getElementById('rating-' + productId).value = rating;
    
    // Update star display
    const stars = document.querySelectorAll('.rating-star-' + productId);
    stars.forEach((star, index) => {
        if (index < rating) {
            star.classList.remove('text-gray-300');
            star.classList.add('text-yellow-400');
        } else {
            star.classList.remove('text-yellow-400');
            star.classList.add('text-gray-300');
        }
    });
}
</script>
@endsection