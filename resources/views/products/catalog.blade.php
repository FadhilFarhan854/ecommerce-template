@extends('layouts.app')

@section('title', 'Katalog Produk - ' . config('app.name'))

@section('content')
<!-- Header -->
<div class="bg-gray-50 py-10 mb-8">
    <div class="container mx-auto px-4">
        <h1 class="text-4xl font-bold text-gray-800 text-center mb-2">Katalog Produk</h1>
        <p class="text-gray-500 text-center max-w-xl mx-auto">
            Temukan berbagai produk berkualitas dengan harga terbaik
        </p>
    </div>
</div>

<div class="container mx-auto px-4 md:px-10">
    <!-- Filter Section -->
    <div class="bg-white p-6 rounded-lg shadow mb-8">
        <form method="GET" action="{{ route('products.catalog') }}">
            <div class="flex flex-wrap gap-4 items-end">
                <!-- Search -->
                <div class="flex flex-col flex-1 min-w-[200px]">
                    <label for="search" class="text-sm font-medium text-gray-700">Cari Produk</label>
                    <input type="text" id="search" name="search" value="{{ $search }}" 
                           placeholder="Nama produk..."
                           class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                </div>

                <!-- Category -->
                <div class="flex flex-col min-w-[200px]">
                    <label for="category" class="text-sm font-medium text-gray-700">Kategori</label>
                    <select id="category" name="category"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="">Semua Kategori</option>
                        @foreach($categories as $cat)
                            <option value="{{ $cat->name }}" {{ $category == $cat->name ? 'selected' : '' }}>
                                {{ $cat->name }}
                            </option>
                        @endforeach
                    </select>
                </div>

                <!-- Sort -->
                <div class="flex flex-col min-w-[150px]">
                    <label for="sort" class="text-sm font-medium text-gray-700">Urutkan</label>
                    <select id="sort" name="sort"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="created_at" {{ $sort == 'created_at' ? 'selected' : '' }}>Terbaru</option>
                        <option value="name" {{ $sort == 'name' ? 'selected' : '' }}>Nama A-Z</option>
                        <option value="price" {{ $sort == 'price' ? 'selected' : '' }}>Harga</option>
                    </select>
                </div>

                <!-- Order -->
                <div class="flex flex-col min-w-[120px]">
                    <label for="order" class="text-sm font-medium text-gray-700">Arah</label>
                    <select id="order" name="order"
                            class="mt-1 px-3 py-2 border border-gray-300 rounded-md text-sm focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                        <option value="asc" {{ $order == 'asc' ? 'selected' : '' }}>Naik</option>
                        <option value="desc" {{ $order == 'desc' ? 'selected' : '' }}>Turun</option>
                    </select>
                </div>

                <!-- Buttons -->
                <div class="flex gap-2">
                    <button type="submit"
                            class="px-4 py-2 bg-blue-600 text-white font-medium rounded-md hover:bg-blue-700 transition">
                        Filter
                    </button>
                    <a href="{{ route('products.catalog') }}"
                       class="px-4 py-2 bg-gray-600 text-white font-medium rounded-md hover:bg-gray-700 transition">
                        Reset
                    </a>
                </div>
            </div>
        </form>
    </div>

    @if($products->count() > 0)
        <!-- Results Info -->
        <div class="text-center text-gray-500 mb-8 mt-12">
            Menampilkan {{ $products->firstItem() }} - {{ $products->lastItem() }} dari {{ $products->total() }} produk
        </div>

        <!-- Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-6">
            @foreach($products as $product)
                <div class="bg-white rounded-lg shadow hover:shadow-lg transition overflow-hidden">
                    <div class="h-52 bg-gray-100 flex items-center justify-center">
                        @if($product->images && $product->images->count() > 0)
                            <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @elseif($product->image)
                            <img src="{{ $product->image }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                        @else
                            <div class="flex flex-col items-center justify-center text-gray-400">
                                <svg class="w-16 h-16 mb-2" fill="currentColor" viewBox="0 0 20 20">
                                    <path fill-rule="evenodd" d="M4 3a2 2 0 00-2 2v10a2 2 0 002 2h12a2 2 0 002-2V5a2 2 0 00-2-2H4zm12 12H4l4-8 3 6 2-4 3 6z" clip-rule="evenodd"></path>
                                </svg>
                                <span class="text-sm">No Image</span>
                            </div>
                        @endif
                    </div>
                    <div class="p-4">
                        <div class="text-xs uppercase tracking-wide text-gray-500 mb-1">
                            {{ $product->category->name ?? 'Tanpa Kategori' }}
                        </div>
                        <h3 class="text-lg font-semibold text-gray-800 mb-1">{{ $product->name }}</h3>
                        <p class="text-sm text-gray-600 mb-3 line-clamp-2">{{ Str::limit($product->description, 100) }}</p>
                        <div class="text-green-600 font-bold text-lg mb-3">
                            Rp {{ number_format($product->price, 0, ',', '.') }}
                        </div>
                        <div class="flex gap-2">
                            <button onclick="openModal({{ $product->id }})"
                               class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-md font-medium hover:bg-blue-700 transition">
                                Lihat Detail
                            </button>
                            @auth
                                <button onclick="addToCart({{ $product->id }})"
                                   class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md font-medium hover:bg-green-700 transition">
                                    + Keranjang
                                </button>
                            @else
                                <a href="{{ route('login') }}"
                                   class="flex-1 px-4 py-2 bg-green-600 text-white rounded-md font-medium hover:bg-green-700 transition text-center">
                                    + Keranjang
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            @endforeach
        </div>

        <!-- Pagination -->
        <div class="mt-10 mb-8 flex justify-center">
            {{ $products->appends(request()->query())->links() }}
        </div>
    @else
        <div class="text-center py-12 text-gray-500">
            <h3 class="text-2xl font-semibold text-gray-700 mb-2">Tidak ada produk yang ditemukan</h3>
            <p class="mb-4">Coba ubah filter pencarian atau kata kunci yang berbeda.</p>
            <a href="{{ route('products.catalog') }}"
               class="inline-block px-5 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700 transition">
               Lihat Semua Produk
            </a>
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
