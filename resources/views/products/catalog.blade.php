@extends('layouts.app')

@section('title', 'Katalog Produk - ' . config('app.name'))

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
    }
    .filter-card {
        background: rgba(255, 255, 255, 0.95);
        backdrop-filter: blur(10px);
        border: 1px solid rgba(255, 255, 255, 0.2);
    }
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
        background: white;
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
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
    .accent-blue { color: #3b82f6; }
    .accent-green { color: #10b981; }
    .bg-blue-gradient {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .bg-green-gradient {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .text-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
    }
</style>
@endpush

@section('content')
<!-- Hero Header Section -->
<section class="relative py-20 overflow-hidden">
    <!-- Background with gradient -->
    <div class="absolute inset-0 hero-gradient"></div>
    
    <!-- Animated background elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-10 left-10 w-48 h-48 bg-white rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
        <div class="absolute top-20 right-20 w-48 h-48 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000"></div>
        <div class="absolute bottom-10 left-1/3 w-48 h-48 bg-green-300 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <h1 class="text-4xl md:text-6xl font-bold text-white mb-6">
            Katalog <span class="text-blue-300">Produk</span>
        </h1>
        <p class="text-xl text-gray-100 max-w-2xl mx-auto leading-relaxed">
            Temukan koleksi parfum terbaik dari brand-brand ternama dunia dengan kualitas terjamin
        </p>
        
        <!-- Quick Stats -->
        <div class="grid grid-cols-2 md:grid-cols-4 gap-6 mt-12">
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-white">{{ $products->total() }}</div>
                <div class="text-gray-200 text-sm">Total Produk</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-white">{{ $categories->count() }}</div>
                <div class="text-gray-200 text-sm">Kategori</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-white">100%</div>
                <div class="text-gray-200 text-sm">Original</div>
            </div>
            <div class="text-center">
                <div class="text-2xl md:text-3xl font-bold text-white">24H</div>
                <div class="text-gray-200 text-sm">Fast Delivery</div>
            </div>
        </div>
    </div>
</section>

<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-4 -mt-10 relative z-20">
    <!-- Modern Filter Section -->
    <div class="filter-card p-8 rounded-3xl shadow-2xl mb-12">
        <div class="text-center mb-8">
            <h2 class="text-2xl font-bold text-gray-900 mb-2">Filter & Pencarian</h2>
            <p class="text-gray-600">Temukan produk parfum sesuai dengan preferensi Anda</p>
        </div>
        
        <form method="GET" action="{{ route('products.catalog') }}">
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-5 gap-6 items-end">
                <!-- Search -->
                <div class="lg:col-span-2">
                    <label for="search" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"></path>
                        </svg>
                        Cari Produk
                    </label>
                    <input type="text" id="search" name="search" value="{{ $search }}" 
                           placeholder="Nama parfum, brand, atau aroma..."
                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                </div>

                <!-- Category -->
                <div>
                    <label for="category" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M7 7h.01M7 3h5c.512 0 1.024.195 1.414.586l7 7a2 2 0 010 2.828l-7 7a2 2 0 01-2.828 0l-7-7A1.994 1.994 0 013 12V7a4 4 0 014-4z"></path>
                        </svg>
                        Kategori
                    </label>
                    <select id="category" name="category"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div>
                    <label for="sort" class="block text-sm font-semibold text-gray-700 mb-2">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4h13M3 8h9m-9 4h6m4 0l4-4m0 0l4 4m-4-4v12"></path>
                        </svg>
                        Urutkan
                    </label>
                    <select id="sort" name="sort"
                            class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500 transition-all duration-300">
                        <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="popular" {{ $sort == 'popular' ? 'selected' : '' }}>Terpopuler</option>
                        <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="price" {{ $sort == 'price' ? 'selected' : '' }}>Harga</option>
                    </select>
                </div>

                <!-- Order Direction (Hidden but combined with sort) -->
                <input type="hidden" name="order" value="{{ $order }}">

                <!-- Action Buttons -->
                <div class="flex gap-3">
                    <button type="submit"
                            class="flex-1 btn-primary-gradient px-6 py-3 text-white font-semibold rounded-xl transition-all duration-300 transform hover:scale-105">
                        <svg class="inline w-4 h-4 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 4a1 1 0 011-1h16a1 1 0 011 1v2.586a1 1 0 01-.293.707l-6.414 6.414a1 1 0 00-.293.707V17l-4 4v-6.586a1 1 0 00-.293-.707L3.293 7.414A1 1 0 013 6.707V4z"></path>
                        </svg>
                        Filter
                    </button>
                    <a href="{{ route('products.catalog') }}"
                       class="px-4 py-3 border-2 border-gray-300 text-gray-700 font-semibold rounded-xl hover:bg-gray-50 transition-all duration-300">
                        <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if($products->count() > 0)
        <!-- Results Info -->
        <div class="text-center mb-12">
            <div class="inline-flex items-center gap-2 bg-white rounded-full px-6 py-3 shadow-lg border border-gray-100">
                <svg class="w-5 h-5 text-green-500" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                </svg>
                <span class="text-gray-700 font-medium">
                    Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
                </span>
            </div>
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-4 gap-8 mb-16">
            @foreach($products as $product)
                <div class="product-card rounded-3xl overflow-hidden shadow-lg flex flex-col h-full">
                    <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden flex-shrink-0">
                        <!-- Badges -->
                        <div class="absolute top-4 left-4 z-10 flex flex-col gap-2">
                            @if($sort == 'popular' && isset($product->order_items_count) && $product->order_items_count > 0)
                                <div class="bg-gradient-to-r from-orange-400 to-red-500 text-white text-xs px-3 py-2 rounded-full font-bold shadow-lg">
                                    🔥 Populer
                                </div>
                            @endif
                            
                            @if($product->discount && $product->discount->isActive())
                                <div class="bg-gradient-to-r from-red-500 to-pink-500 text-white text-xs px-3 py-2 rounded-full font-bold shadow-lg">
                                    -{{ $product->discount->percentage }}% OFF
                                </div>
                            @endif
                        </div>
                        
                        <!-- Product Image -->
                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                        @elseif($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" 
                                 class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                        @else
                            <div class="flex flex-col items-center justify-center h-full text-gray-400">
                                <svg class="w-16 h-16 mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                </svg>
                                <span class="text-sm font-medium">Parfum Premium</span>
                            </div>
                        @endif
                        
                        <!-- Quick Actions Overlay -->
                        <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                            <div class="flex gap-3">
                                <button onclick="openModal({{ $product->id }})" 
                                        class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                    </svg>
                                </button>
                                <button class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                    <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                    </svg>
                                </button>
                            </div>
                        </div>
                    </div>
                    
                    <div class="p-6 flex flex-col flex-1">
                        <!-- Category Tag -->
                        <div class="text-xs font-bold accent-blue uppercase tracking-wider mb-3 flex-shrink-0">
                            {{ $product->category->name ?? 'Eau de Parfum' }}
                        </div>
                        
                        <!-- Product Name -->
                        <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2 min-h-[3.5rem] flex-shrink-0">{{ $product->name }}</h3>
                        
                        <!-- Description -->
                        <p class="text-gray-600 mb-4 text-sm line-clamp-2 min-h-[2.5rem] flex-shrink-0">{{ Str::limit($product->description, 80) }}</p>
                        
                        <!-- Spacer to push content to bottom -->
                        <div class="flex-1"></div>
                        
                        <!-- Sales Info -->
                        @if($sort == 'popular' && isset($product->order_items_count))
                            <div class="text-xs accent-green mb-3 font-semibold flex-shrink-0">
                                🔥 Terjual {{ $product->order_items_count }} kali
                            </div>
                        @endif
                        
                        <!-- Price Section -->
                        <div class="mb-6 flex-shrink-0">
                            @if($product->discount && $product->discount->isActive())
                                <div class="flex items-center gap-2 mb-2">
                                    <span class="bg-green-100 text-green-800 text-xs px-3 py-1 rounded-full font-bold">
                                        {{ $product->discount->percentage }}% OFF
                                    </span>
                                </div>
                                <div class="flex items-center gap-3 mb-1">
                                    <span class="text-2xl font-bold accent-blue">
                                        Rp {{ number_format($product->discount->getDiscountedPrice($product->price), 0, ',', '.') }}
                                    </span>
                                    <span class="text-gray-400 line-through text-lg">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </span>
                                </div>
                                <div class="text-sm accent-green font-semibold">
                                    💚 Hemat Rp {{ number_format($product->discount->getDiscountAmount($product->price), 0, ',', '.') }}
                                </div>
                            @else
                                <div class="text-2xl font-bold accent-blue">
                                    Rp {{ number_format($product->price, 0, ',', '.') }}
                                </div>
                            @endif
                        </div>
                        
                        <!-- Action Buttons -->
                        <div class="flex gap-3 flex-shrink-0">
                            <a href="{{ route('products.show-detail', $product) }}"
                               class="flex-1 px-4 py-3 border-2 border-green-200 accent-green rounded-xl font-semibold hover:bg-green-50 transition-all duration-300 text-center text-sm">
                                Detail
                            </a>
                            @auth
                                <button onclick="addToCart({{ $product->id }})"
                                       class="flex-1 btn-primary-gradient px-4 py-3 text-white rounded-xl font-semibold transition-all duration-300 text-sm">
                                    + Keranjang
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="flex-1 btn-primary-gradient px-4 py-3 text-white rounded-xl font-semibold transition-all duration-300 text-center text-sm">
                                    + Keranjang
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-16 mb-12 flex justify-center">
            <div class="bg-white rounded-2xl shadow-lg border border-gray-100 px-8 py-4">
                {{ $products->appends(request()->query())->links() }}
            </div>
        </div>
    @else
        <!-- Empty State -->
        <div class="flex flex-col items-center justify-center min-h-[60vh] text-center">
            <div class="bg-gradient-to-br from-blue-50 to-green-50 p-16 rounded-3xl shadow-lg border border-gray-100 max-w-2xl mx-auto">
                <div class="flex items-center justify-center w-24 h-24 bg-gradient-to-br from-blue-100 to-green-100 rounded-full mx-auto mb-8">
                    <svg class="w-12 h-12 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                    </svg>
                </div>
                <h3 class="text-3xl font-bold text-gray-800 mb-4">Produk Tidak Ditemukan</h3>
                <p class="text-gray-600 text-lg mb-8 leading-relaxed">
                    Maaf, kami tidak dapat menemukan parfum yang sesuai dengan kriteria pencarian Anda. 
                    <br>Coba sesuaikan filter atau kata kunci pencarian Anda.
                </p>
                <div class="flex flex-wrap gap-4 justify-center">
                    <a href="{{ route('products.catalog') }}" 
                       class="btn-primary-gradient px-8 py-4 text-white rounded-xl font-semibold transition-all duration-300 hover:scale-105 shadow-lg">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 4v5h.582m15.356 2A8.001 8.001 0 004.582 9m0 0H9m11 11v-5h-.581m0 0a8.003 8.003 0 01-15.357-2m15.357 2H15"></path>
                        </svg>
                        Reset Filter
                    </a>
                    <a href="{{ route('home') }}" 
                       class="px-8 py-4 border-2 border-green-200 accent-green rounded-xl font-semibold hover:bg-green-50 transition-all duration-300">
                        <svg class="w-5 h-5 inline-block mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path>
                        </svg>
                        Kembali ke Beranda
                    </a>
                </div>
            </div>
        </div>
    @endif
</div>

<!-- Product Modals -->
@foreach($products as $product)
    <div id="modal-{{ $product->id }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
        <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
            <!-- Modal Header -->
            <div class="flex items-center justify-between p-6 border-b">
                <h2 class="text-2xl font-bold text-gray-800">{{ $product->name }}</h2>
                <button onclick="closeModal({{ $product->id }})" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                    &times;
                </button>
            </div>
            
            <!-- Modal Body -->
            <div class="p-6">
                <div class="grid md:grid-cols-2 gap-6">
                    <!-- Product Image Gallery -->
                    <div class="space-y-4">
                        <!-- Main Image -->
                        <div class="h-80 bg-gray-100 flex items-center justify-center rounded-lg overflow-hidden">
                            @if($product->images && $product->images->count() > 0)
                                <img id="main-image-{{ $product->id }}" src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @elseif($product->image)
                                <img id="main-image-{{ $product->id }}" src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="flex flex-col items-center justify-center text-gray-400 h-full">
                                    <svg class="w-20 h-20 mb-4" fill="currentColor" viewBox="0 0 20 20">
                                        <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                    </svg>
                                    <span class="text-lg">No Image Available</span>
                                </div>
                            @endif
                        </div>
                        
                        <!-- Thumbnail Images -->
                        @if($product->images && $product->images->count() > 1)
                            <div class="flex space-x-2 overflow-x-auto pb-2">
                                @foreach($product->images as $index => $image)
                                    <div class="flex-shrink-0">
                                        <img src="{{ $image->url }}" alt="{{ $product->name }}" 
                                             onclick="changeMainImage('{{ $product->id }}', '{{ $image->url }}', {{ $index }})"
                                             class="w-16 h-16 object-cover rounded-lg cursor-pointer border-2 {{ $index === 0 ? 'border-blue-500' : 'border-gray-200' }} hover:border-blue-400 transition-colors thumbnail-{{ $product->id }}">
                                    </div>
                                @endforeach
                            </div>
                        @endif
                    </div>
                    
                    <!-- Product Details -->
                    <div class="space-y-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </div>
                        
                        <div class="text-green-600 font-bold text-3xl">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                        
                        @if($product->stock > 0)
                            <div class="text-sm text-gray-600">
                                Stok: <span class="font-semibold text-green-600">{{ $product->stock }} tersedia</span>
                            </div>
                        @else
                            <div class="text-sm text-red-600 font-semibold">
                                Stok habis
                            </div>
                        @endif
                        
                        @if($product->weight)
                            <div class="text-sm text-gray-600">
                                Berat: <span class="font-semibold">{{ $product->weight }} gram</span>
                            </div>
                        @endif
                        
                        <div class="border-t pt-4">
                            <h4 class="font-semibold text-gray-800 mb-2">Deskripsi Produk</h4>
                            <p class="text-gray-600 leading-relaxed">{{ $product->description }}</p>
                        </div>
                        
                        @if($product->images && $product->images->count() > 1)
                            <div class="text-sm text-gray-500">
                                <span class="font-medium">{{ $product->images->count() }}</span> foto tersedia
                            </div>
                        @endif
                        
                        @auth
                            @if($product->stock > 0)
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <label for="quantity-{{ $product->id }}" class="text-sm font-medium text-gray-700">Jumlah:</label>
                                        <input type="number" id="quantity-{{ $product->id }}" value="1" min="1" max="{{ $product->stock }}" 
                                               class="w-20 px-3 py-1 border border-gray-300 rounded-md text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <button onclick="addToCartFromModal({{ $product->id }})"
                                       class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                                        Tambah ke Keranjang
                                    </button>
                                </div>
                            @else
                                <button disabled class="w-full px-6 py-3 bg-gray-400 text-white font-medium rounded-lg cursor-not-allowed">
                                    Stok Habis
                                </button>
                            @endif
                        @else
                            <a href="{{ route('login') }}" 
                               class="block w-full px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 transition text-center">
                                Login untuk Membeli
                            </a>
                        @endif
                    </div>
                </div>
            </div>
        </div>
    </div>
@endforeach

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
    // Modal functions
    function openModal(id) {
        const modal = document.getElementById('modal-' + id);
        modal.classList.remove('hidden');
        modal.classList.add('flex');
        document.body.style.overflow = 'hidden';
    }
    
    function closeModal(id) {
        const modal = document.getElementById('modal-' + id);
        modal.classList.add('hidden');
        modal.classList.remove('flex');
        document.body.style.overflow = 'auto';
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('bg-black')) {
            e.target.classList.add('hidden');
            e.target.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    });
    
    // Image gallery functions
    function changeMainImage(productId, imageUrl, index) {
        const mainImage = document.getElementById('main-image-' + productId);
        const thumbnails = document.querySelectorAll('.thumbnail-' + productId);
        
        // Update main image
        mainImage.src = imageUrl;
        
        // Update thumbnail borders
        thumbnails.forEach((thumb, i) => {
            if (i === index) {
                thumb.classList.remove('border-gray-200');
                thumb.classList.add('border-blue-500');
            } else {
                thumb.classList.remove('border-blue-500');
                thumb.classList.add('border-gray-200');
            }
        });
    }
    
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
    
    // Cart functions
    async function addToCart(productId, quantity = 1) {
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
                updateCartCount();
            } else {
                showToast(data.message || 'Terjadi kesalahan', 'error');
            }
        } catch (error) {
            console.error('Error adding to cart:', error);
            showToast('Terjadi kesalahan jaringan', 'error');
        }
    }
    
    function addToCartFromModal(productId) {
        const quantityInput = document.getElementById('quantity-' + productId);
        const quantity = parseInt(quantityInput.value) || 1;
        
        addToCart(productId, quantity).then(() => {
            closeModal(productId);
        });
    }
    
    // Update cart count in navigation (if exists)
    async function updateCartCount() {
        if (window.updateCartCount) {
            window.updateCartCount();
        }
    }
</script>
@endpush
