@extends('layouts.app')

@section('title', $product->name . ' - ' . config('app.name'))

@push('styles')
<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
@endpush

@section('content')


<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Flash Messages -->
    @if(session('success'))
        <div class="mb-6 bg-green-50 border border-green-200 text-green-800 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('success') }}
        </div>
    @endif
    @if(session('error'))
        <div class="mb-6 bg-red-50 border border-red-200 text-red-800 px-4 py-3 rounded-lg flex items-center">
            <svg class="w-5 h-5 mr-3 text-red-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4m0 4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
            </svg>
            {{ session('error') }}
        </div>
    @endif

    <!-- Main Product Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 overflow-hidden">
        <div class="lg:grid lg:grid-cols-12 lg:gap-8 p-6">
            <!-- Product Images -->
            <div class="lg:col-span-5">
                <!-- Main Image -->
                <div class="aspect-w-1 aspect-h-1 bg-gradient-to-br from-gray-50 to-gray-100 rounded-xl overflow-hidden mb-4 border border-gray-200">
                    @if($product->images && $product->images->count() > 0)
                        <img id="mainImage" 
                             src="{{ $product->images->first()->url }}" 
                             alt="{{ $product->name }}" 
                             class="w-full h-80 object-cover cursor-zoom-in hover:scale-105 transition duration-300">
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
                    <div class="flex space-x-2 overflow-x-auto pb-2">
                        @foreach($product->images as $index => $image)
                            <div class="flex-shrink-0">
                                <img src="{{ $image->url }}" 
                                     alt="{{ $product->name }}"
                                     onclick="changeMainImage('{{ $image->url }}', {{ $index + 1 }}, this)"
                                     class="w-16 h-16 object-cover rounded-lg cursor-pointer border-2 hover:border-blue-400 transition duration-200 {{ $index === 0 ? 'border-blue-500 ring-2 ring-blue-200' : 'border-gray-200' }}">
                            </div>
                        @endforeach
                    </div>
                @endif
            </div>

            <!-- Product Information -->
            <div class="lg:col-span-7 mt-6 lg:mt-0">
                <!-- Product Title -->
                <h1 class="text-2xl lg:text-3xl font-bold text-gray-900 mb-4">{{ $product->name }}</h1>
                
                <!-- Rating and Reviews -->
                <div class="flex items-center space-x-4 mb-6">
                    <div class="flex items-center bg-gradient-to-r from-yellow-50 to-orange-50 px-3 py-2 rounded-lg border border-yellow-200">
                        @if($product->reviews && $product->reviews->count() > 0)
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 {{ $i <= $product->average_rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-800">
                                {{ number_format($product->average_rating, 1) }}
                            </span>
                        @else
                            @for($i = 1; $i <= 5; $i++)
                                <svg class="w-4 h-4 text-gray-300" fill="currentColor" viewBox="0 0 20 20">
                                    <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                </svg>
                            @endfor
                            <span class="ml-2 text-sm font-medium text-gray-500">Belum ada rating</span>
                        @endif
                    </div>
                    <div class="text-gray-300">•</div>
                    <a href="#reviews" class="text-sm text-blue-600 hover:text-blue-700 font-medium transition duration-200 hover:underline">
                        {{ $product->reviews ? $product->reviews->count() : 0 }} ulasan pelanggan
                    </a>
                </div>

                <!-- Price Section -->
                <div class="mb-6">
                    <div class="bg-gradient-to-r from-green-50 to-blue-50 rounded-xl p-4 border border-green-200">
                        @if($product->discount && $product->discount->is_active)
                            <div class="flex items-center space-x-2 mb-2">
                                <span class="text-lg text-gray-400 line-through">Rp {{ number_format($product->price, 0, ',', '.') }}</span>
                                <span class="bg-red-100 text-red-600 text-xs px-2 py-1 rounded-full font-medium">{{ $product->discount->percentage }}%</span>
                            </div>
                            <div class="text-3xl font-bold text-green-600">Rp {{ number_format($product->discounted_price, 0, ',', '.') }}</div>
                        @else
                            <div class="text-3xl font-bold text-green-600">Rp {{ number_format($product->price, 0, ',', '.') }}</div>
                        @endif
                        <span class="text-sm text-gray-500 mt-1 block">/ botol</span>
                    </div>
                </div>

                <!-- Stock Information -->
                <div class="mb-6">
                    @if($product->stock > 0)
                        <div class="flex items-center text-green-600 bg-green-100 px-4 py-3 rounded-lg border border-green-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Stok tersedia ({{ $product->stock }} unit)</span>
                        </div>
                    @else
                        <div class="flex items-center text-red-600 bg-red-100 px-4 py-3 rounded-lg border border-red-200">
                            <svg class="w-5 h-5 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zM8.707 7.293a1 1 0 00-1.414 1.414L8.586 10l-1.293 1.293a1 1 0 101.414 1.414L10 11.414l1.293 1.293a1 1 0 001.414-1.414L11.414 10l1.293-1.293a1 1 0 00-1.414-1.414L10 8.586 8.707 7.293z" clip-rule="evenodd"></path>
                            </svg>
                            <span class="font-medium">Stok habis</span>
                        </div>
                    @endif
                </div>
                <!-- Quantity Selector -->
                @if($product->stock > 0)
                    <div class="mb-6">
                        <label class="block text-sm font-medium text-gray-700 mb-2">Kuantitas:</label>
                        <div class="flex items-center space-x-4">
                            <div class="flex items-center border border-gray-300 rounded-lg overflow-hidden">
                                <button type="button" onclick="decreaseQuantity()" 
                                    class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition duration-200">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"></path>
                                    </svg>
                                </button>
                                <input type="number" 
                                       id="quantity" 
                                       value="1" 
                                       min="1" 
                                       max="{{ $product->stock }}"
                                       class="w-16 text-center border-0 focus:ring-0 text-sm py-2"
                                       style="-webkit-appearance: none; -moz-appearance: textfield;">
                                <button type="button" onclick="increaseQuantity()" 
                                    class="px-3 py-2 text-gray-600 hover:text-gray-800 hover:bg-gray-100 transition duration-200">
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
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 border border-gray-200">
                                <h4 class="text-sm font-medium text-gray-700 mb-3 flex items-center">
                                    <svg class="w-4 h-4 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10.325 4.317c.426-1.756 2.924-1.756 3.35 0a1.724 1.724 0 002.573 1.066c1.543-.94 3.31.826 2.37 2.37a1.724 1.724 0 001.065 2.572c1.756.426 1.756 2.924 0 3.35a1.724 1.724 0 00-1.066 2.573c.94 1.543-.826 3.31-2.37 2.37a1.724 1.724 0 00-2.572 1.065c-.426 1.756-2.924 1.756-3.35 0a1.724 1.724 0 00-2.573-1.066c-1.543.94-3.31-.826-2.37-2.37a1.724 1.724 0 00-1.065-2.572c-1.756-.426-1.756-2.924 0-3.35a1.724 1.724 0 001.066-2.573c-.94-1.543.826-3.31 2.37-2.37.996.608 2.296.07 2.572-1.065z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    Panel Admin
                                </h4>
                                <div class="flex space-x-3">
                                    <a href="{{ route('products.edit', $product) }}" 
                                       class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white px-4 py-2.5 rounded-lg text-center text-sm font-medium transition duration-200">
                                        Edit Produk
                                    </a>
                                    <form action="{{ route('products.destroy', $product) }}" method="POST" class="flex-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                            class="w-full bg-gradient-to-r from-red-600 to-red-700 hover:from-red-700 hover:to-red-800 text-white px-4 py-2.5 rounded-lg text-sm font-medium transition duration-200"
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
                                            class="w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-3 px-4 rounded-lg font-medium transition duration-200 flex items-center justify-center">
                                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l5 7m0-7h10"></path>
                                            </svg>
                                            Tambah ke Keranjang
                                        </button>
                                    </form>
                                    <button type="button" 
                                        class="bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-3 px-6 rounded-lg font-medium transition duration-200 flex items-center">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                                        </svg>
                                        Beli Langsung
                                    </button>
                                </div>
                            </div>
                        @else
                            <div class="mb-6">
                                <button disabled class="w-full bg-gray-300 text-gray-500 py-3 px-4 rounded-lg font-medium cursor-not-allowed flex items-center justify-center">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18.364 18.364A9 9 0 005.636 5.636m12.728 12.728L5.636 5.636m12.728 12.728L18.364 5.636M5.636 5.636l12.728 12.728"></path>
                                    </svg>
                                    Stok Habis
                                </button>
                            </div>
                        @endif
                    @endif
                @else
                    <!-- Guest Actions -->
                    <div class="mb-6">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-4 text-center border border-gray-200">
                            <p class="text-sm text-gray-600 mb-3">Silakan masuk untuk berbelanja</p>
                            <div class="flex space-x-3">
                                <a href="{{ route('login') }}" 
                                   class="flex-1 bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white py-2.5 px-4 rounded-lg text-sm text-center font-medium transition duration-200">
                                    Masuk
                                </a>
                                <a href="{{ route('register') }}" 
                                   class="flex-1 bg-gradient-to-r from-green-600 to-green-700 hover:from-green-700 hover:to-green-800 text-white py-2.5 px-4 rounded-lg text-sm text-center font-medium transition duration-200">
                                    Daftar
                                </a>
                            </div>
                        </div>
                    </div>
                @endauth

                <!-- Additional Product Information -->
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4 pt-6 border-t border-gray-200">
                    @if($product->weight)
                        <div class="flex items-center bg-gradient-to-r from-blue-50 to-indigo-50 rounded-xl p-4 border border-blue-200">
                            <div class="bg-blue-100 p-3 rounded-lg mr-4">
                                <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 6l3 1m0 0l-3 9a5.002 5.002 0 006.001 0M6 7l3 9M6 7l6-2m6 2l3-1m-3 1l-3 9a5.002 5.002 0 006.001 0M18 7l3 9m-3-9l-6-2m0-2v2m0 16V5m0 16H9m3 0h3"></path>
                                </svg>
                            </div>
                            <div>
                                <p class="font-medium text-gray-900">Berat Produk</p>
                                <p class="text-blue-600 font-semibold">{{ $product->weight }} gram</p>
                            </div>
                        </div>
                    @endif
                    <div class="flex items-center bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-4 border border-green-200">
                        <div class="bg-green-100 p-3 rounded-lg mr-4">
                            <svg class="w-6 h-6 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                            </svg>
                        </div>
                        <div>
                            <p class="font-medium text-gray-900">Tanggal Ditambahkan</p>
                            <p class="text-green-600 font-semibold">{{ $product->created_at->format('d M Y') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Product Details Tab Section -->
    <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-8 overflow-hidden">
        <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
            <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12h6m-6 4h6m2 5H7a2 2 0 01-2-2V5a2 2 0 012-2h5.586a1 1 0 01.707.293l5.414 5.414a1 1 0 01.293.707V19a2 2 0 01-2 2z"></path>
                </svg>
                Detail Produk
            </h3>
        </div>
        
        <div class="p-6">
            <!-- Tab Navigation -->
            <div class="border-b border-gray-200 mb-6">
                <nav class="flex space-x-8">
                    <button onclick="showTab('description')" 
                        class="tab-button py-2 px-1 border-b-2 border-blue-500 text-blue-600 font-medium text-sm transition duration-200" 
                        id="description-tab">
                        Deskripsi
                    </button>
                    <button onclick="showTab('specifications')" 
                        class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition duration-200" 
                        id="specifications-tab">
                        Spesifikasi
                    </button>
                    <button onclick="showTab('reviews')" 
                        class="tab-button py-2 px-1 border-b-2 border-transparent text-gray-500 hover:text-gray-700 font-medium text-sm transition duration-200" 
                        id="reviews-tab">
                        Ulasan ({{ $product->reviews ? $product->reviews->count() : 0 }})
                    </button>
                </nav>
            </div>

            <!-- Tab Content -->
            <div id="description-content" class="tab-content">
                <div class="bg-gradient-to-r from-blue-50 to-green-50 rounded-xl p-6 border border-gray-100">
                    <p class="text-gray-700 leading-relaxed text-base">
                        {{ $product->description ?: 'Deskripsi produk belum tersedia.' }}
                    </p>
                </div>
            </div>

            <div id="specifications-content" class="tab-content hidden">
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"></path>
                            </svg>
                            Detail Produk
                        </h4>
                        <dl class="space-y-3">
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <dt class="text-sm font-medium text-gray-600">Kategori:</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $product->category->name ?? 'Tidak dikategorikan' }}</dd>
                            </div>
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <dt class="text-sm font-medium text-gray-600">Stok:</dt>
                                <dd class="text-sm font-semibold {{ $product->stock > 0 ? 'text-green-600' : 'text-red-600' }}">
                                    {{ $product->stock }} unit
                                </dd>
                            </div>
                            @if($product->weight)
                            <div class="flex justify-between border-b border-gray-200 pb-2">
                                <dt class="text-sm font-medium text-gray-600">Berat:</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $product->weight }} gram</dd>
                            </div>
                            @endif
                            <div class="flex justify-between">
                                <dt class="text-sm font-medium text-gray-600">Tanggal Ditambahkan:</dt>
                                <dd class="text-sm font-semibold text-gray-900">{{ $product->created_at->format('d M Y') }}</dd>
                            </div>
                        </dl>
                    </div>
                    
                    <div class="bg-gradient-to-r from-green-50 to-emerald-50 rounded-xl p-6 border border-green-200">
                        <h4 class="font-semibold text-gray-900 mb-4 flex items-center">
                            <svg class="w-5 h-5 mr-2 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 16h-1v-4h-1m1-4h.01M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                            </svg>
                            Informasi Tambahan
                        </h4>
                        <div class="space-y-3">
                            <div class="flex items-center text-green-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">Produk Original</span>
                            </div>
                            <div class="flex items-center text-green-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">Garansi Kualitas</span>
                            </div>
                            <div class="flex items-center text-green-700">
                                <svg class="w-4 h-4 mr-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">Pengiriman Aman</span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div id="reviews-content" class="tab-content hidden">
                @if($product->reviews && $product->reviews->count() > 0)
                    <div class="space-y-6">
                        @foreach($product->reviews as $review)
                            <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200 hover:shadow-lg transition duration-300">
                                <div class="flex items-center justify-between mb-4">
                                    <div class="flex items-center space-x-3">
                                        <div class="w-10 h-10 bg-gradient-to-r from-blue-600 to-blue-700 rounded-full flex items-center justify-center text-white text-sm font-semibold">
                                            {{ strtoupper(substr($review->user->name ?? 'U', 0, 1)) }}
                                        </div>
                                        <div>
                                            <span class="font-semibold text-gray-900">{{ $review->user->name ?? 'Pelanggan' }}</span>
                                            <div class="flex items-center mt-1">
                                                @for($i = 1; $i <= 5; $i++)
                                                    <svg class="w-4 h-4 {{ $i <= $review->rating ? 'text-yellow-400' : 'text-gray-300' }}" fill="currentColor" viewBox="0 0 20 20">
                                                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                                    </svg>
                                                @endfor
                                                <span class="text-xs text-gray-500 ml-2">{{ $review->created_at->format('d M Y') }}</span>
                                            </div>
                                        </div>
                                    </div>
                                    
                                    <!-- Admin Actions for Reviews -->
                                    @auth
                                        @if(auth()->user()->role === 'admin')
                                            <div class="flex space-x-2">
                                                <button onclick="editReview({{ $review->id }}, '{{ addslashes($review->review ?? '') }}', {{ $review->rating }})" 
                                                        class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-yellow-500 to-orange-500 text-white text-xs font-medium rounded-lg hover:from-yellow-600 hover:to-orange-600 transition duration-200">
                                                    <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                                    </svg>
                                                    Edit
                                                </button>
                                                <form action="{{ route('admin.reviews.destroy', $review) }}" method="POST" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" 
                                                            onclick="return confirm('Yakin ingin menghapus review ini?')"
                                                            class="inline-flex items-center px-3 py-1.5 bg-gradient-to-r from-red-500 to-red-600 text-white text-xs font-medium rounded-lg hover:from-red-600 hover:to-red-700 transition duration-200">
                                                        <svg class="w-3 h-3 mr-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6m1-10V4a1 1 0 00-1-1h-4a1 1 0 00-1 1v3M4 7h16"></path>
                                                        </svg>
                                                        Hapus
                                                    </button>
                                                </form>
                                            </div>
                                        @endif
                                    @endauth
                                </div>
                                
                                <div class="text-gray-700 leading-relaxed" id="review-text-{{ $review->id }}">
                                    "{{ $review->review ?? $review->comment ?? 'Tidak ada komentar' }}"
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
                                                <textarea name="review" rows="3" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">{{ $review->review ?? $review->comment ?? '' }}</textarea>
                                            </div>
                                            <div class="flex space-x-2">
                                                <button type="submit" class="px-4 py-2 bg-gradient-to-r from-green-600 to-green-700 text-white rounded-lg hover:from-green-700 hover:to-green-800 transition duration-200 text-sm font-medium">
                                                    Simpan
                                                </button>
                                                <button type="button" onclick="cancelEdit({{ $review->id }})" class="px-4 py-2 bg-gradient-to-r from-gray-600 to-gray-700 text-white rounded-lg hover:from-gray-700 hover:to-gray-800 transition duration-200 text-sm font-medium">
                                                    Batal
                                                </button>
                                            </div>
                                        </div>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @else
                    <div class="text-center py-12">
                        <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-8 border border-gray-200">
                            <svg class="w-16 h-16 mx-auto text-gray-400 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                            </svg>
                            <h3 class="text-lg font-semibold text-gray-900 mb-2">Belum Ada Ulasan</h3>
                            <p class="text-gray-500 mb-4">Jadilah yang pertama memberikan ulasan untuk produk ini!</p>
                            @auth
                                @if(auth()->user()->role !== 'admin')
                                    <button class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200">
                                        <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 6v6m0 0v6m0-6h6m-6 0H6"></path>
                                        </svg>
                                        Tulis Ulasan Pertama
                                    </button>
                                @endif
                            @else
                                <a href="{{ route('login') }}" class="inline-flex items-center px-6 py-3 bg-gradient-to-r from-blue-600 to-blue-700 text-white font-medium rounded-lg hover:from-blue-700 hover:to-blue-800 transition duration-200">
                                    <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
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

    <!-- Sales Trend Chart for Admin -->
    @auth
        @if(auth()->user()->role === 'admin')
            <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-8 overflow-hidden">
                <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                        <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                        </svg>
                        Trend Penjualan Produk
                    </h3>
                </div>
                
                <div class="p-6">
                    <div class="bg-gradient-to-r from-gray-50 to-blue-50 rounded-xl p-6 border border-gray-200 mb-6">
                        <canvas id="salesChart" width="400" height="200"></canvas>
                    </div>
                    
                    <!-- Sales Statistics -->
                    <div class="grid grid-cols-1 md:grid-cols-4 gap-4">
                        <div class="bg-gradient-to-r from-blue-50 to-blue-100 rounded-xl p-4 border border-blue-200">
                            <div class="text-2xl font-bold text-blue-600">{{ $salesData['total_sold'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600 font-medium">Total Terjual</div>
                        </div>
                        <div class="bg-gradient-to-r from-green-50 to-green-100 rounded-xl p-4 border border-green-200">
                            <div class="text-2xl font-bold text-green-600">Rp {{ number_format($salesData['total_revenue'] ?? 0, 0, ',', '.') }}</div>
                            <div class="text-sm text-gray-600 font-medium">Total Pendapatan</div>
                        </div>
                        <div class="bg-gradient-to-r from-yellow-50 to-yellow-100 rounded-xl p-4 border border-yellow-200">
                            <div class="text-2xl font-bold text-yellow-600">{{ $salesData['this_month'] ?? 0 }}</div>
                            <div class="text-sm text-gray-600 font-medium">Bulan Ini</div>
                        </div>
                        <div class="bg-gradient-to-r from-purple-50 to-purple-100 rounded-xl p-4 border border-purple-200">
                            <div class="text-2xl font-bold text-purple-600">{{ number_format($salesData['avg_monthly'] ?? 0, 1) }}</div>
                            <div class="text-sm text-gray-600 font-medium">Rata-rata/Bulan</div>
                        </div>
                    </div>
                </div>
            </div>
        @endif
    @endauth

    <!-- Related Products -->
    @if(isset($relatedProducts) && $relatedProducts->count() > 0)
        <div class="bg-white rounded-xl shadow-lg border border-gray-100 mt-8 overflow-hidden">
            <div class="bg-gradient-to-r from-gray-50 to-blue-50 px-6 py-4 border-b border-gray-200">
                <h3 class="text-lg font-semibold text-gray-900 flex items-center">
                    <svg class="w-5 h-5 mr-2 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                    Produk Serupa
                    <span class="ml-2 bg-blue-100 text-blue-800 text-sm font-medium px-2.5 py-0.5 rounded-full">
                        {{ $relatedProducts->count() }} produk
                    </span>
                </h3>
            </div>
            
            <div class="p-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-4 gap-6">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="bg-white border border-gray-200 rounded-xl shadow-sm hover:shadow-lg transition duration-300 overflow-hidden group flex flex-col h-full">
                            <div class="relative">
                                @if($relatedProduct->images && $relatedProduct->images->count() > 0)
                                    <img src="{{ $relatedProduct->images->first()->url }}" 
                                         class="w-full h-48 object-cover group-hover:scale-105 transition duration-300" 
                                         alt="{{ $relatedProduct->name }}">
                                @else
                                    <div class="w-full h-48 bg-gradient-to-br from-gray-100 to-gray-200 flex items-center justify-center">
                                        <div class="text-center">
                                            <svg class="w-12 h-12 text-gray-400 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                            </svg>
                                            <span class="text-gray-500 text-sm">No Image</span>
                                        </div>
                                    </div>
                                @endif
                                
                                <!-- Category Badge -->
                                <div class="absolute top-3 left-3">
                                    <span class="bg-blue-600 text-white text-xs font-medium px-2.5 py-1 rounded-full shadow-lg">
                                        {{ $relatedProduct->category->name ?? 'Parfum' }}
                                    </span>
                                </div>
                                
                                <!-- Stock Badge -->
                                <div class="absolute top-3 right-3">
                                    <span class="px-2.5 py-1 text-xs font-medium rounded-full shadow-lg {{ $relatedProduct->stock > 0 ? 'bg-green-100 text-green-800' : 'bg-red-100 text-red-800' }}">
                                        Stock: {{ $relatedProduct->stock }}
                                    </span>
                                </div>
                                
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
                            
                            <!-- Card Content with flexbox to ensure equal heights -->
                            <div class="p-5 flex flex-col flex-1">
                                <!-- Product Title - Fixed height with line clamp -->
                                <h5 class="text-lg font-semibold text-gray-900 mb-2 min-h-[3.5rem] line-clamp-2 leading-relaxed">
                                    {{ $relatedProduct->name }}
                                </h5>
                                
                                <!-- Product Description - Fixed height with line clamp -->
                                <p class="text-gray-600 text-sm mb-4 min-h-[4.5rem] line-clamp-3 leading-relaxed">
                                    {{ Str::limit($relatedProduct->description, 100) }}
                                </p>
                                
                                <!-- Rating -->
                                <div class="flex items-center gap-1 mb-4">
                                    <div class="flex text-yellow-400">
                                        @for($i = 1; $i <= 5; $i++)
                                            <svg class="w-3 h-3" fill="currentColor" viewBox="0 0 20 20">
                                                <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                            </svg>
                                        @endfor
                                    </div>
                                    <span class="text-xs text-gray-500">({{ $relatedProduct->reviews ? $relatedProduct->reviews->count() : 0 }})</span>
                                </div>
                                
                                <!-- Spacer to push price and button to bottom -->
                                <div class="flex-1"></div>
                                
                                <!-- Price Section - Fixed at bottom -->
                                <div class="mb-4">
                                    @if($relatedProduct->discount && $relatedProduct->discount->is_active)
                                        <div class="flex items-center space-x-2 mb-1">
                                            <span class="text-sm text-gray-500 line-through">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</span>
                                            <span class="bg-red-100 text-red-800 text-xs font-medium px-2 py-1 rounded-full">
                                                -{{ $relatedProduct->discount->percentage }}%
                                            </span>
                                        </div>
                                        <div class="text-xl font-bold text-green-600">Rp {{ number_format($relatedProduct->discounted_price, 0, ',', '.') }}</div>
                                    @else
                                        <div class="text-xl font-bold text-green-600">Rp {{ number_format($relatedProduct->price, 0, ',', '.') }}</div>
                                    @endif
                                </div>
                                
                                <!-- Action Button - Fixed at bottom -->
                                <a href="{{ route('products.show-detail', $relatedProduct) }}" 
                                   class="block w-full bg-gradient-to-r from-blue-600 to-blue-700 hover:from-blue-700 hover:to-blue-800 text-white text-center py-2.5 rounded-lg font-medium transition duration-200 text-sm">
                                    Lihat Detail
                                </a>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
// Tab functionality
function showTab(tabName) {
    // Hide all tab contents
    document.querySelectorAll('.tab-content').forEach(content => {
        content.classList.add('hidden');
    });
    
    // Remove active state from all tab buttons
    document.querySelectorAll('.tab-button').forEach(button => {
        button.classList.remove('border-blue-500', 'text-blue-600');
        button.classList.add('border-transparent', 'text-gray-500');
    });
    
    // Show selected tab content
    document.getElementById(tabName + '-content').classList.remove('hidden');
    
    // Add active state to selected tab button
    const activeTab = document.getElementById(tabName + '-tab');
    activeTab.classList.remove('border-transparent', 'text-gray-500');
    activeTab.classList.add('border-blue-500', 'text-blue-600');
}

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
            updateCartQuantity(current + 1);
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
            updateCartQuantity(current - 1);
        }
    }
}

function updateCartQuantity(quantity) {
    const cartQuantityInput = document.getElementById('cartQuantity');
    if (cartQuantityInput) {
        cartQuantityInput.value = quantity;
    }
}

// Image gallery
function changeMainImage(imageUrl, imageIndex, thumbnailElement) {
    // Update main image
    document.getElementById('mainImage').src = imageUrl;
    
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
            modal.className = 'fixed inset-0 bg-black bg-opacity-75 z-50 flex items-center justify-center p-4 backdrop-blur-sm';
            
            const zoomedImage = document.createElement('img');
            zoomedImage.src = this.src;
            zoomedImage.className = 'max-w-full max-h-full object-contain rounded-xl cursor-zoom-out shadow-2xl';
            
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
            <button onclick="this.parentElement.parentElement.remove()" class="text-white hover:text-gray-200 transition duration-200">
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

// Update quantity input listener
document.addEventListener('DOMContentLoaded', function() {
    const quantityInput = document.getElementById('quantity');
    if (quantityInput) {
        quantityInput.addEventListener('change', function() {
            updateCartQuantity(this.value);
        });
    }
});
</script>
@endpush
