@extends('layouts.app')

@section('title', 'Beranda - Your Fragrance Life Partner')

@push('styles')
<style>
    .hero-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
    }
    .feature-card {
        transition: all 0.3s ease;
    }
    .feature-card:hover {
        transform: translateY(-5px);
    }
    .product-card {
        transition: all 0.3s ease;
        border: 1px solid #e5e7eb;
    }
    .product-card:hover {
        transform: translateY(-8px);
        box-shadow: 0 20px 25px -5px rgba(0, 0, 0, 0.1), 0 10px 10px -5px rgba(0, 0, 0, 0.04);
    }
    .text-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
        -webkit-background-clip: text;
        -webkit-text-fill-color: transparent;
        background-clip: text;
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
    .accent-blue {
        color: #3b82f6;
    }
    .accent-green {
        color: #10b981;
    }
    .bg-blue-gradient {
        background: linear-gradient(135deg, #eff6ff 0%, #dbeafe 100%);
    }
    .bg-green-gradient {
        background: linear-gradient(135deg, #f0fdf4 0%, #dcfce7 100%);
    }
    .newsletter-gradient {
        background: linear-gradient(135deg, #3b82f6 0%, #10b981 100%);
    }
    /* Smooth scroll behavior */
    html {
        scroll-behavior: smooth;
        scroll-padding-top: 80px;
    }
    /* Reduce hero height to prevent overlap */
    .hero-section {
        min-height: 85vh;
    }
    @media (max-width: 768px) {
        .hero-section {
            min-height: 80vh;
        }
    }
    
    /* Banner Slider Styles */
    .banner-slider {
        position: relative;
        width: 100%;
        height: 100vh;
        overflow: hidden;
    }
    
    .banner-slide {
        position: absolute;
        top: 0;
        left: 0;
        width: 100%;
        height: 100%;
        opacity: 0;
        transition: opacity 0.5s ease-in-out;
        z-index: 1;
    }
    
    .banner-slide.active {
        opacity: 1;
        z-index: 2;
    }
    
    .banner-dot {
        cursor: pointer;
        transition: all 0.3s ease;
    }
    
    .banner-dot:hover {
        transform: scale(1.2);
    }
    
    /* Animation delays for fallback hero */
    .animation-delay-2000 {
        animation-delay: 2s;
    }
    .animation-delay-4000 {
        animation-delay: 4s;
    }
    .animation-delay-6000 {
        animation-delay: 6s;
    }
</style>
@endpush

@push('scripts')
<script>
    document.addEventListener('DOMContentLoaded', function() {
        // Banner Slider Functionality
        const bannerSlides = document.querySelectorAll('.banner-slide');
        const bannerDots = document.querySelectorAll('.banner-dot');
        const prevButton = document.querySelector('.banner-prev');
        const nextButton = document.querySelector('.banner-next');
        
        let currentSlide = 0;
        let slideInterval;
        
        // Only initialize slider if there are multiple banners
        if (bannerSlides.length > 1) {
            // Function to show specific slide
            function showSlide(index) {
                // Hide all slides
                bannerSlides.forEach(slide => slide.classList.remove('active'));
                bannerDots.forEach(dot => {
                    dot.classList.remove('bg-white');
                    dot.classList.add('bg-white', 'bg-opacity-50');
                });
                
                // Show current slide
                bannerSlides[index].classList.add('active');
                bannerDots[index].classList.remove('bg-opacity-50');
                bannerDots[index].classList.add('bg-white');
                
                currentSlide = index;
            }
            
            // Function to go to next slide
            function nextSlide() {
                const next = (currentSlide + 1) % bannerSlides.length;
                showSlide(next);
            }
            
            // Function to go to previous slide
            function prevSlide() {
                const prev = (currentSlide - 1 + bannerSlides.length) % bannerSlides.length;
                showSlide(prev);
            }
            
            // Auto-play slider
            function startSlideInterval() {
                slideInterval = setInterval(nextSlide, 5000); // Change slide every 5 seconds
            }
            
            function stopSlideInterval() {
                clearInterval(slideInterval);
            }
            
            // Event listeners for dots
            bannerDots.forEach((dot, index) => {
                dot.addEventListener('click', () => {
                    showSlide(index);
                    stopSlideInterval();
                    startSlideInterval(); // Restart auto-play
                });
            });
            
            // Event listeners for navigation buttons
            if (prevButton) {
                prevButton.addEventListener('click', () => {
                    prevSlide();
                    stopSlideInterval();
                    startSlideInterval(); // Restart auto-play
                });
            }
            
            if (nextButton) {
                nextButton.addEventListener('click', () => {
                    nextSlide();
                    stopSlideInterval();
                    startSlideInterval(); // Restart auto-play
                });
            }
            
            // Pause auto-play on hover
            const bannerSlider = document.querySelector('.banner-slider');
            if (bannerSlider) {
                bannerSlider.addEventListener('mouseenter', stopSlideInterval);
                bannerSlider.addEventListener('mouseleave', startSlideInterval);
            }
            
            // Start auto-play
            startSlideInterval();
            
            // Keyboard navigation
            document.addEventListener('keydown', (e) => {
                if (e.key === 'ArrowLeft') {
                    prevSlide();
                    stopSlideInterval();
                    startSlideInterval();
                } else if (e.key === 'ArrowRight') {
                    nextSlide();
                    stopSlideInterval();
                    startSlideInterval();
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

        // Smooth scrolling for anchor links
        document.querySelectorAll('a[href^="#"]').forEach(anchor => {
            anchor.addEventListener('click', function (e) {
                e.preventDefault();
                const target = document.querySelector(this.getAttribute('href'));
                if (target) {
                    target.scrollIntoView({
                        behavior: 'smooth',
                        block: 'start'
                    });
                }
            });
        });

        // Parallax effect for hero section
        window.addEventListener('scroll', function() {
            const scrolled = window.pageYOffset;
            const heroSection = document.querySelector('.hero-gradient').parentElement;
            if (heroSection) {
                heroSection.style.transform = `translateY(${scrolled * 0.4}px)`;
            }
        });

        // Product card hover effects
        document.querySelectorAll('.product-card').forEach(card => {
            card.addEventListener('mouseenter', function() {
                this.style.transform = 'translateY(-8px)';
            });
            
            card.addEventListener('mouseleave', function() {
                this.style.transform = 'translateY(0)';
            });
        });

        // Feature card animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -100px 0px'
        };

        const observer = new IntersectionObserver(function(entries) {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        document.querySelectorAll('.feature-card').forEach(card => {
            card.style.opacity = '0';
            card.style.transform = 'translateY(30px)';
            card.style.transition = 'all 0.6s ease';
            observer.observe(card);
        });
    });

    // Toast notification functions - make them global
    window.showToast = function(message, type = 'success') {
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
    
    window.hideToast = function() {
        document.getElementById('toast').classList.add('hidden');
    }

    // Cart functions - make them global
    window.addToCart = async function(productId, quantity = 1) {
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
    
    window.showLoginMessage = function() {
        showToast('Silakan daftar di halaman katalog untuk melihat produk yang tersedia!', 'info');
    }

    // Update cart count in navigation (if exists)
    window.updateCartCount = async function() {
        if (window.updateCartCount) {
            window.updateCartCount();
        }
    }
</script>
@endpush

@section('content')
<!-- Dynamic Banner Section -->
@if($banners && $banners->count() > 0)
<section class="hero-section relative overflow-hidden">
    <div class="banner-slider">
        @foreach($banners as $index => $banner)
        <div class="banner-slide {{ $index === 0 ? 'active' : '' }}" data-slide="{{ $index }}">
            <!-- Banner Image Background -->
            <div class="absolute inset-0">
                <img src="{{ asset('storage/' . $banner->image) }}" 
                     alt="Banner {{ $index + 1 }}" 
                     class="w-full h-full object-cover">
                <!-- Dark overlay for better text readability -->
                <div class="absolute inset-0 bg-black bg-opacity-40"></div>
            </div>
            
            <!-- Banner Content Overlay -->
            <div class="relative z-10 h-full flex items-center justify-center">
                <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
                    <div class="space-y-8">
                        <!-- Main headline -->
                        <h1 class="text-5xl md:text-7xl font-bold text-white leading-tight">
                            Your Fragrance<br>
                            <span class="text-blue-300">Life Partner</span>
                        </h1>
                        
                        <!-- Subtitle -->
                        <p class="text-xl md:text-2xl text-gray-100 max-w-3xl mx-auto leading-relaxed">
                            Melanjutkan perjalanan indra penciuman Anda dengan koleksi parfum premium yang menginspirasi dan memperkaya pengalaman harianmu.
                        </p>
                        
                        <!-- CTA Buttons -->
                        <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                            <a href="{{ route('products.catalog') }}" 
                               class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 inline-flex items-center gap-2">
                                <span>Jelajahi Koleksi</span>
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                                </svg>
                            </a>
                            <a href="#about" 
                               class="px-8 py-4 border-2 border-white text-white rounded-full font-semibold text-lg hover:bg-white hover:text-green-600 transition-all duration-300 transform hover:scale-105">
                                Tentang Kami
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        @endforeach
    </div>
    
    <!-- Banner Navigation -->
    @if($banners->count() > 1)
    <div class="absolute inset-x-0 bottom-8 z-20">
        <div class="flex justify-center space-x-2">
            @foreach($banners as $index => $banner)
            <button class="banner-dot w-3 h-3 rounded-full {{ $index === 0 ? 'bg-white' : 'bg-white bg-opacity-50' }} transition-all duration-300" 
                    data-slide="{{ $index }}"></button>
            @endforeach
        </div>
    </div>
    
    <!-- Banner Arrow Navigation -->
    <button class="banner-prev absolute left-4 top-1/2 transform -translate-y-1/2 z-20 bg-black bg-opacity-30 hover:bg-opacity-50 text-white p-3 rounded-full transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 19l-7-7 7-7"></path>
        </svg>
    </button>
    <button class="banner-next absolute right-4 top-1/2 transform -translate-y-1/2 z-20 bg-black bg-opacity-30 hover:bg-opacity-50 text-white p-3 rounded-full transition-all duration-300">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5l7 7-7 7"></path>
        </svg>
    </button>
    @endif
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce z-20">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>
@else
<!-- Fallback Hero Section when no banners -->
<section class="hero-section relative flex items-center justify-center overflow-hidden">
    <!-- Background with gradient -->
    <div class="absolute inset-0 hero-gradient"></div>
    
    <!-- Animated background elements -->
    <div class="absolute inset-0 opacity-10">
        <div class="absolute top-20 left-10 w-72 h-72 bg-white rounded-full mix-blend-multiply filter blur-xl animate-pulse"></div>
        <div class="absolute top-40 right-10 w-72 h-72 bg-blue-300 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-2000"></div>
        <div class="absolute -bottom-8 left-20 w-72 h-72 bg-green-300 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-4000"></div>
        <div class="absolute top-32 right-32 w-48 h-48 bg-blue-400 rounded-full mix-blend-multiply filter blur-xl animate-pulse animation-delay-6000"></div>
    </div>
    
    <div class="relative z-10 max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 text-center">
        <div class="space-y-8">
            <!-- Main headline -->
            <h1 class="text-5xl md:text-7xl font-bold text-white leading-tight">
                Your Fragrance<br>
                <span class="text-blue-300">Life Partner</span>
            </h1>
            
            <!-- Subtitle -->
            <p class="text-xl md:text-2xl text-gray-100 max-w-3xl mx-auto leading-relaxed">
                Melanjutkan perjalanan indra penciuman Anda dengan koleksi parfum premium yang menginspirasi dan memperkaya pengalaman harianmu.
            </p>
            
            <!-- CTA Buttons -->
            <div class="flex flex-col sm:flex-row gap-4 justify-center items-center">
                <a href="{{ route('products.catalog') }}" 
                   class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold text-lg hover:bg-gray-100 transition-all duration-300 transform hover:scale-105 inline-flex items-center gap-2">
                    <span>Jelajahi Koleksi</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
                <a href="#about" 
                   class="px-8 py-4 border-2 border-white text-white rounded-full font-semibold text-lg hover:bg-white hover:text-green-600 transition-all duration-300 transform hover:scale-105">
                    Tentang Kami
                </a>
            </div>
        </div>
    </div>
    
    <!-- Scroll indicator -->
    <div class="absolute bottom-8 left-1/2 transform -translate-x-1/2 text-white animate-bounce">
        <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 14l-7 7m0 0l-7-7m7 7V3"></path>
        </svg>
    </div>
</section>
@endif

<!-- Features Section - AZKO Style Benefits -->
<section class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-16">
          
        </div>
        
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8">
            <!-- Feature 1 -->
            <div class="feature-card bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 mx-auto mb-6 bg-blue-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8v4l3 3m6-3a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Pengiriman 24 Jam</h3>
                <p class="text-gray-600">Layanan express untuk wilayah Jakarta dan sekitarnya</p>
            </div>
            
            <!-- Feature 2 -->
            <div class="feature-card bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 mx-auto mb-6 bg-green-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Garansi Keaslian</h3>
                <p class="text-gray-600">100% original dengan sertifikat resmi dari brand</p>
            </div>
            
            <!-- Feature 3 -->
            <div class="feature-card bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 mx-auto mb-6 bg-blue-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 accent-blue" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Harga Terjangkau</h3>
                <p class="text-gray-600">Dapatkan parfum berkualitas dengan harga terjangkau</p>
            </div>
            
            <!-- Feature 4 -->
            <div class="feature-card bg-white rounded-xl p-8 text-center shadow-lg">
                <div class="w-16 h-16 mx-auto mb-6 bg-green-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 accent-green" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                    </svg>
                </div>
                <h3 class="text-xl font-semibold text-gray-900 mb-3">Tidak ada kandungan berbahaya</h3>
                <p class="text-gray-600">Formulasi aman dan telah teruji secara dermatologis</p>
            </div>
        </div>
    </div>
</section>

<!-- About Section - AZKO Inspired -->
<section id="about" class="py-16 bg-white">
    <div  class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="grid lg:grid-cols-2 gap-16 items-center">
            <div>
                <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-8 leading-tight">
                    Dari sini<br>
                    <span class="text-gradient">bisa lebih</span><br>
                    <div class="space-y-2">
                        <div class="text-2xl md:text-3xl text-gray-600">elegan</div>
                        <div class="text-2xl md:text-3xl text-gray-600">mempesona</div>
                        <div class="text-2xl md:text-3xl text-gray-600">berkesan</div>
                    </div>
                </h2>
                
                <p class="text-lg text-gray-600 mb-8 leading-relaxed">
                    {{ $pageData['about']['description'] ?? 'Melanjutkan legasi dalam dunia parfum, ini awal cerita baru kami untuk memperkaya pengalaman aroma dan memperkuat kepercayaan diri dengan inspirasi A-Z yang bisa diandalkan, kini dan seterusnya.' }}
                </p>
                
                <div class="grid grid-cols-3 gap-8 mb-8">
                    <div class="text-center">
                        <div class="text-3xl font-bold accent-blue">5+</div>
                        <div class="text-sm text-gray-600">tahun</div>
                        <div class="text-xs text-gray-500 mt-1">Pengalaman terpercaya</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold accent-green">500+</div>
                        <div class="text-sm text-gray-600">produk</div>
                        <div class="text-xs text-gray-500 mt-1">Koleksi premium pilihan</div>
                    </div>
                    <div class="text-center">
                        <div class="text-3xl font-bold accent-blue">Brand</div>
                        <div class="text-sm text-gray-600">Indonesia</div>
                        <div class="text-xs text-gray-500 mt-1">Kualitas internasional</div>
                    </div>
                </div>
                
                <a href="{{ route('products.catalog') }}" 
                   class="btn-primary-gradient inline-flex items-center gap-3 px-8 py-4 text-white rounded-full font-semibold text-lg">
                    <span>Lebih Lanjut</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
            
            <div class="relative">
                <div class="absolute inset-0 bg-green-gradient rounded-3xl transform rotate-6"></div>
                <div class="relative bg-white rounded-3xl p-8 shadow-2xl">
                    <div class="text-center">
                        <div class="w-24 h-24 mx-auto mb-6 bg-gradient-to-br from-blue-500 to-green-500 rounded-full flex items-center justify-center">
                            <svg class="w-12 h-12 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                            </svg>
                        </div>
                        <h3 class="text-2xl font-bold text-gray-900 mb-4">Temukan Aroma Anda</h3>
                        <p class="text-gray-600 mb-6">Setiap orang memiliki signature scent yang unik. Mari temukan parfum yang sempurna untuk kepribadian Anda.</p>
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Products Section - Modern Grid Layout -->
<section id="products" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Koleksi <span class="text-gradient">Terpopuler</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Siap untuk menjelajah lebih banyak? Temukan parfum yang sesuai dengan kepribadian dan gaya hidup Anda
            </p>
            <div class="inline-flex items-center gap-2 bg-white rounded-full px-6 py-3 shadow-lg">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-700">Always on stock !</span>
            </div>
        </div>
        
        <!-- Featured Products Grid -->
        <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 gap-8 mb-16">
            @if($products && $products->count() > 0)
                @foreach($products->take(6) as $product)
                    <div class="product-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <div class="relative h-64 bg-gradient-to-br from-gray-100 to-gray-200 overflow-hidden">
                            <!-- Badge Diskon -->
                            @if($product->discount && $product->discount->isActive())
                                <div class="absolute top-4 right-4 bg-red-500 text-white text-xs px-3 py-2 rounded-full font-bold z-10 shadow-lg">
                                    -{{ $product->discount->percentage }}%
                                </div>
                            @endif
                            
                            <!-- Bestseller Badge -->
                            <div class="absolute top-4 left-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-3 py-2 rounded-full font-bold z-10 shadow-lg">
                                ⭐ Bestseller
                            </div>
                            
                            @if($product->images && $product->images->count() > 0)
                                <img src="{{ $product->images->first()->url }}" 
                                     alt="{{ $product->name }}" 
                                     class="w-full h-full object-cover transition-transform duration-500 hover:scale-110">
                            @else
                                <div class="flex items-center justify-center h-full">
                                    <div class="text-center text-gray-400">
                                        <svg class="w-16 h-16 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                        </svg>
                                        <span class="text-sm font-medium">Parfum Premium</span>
                                    </div>
                                </div>
                            @endif
                            
                            <!-- Quick Actions -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                                <div class="flex gap-3">
                                    <button class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                    <button class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Product Category -->
                            <div class="text-xs font-semibold accent-blue uppercase tracking-wide mb-2">
                                {{ $product->category->name ?? 'Eau de Parfum' }}
                            </div>
                            
                            <!-- Product Name -->
                            <h3 class="text-xl font-bold text-gray-900 mb-3 line-clamp-2">{{ $product->name }}</h3>
                            
                            <!-- Product Description -->
                            <p class="text-gray-600 mb-4 text-sm line-clamp-2">{{ Str::limit($product->description, 80) }}</p>
                            
                            <!-- Rating -->
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">(4.8)</span>
                                <span class="text-xs text-gray-400">• 127 ulasan</span>
                            </div>
                            
                            <!-- Price Section -->
                            <div class="mb-6">
                                @if($product->discount && $product->discount->isActive())
                                    <div class="flex items-center gap-3 mb-2">
                                        <span class="text-2xl font-bold accent-blue">
                                            Rp {{ number_format($product->discount->getDiscountedPrice($product->price), 0, ',', '.') }}
                                        </span>
                                        <span class="text-gray-400 line-through text-lg">
                                            Rp {{ number_format($product->price, 0, ',', '.') }}
                                        </span>
                                    </div>
                                    <div class="text-sm text-green-600 font-medium">
                                        💚 Hemat Rp {{ number_format($product->discount->getDiscountAmount($product->price), 0, ',', '.') }}
                                    </div>
                                @else
                                    <div class="text-2xl font-bold accent-blue">
                                        Rp {{ number_format($product->price, 0, ',', '.') }}
                                    </div>
                                @endif
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <a href="{{ route('products.show-detail', $product) }}"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 accent-green rounded-xl font-semibold hover:bg-green-50 transition-all duration-300 text-center text-sm">
                                    Detail
                                </a>
                                @auth
                                    <button onclick="addToCart({{ $product->id ?? 0 }})"
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
            @else
                <!-- Default Products with Perfume Theme -->
                @php
                    $defaultPerfumes = [
                        ['name' => 'Chanel No. 5 Eau de Parfum', 'description' => 'Parfum klasik dengan aroma floral yang timeless dan elegan, cocok untuk segala suasana.', 'price' => 2890000, 'category' => 'Eau de Parfum'],
                        ['name' => 'Dior Sauvage EDT', 'description' => 'Aroma segar dan maskulin dengan notes bergamot dan pepper, sempurna untuk pria modern.', 'price' => 1890000, 'category' => 'Eau de Toilette'],
                        ['name' => 'Tom Ford Black Orchid', 'description' => 'Parfum mewah dengan aroma oriental yang misterius dan sensual untuk malam istimewa.', 'price' => 3290000, 'category' => 'Eau de Parfum'],
                        ['name' => 'Versace Bright Crystal', 'description' => 'Aroma segar dan feminin dengan notes buah dan bunga, cocok untuk wanita energik.', 'price' => 1590000, 'category' => 'Eau de Toilette'],
                        ['name' => 'Armani Code Homme', 'description' => 'Parfum elegan dengan aroma woody yang sophisticated untuk pria berkelas.', 'price' => 1790000, 'category' => 'Eau de Parfum'],
                        ['name' => 'Marc Jacobs Daisy', 'description' => 'Aroma manis dan playful dengan notes bunga daisy, sempurna untuk sehari-hari.', 'price' => 1690000, 'category' => 'Eau de Toilette'],
                    ];
                @endphp
                
                @foreach($defaultPerfumes as $index => $perfume)
                    <div class="product-card bg-white rounded-2xl overflow-hidden shadow-lg">
                        <div class="relative h-64 bg-gradient-to-br from-purple-100 to-pink-100 overflow-hidden">
                            <!-- Bestseller Badge -->
                            @if($index < 3)
                                <div class="absolute top-4 left-4 bg-gradient-to-r from-yellow-400 to-orange-500 text-white text-xs px-3 py-2 rounded-full font-bold z-10 shadow-lg">
                                    ⭐ Bestseller
                                </div>
                            @endif
                            
                            <div class="flex items-center justify-center h-full">
                                <div class="text-center text-purple-400">
                                    <svg class="w-20 h-20 mx-auto mb-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="1.5" d="M9.663 17h4.673M12 3v1m6.364 1.636l-.707.707M21 12h-1M4 12H3m3.343-5.657l-.707-.707m2.828 9.9a5 5 0 117.072 0l-.548.547A3.374 3.374 0 0014 18.469V19a2 2 0 11-4 0v-.531c0-.895-.356-1.754-.988-2.386l-.548-.547z"></path>
                                    </svg>
                                    <span class="text-sm font-medium">Parfum Premium</span>
                                </div>
                            </div>
                            
                            <!-- Quick Actions -->
                            <div class="absolute inset-0 bg-black bg-opacity-0 hover:bg-opacity-30 transition-all duration-300 flex items-center justify-center opacity-0 hover:opacity-100">
                                <div class="flex gap-3">
                                    <button class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4.318 6.318a4.5 4.5 0 000 6.364L12 20.364l7.682-7.682a4.5 4.5 0 00-6.364-6.364L12 7.636l-1.318-1.318a4.5 4.5 0 00-6.364 0z"></path>
                                        </svg>
                                    </button>
                                    <button class="w-12 h-12 bg-white rounded-full flex items-center justify-center shadow-lg transform hover:scale-110 transition-all duration-200">
                                        <svg class="w-5 h-5 text-gray-700" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 12a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M2.458 12C3.732 7.943 7.523 5 12 5c4.478 0 8.268 2.943 9.542 7-1.274 4.057-5.064 7-9.542 7-4.477 0-8.268-2.943-9.542-7z"></path>
                                        </svg>
                                    </button>
                                </div>
                            </div>
                        </div>
                        
                        <div class="p-6">
                            <!-- Product Category -->
                            <div class="text-xs font-semibold accent-blue uppercase tracking-wide mb-2">
                                {{ $perfume['category'] }}
                            </div>
                            
                            <!-- Product Name -->
                            <h3 class="text-xl font-bold text-gray-900 mb-3">{{ $perfume['name'] }}</h3>
                            
                            <!-- Product Description -->
                            <p class="text-gray-600 mb-4 text-sm">{{ $perfume['description'] }}</p>
                            
                            <!-- Rating -->
                            <div class="flex items-center gap-2 mb-4">
                                <div class="flex text-yellow-400">
                                    @for($i = 1; $i <= 5; $i++)
                                        <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                                            <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                                        </svg>
                                    @endfor
                                </div>
                                <span class="text-sm text-gray-500">(4.{{ rand(5,9) }})</span>
                                <span class="text-xs text-gray-400">• {{ rand(50,200) }} ulasan</span>
                            </div>
                            
                            <!-- Price -->
                            <div class="text-2xl font-bold accent-blue mb-6">
                                Rp {{ number_format($perfume['price'], 0, ',', '.') }}
                            </div>
                            
                            <!-- Action Buttons -->
                            <div class="flex gap-3">
                                <a href="{{ route('products.catalog') }}"
                                   class="flex-1 px-4 py-3 border-2 border-green-200 accent-green rounded-xl font-semibold hover:bg-green-50 transition-all duration-300 text-center text-sm">
                                    Detail
                                </a>
                                @auth
                                    <button onclick="showLoginMessage()"
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
            @endif
        </div>
        
        <!-- CTA Section -->
        <div class="text-center">
            <div class="bg-white rounded-2xl p-8 shadow-lg max-w-2xl mx-auto">
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Jelajahi Koleksi Lengkap</h3>
                <p class="text-gray-600 mb-6">Temukan lebih dari 500+ parfum premium dari brand ternama dunia</p>
                <a href="{{ route('products.catalog') }}" 
                   class="btn-primary-gradient inline-flex items-center gap-3 px-8 py-4 text-white rounded-full font-semibold text-lg">
                    <span>Lihat Semua Produk</span>
                    <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 8l4 4m0 0l-4 4m4-4H3"></path>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section>

<!-- Contact Section - Store Locations -->
<section id="contact" class="py-20 bg-gray-50">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <!-- Section Header -->
        <div class="text-center mb-16">
            <h2 class="text-4xl md:text-5xl font-bold text-gray-900 mb-6">
                Lokasi <span class="text-gradient">Toko Kami</span>
            </h2>
            <p class="text-xl text-gray-600 max-w-3xl mx-auto mb-8">
                Kunjungi langsung toko kami untuk merasakan pengalaman aroma yang tak terlupakan
            </p>
            <div class="inline-flex items-center gap-2 bg-white rounded-full px-6 py-3 shadow-lg">
                <div class="w-3 h-3 bg-green-500 rounded-full animate-pulse"></div>
                <span class="text-sm font-medium text-gray-700">Buka setiap hari 09:00 - 21:00</span>
            </div>
        </div>
        
        <!-- Store Cards Grid -->
        <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-8 mb-12">
            <!-- Store 1 - Rama Parfum Pusat -->
            <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                <!-- Store Badge -->
                <div class="inline-flex items-center gap-2 bg-blue-gradient text-blue-600 text-xs px-3 py-2 rounded-full font-bold mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path d="M9.049 2.927c.3-.921 1.603-.921 1.902 0l1.07 3.292a1 1 0 00.95.69h3.462c.969 0 1.371 1.24.588 1.81l-2.8 2.034a1 1 0 00-.364 1.118l1.07 3.292c.3.921-.755 1.688-1.54 1.118l-2.8-2.034a1 1 0 00-1.175 0l-2.8 2.034c-.784.57-1.838-.197-1.539-1.118l1.07-3.292a1 1 0 00-.364-1.118L2.98 8.72c-.783-.57-.38-1.81.588-1.81h3.461a1 1 0 00.951-.69l1.07-3.292z"></path>
                    </svg>
                    Toko Pusat
                </div>
                
                <!-- Store Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-blue-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                
                <!-- Store Info -->
                <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rama Parfum Pusat</h3>
                
                <!-- Address -->
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jl. Dewi Sartika No.9, RT.4/RW.7, Cililitan, Kec. Kramat jati, Kota Jakarta Timur, 13640
                    </p>
                </div>
                
                <!-- Phone -->
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+6281931400047" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        +62 819-3140-0047
                    </a>
                </div>
                
                <!-- Action Button -->
                <a href="https://maps.google.com/?q=Jl. Dewi Sartika No.9, RT.4/RW.7, Cililitan, Kec. Kramat jati, Kota Jakarta Timur" 
                   target="_blank"
                   class="w-full btn-primary-gradient px-4 py-3 text-white rounded-xl font-semibold text-center inline-block transition-all duration-300 hover:transform hover:scale-105">
                    Lihat di Maps
                </a>
            </div>
            
            <!-- Store 2 - Toko Rehlah Jakarta -->
            <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                <!-- Store Badge -->
                <div class="inline-flex items-center gap-2 bg-green-gradient text-green-600 text-xs px-3 py-2 rounded-full font-bold mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Jakarta Timur
                </div>
                
                <!-- Store Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-green-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 11V7a4 4 0 00-8 0v4M5 9h14l1 12H4L5 9z"></path>
                    </svg>
                </div>
                
                <!-- Store Info -->
                <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Toko Rehlah Jakarta</h3>
                
                <!-- Address -->
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jalan Ciliwung No 14 RT 4/RW6, Condet, Cililitan, Kramatjati Kota Jakarta Timur
                    </p>
                </div>
                
                <!-- Phone -->
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+6281281785073" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        +62 812-8178-5073
                    </a>
                </div>
                
                <!-- Action Button -->
                <a href="https://maps.google.com/?q=Jalan Ciliwung No 14 RT 4/RW6, Condet, Cililitan, Kramatjati Kota Jakarta Timur" 
                   target="_blank"
                   class="w-full btn-secondary-gradient px-4 py-3 text-white rounded-xl font-semibold text-center inline-block transition-all duration-300 hover:transform hover:scale-105">
                    Lihat di Maps
                </a>
            </div>
            
            <!-- Store 3 - Rama 1 -->
            <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                <!-- Store Badge -->
                <div class="inline-flex items-center gap-2 bg-green-gradient text-green-600 text-xs px-3 py-2 rounded-full font-bold mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Jakarta Timur
                </div>
                <!-- Store Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-blue-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                
                <!-- Store Info -->
                <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rama 1</h3>
                
                <!-- Address -->
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jl. Dewi Sartika No.535 Cililitan, Kramat Jati, Jakarta Timur 13640
                    </p>
                </div>
                
                <!-- Phone -->
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+6281818186664" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        +62 818-1818-6664
                    </a>
                </div>
                
                <!-- Action Button -->
                <a href="https://maps.google.com/?q=Jl. Dewi Sartika No.535 Cililitan, Kramat Jati, Jakarta Timur 13640" 
                   target="_blank"
                   class="w-full btn-primary-gradient px-4 py-3 text-white rounded-xl font-semibold text-center inline-block transition-all duration-300 hover:transform hover:scale-105">
                    Lihat di Maps
                </a>
            </div>
            
            <!-- Store 4 - Rama 2 (Tanah Abang) -->
            <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100">
                <!-- Store Badge -->
                <div class="inline-flex items-center gap-2 bg-green-gradient text-green-600 text-xs px-3 py-2 rounded-full font-bold mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Tanah Abang
                </div>
                
                <!-- Store Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-green-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                
                <!-- Store Info -->
                <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rama 2 (Tanah Abang)</h3>
                
                <!-- Address -->
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jl. Dewi Sartika No.535 Cililitan, Kramat Jati, Jakarta Timur 13640
                    </p>
                </div>
                
                <!-- Phone -->
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+6287872612181" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        +62 878-7261-2181
                    </a>
                </div>
                
                <!-- Action Button -->
                <a href="https://maps.google.com/?q=Jl. Dewi Sartika No.535 Cililitan, Kramat Jati, Jakarta Timur 13640" 
                   target="_blank"
                   class="w-full btn-secondary-gradient px-4 py-3 text-white rounded-xl font-semibold text-center inline-block transition-all duration-300 hover:transform hover:scale-105">
                    Lihat di Maps
                </a>
            </div>
            
            <!-- Store 5 - Rehlah Tegal -->
            <div class="feature-card bg-white rounded-2xl p-8 shadow-lg border border-gray-100 lg:col-span-1 md:col-start-2 lg:col-start-auto">
                <!-- Store Badge -->
                <div class="inline-flex items-center gap-2 bg-blue-gradient text-blue-600 text-xs px-3 py-2 rounded-full font-bold mb-6">
                    <svg class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20">
                        <path fill-rule="evenodd" d="M5.05 4.05a7 7 0 119.9 9.9L10 18.9l-4.95-4.95a7 7 0 010-9.9zM10 11a2 2 0 100-4 2 2 0 000 4z" clip-rule="evenodd"></path>
                    </svg>
                    Tegal
                </div>
                
                <!-- Store Icon -->
                <div class="w-16 h-16 mx-auto mb-6 bg-blue-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 21V5a2 2 0 00-2-2H7a2 2 0 00-2 2v16m14 0h2m-2 0h-5m-9 0H3m2 0h5M9 7h1m-1 4h1m4-4h1m-1 4h1m-5 10v-5a1 1 0 011-1h2a1 1 0 011 1v5m-4 0h4"></path>
                    </svg>
                </div>
                
                <!-- Store Info -->
                <h3 class="text-xl font-bold text-gray-900 mb-3 text-center">Rehlah Tegal</h3>
                
                <!-- Address -->
                <div class="flex items-start gap-3 mb-4">
                    <svg class="w-5 h-5 text-green-500 mt-0.5 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                    </svg>
                    <p class="text-gray-600 text-sm leading-relaxed">
                        Jln. Merpati No 134, Perempatan Puskesmas Randugunting Kota Tegal
                    </p>
                </div>
                
                <!-- Phone -->
                <div class="flex items-center gap-3 mb-6">
                    <svg class="w-5 h-5 text-blue-500 flex-shrink-0" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                    </svg>
                    <a href="tel:+6282328086878" class="text-blue-600 hover:text-blue-800 font-medium transition-colors duration-200">
                        +62 823-2808-6878
                    </a>
                </div>
                
                <!-- Action Button -->
                <a href="https://maps.google.com/?q=Jln. Merpati No 134, Perempatan Puskesmas Randugunting Kota Tegal" 
                   target="_blank"
                   class="w-full btn-primary-gradient px-4 py-3 text-white rounded-xl font-semibold text-center inline-block transition-all duration-300 hover:transform hover:scale-105">
                    Lihat di Maps
                </a>
            </div>
        </div>
        
        <!-- Contact CTA -->
        <div class="text-center">
            <div class="bg-white rounded-2xl p-8 shadow-lg max-w-2xl mx-auto">
                <div class="w-16 h-16 mx-auto mb-6 newsletter-gradient rounded-full flex items-center justify-center">
                    <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"></path>
                    </svg>
                </div>
                <h3 class="text-2xl font-bold text-gray-900 mb-4">Ada Pertanyaan?</h3>
                <p class="text-gray-600 mb-6">Tim customer service kami siap membantu Anda menemukan parfum yang tepat</p>
                <div class="flex flex-col sm:flex-row gap-4 justify-center">
                    
                    <a href="https://wa.me/6281931400047" 
                       target="_blank"
                       class="btn-secondary-gradient inline-flex items-center gap-3 px-6 py-3 text-white rounded-full font-semibold">
                        <svg class="w-5 h-5" fill="currentColor" viewBox="0 0 24 24">
                            <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.108"/>
                        </svg>
                        <span>WhatsApp</span>
                    </a>
                </div>
            </div>
        </div>
    </div>
</section>

<!-- Newsletter Section - AZKO Style -->
{{-- <section class="py-20 bg-white">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
        <div class="newsletter-gradient rounded-3xl p-12 text-center text-white">
            <h2 class="text-3xl md:text-4xl font-bold mb-6">
                Dapatkan info dan promo terbaru dari TokoKu
            </h2>
            <p class="text-xl mb-8 opacity-90">
                Jadilah yang pertama mengetahui koleksi terbaru dan penawaran eksklusif
            </p>
            
            <div class="max-w-md mx-auto">
                <div class="flex gap-3">
                    <input type="email" 
                           placeholder="Masukkan email Anda" 
                           class="flex-1 px-6 py-4 rounded-full text-gray-900 placeholder-gray-500 focus:outline-none focus:ring-4 focus:ring-white focus:ring-opacity-30">
                    <button class="px-8 py-4 bg-white text-blue-600 rounded-full font-semibold hover:bg-gray-100 transition-colors duration-300">
                        Kirim
                    </button>
                </div>
            </div>
            
            <!-- Social Media Links -->
            <div class="flex justify-center gap-4 mt-8">
                <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-colors duration-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M24 4.557c-.883.392-1.832.656-2.828.775 1.017-.609 1.798-1.574 2.165-2.724-.951.564-2.005.974-3.127 1.195-.897-.957-2.178-1.555-3.594-1.555-3.179 0-5.515 2.966-4.797 6.045-4.091-.205-7.719-2.165-10.148-5.144-1.29 2.213-.669 5.108 1.523 6.574-.806-.026-1.566-.247-2.229-.616-.054 2.281 1.581 4.415 3.949 4.89-.693.188-1.452.232-2.224.084.626 1.956 2.444 3.379 4.6 3.419-2.07 1.623-4.678 2.348-7.29 2.04 2.179 1.397 4.768 2.212 7.548 2.212 9.142 0 14.307-7.721 13.995-14.646.962-.695 1.797-1.562 2.457-2.549z"/>
                    </svg>
                </a>
                <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-colors duration-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12.017 0C5.396 0 .029 5.367.029 11.987c0 5.079 3.158 9.417 7.618 11.174-.105-.949-.199-2.403.041-3.439.219-.937 1.406-5.957 1.406-5.957s-.359-.72-.359-1.781c0-1.663.967-2.911 2.168-2.911 1.024 0 1.518.769 1.518 1.688 0 1.029-.653 2.567-.992 3.992-.285 1.193.6 2.165 1.775 2.165 2.128 0 3.768-2.245 3.768-5.487 0-2.861-2.063-4.869-5.008-4.869-3.41 0-5.409 2.562-5.409 5.199 0 1.033.394 2.143.889 2.741.097.118.112.221.085.342-.09.375-.293 1.199-.334 1.363-.053.225-.172.271-.402.165-1.495-.69-2.433-2.878-2.433-4.646 0-3.776 2.748-7.252 7.92-7.252 4.158 0 7.392 2.967 7.392 6.923 0 4.135-2.607 7.462-6.233 7.462-1.214 0-2.357-.629-2.75-1.378l-.748 2.853c-.271 1.043-1.002 2.35-1.492 3.146C9.57 23.812 10.763 24.009 12.017 24.009c6.624 0 11.99-5.367 11.99-12.014C24.007 5.36 18.641.001 12.017.001z"/>
                    </svg>
                </a>
                <a href="#" class="w-12 h-12 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-colors duration-300">
                    <svg class="w-6 h-6" fill="currentColor" viewBox="0 0 24 24">
                        <path d="M12 2.163c3.204 0 3.584.012 4.85.07 3.252.148 4.771 1.691 4.919 4.919.058 1.265.069 1.645.069 4.849 0 3.205-.012 3.584-.069 4.849-.149 3.225-1.664 4.771-4.919 4.919-1.266.058-1.644.07-4.85.07-3.204 0-3.584-.012-4.849-.07-3.26-.149-4.771-1.699-4.919-4.92-.058-1.265-.07-1.644-.07-4.849 0-3.204.013-3.583.07-4.849.149-3.227 1.664-4.771 4.919-4.919 1.266-.057 1.645-.069 4.849-.069zm0-2.163c-3.259 0-3.667.014-4.947.072-4.358.2-6.78 2.618-6.98 6.98-.059 1.281-.073 1.689-.073 4.948 0 3.259.014 3.668.072 4.948.2 4.358 2.618 6.78 6.98 6.98 1.281.058 1.689.072 4.948.072 3.259 0 3.668-.014 4.948-.072 4.354-.2 6.782-2.618 6.979-6.98.059-1.28.073-1.689.073-4.948 0-3.259-.014-3.667-.072-4.947-.196-4.354-2.617-6.78-6.979-6.98-1.281-.059-1.69-.073-4.949-.073zm0 5.838c-3.403 0-6.162 2.759-6.162 6.162s2.759 6.163 6.162 6.163 6.162-2.759 6.162-6.163c0-3.403-2.759-6.162-6.162-6.162zm0 10.162c-2.209 0-4-1.79-4-4 0-2.209 1.791-4 4-4s4 1.791 4 4c0 2.21-1.791 4-4 4zm6.406-11.845c-.796 0-1.441.645-1.441 1.44s.645 1.44 1.441 1.44c.795 0 1.439-.645 1.439-1.44s-.644-1.44-1.439-1.44z"/>
                    </svg>
                </a>
            </div>
        </div>
    </div>
</section> --}}

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
