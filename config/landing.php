<?php

return [
    /*
    |--------------------------------------------------------------------------
    | Site Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi umum untuk website
    |
    */
    'site' => [
        'name' => env('SITE_NAME', 'quickstart e-commerce'),
        'description' => env('SITE_DESCRIPTION', 'Platform e-commerce terpercaya dengan produk berkualitas dan pelayanan terbaik di Indonesia.'),
        'tagline' => env('SITE_TAGLINE', 'Belanja Smart, Hidup Berkualitas'),
        'logo' => env('SITE_LOGO', '/images/logo.png'),
        'favicon' => env('SITE_FAVICON', '/favicon.ico'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Hero Section Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk slider hero section
    |
    */
    'hero' => [
        'slides' => [
            [
                'title' => 'Selamat Datang di Rama Perfume',
                'subtitle' => 'Temukan parfum berkualitas dengan harga terbaik',
                'button_text' => 'Lihat Produk',
                'button_link' => "{{ route('products.catalog') }}",
                'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)',
            ],
           
            [
                'title' => 'Promo Spesial',
                'subtitle' => 'Hemat hingga 50% untuk semua kategori produk',
                'button_text' => 'Belanja Sekarang',
                'button_link' => '#products',
                'background' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)',
            ],
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | About Section Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi untuk section tentang kami
    |
    */
    'about' => [
        'title' => 'Tentang Rama Perfume',
        'description' => 'Rama Perfume adalah platform e-commerce terpercaya yang menyediakan berbagai produk berkualitas tinggi dengan harga kompetitif. Kami berkomitmen untuk memberikan pengalaman belanja terbaik untuk setiap pelanggan.',
        'additional_info' => 'Dengan layanan customer service 24/7, pengiriman cepat, dan jaminan kualitas produk, kami telah melayani ribuan pelanggan di seluruh Indonesia sejak tahun 2020.',
        'vision' => 'Menjadi platform e-commerce pilihan utama di Indonesia',
        'mission' => 'Memberikan akses mudah ke produk berkualitas dengan pelayanan terbaik',
    ],

    /*
    |--------------------------------------------------------------------------
    | Contact Information
    |--------------------------------------------------------------------------
    |
    | Informasi kontak perusahaan
    |
    */
    'contact' => [
        'address' => [
            'street' => env('CONTACT_ADDRESS_STREET', 'Jl. Contoh No. 123'),
            'city' => env('CONTACT_ADDRESS_CITY', 'Jakarta Pusat'),
            'postal_code' => env('CONTACT_ADDRESS_POSTAL', '10110'),
        ],
        'phone' => env('CONTACT_PHONE', '+62 21 1234 5678'),
        'email' => env('CONTACT_EMAIL', 'info@tokoku.com'),
        'whatsapp' => env('CONTACT_WHATSAPP', '+62 812 3456 7890'),
        'operating_hours' => [
            'weekdays' => 'Senin - Jumat: 08:00 - 17:00 WIB',
            'weekend' => 'Sabtu - Minggu: 09:00 - 15:00 WIB',
        ],
    ],

    /*
    |--------------------------------------------------------------------------
    | Navigation Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi menu navigasi
    |
    */
    'navigation' => [
        'main_menu' => [
            ['text' => 'Beranda', 'url' => '/', 'anchor' => '#home'],
            ['text' => 'Tentang', 'url' => '/#about', 'anchor' => '#about'],
            ['text' => 'Produk', 'url' => '/catalog', 'anchor' => '#catalog'],
            ['text' => 'Kontak', 'url' => '#contact', 'anchor' => '#contact'],
            ['text' => 'FAQ', 'url' => '/faq', 'anchor' => '#faq'],
        ],
        'admin_menu' =>[
            ['text' => 'Dashboard', 'url' => '/admin-dashboard', 'anchor' => '#dashboard'],
            ['text' => 'Produk', 'url' => '/products', 'anchor' => '#products'],
            ['text' => 'Kategori', 'url' => '/categories', 'anchor' => '#categories'],
            ['text' => 'Pesanan', 'url' => '/orders', 'anchor' => '#orders'],
            ['text' => 'Pengguna', 'url' => '/users', 'anchor' => '#users'],
           
        ]
    ],

    /*
    |--------------------------------------------------------------------------
    | Footer Configuration
    |--------------------------------------------------------------------------
    |
    | Konfigurasi footer
    |
    */
    'footer' => [
        'links' => [
            'services' => [
                'title' => 'Layanan',
                'items' => [
                    ['text' => 'Bantuan Pelanggan', 'url' => '#'],
                    ['text' => 'Kebijakan Pengembalian', 'url' => '#'],
                    ['text' => 'Metode Pembayaran', 'url' => '#'],
                    ['text' => 'Pengiriman', 'url' => '#'],
                    ['text' => 'FAQ', 'url' => '#'],
                ],
            ],
            'information' => [
                'title' => 'Informasi',
                'items' => [
                    ['text' => 'Tentang Kami', 'url' => '#'],
                    ['text' => 'Syarat & Ketentuan', 'url' => '#'],
                    ['text' => 'Kebijakan Privasi', 'url' => '#'],
                    ['text' => 'Karir', 'url' => '#'],
                    ['text' => 'Blog', 'url' => '#'],
                ],
            ],
        ],
        'copyright' => '&copy; 2024 TokoKu Store. Semua hak cipta dilindungi. | Template E-Commerce Laravel',
    ],

    /*
    |--------------------------------------------------------------------------
    | Sample Products
    |--------------------------------------------------------------------------
    |
    | Produk contoh untuk ditampilkan di landing page
    |
    */
    'sample_products' => [
        [
            'name' => 'Smartphone Flagship',
            'description' => 'Smartphone terbaru dengan teknologi canggih, kamera berkualitas tinggi, dan performa yang luar biasa.',
            'price' => 8999000,
            'image' => null,
        ],
        [
            'name' => 'Laptop Gaming',
            'description' => 'Laptop gaming dengan spesifikasi tinggi, cocok untuk gaming dan pekerjaan berat lainnya.',
            'price' => 15499000,
            'image' => null,
        ],
        [
            'name' => 'Headphone Wireless',
            'description' => 'Headphone wireless dengan noise cancelling, kualitas suara premium dan baterai tahan lama.',
            'price' => 2299000,
            'image' => null,
        ],
        [
            'name' => 'Smart Watch',
            'description' => 'Smart watch dengan fitur lengkap untuk monitoring kesehatan dan aktivitas harian Anda.',
            'price' => 3599000,
            'image' => null,
        ],
        [
            'name' => 'Kamera DSLR',
            'description' => 'Kamera DSLR profesional dengan kualitas gambar superior untuk fotografi dan videografi.',
            'price' => 12899000,
            'image' => null,
        ],
        [
            'name' => 'Speaker Bluetooth',
            'description' => 'Speaker bluetooth portabel dengan suara bass yang powerful dan desain yang elegan.',
            'price' => 899000,
            'image' => null,
        ],
    ],
];
