@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<style>
    /* Modern Color Scheme - Blue & Green Theme */
    .accent-blue { color: #3b82f6; }
    .accent-green { color: #10b981; }
    .accent-blue-bg { background-color: #3b82f6; }
    .accent-green-bg { background-color: #10b981; }
    
    /* Gradient Backgrounds */
    .bg-primary-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
    }
    .btn-primary-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        transition: all 0.3s ease;
    }
    .btn-primary-gradient:hover {
        background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
        transform: translateY(-2px);
        box-shadow: 0 10px 25px rgba(59, 130, 246, 0.3);
    }
    
    /* Star Rating */
    .star {
        color: #d1d5db;
        font-size: 1.2rem;
        transition: color 0.2s;
    }
    .star.filled {
        color: #fbbf24;
    }
    .star.half {
        background: linear-gradient(90deg, #fbbf24 50%, #d1d5db 50%);
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
    }
    
    /* Modern Card Effects */
    .product-card {
        background: white;
        transition: all 0.3s ease;
        border: 1px solid rgba(59, 130, 246, 0.1);
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 40px rgba(59, 130, 246, 0.15);
        border-color: rgba(59, 130, 246, 0.3);
    }
    
    /* Image Effects */
    .image-zoom-modal {
        backdrop-filter: blur(8px);
    }
    .image-container {
        background: linear-gradient(135deg, rgba(59, 130, 246, 0.05) 0%, rgba(16, 185, 129, 0.05) 100%);
    }
    
    /* Quantity Input */
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .quantity-input[type=number] {
        -moz-appearance: textfield;
    }
    
    /* Modern Breadcrumb */
    .breadcrumb-item {
        transition: all 0.3s ease;
    }
    .breadcrumb-item:hover {
        color: #3b82f6;
        transform: translateX(2px);
    }
    
    /* Review Cards */
    .review-card {
        background: white;
        border: 1px solid rgba(59, 130, 246, 0.1);
        transition: all 0.3s ease;
    }
    .review-card:hover {
        border-color: rgba(59, 130, 246, 0.3);
        box-shadow: 0 8px 25px rgba(59, 130, 246, 0.1);
    }
    
    /* Gradient Text */
    .gradient-text {
        background:  #2f2f2f;
        background-clip: text;
        -webkit-background-clip: text;
        color: transparent;
    }
    
    /* Animation Classes */
    .fade-in {
        animation: fadeIn 0.6s ease-out;
    }
    
    @keyframes fadeIn {
        from { opacity: 0; transform: translateY(20px); }
        to { opacity: 1; transform: translateY(0); }
    }
    
    /* Custom Scrollbar */
    .custom-scrollbar::-webkit-scrollbar {
        width: 8px;
        height: 8px;
    }
    .custom-scrollbar::-webkit-scrollbar-track {
        background: #f1f5f9;
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        border-radius: 4px;
    }
    .custom-scrollbar::-webkit-scrollbar-thumb:hover {
        background: linear-gradient(135deg, #2563eb 0%, #059669 100%);
    }
</style>
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')
<div class="bg-gray-50 min-h-screen">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-4">
        <!-- Compact Breadcrumb -->
        <nav class="flex mb-4" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 text-sm">
                <li class="inline-flex items-center">
                    <a href="{{ route('products.catalog') }}" class="text-gray-500 hover:text-blue-600">
                        Beranda
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <a href="{{ route('products.catalog') }}" class="ml-1 text-gray-500 hover:text-blue-600">{{ $product->category->name ?? 'Parfum' }}</a>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-4 h-4 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-gray-700 text-sm">{{ Str::limit($product->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <!-- Main Product Section -->
        <div class="bg-white rounded-lg shadow-sm">
            <div class="lg:grid lg:grid-cols-12 lg:gap-8 p-6">
                <!-- Product Images - Tokopedia Style -->
                <div class="lg:col-span-5">
                    <!-- Main Image -->
                    <div class="aspect-w-1 aspect-h-1 bg-gray-100 rounded-lg overflow-hidden mb-4">
                        @if($product->images && $product->images->count() > 0)
                            <img id="mainImage" 
                                 src="{{ $product->images->first()->url }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-80 object-cover cursor-zoom-in">
                        @else
                            <div class="w-full h-80 flex items-center justify-center text-gray-400">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-sm text-gray-500">Gambar tidak tersedia</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnail Images -->
                    @if($product->images && $product->images->count() > 1)
                        <div class="flex space-x-2 overflow-x-auto">
                            @foreach($product->images as $index => $image)
                                <div class="flex-shrink-0">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $product->name }}"
                                         onclick="changeMainImage('{{ $image->url }}', {{ $index + 1 }}, this)"
                                         class="w-16 h-16 object-cover rounded-md cursor-pointer border-2 {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }} hover:border-blue-400">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Information - Tokopedia Style -->
                <div class="lg:col-span-7 mt-6 lg:mt-0">
                    <!-- Product Title -->
                    <h1 class="text-xl font-medium text-gray-900 mb-2">{{ $product->name }}</h1>
                    
                    <!-- Rating and Sold -->
                    <div class="flex items-center space-x-4 mb-4">
                        <div class="flex items-center">
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

                    <!-- Product Title -->
                    <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>

                    <!-- Rating and Reviews -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center bg-gradient-to-r from-yellow-50 to-orange-50 px-3 py-2 rounded-xl border border-yellow-200">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <span class="star filled text-base">★</span>
                                @elseif($i == ceil($product->average_rating) && $product->average_rating - floor($product->average_rating) >= 0.5)
                                    <span class="star half text-base">★</span>
                                @else
                                    <span class="star text-base">☆</span>
                                @endif
                            @endfor
                            <span class="ml-2 text-sm font-bold text-gray-800">
                                {{ number_format($product->average_rating, 1) }}
                            </span>
                        </div>
                        <div class="text-gray-500">•</div>
                        <a href="#reviews" class="text-sm accent-blue hover:text-blue-700 font-semibold transition-colors duration-300 hover:underline">
                            {{ $product->review_count }} ulasan pelanggan
                        </a>
                    </div>

                    <!-- Price -->
                    <div class="mb-8">
                        <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-2xl p-4 border border-green-200">
                            <div class="flex items-baseline space-x-2">
                                <p class="text-3xl font-bold gradient-text">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                                <span class="text-sm text-gray-500">/ botol</span>
                            </div>
                            @if($product->stock > 0)
                                <div class="flex items-center mt-4">
                                    <div class="flex items-center text-green-600 bg-green-100 px-4 py-2 rounded-full">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-semibold">Stok tersedia ({{ $product->stock }} unit)</span>
                                    </div>
                                </div>
                            @else
                                <div class="flex items-center mt-4">
                                    <div class="flex items-center text-red-600 bg-red-100 px-4 py-2 rounded-full">
                                        <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                        </svg>
                                        <span class="font-semibold">Stok habis</span>
                                    </div>
                                </div>
                            @endif
                        </div>
                    </div>

                    <!-- Quantity Selector -->
                    @if($product->stock > 0)
                        <div class="mb-6">
                            <div class="flex items-center space-x-4">
                                <span class="text-sm text-gray-600">Kuantitas:</span>
                                <div class="flex items-center border border-gray-300 rounded">
                                    <button type="button" onclick="decreaseQuantity()" class="p-2 text-gray-600 hover:text-gray-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                        </svg>
                                    </button>
                                    <input type="number" 
                                           id="quantity" 
                                           value="1" 
                                           min="1" 
                                           max="{{ $product->stock }}"
                                           class="w-16 text-center border-0 focus:ring-0 text-sm">
                                    <button type="button" onclick="increaseQuantity()" class="p-2 text-gray-600 hover:text-gray-800">
                                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                    </button>
                                </div>
                                <span class="text-sm text-gray-500">Tersisa {{ $product->stock }} buah</span>
                            </div>
                        </div>
                    @endif

                    <!-- Action Buttons -->
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <!-- Admin Actions -->
                            <div class="mb-6">
                                <div class="bg-gray-50 rounded-lg p-4">
                                    <h4 class="text-sm font-medium text-gray-700 mb-3">Panel Admin</h4>
                                    <div class="flex space-x-2">
                                        <a href="{{ route('products.edit', $product) }}" 
                                           class="flex-1 bg-blue-600 text-white px-4 py-2 rounded text-center text-sm hover:bg-blue-700">
                                            Edit Produk
                                        </a>
                                        <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                                            @csrf
                                            @method('DELETE')
                                            <button type="submit" 
                                                class="w-full bg-red-600 text-white px-4 py-2 rounded text-sm hover:bg-red-700"
                                                onclick="return confirm('Hapus produk ini?')">
                                                Hapus
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Customer Actions -->
                            @if($product->stock > 0)
                                <div class="mb-6">
                                    <div class="flex space-x-3">
                                        <form action="{{ route('cart.store') }}" method="POST" class="flex-1">
                                            @csrf
                                            <input type="hidden" name="product_id" value="{{ $product->id }}">
                                            <input type="hidden" name="quantity" id="cartQuantity" value="1">
                                            <button type="submit" 
                                                class="w-full bg-blue-600 text-white py-3 px-4 rounded-lg font-medium hover:bg-blue-700 transition duration-200">
                                                + Keranjang
                                            </button>
                                        </form>
                                        <button type="button" 
                                            class="bg-green-600 text-white py-3 px-6 rounded-lg font-medium hover:bg-green-700 transition duration-200">
                                            Beli Langsung
                                        </button>
                                    </div>
                                </div>
                            @else
                                <div class="mb-6">
                                    <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed">
                                        Stok Habis
                                    </button>
                                </div>
                            @endif
                        @endif
                    @else
                        <!-- Guest Actions -->
                        <div class="mb-6">
                            <div class="bg-gray-50 rounded-lg p-4 text-center">
                                <p class="text-sm text-gray-600 mb-3">Silakan masuk untuk berbelanja</p>
                                <div class="flex space-x-2">
                                    <a href="{{ route('login') }}" class="flex-1 bg-blue-600 text-white py-2 px-4 rounded text-sm text-center hover:bg-blue-700">
                                        Masuk
                                    </a>
                                    <a href="{{ route('register') }}" class="flex-1 bg-green-600 text-white py-2 px-4 rounded text-sm text-center hover:bg-green-700">
                                        Daftar
                                    </a>
                                </div>
                            </div>
                        </div>
                    @endauth
                                            </svg>
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
                    @endauth

                    <!-- Additional Info -->
                    <div class="grid grid-cols-2 gap-6 text-gray-600 pt-8 border-t border-gray-200">
                        @if($product->weight)
                            <div class="flex items-center bg-blue-50 rounded-2xl p-4">
                                <div class="bg-blue-100 p-3 rounded-xl mr-4">
                                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                    </svg>
                                </div>
                                <div>
                                    <p class="font-bold text-gray-800">Berat Produk</p>
                                    <p class="text-blue-600 font-semibold">{{ $product->weight }} gram</p>
                                </div>
                            </div>
                        @endif
                        <div class="flex items-center bg-green-50 rounded-2xl p-4">
                            <div class="bg-green-100 p-3 rounded-xl mr-4">
                                <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-bold text-gray-800">Tanggal Ditambahkan</p>
                                <p class="text-green-600 font-semibold">{{ $product->created_at->format('d M Y') }}</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="border-t border-gray-200 px-6 lg:px-8 py-8">
                <div class="max-w-4xl mx-auto">
                    <div class="text-center mb-6">
                        <h2 class="text-2xl font-bold gradient-text mb-3">Deskripsi Produk</h2>
                        <div class="w-20 h-1 bg-primary-gradient mx-auto rounded-full"></div>
                    </div>
                    <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-2xl p-6 border border-gray-100">
                        <div class="prose prose-blue max-w-none">
                            <p class="text-gray-700 leading-relaxed text-base text-center">{{ $product->description }}</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sales Trend Chart for Admin -->
            @auth
                @if(auth()->user()->role === 'admin')
                    <div class="border-t border-gray-200 px-6 lg:px-8 py-8">
                        <h2 class="text-2xl font-bold text-gray-900 mb-6">Trend Penjualan Produk</h2>
                        <div class="bg-white rounded-lg border border-gray-200 p-6">
                            <canvas id="salesChart" width="400" height="200"></canvas>
                        </div>
                        
                        <!-- Sales Statistics -->
                        <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mt-6">
                            <div class="bg-blue-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-blue-600">{{ $salesData['total_sold'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Total Terjual</div>
                            </div>
                            <div class="bg-green-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-green-600">Rp {{ number_format($salesData['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                                <div class="text-sm text-gray-600">Total Pendapatan</div>
                            </div>
                            <div class="bg-yellow-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-yellow-600">{{ $salesData['this_month'] ?? 0 }}</div>
                                <div class="text-sm text-gray-600">Bulan Ini</div>
                            </div>
                            <div class="bg-purple-50 rounded-lg p-4">
                                <div class="text-2xl font-bold text-purple-600">{{ number_format($salesData['avg_monthly'] ?? 0, 1) }}</div>
                                <div class="text-sm text-gray-600">Rata-rata/Bulan</div>
                            </div>
                        </div>
                    </div>
                @endif
            @endauth

            <!-- Reviews Section -->
            <div id="reviews" class="border-t border-gray-200 px-6 lg:px-8 py-8">
                <div class="max-w-6xl mx-auto">
                    <div class="text-center mb-8">
                        <h2 class="text-2xl font-bold gradient-text mb-3">Ulasan Pelanggan</h2>
                        <div class="w-20 h-1 bg-primary-gradient mx-auto rounded-full"></div>
                    </div>
                
                    @if($product->reviews->count() > 0)
                        <!-- Rating Summary -->
                        <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-2xl p-6 mb-8 border border-gray-100 shadow-lg">
                            <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8 space-y-6 lg:space-y-0">
                                <!-- Overall Rating -->
                                <div class="text-center lg:text-left">
                                    <div class="text-4xl font-bold gradient-text mb-3">
                                        {{ number_format($product->average_rating, 1) }}
                                    </div>
                                    <div class="flex justify-center lg:justify-start items-center mb-2">
                                        @for($i = 1; $i <= 5; $i++)
                                            @if($i <= floor($product->average_rating))
                                                <span class="star filled text-xl">★</span>
                                            @elseif($i == ceil($product->average_rating) && $product->average_rating - floor($product->average_rating) >= 0.5)
                                                <span class="star half text-3xl">★</span>
                                            @else
                                                <span class="star text-3xl">☆</span>
                                            @endif
                                        @endfor
                                    </div>
                                    <div class="text-lg text-gray-600 font-semibold">
                                        Berdasarkan {{ $product->review_count }} ulasan pelanggan
                                    </div>
                                </div>
                            
                                <!-- Rating Distribution -->
                                <div class="flex-1">
                                    @php
                                        $ratingDistribution = [];
                                        for($i = 5; $i >= 1; $i--) {
                                            $count = $product->reviews->where('rating', $i)->count();
                                            $percentage = $product->review_count > 0 ? ($count / $product->review_count) * 100 : 0;
                                            $ratingDistribution[$i] = ['count' => $count, 'percentage' => $percentage];
                                        }
                                    @endphp
                                
                                    @foreach($ratingDistribution as $rating => $data)
                                        <div class="flex items-center space-x-3 mb-2">
                                            <span class="text-sm font-bold text-gray-700 w-8">{{ $rating }}★</span>
                                            <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                                <div class="h-full bg-primary-gradient rounded-full transition-all duration-700" 
                                                     style="width: {{ $data['percentage'] }}%"></div>
                                            </div>
                                            <span class="text-sm text-gray-600 w-8 text-right font-semibold">{{ $data['count'] }}</span>
                                        </div>
                                    @endforeach
                                </div>
                            </div>
                        </div>

                        <!-- Individual Reviews -->
                        <div class="space-y-6">
                            @foreach($product->reviews->sortByDesc('created_at') as $review)
                                <div class="review-card rounded-2xl p-6 hover:shadow-xl transition-all duration-300">
                                    <div class="flex items-start justify-between mb-4">
                                        <div class="flex items-center space-x-4">
                                            <!-- User Avatar -->
                                            <div class="w-12 h-12 bg-primary-gradient rounded-full flex items-center justify-center text-white font-bold text-base shadow-lg">
                                                {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                            </div>
                                            <div>
                                                <h4 class="font-bold text-base text-gray-900">{{ $review->user->name ?? 'Pelanggan' }}</h4>
                                                <div class="flex items-center space-x-2 mt-1">
                                                    <div class="flex items-center bg-yellow-50 px-3 py-1 rounded-full border border-yellow-200">
                                                        @for($i = 1; $i <= 5; $i++)
                                                            @if($i <= $review->rating)
                                                                <span class="star filled text-lg">★</span>
                                                            @else
                                                                <span class="star text-lg">☆</span>
                                                            @endif
                                                        @endfor
                                                    </div>
                                                    <span class="text-gray-400">•</span>
                                                    <span class="text-gray-500 font-medium">
                                                        {{ $review->created_at->format('d F Y') }}
                                                    </span>
                                                </div>
                                            </div>
                                        </div>
                                    
                                        <!-- Admin Actions for Reviews -->
                                        @auth
                                            @if(auth()->user()->role === 'admin')
                                                <div class="flex space-x-3">
                                                    <button onclick="editReview({{ $review->id }}, '{{ addslashes($review->review) }}', {{ $review->rating }})" 
                                                            class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-sm font-bold rounded-xl hover:from-yellow-600 hover:to-orange-600 focus:outline-none focus:ring-4 focus:ring-yellow-300 transition-all duration-300 shadow-lg hover:scale-105">
                                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                        </svg>
                                                        Edit
                                                    </button>
                                                    <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                                        @csrf
                                                        @method('DELETE')
                                                        <button type="submit" 
                                                                onclick="return confirm('Yakin ingin menghapus review ini?')"
                                                                class="inline-flex items-center px-4 py-2 bg-gradient-to-r from-red-500 to-pink-500 text-white text-sm font-bold rounded-xl hover:from-red-600 hover:to-pink-600 focus:outline-none focus:ring-4 focus:ring-red-300 transition-all duration-300 shadow-lg hover:scale-105">
                                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                            </svg>
                                                            Hapus
                                                        </button>
                                                    </form>
                                                </div>
                                            @endif
                                        @endauth
                                    </div>
                                
                                    <div class="text-gray-700 leading-relaxed text-base" id="review-text-{{ $review->id }}">
                                        "{{ $review->review }}"
                                    </div>
                                
                                <!-- Edit Form (Hidden by default) -->
                                <div id="edit-form-{{ $review->id }}" class="hidden mt-4">
                                    <form action="{{ route('admin.reviews.update', $review) }}" method="POST">
                                        @csrf
                                        @method('PUT')
                                        <div class="space-y-4">
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Rating</label>
                                                <div class="flex space-x-2">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        <button type="button" onclick="setRating({{ $review->id }}, {{ $i }})" 
                                                                class="rating-star-{{ $review->id }} text-2xl text-gray-300 hover:text-yellow-400 transition-colors">
                                                            ★
                                                        </button>
                                                    @endfor
                                                </div>
                                                <input type="hidden" name="rating" id="rating-{{ $review->id }}" value="{{ $review->rating }}">
                                            </div>
                                            <div>
                                                <label class="block text-sm font-medium text-gray-700 mb-2">Review</label>
                                                <textarea name="review" rows="3" class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $review->review }}</textarea>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button type="submit" class="px-4 py-2 bg-green-600 text-white rounded-md hover:bg-green-700 transition-colors">
                                                    Simpan
                                                </button>
                                                <button type="button" onclick="cancelEdit({{ $review->id }})" class="px-4 py-2 bg-gray-600 text-white rounded-md hover:bg-gray-700 transition-colors">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Load More Reviews (if there are many) -->
                    @if($product->reviews->count() > 5)
                        <div class="text-center mt-12">
                            <button class="inline-flex items-center px-8 py-4 border-2 border-blue-200 text-lg font-bold rounded-2xl accent-blue bg-white hover:bg-blue-50 focus:outline-none focus:ring-4 focus:ring-blue-200 transition-all duration-300 hover:scale-105 shadow-lg">
                                Lihat Semua Ulasan
                                <svg class="ml-3 w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-16">
                        <div class="bg-gradient-to-br from-blue-50 to-green-50 rounded-3xl p-12 max-w-2xl mx-auto border border-gray-100">
                            <div class="text-8xl mb-6">📝</div>
                            <h3 class="text-3xl font-bold text-gray-900 mb-4">Belum Ada Ulasan</h3>
                            <p class="text-gray-600 mb-8 text-lg leading-relaxed">Jadilah yang pertama memberikan ulasan untuk produk parfum premium ini dan bantu pelanggan lain membuat keputusan!</p>
                            @auth
                                <button class="inline-flex items-center px-8 py-4 btn-primary-gradient text-white font-bold rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 hover:scale-105 shadow-lg">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                    </svg>
                                    Tulis Ulasan Pertama
                                </button>
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-8 py-4 btn-primary-gradient text-white font-bold rounded-2xl focus:outline-none focus:ring-4 focus:ring-blue-300 transition-all duration-300 hover:scale-105 shadow-lg">
                                    <svg class="w-6 h-6 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                    </svg>
                                    Login untuk Menulis Ulasan
                                </a>
                            @endif
                        </div>
                    </div>
                @endif
                </div>
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <div class="text-center mb-8">
                    <h2 class="text-2xl font-bold gradient-text mb-3">Produk Serupa</h2>
                    <div class="w-20 h-1 bg-primary-gradient mx-auto rounded-full"></div>
                    <p class="text-gray-600 mt-3 text-base">Parfum premium lainnya yang mungkin Anda sukai</p>
                </div>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="product-card rounded-2xl overflow-hidden shadow-lg hover:shadow-2xl">
                            <div class="aspect-w-1 aspect-h-1 bg-gradient-to-br from-gray-100 to-gray-200 relative overflow-hidden">
                                @if($relatedProduct->images && $relatedProduct->images->count() > 0)
                                    <img src="{{ $relatedProduct->images->first()->url }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-48 object-cover transition-transform duration-500 hover:scale-110">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center text-gray-400">
                                        <div class="text-center">
                                            <div class="w-12 h-12 bg-gradient-to-br from-blue-100 to-green-100 rounded-full flex items-center justify-center mx-auto mb-2">
                                                <svg class="w-6 h-6 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                                </svg>
                                            </div>
                                            <span class="text-xs font-medium">Parfum Premium</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Quick View Overlay -->
                                <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                                    <a href="{{ route('products.show-detail', $relatedProduct) }}" 
                                       class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </a>
                                </div>
                            </div>
                            <div class="p-4">
                                <!-- Category Tag -->
                                <div class="text-xs font-bold accent-blue uppercase tracking-wider mb-2">
                                    {{ $relatedProduct->category->name ?? 'Eau de Parfum' }}
                                </div>
                                
                                <h3 class="font-bold text-base text-gray-900 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                
                                <!-- Rating -->
                                <div class="flex items-center gap-1 mb-3">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500">(4.{{ rand(5,9) }})</span>
                                </div>
                                
                                <div class="text-xl font-bold gradient-text mb-4">
                                    Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                </div>
                                
                                <a href="{{ route('products.show-detail', $relatedProduct) }}" 
                                   class="block w-full btn-primary-gradient text-white text-center py-2 rounded-lg font-semibold transition-all duration-300 text-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        @endif
    </div>
</div>
@endsection

@push('scripts')
<script>
// Initialize Sales Chart for Admin
@auth
@if(auth()->user()->role === 'admin')
document.addEventListener('DOMContentLoaded', function() {
    const ctx = document.getElementById('salesChart');
    if (ctx) {
        // Sample data - replace with actual data from backend
        const salesData = {
            labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'],
            datasets: [{
                label: 'Penjualan',
                data: {!! json_encode($chartData ?? [0, 5, 3, 8, 12, 7, 15, 10, 9, 14, 11, 16]) !!},
                borderColor: 'rgb(59, 130, 246)',
                backgroundColor: 'rgba(59, 130, 246, 0.1)',
                tension: 0.4,
                fill: true
            }]
        };

        new Chart(ctx, {
            type: 'line',
            data: salesData,
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    title: {
                        display: true,
                        text: 'Trend Penjualan Bulanan'
                    },
                    legend: {
                        display: false
                    }
                },
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });
    }
});
@endif
@endauth

// Review Management Functions for Admin
function editReview(reviewId, reviewText, rating) {
    // Hide review text and show edit form
    document.getElementById('review-text-' + reviewId).style.display = 'none';
    document.getElementById('edit-form-' + reviewId).classList.remove('hidden');
    
    // Set current rating stars
    setRatingDisplay(reviewId, rating);
}

function cancelEdit(reviewId) {
    // Show review text and hide edit form
    document.getElementById('review-text-' + reviewId).style.display = 'block';
    document.getElementById('edit-form-' + reviewId).classList.add('hidden');
}

function setRating(reviewId, rating) {
    document.getElementById('rating-' + reviewId).value = rating;
    setRatingDisplay(reviewId, rating);
}

function setRatingDisplay(reviewId, rating) {
    const stars = document.querySelectorAll('.rating-star-' + reviewId);
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

// Quantity controls
function increaseQuantity() {
    const input = document.getElementById('quantity');
    if (input) {
        const max = parseInt(input.getAttribute('max'));
        const current = parseInt(input.value);
        if (current < max) {
            input.value = current + 1;
        }
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    if (input) {
        const min = parseInt(input.getAttribute('min'));
        const current = parseInt(input.value);
        if (current > min) {
            input.value = current - 1;
        }
    }
}

// Image gallery
function changeMainImage(imageUrl, imageIndex, thumbnailElement) {
    // Update main image
    document.getElementById('mainImage').src = imageUrl;
    
    // Update counter
    const counter = document.getElementById('currentImageIndex');
    if (counter) {
        counter.textContent = imageIndex;
    }
    
    // Update active thumbnail
    document.querySelectorAll('[onclick*="changeMainImage"]').forEach(thumb => {
        thumb.classList.remove('border-blue-500', 'ring-2', 'ring-blue-200');
        thumb.classList.add('border-gray-200');
    });
    thumbnailElement.classList.remove('border-gray-200');
    thumbnailElement.classList.add('border-blue-500', 'ring-2', 'ring-blue-200');
}

// Image zoom modal
document.addEventListener('DOMContentLoaded', function() {
    const mainImage = document.getElementById('mainImage');
    if (mainImage) {
        mainImage.addEventListener('click', function() {
            // Create modal for zoomed image
            const modal = document.createElement('div');
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 image-zoom-modal z-50 flex items-center justify-center p-4';
            
            const zoomedImage = document.createElement('img');
            zoomedImage.src = this.src;
            zoomedImage.className = 'max-w-full max-h-full object-contain rounded-lg cursor-zoom-out';
            
            modal.appendChild(zoomedImage);
            document.body.appendChild(modal);
            document.body.style.overflow = 'hidden';
            
            // Close on click
            modal.addEventListener('click', function() {
                document.body.removeChild(modal);
                document.body.style.overflow = 'auto';
            });
            
            // Close on escape key
            const closeOnEscape = function(e) {
                if (e.key === 'Escape') {
                    document.body.removeChild(modal);
                    document.body.style.overflow = 'auto';
                    document.removeEventListener('keydown', closeOnEscape);
                }
            };
            document.addEventListener('keydown', closeOnEscape);
        });
    }
});

// Cart functions
async function addToCart(productId) {
    const quantityInput = document.getElementById('quantity');
    const quantity = quantityInput ? parseInt(quantityInput.value) || 1 : 1;
    
    try {
        const formData = new FormData();
        formData.append('_token', '{{ csrf_token() }}');
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        const response = await fetch('{{ route("cart.store") }}', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });
        
        const data = await response.json();
        
        if (response.ok) {
            showToast('Produk berhasil ditambahkan ke keranjang!', 'success');
            // Update cart count if function exists
            if (window.updateCartCount) {
                window.updateCartCount();
            }
        } else {
            showToast(data.message || 'Terjadi kesalahan', 'error');
        }
    } catch (error) {
        console.error('Error adding to cart:', error);
        showToast('Terjadi kesalahan jaringan', 'error');
    }
}

// Toast notification
function showToast(message, type = 'success') {
    // Create toast element
    const toast = document.createElement('div');
    toast.className = `fixed top-4 right-4 z-50 max-w-sm w-full transform transition-all duration-300 translate-x-full`;
    
    const bgColor = type === 'success' ? 'bg-green-500' : type === 'error' ? 'bg-red-500' : 'bg-blue-500';
    
    toast.innerHTML = `
        <div class="${bgColor} text-white px-6 py-4 rounded-lg shadow-lg flex items-center space-x-3">
            <svg class="w-6 h-6 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                ${type === 'success' ? 
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>' :
                    '<path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 9v2m0 4h.01m-6.938 4h13.856c1.54 0 2.502-1.667 1.732-2.5L13.732 4c-.77-.833-1.732-.833-2.464 0L3.34 16.5c-.77.833.192 2.5 1.732 2.5z"></path>'
                }
            </svg>
            <span class="flex-1">${message}</span>
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200">
                <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                </svg>
            </button>
        </div>
    `;
    
    document.body.appendChild(toast);
    
    // Animate in
    setTimeout(() => {
        toast.classList.remove('translate-x-full');
    }, 100);
    
    // Auto hide after 3 seconds
    setTimeout(() => {
        toast.classList.add('translate-x-full');
        setTimeout(() => {
            if (toast.parentElement) {
                toast.parentElement.removeChild(toast);
            }
        }, 300);
    }, 3000);
}

// Keyboard navigation for image gallery
document.addEventListener('keydown', function(e) {
    const thumbnails = document.querySelectorAll('[onclick*="changeMainImage"]');
    
    if (thumbnails.length <= 1) return;
    
    let activeIndex = -1;
    thumbnails.forEach((thumb, index) => {
        if (thumb.classList.contains('border-blue-500')) {
            activeIndex = index;
        }
    });
    
    if (e.key === 'ArrowLeft' && activeIndex > 0) {
        thumbnails[activeIndex - 1].click();
    } else if (e.key === 'ArrowRight' && activeIndex < thumbnails.length - 1) {
        thumbnails[activeIndex + 1].click();
    }
});
</script>
@endpush
