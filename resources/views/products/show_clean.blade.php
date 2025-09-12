@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-50">
    <div class="container mx-auto px-4 py-6">
        <!-- Compact Breadcrumb -->
        <nav class="text-sm text-gray-600 mb-4">
            <ol class="flex items-center space-x-2">
                <li><a href="{{ route('home') }}" class="hover:text-blue-600">Home</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('products.index') }}" class="hover:text-blue-600">Produk</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li><a href="{{ route('products.category', $product->category) }}" class="hover:text-blue-600">{{ $product->category->name }}</a></li>
                <li><span class="text-gray-400">/</span></li>
                <li class="text-gray-900">{{ $product->name }}</li>
            </ol>
        </nav>

        <!-- Main Product Content -->
        <div class="bg-white rounded-lg shadow-sm overflow-hidden">
            <div class="grid grid-cols-1 lg:grid-cols-12 gap-6 p-6">
                <!-- Product Images -->
                <div class="lg:col-span-5">
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div class="aspect-square bg-gray-100 rounded-lg overflow-hidden">
                            @if($product->images && $product->images->count() > 0)
                                <img id="mainImage" src="{{ asset('storage/' . $product->images->first()->file_path) }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover">
                            @else
                                <div class="w-full h-full flex items-center justify-center text-gray-400">
                                    <svg class="w-16 h-16" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                </div>
                            @endif
                        </div>

                        <!-- Thumbnail Images -->
                        @if($product->images && $product->images->count() > 1)
                            <div class="grid grid-cols-4 gap-2">
                                @foreach($product->images as $image)
                                    <button onclick="changeMainImage('{{ asset('storage/' . $image->file_path) }}')"
                                            class="aspect-square bg-gray-100 rounded-lg overflow-hidden border-2 border-transparent hover:border-blue-500 transition-colors">
                                        <img src="{{ asset('storage/' . $image->file_path) }}" 
                                             alt="{{ $product->name }}" 
                                             class="w-full h-full object-cover">
                                    </button>
                                @endforeach
                            </div>
                        @endif
                    </div>
                </div>

                <!-- Product Information -->
                <div class="lg:col-span-7">
                    <!-- Product Title -->
                    <h1 class="text-xl font-medium text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    <!-- Rating and Reviews -->
                    <div class="flex items-center space-x-4 mb-4">
                        @if($product->reviews && $product->reviews->count() > 0)
                            <div class="flex items-center">
                                @for($i = 1; $i <= 5; $i++)
                                    <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                    </svg>
                                @endfor
                                <span class="text-sm text-gray-600 ml-1">{{ number_format($product->average_rating, 1) }}</span>
                            </div>
                            <span class="text-gray-300">|</span>
                            <span class="text-sm text-gray-600">{{ $product->reviews->count() }} ulasan</span>
                        @else
                            <span class="text-sm text-gray-500">Belum ada ulasan</span>
                        @endif
                    </div>

                    <!-- Price Section -->
                    <div class="mb-6">
                        @if($product->discount && $product->discount->is_active)
                            <div class="flex items-center space-x-2 mb-1">
                                <span class="text-lg text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded">{{ $product->discount->percentage }}%</span>
                            </div>
                            <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</div>
                        @else
                            <div class="text-2xl font-bold text-orange-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        @endif
                    </div>

                    <!-- Stock Info -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Stok:</span>
                            <span class="text-sm font-medium {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                {{ $product->stock > 0 ? $product->stock . ' tersedia' : 'Habis' }}
                            </span>
                        </div>
                    </div>

                    <!-- Category Info -->
                    <div class="mb-6">
                        <div class="flex items-center space-x-2">
                            <span class="text-sm text-gray-600">Kategori:</span>
                            <a href="{{ route('products.category', $product->category) }}" 
                               class="text-sm font-medium text-blue-600 hover:text-blue-800">{{ $product->category->name }}</a>
                        </div>
                    </div>

                    <!-- Actions -->
                    @auth
                        @if(auth()->user()->is_admin)
                            <div class="flex space-x-3">
                                <a href="{{ route('products.edit', $product) }}" 
                                   class="flex-1 bg-blue-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-blue-700 transition-colors text-center text-sm">
                                    Edit Produk
                                </a>
                                <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                            class="w-full bg-red-600 text-white px-4 py-3 rounded-lg font-semibold hover:bg-red-700 transition-colors text-sm">
                                        Hapus Produk
                                    </button>
                                </form>
                            </div>
                        @else
                            @if($product->stock > 0)
                                <div class="space-y-4">
                                    <!-- Quantity Selector -->
                                    <div class="flex items-center space-x-4">
                                        <span class="text-sm text-gray-600">Jumlah:</span>
                                        <div class="flex items-center border border-gray-300 rounded-lg">
                                            <button type="button" 
                                                    onclick="decreaseQuantity()" 
                                                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                                </svg>
                                            </button>
                                            <input type="number" 
                                                   id="quantity" 
                                                   value="1" 
                                                   min="1" 
                                                   max="{{ $product->stock }}" 
                                                   class="w-16 px-3 py-2 text-center border-0 focus:ring-0 text-sm font-medium">
                                            <button type="button" 
                                                    onclick="increaseQuantity()" 
                                                    class="p-2 text-gray-600 hover:text-blue-600 hover:bg-blue-50 transition-colors">
                                                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                                </svg>
                                            </button>
                                        </div>
                                    </div>
                                    
                                    <!-- Add to Cart Button -->
                                    <button onclick="addToCart({{ $product->id }})" 
                                            class="w-full bg-gradient-to-r from-blue-600 to-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-green-700 transition-all duration-300 flex items-center justify-center text-sm">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 4M7 13l2.5 4m6 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                            @else
                                <button disabled class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center text-sm">
                                    <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Stok Habis
                                </button>
                            @endif
                        @endif
                    @else
                        <a href="{{ route('login') }}" 
                           class="w-full bg-gradient-to-r from-blue-600 to-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:from-blue-700 hover:to-green-700 transition-all duration-300 flex items-center justify-center text-sm">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                            </svg>
                            Login untuk Membeli
                        </a>
                    @endauth
                </div>
            </div>
        </div>

        <!-- Product Description and Details -->
        <div class="bg-white rounded-lg shadow-sm mt-6">
            <div class="p-6">
                <!-- Tab Navigation -->
                <div class="border-b border-gray-200 mb-6">
                    <nav class="flex space-x-8">
                        <button onclick="showTab('description')" 
                            class="tab-button py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm" 
                            id="description-tab">
                            Deskripsi
                        </button>
                        <button onclick="showTab('specifications')" 
                            class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" 
                            id="specifications-tab">
                            Spesifikasi
                        </button>
                        <button onclick="showTab('reviews')" 
                            class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm" 
                            id="reviews-tab">
                            Ulasan ({{ $product->reviews->count() }})
                        </button>
                    </nav>
                </div>

                <!-- Tab Content -->
                <div id="description-content" class="tab-content">
                    <div class="prose max-w-none">
                        <p class="text-gray-700 leading-relaxed">{{ $product->description ?: 'Deskripsi produk belum tersedia.' }}</p>
                    </div>
                </div>

                <div id="specifications-content" class="tab-content hidden">
                    <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                        <div class="bg-gray-50 p-4 rounded-lg">
                            <h4 class="font-medium text-gray-900 mb-2">Detail Produk</h4>
                            <dl class="space-y-2">
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Kategori:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $product->category->name }}</dd>
                                </div>
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Stok:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $product->stock }} unit</dd>
                                </div>
                                @if($product->weight)
                                <div class="flex justify-between">
                                    <dt class="text-sm text-gray-600">Berat:</dt>
                                    <dd class="text-sm font-medium text-gray-900">{{ $product->weight }} gram</dd>
                                </div>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>

                <div id="reviews-content" class="tab-content hidden">
                    @if($product->reviews && $product->reviews->count() > 0)
                        <div class="space-y-4">
                            @foreach($product->reviews as $review)
                                <div class="border-b border-gray-200 pb-4">
                                    <div class="flex items-center justify-between mb-2">
                                        <div class="flex items-center space-x-2">
                                            <div class="w-8 h-8 bg-blue-600 rounded-full flex items-center justify-center text-white text-sm font-medium">
                                                {{ strtoupper(substr($review->user->name, 0, 1)) }}
                                            </div>
                                            <span class="font-medium text-gray-900">{{ $review->user->name }}</span>
                                        </div>
                                        <div class="flex items-center">
                                            @for($i = 1; $i <= 5; $i++)
                                                <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                </svg>
                                            @endfor
                                        </div>
                                    </div>
                                    <p class="text-gray-700 text-sm">{{ $review->comment }}</p>
                                    <p class="text-xs text-gray-500 mt-2">{{ $review->created_at->format('d M Y') }}</p>
                                </div>
                            @endforeach
                        </div>
                    @else
                        <div class="text-center py-8">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <p class="text-gray-500">Belum ada ulasan untuk produk ini</p>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

