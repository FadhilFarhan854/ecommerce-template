@extends('layouts.app')

@section('title', 'Beranda - E-Commerce Template')

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        const slides = document.querySelectorAll('.slide');
        const dots = document.querySelectorAll('.slider-dot');
        const prevBtn = document.querySelector('.slider-arrow.prev');
        const nextBtn = document.querySelector('.slider-arrow.next');
        let currentSlide = 0;
        
        function showSlide(index) {
            slides.forEach(slide => slide.classList.remove('active'));
            dots.forEach(dot => dot.classList.remove('active'));
            slides[index].classList.add('active');
            dots[index].classList.add('active');
        }
        
        function nextSlide() {
            currentSlide = (currentSlide + 1) % slides.length;
            showSlide(currentSlide);
        }
        
        function prevSlide() {
            currentSlide = (currentSlide - 1 + slides.length) % slides.length;
            showSlide(currentSlide);
        }
        
        // Event listeners
        if (nextBtn) nextBtn.addEventListener('click', nextSlide);
        if (prevBtn) prevBtn.addEventListener('click', prevSlide);
        
        dots.forEach((dot, index) => {
            dot.addEventListener('click', () => {
                currentSlide = index;
                showSlide(currentSlide);
            });
        });
        
        // Auto slide every 5 seconds
        if (slides.length > 1) {
            setInterval(nextSlide, 5000);
        }
        
        // Initialize first slide
        showSlide(0);
    });

    // Modal functions
    function openModal(id) {
        const modal = document.getElementById('modal-' + id);
        if (modal) {
            modal.classList.remove('hidden');
            modal.classList.add('flex');
            document.body.style.overflow = 'hidden';
        }
    }
    
    function closeModal(id) {
        const modal = document.getElementById('modal-' + id);
        if (modal) {
            modal.classList.add('hidden');
            modal.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    }
    
    // Close modal when clicking outside
    window.addEventListener('click', function(e) {
        if (e.target.classList.contains('bg-black') && e.target.classList.contains('bg-opacity-50')) {
            e.target.classList.add('hidden');
            e.target.classList.remove('flex');
            document.body.style.overflow = 'auto';
        }
    });

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
        } else if (type === 'info') {
            border.className = 'bg-white border-l-4 border-blue-500 rounded-lg shadow-lg p-4';
            icon.className = 'w-6 h-6 text-blue-500';
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

    function showLoginMessage() {
        showToast('Silakan daftar di halaman katalog untuk melihat produk yang tersedia!', 'info');
    }

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
    
    // Update cart count in navigation (if exists)
    async function updateCartCount() {
        if (window.updateCartCount) {
            window.updateCartCount();
        }
    }
</script>
@endpush

@section('content')
<!-- Hero Section with Slider -->
<section id="home" class="relative h-96 md:h-[500px] overflow-hidden">
    <div class="slider relative w-full h-full">
        @if(isset($pageData['hero']['slides']))
            @foreach($pageData['hero']['slides'] as $index => $slide)
                <div class="slide absolute inset-0 flex items-center justify-center {{ $index == 0 ? 'active' : '' }}" 
                     style="background: {{ $slide['background'] }};">
                    <div class="text-center text-white px-4">
                        <h2 class="text-3xl md:text-5xl font-bold mb-4">{{ $slide['title'] }}</h2>
                        <p class="text-lg md:text-xl mb-6 max-w-2xl">{{ $slide['subtitle'] }}</p>
                        <a href="{{ $slide['button_link'] }}" 
                           class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-300">
                            {{ $slide['button_text'] }}
                        </a>
                    </div>
                </div>
            @endforeach
        @else
            <div class="slide absolute inset-0 flex items-center justify-center active bg-gradient-to-br from-blue-600 to-purple-700">
                <div class="text-center text-white px-4">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Selamat Datang di TokoKu</h2>
                    <p class="text-lg md:text-xl mb-6 max-w-2xl">Temukan produk berkualitas dengan harga terbaik</p>
                    <a href="#products" 
                       class="inline-block px-8 py-3 bg-white text-blue-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-300">
                        Lihat Produk
                    </a>
                </div>
            </div>
            <div class="slide absolute inset-0 flex items-center justify-center bg-gradient-to-br from-green-600 to-blue-600">
                <div class="text-center text-white px-4">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Kualitas Terjamin</h2>
                    <p class="text-lg md:text-xl mb-6 max-w-2xl">Produk pilihan dengan standar kualitas internasional</p>
                    <a href="#about" 
                       class="inline-block px-8 py-3 bg-white text-green-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-300">
                        Pelajari Lebih Lanjut
                    </a>
                </div>
            </div>
            <div class="slide absolute inset-0 flex items-center justify-center bg-gradient-to-br from-purple-600 to-pink-600">
                <div class="text-center text-white px-4">
                    <h2 class="text-3xl md:text-5xl font-bold mb-4">Pengiriman Cepat</h2>
                    <p class="text-lg md:text-xl mb-6 max-w-2xl">Gratis ongkir ke seluruh Indonesia untuk pembelian minimal</p>
                    <a href="{{ route('products.catalog') }}" 
                       class="inline-block px-8 py-3 bg-white text-purple-600 rounded-lg font-semibold hover:bg-gray-100 transition-colors duration-300">
                        Belanja Sekarang
                    </a>
                </div>
            </div>
        @endif
    </div>
    
    <!-- Slider Navigation Arrows -->
    <button class="slider-arrow prev absolute left-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button class="slider-arrow next absolute right-4 top-1/2 transform -translate-y-1/2 bg-black bg-opacity-50 text-white p-3 rounded-full hover:bg-opacity-75 transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
    
    <!-- Slider Dots -->
    <div class="slider-nav absolute bottom-6 left-1/2 transform -translate-x-1/2 flex space-x-3">
        @if(isset($pageData['hero']['slides']))
            @foreach($pageData['hero']['slides'] as $index => $slide)
                <button class="slider-dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 transition-all duration-300 {{ $index == 0 ? 'active bg-opacity-100' : '' }}"></button>
            @endforeach
        @else
            <button class="slider-dot w-3 h-3 rounded-full bg-white bg-opacity-100 transition-all duration-300 active"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 transition-all duration-300"></button>
            <button class="slider-dot w-3 h-3 rounded-full bg-white bg-opacity-50 hover:bg-opacity-75 transition-all duration-300"></button>
        @endif
    </div>
</section>

<!-- About Section -->
<section id="about" class="py-16 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center max-w-4xl mx-auto">
            <h2 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">
                {{ $pageData['about']['title'] ?? 'Tentang TokoKu Store' }}
            </h2>
            <p class="text-lg text-gray-600 mb-6 leading-relaxed">
                {{ $pageData['about']['description'] ?? 'TokoKu Store adalah platform e-commerce terpercaya yang menyediakan berbagai produk berkualitas tinggi dengan harga kompetitif. Kami berkomitmen untuk memberikan pengalaman belanja terbaik untuk setiap pelanggan.' }}
            </p>
            @if(isset($pageData['about']['additional_info']))
                <p class="text-lg text-gray-600 mb-6 leading-relaxed">{{ $pageData['about']['additional_info'] }}</p>
            @endif
            
            @if(isset($pageData['about']['vision']) || isset($pageData['about']['mission']))
                <div class="grid md:grid-cols-2 gap-8 mt-8">
                    @if(isset($pageData['about']['vision']))
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Visi</h3>
                            <p class="text-gray-600">{{ $pageData['about']['vision'] }}</p>
                        </div>
                    @endif
                    @if(isset($pageData['about']['mission']))
                        <div class="bg-white p-6 rounded-lg shadow-sm">
                            <h3 class="text-xl font-semibold text-gray-900 mb-3">Misi</h3>
                            <p class="text-gray-600">{{ $pageData['about']['mission'] }}</p>
                        </div>
                    @endif
                </div>
            @endif
        </div>
    </div>
</section>

<!-- Products Section -->
<section id="products" class="py-16 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <h2 class="text-3xl md:text-4xl font-bold text-center text-gray-900 mb-12">Katalog Produk</h2>
        
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            @if($products && $products->count() > 0)
                @foreach($products as $product)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="h-48 bg-gray-200 flex items-center justify-center overflow-hidden">
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ $product->images->first()->url }}" alt="{{ $product->name }}" class="w-full h-full object-cover">
                            @else
                                <div class="text-gray-400 text-center">
                                    <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                    </svg>
                                    <span class="text-sm">Gambar Produk</span>
                                </div>
                            @endif
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product->name }}</h3>
                            <p class="text-gray-600 mb-4 line-clamp-3">{{ Str::limit($product->description, 100) }}</p>
                            <div class="text-2xl font-bold text-blue-600 mb-4">
                                Rp {{ number_format($product->price, 0, ',', '.') }}
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openModal({{ $product->id ?? 'default' }})"
                                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300">
                                    Lihat Detail
                                </button>
                                @auth
                                    <button onclick="addToCart({{ $product->id ?? 0 }})"
                                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300">
                                        + Keranjang
                                    </button>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300 text-center">
                                        + Keranjang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @else
                <!-- Default fallback products -->
                @php
                    $defaultProducts = [
                        ['name' => 'Smartphone Flagship', 'description' => 'Smartphone terbaru dengan teknologi canggih, kamera berkualitas tinggi, dan performa yang luar biasa.', 'price' => 8999000],
                        ['name' => 'Laptop Gaming', 'description' => 'Laptop gaming dengan spesifikasi tinggi, cocok untuk gaming dan pekerjaan berat lainnya.', 'price' => 15499000],
                        ['name' => 'Headphone Wireless', 'description' => 'Headphone wireless dengan noise cancelling, kualitas suara premium dan baterai tahan lama.', 'price' => 2299000],
                        ['name' => 'Smart Watch', 'description' => 'Smart watch dengan fitur lengkap untuk monitoring kesehatan dan aktivitas harian Anda.', 'price' => 3599000],
                        ['name' => 'Kamera DSLR', 'description' => 'Kamera DSLR profesional dengan kualitas gambar superior untuk fotografi dan videografi.', 'price' => 12899000],
                        ['name' => 'Speaker Bluetooth', 'description' => 'Speaker bluetooth portabel dengan suara bass yang powerful dan desain yang elegan.', 'price' => 899000],
                    ];
                @endphp
                
                @foreach($defaultProducts as $product)
                    <div class="bg-white rounded-xl shadow-lg overflow-hidden hover:shadow-xl transition-shadow duration-300">
                        <div class="h-48 bg-gray-200 flex items-center justify-center">
                            <div class="text-gray-400 text-center">
                                <svg class="w-16 h-16 mx-auto mb-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 00-2 2v12a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-sm">Gambar Produk</span>
                            </div>
                        </div>
                        <div class="p-6">
                            <h3 class="text-xl font-semibold text-gray-900 mb-2">{{ $product['name'] }}</h3>
                            <p class="text-gray-600 mb-4">{{ $product['description'] }}</p>
                            <div class="text-2xl font-bold text-blue-600 mb-4">
                                Rp {{ number_format($product['price'], 0, ',', '.') }}
                            </div>
                            <div class="flex gap-2">
                                <button onclick="openModal('default-{{ $loop->index }}')"
                                   class="flex-1 px-4 py-2 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700 transition-colors duration-300">
                                    Lihat Detail
                                </button>
                                @auth
                                    <button onclick="showLoginMessage()"
                                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300">
                                        + Keranjang
                                    </button>
                                @else
                                    <a href="{{ route('login') }}"
                                       class="flex-1 px-4 py-2 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700 transition-colors duration-300 text-center">
                                        + Keranjang
                                    </a>
                                @endif
                            </div>
                        </div>
                    </div>
                @endforeach
            @endif
        </div>
        
        <div class="text-center">
            <a href="{{ route('products.catalog') }}" 
               class="inline-block px-8 py-3 bg-gray-600 text-white rounded-lg font-semibold hover:bg-gray-700 transition-colors duration-300">
                Lihat Semua Produk
            </a>
        </div>
    </div>
