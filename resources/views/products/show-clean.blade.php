@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<style>
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
    .image-zoom-modal {
        backdrop-filter: blur(4px);
    }
    .quantity-input::-webkit-outer-spin-button,
    .quantity-input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }
    .quantity-input[type=number] {
        -moz-appearance: textfield;
    }
</style>
@endpush

@section('content')
<div class="min-h-screen bg-gray-50 py-8">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Breadcrumb -->
        <nav class="flex mb-8" aria-label="Breadcrumb">
            <ol class="inline-flex items-center space-x-1 md:space-x-3">
                <li class="inline-flex items-center">
                    <a href="{{ route('products.catalog') }}" class="inline-flex items-center text-sm font-medium text-gray-700 hover:text-blue-600">
                        <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                            <path d="M10.707 2.293a1 1 0 00-1.414 0l-7 7a1 1 0 001.414 1.414L4 10.414V17a1 1 0 001 1h2a1 1 0 001-1v-2a1 1 0 011-1h2a1 1 0 011 1v2a1 1 0 001 1h2a1 1 0 001-1v-6.586l.293.293a1 1 0 001.414-1.414l-7-7z"></path>
                        </svg>
                        Katalog
                    </a>
                </li>
                <li>
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2">{{ $product->category->name ?? 'Tanpa Kategori' }}</span>
                    </div>
                </li>
                <li aria-current="page">
                    <div class="flex items-center">
                        <svg class="w-6 h-6 text-gray-400" fill="currentColor" viewBox="0 0 20 20">
                            <path fill-rule="evenodd" d="M7.293 14.707a1 1 0 010-1.414L10.586 10 7.293 6.707a1 1 0 011.414-1.414l4 4a1 1 0 010 1.414l-4 4a1 1 0 01-1.414 0z" clip-rule="evenodd"></path>
                        </svg>
                        <span class="ml-1 text-sm font-medium text-gray-500 md:ml-2 truncate">{{ Str::limit($product->name, 30) }}</span>
                    </div>
                </li>
            </ol>
        </nav>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden">
            <!-- Main Product Section -->
            <div class="lg:grid lg:grid-cols-2 lg:gap-8">
                <!-- Product Images -->
                <div class="relative">
                    <!-- Main Image -->
                    <div class="aspect-w-1 aspect-h-1 bg-gray-200 rounded-tl-2xl lg:rounded-tr-none lg:rounded-bl-2xl overflow-hidden group">
                        @if($product->images && $product->images->count() > 0)
                            <img id="mainImage" 
                                 src="{{ $product->images->first()->url }}" 
                                 alt="{{ $product->name }}" 
                                 class="w-full h-96 lg:h-full object-cover cursor-zoom-in transition-transform duration-300 hover:scale-105">
                            @if($product->images->count() > 1)
                                <div class="absolute top-4 right-4 bg-black bg-opacity-60 text-white px-3 py-1 rounded-full text-sm font-medium">
                                    <span id="currentImageIndex">1</span> / {{ $product->images->count() }}
                                </div>
                            @endif
                            
                            <!-- Zoom Icon -->
                            <div class="absolute top-4 left-4 bg-black bg-opacity-60 text-white p-2 rounded-full opacity-0 group-hover:opacity-100 transition-opacity duration-300">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0zM10 7v3m0 0v3m0-3h3m-3 0H7"></path>
                                </svg>
                            </div>
                        @else
                            <div class="w-full h-96 lg:h-full flex items-center justify-center text-gray-400 bg-gray-100">
                                <div class="text-center">
                                    <svg class="w-16 h-16 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                    </svg>
                                    <p class="text-lg font-medium">Foto Produk Tidak Tersedia</p>
                                </div>
                            </div>
                        @endif
                    </div>
                    
                    <!-- Thumbnail Images -->
                    @if($product->images && $product->images->count() > 1)
                        <div class="flex space-x-3 p-4 overflow-x-auto bg-gray-50">
                            @foreach($product->images as $index => $image)
                                <div class="flex-shrink-0">
                                    <img src="{{ $image->url }}" 
                                         alt="{{ $product->name }} - Image {{ $index + 1 }}"
                                         onclick="changeMainImage('{{ $image->url }}', {{ $index + 1 }}, this)"
                                         class="w-20 h-20 object-cover rounded-lg cursor-pointer border-2 transition-all duration-300 hover:shadow-md {{ $index === 0 ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200 hover:border-blue-300' }}">
                                </div>
                            @endforeach
                        </div>
                    @endif
                </div>

                <!-- Product Information -->
                <div class="p-6 lg:p-8">
                    <!-- Category Badge -->
                    <div class="mb-4">
                        <span class="inline-flex items-center px-3 py-1 rounded-full text-sm font-medium bg-blue-100 text-blue-800">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </span>
                    </div>

                    <!-- Product Title -->
                    <h1 class="text-3xl lg:text-4xl font-bold text-gray-900 mb-4 leading-tight">{{ $product->name }}</h1>

                    <!-- Rating and Reviews -->
                    <div class="flex items-center space-x-4 mb-6">
                        <div class="flex items-center">
                            @for($i = 1; $i <= 5; $i++)
                                @if($i <= floor($product->average_rating))
                                    <span class="star filled">★</span>
                                @elseif($i == ceil($product->average_rating) && $product->average_rating - floor($product->average_rating) >= 0.5)
                                    <span class="star half">★</span>
                                @else
                                    <span class="star">☆</span>
                                @endif
                            @endfor
                        </div>
                        <span class="text-sm text-gray-600">
                            {{ number_format($product->average_rating, 1) }} dari 5
                        </span>
                        <span class="text-sm text-gray-400">•</span>
                        <a href="#reviews" class="text-sm text-blue-600 hover:text-blue-800 font-medium">
                            {{ $product->review_count }} ulasan
                        </a>
                    </div>

                    <!-- Price -->
                    <div class="mb-8">
                        <p class="text-4xl font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</p>
                        @if($product->stock > 0)
                            <p class="text-sm text-green-600 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                Stok tersedia ({{ $product->stock }} unit)
                            </p>
                        @else
                            <p class="text-sm text-red-600 mt-2 flex items-center">
                                <svg class="w-4 h-4 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                                </svg>
                                Stok habis
                            </p>
                        @endif
                    </div>

                    <!-- Product Actions -->
                    @auth
                        @if($product->stock > 0)
                            <div class="space-y-4 mb-8">
                                <!-- Quantity Selector -->
                                <div class="flex items-center space-x-4">
                                    <label class="text-sm font-medium text-gray-700">Jumlah:</label>
                                    <div class="flex items-center border border-gray-300 rounded-lg">
                                        <button type="button" 
                                                onclick="decreaseQuantity()" 
                                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-l-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                            </svg>
                                        </button>
                                        <input type="number" 
                                               id="quantity" 
                                               value="1" 
                                               min="1" 
                                               max="{{ $product->stock }}" 
                                               class="w-16 px-3 py-2 text-center border-0 focus:ring-0 quantity-input bg-white">
                                        <button type="button" 
                                                onclick="increaseQuantity()" 
                                                class="p-2 text-gray-500 hover:text-gray-700 hover:bg-gray-50 rounded-r-lg transition-colors">
                                            <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                            </svg>
                                        </button>
                                    </div>
                                </div>
                                
                                <!-- Action Buttons -->
                                <div class="flex space-x-4">
                                    <button onclick="addToCart({{ $product->id }})" 
                                            class="flex-1 bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 4M7 13l2.5 4m6 0a2 2 0 100 4 2 2 0 000-4zm-8 0a2 2 0 100 4 2 2 0 000-4z"></path>
                                        </svg>
                                        Tambah ke Keranjang
                                    </button>
                                    <button class="flex-1 bg-green-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-green-700 focus:outline-none focus:ring-2 focus:ring-green-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Beli Sekarang
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mb-8">
                                <button disabled class="w-full bg-gray-400 text-white px-6 py-3 rounded-lg font-semibold cursor-not-allowed flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"></path>
                                    </svg>
                                    Stok Habis
                                </button>
                            </div>
                        @endif
                    @else
                        <div class="mb-8">
                            <a href="{{ route('login') }}" 
                               class="w-full bg-blue-600 text-white px-6 py-3 rounded-lg font-semibold hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors duration-200 flex items-center justify-center">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h7a3 3 0 013 3v1"></path>
                                </svg>
                                Login untuk Membeli
                            </a>
                        </div>
                    @endauth

                    <!-- Admin Actions -->
                    @auth
                        @if(auth()->user()->role === 'admin')
                            <div class="border-t pt-6 mt-6">
                                <h3 class="text-lg font-semibold text-gray-900 mb-4">Panel Admin</h3>
                                <div class="flex space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="inline-flex items-center px-4 py-2 bg-yellow-600 text-white text-sm font-medium rounded-lg hover:bg-yellow-700 focus:outline-none focus:ring-2 focus:ring-yellow-500 focus:ring-offset-2 transition-colors">
                                        <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Produk
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="inline">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                onclick="return confirm('Yakin ingin menghapus produk ini?')"
                                                class="inline-flex items-center px-4 py-2 bg-red-600 text-white text-sm font-medium rounded-lg hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-red-500 focus:ring-offset-2 transition-colors">
                                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                            </svg>
                                            Hapus Produk
                                        </button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    @endauth

                    <!-- Additional Info -->
                    <div class="grid grid-cols-2 gap-4 text-sm text-gray-600 mt-6 pt-6 border-t">
                        @if($product->weight)
                            <div class="flex items-center">
                                <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                                Berat: {{ $product->weight }} gram
                            </div>
                        @endif
                        <div class="flex items-center">
                            <svg class="w-4 h-4 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                            Ditambahkan: {{ $product->created_at->format('d M Y') }}
                        </div>
                    </div>
                </div>
            </div>

            <!-- Product Description -->
            <div class="border-t border-gray-200 px-6 lg:px-8 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-4">Deskripsi Produk</h2>
                <div class="prose prose-blue max-w-none">
                    <p class="text-gray-700 leading-relaxed text-lg">{{ $product->description }}</p>
                </div>
            </div>

            <!-- Reviews Section -->
            <div id="reviews" class="border-t border-gray-200 px-6 lg:px-8 py-8">
                <h2 class="text-2xl font-bold text-gray-900 mb-6">Ulasan Produk</h2>
                
                @if($product->reviews->count() > 0)
                    <!-- Rating Summary -->
                    <div class="bg-gradient-to-r from-blue-50 to-indigo-50 rounded-2xl p-6 mb-8">
                        <div class="flex flex-col lg:flex-row lg:items-center lg:space-x-8 space-y-6 lg:space-y-0">
                            <!-- Overall Rating -->
                            <div class="text-center lg:text-left">
                                <div class="text-5xl font-bold text-gray-900 mb-2">
                                    {{ number_format($product->average_rating, 1) }}
                                </div>
                                <div class="flex justify-center lg:justify-start items-center mb-2">
                                    @for($i = 1; $i <= 5; $i++)
                                        @if($i <= floor($product->average_rating))
                                            <span class="star filled text-2xl">★</span>
                                        @elseif($i == ceil($product->average_rating) && $product->average_rating - floor($product->average_rating) >= 0.5)
                                            <span class="star half text-2xl">★</span>
                                        @else
                                            <span class="star text-2xl">☆</span>
                                        @endif
                                    @endfor
                                </div>
                                <div class="text-sm text-gray-600">
                                    Berdasarkan {{ $product->review_count }} ulasan
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
                                        <span class="text-sm font-medium text-gray-700 w-8">{{ $rating }}★</span>
                                        <div class="flex-1 h-3 bg-gray-200 rounded-full overflow-hidden">
                                            <div class="h-full bg-gradient-to-r from-yellow-400 to-yellow-500 rounded-full transition-all duration-500" 
                                                 style="width: {{ $data['percentage'] }}%"></div>
                                        </div>
                                        <span class="text-sm text-gray-600 w-8 text-right">{{ $data['count'] }}</span>
                                    </div>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    <!-- Individual Reviews -->
                    <div class="space-y-6">
                        @foreach($product->reviews->sortByDesc('created_at') as $review)
                            <div class="bg-white border border-gray-200 rounded-xl p-6 hover:shadow-md transition-shadow duration-300">
                                <div class="flex items-start justify-between mb-4">
                                    <div class="flex items-center space-x-4">
                                        <!-- User Avatar -->
                                        <div class="w-12 h-12 bg-gradient-to-br from-blue-500 to-purple-600 rounded-full flex items-center justify-center text-white font-semibold text-lg">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <h4 class="font-semibold text-gray-900">{{ $review->user->name ?? 'User' }}</h4>
                                            <div class="flex items-center space-x-2 mt-1">
                                                <div class="flex items-center">
                                                    @for($i = 1; $i <= 5; $i++)
                                                        @if($i <= $review->rating)
                                                            <span class="star filled">★</span>
                                                        @else
                                                            <span class="star">☆</span>
                                                        @endif
                                                    @endfor
                                                </div>
                                                <span class="text-sm text-gray-500">•</span>
                                                <span class="text-sm text-gray-500">
                                                    {{ $review->created_at->format('d F Y') }}
                                                </span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Verified Badge (if applicable) -->
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-green-100 text-green-800">
                                        <svg class="w-3 h-3 mr-1" fill="currentColor" viewBox="0 0 20 20">
                                            <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                        </svg>
                                        Terverifikasi
                                    </span>
                                </div>
                                
                                <div class="text-gray-700 leading-relaxed">
                                    {{ $review->review }}
                                </div>
                            </div>
                        @endforeach
                    </div>

                    <!-- Load More Reviews (if there are many) -->
                    @if($product->reviews->count() > 5)
                        <div class="text-center mt-8">
                            <button class="inline-flex items-center px-6 py-3 border border-gray-300 text-base font-medium rounded-lg text-gray-700 bg-white hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition-colors">
                                Lihat Semua Ulasan
                                <svg class="ml-2 w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 9l-7 7-7-7"></path>
                                </svg>
                            </button>
                        </div>
                    @endif
                @else
                    <div class="text-center py-12">
                        <div class="text-6xl mb-4">📝</div>
                        <h3 class="text-xl font-semibold text-gray-900 mb-2">Belum Ada Ulasan</h3>
                        <p class="text-gray-600 mb-6">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                        @auth
                            <button class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                </svg>
                                Tulis Ulasan Pertama
                            </button>
                        @else
                            <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-blue-600 text-white font-medium rounded-lg hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition-colors">
                                Login untuk Menulis Ulasan
                            </a>
                        @endif
                    </div>
                @endif
            </div>
        </div>

        <!-- Related Products -->
        @if($relatedProducts->count() > 0)
            <div class="mt-16">
                <h2 class="text-2xl font-bold text-gray-900 mb-8 text-center">Produk Serupa</h2>
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1">
                            <div class="aspect-w-1 aspect-h-1 bg-gray-200">
                                @if($relatedProduct->images && $relatedProduct->images->count() > 0)
                                    <img src="{{ $relatedProduct->images->first()->url }}" 
                                         alt="{{ $relatedProduct->name }}" 
                                         class="w-full h-48 object-cover">
                                @else
                                    <div class="w-full h-48 flex items-center justify-center text-gray-400">
                                        <svg class="w-12 h-12" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                        </svg>
                                    </div>
                                @endif
                            </div>
                            <div class="p-4">
                                <h3 class="font-semibold text-gray-900 mb-2 line-clamp-2">{{ $relatedProduct->name }}</h3>
                                <div class="text-lg font-bold text-green-600 mb-3">
                                    Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}
                                </div>
                                <a href="{{ route('products.show-detail', $relatedProduct) }}" 
                                   class="block w-full bg-blue-600 text-white text-center py-2 rounded-lg hover:bg-blue-700 transition-colors">
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
// Quantity controls
function increaseQuantity() {
    const input = document.getElementById('quantity');
    const max = parseInt(input.getAttribute('max'));
    const current = parseInt(input.value);
    if (current < max) {
        input.value = current + 1;
    }
}

function decreaseQuantity() {
    const input = document.getElementById('quantity');
    const min = parseInt(input.getAttribute('min'));
    const current = parseInt(input.value);
    if (current > min) {
        input.value = current - 1;
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
    const quantity = parseInt(document.getElementById('quantity').value) || 1;
    
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