<script>
    // Tab functionality
    function showTab(tabName) {
        // Hide all tab contents
        document.querySelectorAll('.tab-content').forEach(content => {
            content.classList.add('hidden');
        });
        
        // Show selected tab content
        document.getElementById(tabName + '-content').classList.remove('hidden');
        
        // Update tab buttons
        document.querySelectorAll('.tab-button').forEach(button => {
            button.classList.remove('border-blue-500', 'text-blue-600');
            button.classList.add('border-transparent', 'text-gray-500');
        });
        
        // Activate selected tab button
        const activeTab = document.getElementById(tabName + '-tab');
        activeTab.classList.remove('border-transparent', 'text-gray-500');
        activeTab.classList.add('border-blue-500', 'text-blue-600');
    }

    // Image gallery
    function changeMainImage(src) {
        document.getElementById('mainImage').src = src;
    }

    // Quantity controls
    function increaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        const maxValue = parseInt(quantityInput.max);
        
        if (currentValue < maxValue) {
            quantityInput.value = currentValue + 1;
        }
    }

    function decreaseQuantity() {
        const quantityInput = document.getElementById('quantity');
        const currentValue = parseInt(quantityInput.value);
        
        if (currentValue > 1) {
            quantityInput.value = currentValue - 1;
        }
    }

    // Add to cart function
    function addToCart(productId) {
        const quantity = document.getElementById('quantity').value;
        
        fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            },
            body: JSON.stringify({
                product_id: productId,
                quantity: quantity
            })
        })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Produk berhasil ditambahkan ke keranjang!');
                // Update cart count if available
                updateCartCount();
            } else {
                alert('Gagal menambahkan produk ke keranjang');
            }
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Terjadi kesalahan');
        });
    }

    // Update cart count in navigation
    function updateCartCount() {
        fetch('{{ route("cart.count") }}')
            .then(response => response.json())
            .then(data => {
                const cartBadge = document.querySelector('.cart-count');
                if (cartBadge) {
                    cartBadge.textContent = data.count;
                }
            })
            .catch(error => console.error('Error updating cart count:', error));
    }
</script>

@endsection
