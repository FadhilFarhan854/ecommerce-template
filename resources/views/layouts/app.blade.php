<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">
        <title>@yield('title', config('app.name', 'E-Commerce Template'))</title>
        
        <!-- Fonts -->
        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=inter:400,500,600,700" rel="stylesheet" />
        
        <!-- Tailwind CSS -->
        <script src="https://cdn.tailwindcss.com"></script>
        
        <!-- Heroicons -->
        <script src="https://unpkg.com/heroicons@2.0.18/20/solid/index.js" type="module"></script>
        
        <!-- Styles / Scripts -->
        @if (file_exists(public_path('build/manifest.json')) || file_exists(public_path('hot')))
            @vite(['resources/css/app.css', 'resources/js/app.js'])
        @endif
        
        <style>
            /* Custom styles for slider animations */
            .slide {
                opacity: 0;
                transition: opacity 1s ease-in-out;
            }
            .slide.active {
                opacity: 1;
            }
        </style>
        
        @stack('styles')
    </head>
    <body class="font-inter bg-gray-50">
        <!-- Header -->
        <header class="bg-white shadow-lg sticky top-0 z-50">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
                <nav class="flex justify-between items-center py-4">
                    <div class="text-2xl font-bold text-gray-900">
                        {{ $pageData['site']['name'] ?? config('app.name', 'E-Commerce Template') }}
                    </div>
                    
                    <!-- Desktop Navigation -->
                    <ul class="hidden md:flex space-x-8">
                        @if(isset($pageData['navigation']['main_menu']))
                            @if(auth()->check() && auth()->user()->role === 'admin' && isset($pageData['navigation']['admin_menu']))
                                @foreach($pageData['navigation']['admin_menu'] as $menu)
                                    <li>
                                        <a href="{{ $menu['url'] }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">
                                            {{ $menu['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                @foreach($pageData['navigation']['main_menu'] as $menu)
                                    <li>
                                        <a href="{{ $menu['url'] }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">
                                            {{ $menu['text'] }}
                                        </a>
                                    </li>
                                @endforeach
                            @endif
                        @else
                            <li><a href="{{ url('/') }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">Beranda</a></li>
                            <li><a href="#about" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">Tentang</a></li>
                            <li><a href="{{ route('products.catalog') }}" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">Produk</a></li>
                            <li><a href="#contact" class="text-gray-600 hover:text-blue-600 font-medium transition-colors duration-300">Kontak</a></li>
                        @endif
                    </ul>
                    
                    <!-- Authentication Navigation -->
                    <div class="flex items-center space-x-4">
                        @auth
                            <!-- Cart Icon -->
                            <div class="relative">
                                <a href="{{ route('cart.index') }}" class="p-2 rounded-full hover:bg-gray-100 transition-colors duration-200 relative">
                                    <svg class="w-6 h-6 text-gray-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 13h8"></path>
                                    </svg>
                                    
                                </a>
                            </div>
                            
                            <!-- User Menu -->
                            <div class="relative">
                                <button onclick="toggleDropdown(event)" class="flex items-center space-x-2 p-2 rounded-full hover:bg-gray-100 transition-colors duration-200">
                                    <div class="w-10 h-10 bg-blue-600 rounded-full flex items-center justify-center text-white font-semibold hover:bg-blue-700 transition-colors duration-200">
                                        {{ strtoupper(substr(Auth::user()->name, 0, 1)) }}
                                    </div>
                                </button>
                                
                                <div id="userDropdown" class="hidden absolute right-0 mt-2 w-64 bg-white rounded-lg shadow-lg border border-gray-200 py-2 z-50">
                                    <div class="px-4 py-3 border-b border-gray-200">
                                        <p class="text-sm font-semibold text-gray-900">{{ Auth::user()->name }}</p>
                                        <p class="text-sm text-gray-600">{{ Auth::user()->email }}</p>
                                    </div>
                                    
                                    <a href="{{ route('profile.show') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                                        </svg>
                                        Lihat Profile
                                    </a>
                                    
                                    <a href="{{ route('profile.edit') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"></path>
                                        </svg>
                                        Edit Profile
                                    </a>
                                    
                                    <a href="{{ route('cart.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4m0 0L7 13m0 0l-2.5 5M7 13l2.5 5m0 0H17M9 13h8"></path>
                                        </svg>
                                        Keranjang
                                    </a>

                                    <a href="{{ route('orders.history') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" stroke="currentColor" class="size-6">
                                            <path stroke-linecap="round" stroke-linejoin="round" d="M12 6v6h4.5m4.5 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
                                        </svg>

                                        History Pembelian
                                    </a>
                                    
                                    @if(auth()->check() && auth()->user()->role === 'admin')
                                    <a href="{{ route('finance.index') }}" class="flex items-center px-4 py-3 text-sm text-gray-700 hover:bg-gray-50 transition-colors duration-200">
                                        <svg class="w-5 h-5 mr-3 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 7h6m0 10v-3m-3 3h.01M9 17h.01M9 14h.01M12 14h.01M15 11h.01M12 11h.01M9 11h.01M7 21h10a2 2 0 002-2V5a2 2 0 00-2-2H7a2 2 0 00-2 2v14a2 2 0 002 2z"></path>
                                        </svg>
                                        Finance Management
                                    </a>
                                    @endif
                                    
                                    <div class="border-t border-gray-200 mt-2 pt-2">
                                        <form method="POST" action="{{ route('logout') }}">
                                            @csrf
                                            <button type="submit" class="flex items-center w-full px-4 py-3 text-sm text-red-600 hover:bg-red-50 transition-colors duration-200">
                                                <svg class="w-5 h-5 mr-3" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 16l4-4m0 0l-4-4m4 4H7m6 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3V7a3 3 0 013-3h4a3 3 0 013 3v1"></path>
                                                </svg>
                                                Logout
                                            </button>
                                        </form>
                                    </div>
                                </div>
                            </div>
                        @else
                            <!-- Guest Navigation -->
                            <div class="flex items-center space-x-3">
                                @if (Route::has('login'))
                                    <a href="{{ route('login') }}" class="px-4 py-2 text-gray-600 hover:text-gray-900 font-medium transition-colors duration-200">
                                        Masuk
                                    </a>
                                @endif
                                @if (Route::has('register'))
                                    <a href="{{ route('register') }}" class="px-6 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700 font-medium transition-colors duration-200">
                                        Daftar
                                    </a>
                                @endif
                            </div>
                        @endauth
                        
                        <!-- Mobile Menu Button -->
                        <button class="md:hidden p-2 rounded-lg hover:bg-gray-100" onclick="toggleMobileMenu()">
                            <svg class="w-6 h-6" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M4 6h16M4 12h16M4 18h16"></path>
                            </svg>
                        </button>
                    </div>
                </nav>
                
                <!-- Mobile Navigation -->
                <div id="mobileMenu" class="hidden md:hidden border-t border-gray-200 py-4">
                    @if(isset($pageData['navigation']['main_menu']))
                        @if(auth()->check() && auth()->user()->role === 'admin' && isset($pageData['navigation']['admin_menu']))
                            @foreach($pageData['navigation']['admin_menu'] as $menu)
                                <a href="{{ $menu['url'] }}" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">
                                    {{ $menu['text'] }}
                                </a>
                            @endforeach
                        @else
                            @foreach($pageData['navigation']['main_menu'] as $menu)
                                <a href="{{ $menu['url'] }}" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">
                                    {{ $menu['text'] }}
                                </a>
                            @endforeach
                        @endif
                    @else
                        <a href="{{ url('/') }}" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">Beranda</a>
                        <a href="#about" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">Tentang</a>
                        <a href="{{ route('products.catalog') }}" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">Produk</a>
                        <a href="#contact" class="block py-2 text-gray-600 hover:text-blue-600 font-medium">Kontak</a>
                    @endif
                </div>
            </div>
        </header>

        <!-- Main Content -->
        <main class="min-h-screen">
            @yield('content')
        </main>

        <!-- Footer -->
        <footer id="contact" class="bg-gray-900 text-white">
            <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-12">
                <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-8 mb-8">
                    <div class="lg:col-span-2">
                        <h3 class="text-xl font-bold mb-4">{{ $pageData['footer']['company_info']['name'] ?? 'TokoKu Store' }}</h3>
                        <p class="text-gray-300 mb-4 leading-relaxed">
                            {{ $pageData['footer']['company_info']['description'] ?? 'Platform e-commerce terpercaya dengan produk berkualitas dan pelayanan terbaik di Indonesia.' }}
                        </p>
                        @if(isset($pageData['contact']['operating_hours']))
                            <div class="text-gray-300">
                                <p class="font-semibold mb-2">Jam Operasional:</p>
                                <p>{{ $pageData['contact']['operating_hours']['weekdays'] }}</p>
                                <p>{{ $pageData['contact']['operating_hours']['weekend'] }}</p>
                            </div>
                        @endif
                    </div>
                    
                    <div>
                        <h3 class="text-xl font-bold mb-4">Kontak Kami</h3>
                        <div class="space-y-3">
                            @if(isset($pageData['contact']['address']))
                                <div class="flex items-start space-x-3">
                                    <svg class="w-5 h-5 text-gray-400 mt-1" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17.657 16.657L13.414 20.9a1.998 1.998 0 01-2.827 0l-4.244-4.243a8 8 0 1111.314 0z"></path>
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M15 11a3 3 0 11-6 0 3 3 0 016 0z"></path>
                                    </svg>
                                    <div class="text-gray-300">
                                        <p>{{ $pageData['contact']['address']['street'] }}</p>
                                        <p>{{ $pageData['contact']['address']['city'] }}, {{ $pageData['contact']['address']['postal_code'] }}</p>
                                    </div>
                                </div>
                            @endif
                            
                            @if(isset($pageData['contact']['phone']))
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 5a2 2 0 012-2h3.28a1 1 0 01.948.684l1.498 4.493a1 1 0 01-.502 1.21l-2.257 1.13a11.042 11.042 0 005.516 5.516l1.13-2.257a1 1 0 011.21-.502l4.493 1.498a1 1 0 01.684.949V19a2 2 0 01-2 2h-1C9.716 21 3 14.284 3 6V5z"></path>
                                    </svg>
                                    <span class="text-gray-300">{{ $pageData['contact']['phone'] }}</span>
                                </div>
                            @endif
                            
                            @if(isset($pageData['contact']['email']))
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 8l7.89 5.26a2 2 0 002.22 0L21 8M5 19h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v10a2 2 0 002 2z"></path>
                                    </svg>
                                    <a href="mailto:{{ $pageData['contact']['email'] }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-200">
                                        {{ $pageData['contact']['email'] }}
                                    </a>
                                </div>
                            @endif
                            
                            @if(isset($pageData['contact']['whatsapp']))
                                <div class="flex items-center space-x-3">
                                    <svg class="w-5 h-5 text-gray-400" fill="currentColor" viewBox="0 0 24 24">
                                        <path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347m-5.421 7.403h-.004a9.87 9.87 0 01-5.031-1.378l-.361-.214-3.741.982.998-3.648-.235-.374a9.86 9.86 0 01-1.51-5.26c.001-5.45 4.436-9.884 9.888-9.884 2.64 0 5.122 1.03 6.988 2.898a9.825 9.825 0 012.893 6.994c-.003 5.45-4.437 9.884-9.885 9.884m8.413-18.297A11.815 11.815 0 0012.05 0C5.495 0 .16 5.335.157 11.892c0 2.096.547 4.142 1.588 5.945L.057 24l6.305-1.654a11.882 11.882 0 005.683 1.448h.005c6.554 0 11.89-5.335 11.893-11.893A11.821 11.821 0 0020.885 3.488"/>
                                    </svg>
                                    <span class="text-gray-300">{{ $pageData['contact']['whatsapp'] }}</span>
                                </div>
                            @endif
                        </div>
                    </div>
                    
                    @if(isset($pageData['footer']['links']['services']) || isset($pageData['footer']['links']['information']))
                        <div>
                            @if(isset($pageData['footer']['links']['services']))
                                <h3 class="text-lg font-semibold mb-4">{{ $pageData['footer']['links']['services']['title'] }}</h3>
                                <ul class="space-y-2">
                                    @foreach($pageData['footer']['links']['services']['items'] as $item)
                                        <li>
                                            <a href="{{ $item['url'] }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-200">
                                                {{ $item['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                            
                            @if(isset($pageData['footer']['links']['information']))
                                <h3 class="text-lg font-semibold mb-4 mt-6">{{ $pageData['footer']['links']['information']['title'] }}</h3>
                                <ul class="space-y-2">
                                    @foreach($pageData['footer']['links']['information']['items'] as $item)
                                        <li>
                                            <a href="{{ $item['url'] }}" class="text-gray-300 hover:text-blue-400 transition-colors duration-200">
                                                {{ $item['text'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            @endif
                        </div>
                    @endif
                </div>
                
                <div class="border-t border-gray-800 pt-6 text-center">
                    <p class="text-gray-400">
                        {!! $pageData['footer']['copyright'] ?? '&copy; 2024 TokoKu Store. Semua hak cipta dilindungi. | Template E-Commerce Laravel' !!}
                    </p>
                </div>
            </div>
        </footer>

        @stack('scripts')
        
        <script>
            function toggleDropdown(event) {
                event.preventDefault();
                const dropdown = document.getElementById('userDropdown');
                dropdown.classList.toggle('hidden');
            }

            function toggleMobileMenu() {
                const mobileMenu = document.getElementById('mobileMenu');
                mobileMenu.classList.toggle('hidden');
            }

            // Close dropdown when clicking outside
            document.addEventListener('click', function(event) {
                const userMenu = event.target.closest('.relative');
                const dropdown = document.getElementById('userDropdown');
                
                if (dropdown && !userMenu) {
                    dropdown.classList.add('hidden');
                }
            });

            @auth
            // Load cart count on page load
            document.addEventListener('DOMContentLoaded', function() {
                loadCartCount();
            });

            async function loadCartCount() {
                try {
                    const response = await fetch('{{ route("cart.count") }}', {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest',
                            'X-CSRF-TOKEN': '{{ csrf_token() }}'
                        },
                        credentials: 'same-origin'
                    });
                    
                    if (response.ok) {
                        const data = await response.json();
                        const cartCountElement = document.getElementById('cart-count');
                        if (cartCountElement) {
                            const count = data.count || 0;
                            cartCountElement.textContent = count;
                            cartCountElement.style.display = count > 0 ? 'flex' : 'none';
                        }
                    }
                } catch (error) {
                    console.log('Could not load cart count');
                }
            }

            // Function to update cart count (called from other pages)
            window.updateCartCount = loadCartCount;
            @endauth
        </script>
    </body>
</html>
