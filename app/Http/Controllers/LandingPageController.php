<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class LandingPageController extends Controller
{
    public function index()
    {
        // Jika ada produk di database, gunakan itu. Jika tidak, gunakan sample products
        $products = \App\Models\Product::take(6)->get();
        if ($products->isEmpty()) {
            $products = collect(config('landing.sample_products'))->map(function ($product) {
                return (object) $product;
            });
        }
        $pageData = [
            'hero' => [
                'slides' => [
                    [
                        'title' => 'Selamat Datang di TokoKu',
                        'subtitle' => 'Temukan produk berkualitas dengan harga terbaik',
                        'button_text' => 'Lihat Produk',
                        'button_link' => '#products',
                        'background' => 'linear-gradient(135deg, #667eea 0%, #764ba2 100%)'
                    ],
                    [
                        'title' => 'Kualitas Terjamin',
                        'subtitle' => 'Produk pilihan dengan standar kualitas internasional',
                        'button_text' => 'Pelajari Lebih Lanjut',
                        'button_link' => '#about',
                        'background' => 'linear-gradient(135deg, #f093fb 0%, #f5576c 100%)'
                    ],
                    [
                        'title' => 'Pengiriman Cepat',
                        'subtitle' => 'Gratis ongkir ke seluruh Indonesia untuk pembelian minimal',
                        'button_text' => 'Belanja Sekarang',
                        'button_link' => route('products.catalog'),
                        'background' => 'linear-gradient(135deg, #4facfe 0%, #00f2fe 100%)'
                    ]
                ]
            ],
            'about' => [
                'title' => config('app.about.title', 'Tentang TokoKu Store'),
                'description' => config('app.about.description', 'TokoKu Store adalah platform e-commerce terpercaya yang menyediakan berbagai produk berkualitas tinggi dengan harga kompetitif. Kami berkomitmen untuk memberikan pengalaman belanja terbaik untuk setiap pelanggan.'),
                'additional_info' => config('app.about.additional_info'),
                'vision' => config('app.about.vision'),
                'mission' => config('app.about.mission')
            ]
        ];

        
        return view('welcome', compact('products', 'pageData'));
    }
}