</section>

<!-- Product Modals for Real Products -->
@if($products && $products->count() > 0)
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
@endif

<!-- Product Modals for Default Products -->
@php
    $defaultProducts = [
        ['name' => 'Smartphone Flagship', 'description' => 'Smartphone terbaru dengan teknologi canggih, kamera berkualitas tinggi, dan performa yang luar biasa.', 'price' => 8999000],
        ['name' => 'Laptop Gaming', 'description' => 'Laptop gaming dengan spesifikasi tinggi, cocok untuk gaming dan pekerjaan berat lainnya.', 'price' => 15499000],
        ['name' => 'Headphone Wireless', 'description' => 'Headphone wireless dengan noise cancelling, kualitas suara premium dan baterai tahan lama.', 'price' => 2299000],
        ['name' => 'Smart Watch', 'description' => 'Smart watch dengan fitur lengkap untuk monitoring kesehatan dan aktivitas harian Anda.', 'price' => 3599000],
        ['name' => 'Kamera DSLR', 'description' => 'Kamera DSLR profesional dengan kualitas gambar superior untuk fotografi dan videografi.', 'price' => 12899000],
        ['name' => 'Speaker Bluetooth', 'description' => 'Speaker bluetooth portabel dengan suara bass yang powerful dan desain yang elegan.', 'price' => 899000],
    ];
@endphp

@if(!$products || $products->count() == 0)
    @foreach($defaultProducts as $index => $product)
        <div id="modal-default-{{ $index }}" class="hidden fixed inset-0 bg-black bg-opacity-50 z-50 items-center justify-center p-4">
            <div class="bg-white rounded-lg shadow-lg max-w-2xl w-full max-h-[90vh] overflow-y-auto">
                <!-- Modal Header -->
                <div class="flex items-center justify-between p-6 border-b">
                    <h2 class="text-2xl font-bold text-gray-800">{{ $product['name'] }}</h2>
                    <button onclick="closeModal('default-{{ $index }}')" class="text-gray-400 hover:text-gray-600 text-2xl font-bold">
                        &times;
                    </button>
                </div>
                
                <!-- Modal Body -->
                <div class="p-6">
                    <div class="grid md:grid-cols-2 gap-6">
                        <!-- Product Image -->
                        <div class="h-80 bg-gray-100 flex items-center justify-center rounded-lg overflow-hidden">
                            <div class="text-gray-400 text-center">
                                <svg class="w-20 h-20 mx-auto mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 16l4.586-4.586a2 2 0 012.828 0L16 16m-2-2l1.586-1.586a2 2 0 012.828 0L20 14m-6-6h.01M6 20h12a2 2 0 002-2V6a2 2 0 00-2-2H6a2 2 0 002 2z"></path>
                                </svg>
                                <span class="text-lg">Gambar Produk</span>
                            </div>
                        </div>
                        
                        <!-- Product Details -->
                        <div class="space-y-4">
                            <div class="text-green-600 font-bold text-3xl">
                                Rp {{ number_format($product['price'], 0, ',', '.') }}
                            </div>
                            
                            <div class="border-t pt-4">
                                <h4 class="font-semibold text-gray-800 mb-2">Deskripsi Produk</h4>
                                <p class="text-gray-600 leading-relaxed">{{ $product['description'] }}</p>
                            </div>
                            
                            @auth
                                <div class="space-y-4">
                                    <div class="flex items-center space-x-2">
                                        <label class="text-sm font-medium text-gray-700">Jumlah:</label>
                                        <input type="number" value="1" min="1" 
                                               class="w-20 px-3 py-1 border border-gray-300 rounded-md text-center focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
                                    </div>
                                    
                                    <button onclick="showLoginMessage()"
                                       class="w-full px-6 py-3 bg-green-600 text-white font-medium rounded-lg hover:bg-green-700 transition">
                                        Tambah ke Keranjang
                                    </button>
                                </div>
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
@endif

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
