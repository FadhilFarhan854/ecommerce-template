<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use App\Models\Banner;
use App\Models\OrderItem;
use App\Models\Product;

class LandingPageController extends Controller
{
    public function index()
    {
        // Pertama, coba ambil produk terpopuler berdasarkan penjualan
        $products = Product::with('images', 'discount', 'reviews')
            ->whereHas('orderItems') // Hanya produk yang memiliki orderItems
            ->withCount(['orderItems as total_sold' => function ($query) {
                $query->select(DB::raw('SUM(quantity)'));
            }])
            ->orderByDesc('total_sold')
            ->take(8)
            ->get();
        
        // Jika produk terpopuler kosong, ambil produk terbaru
        if ($products->isEmpty()) {
            $products = Product::with('images', 'discount', 'reviews')
                ->latest()
                ->take(8)
                ->get();
        }

        // Get active banners
        $banners = Banner::where('status', true)->latest()->get();

        // Tidak perlu mengirim pageData lagi karena sudah dihandle oleh PageDataComposer
        // Data akan otomatis tersedia di semua view melalui View Composer
        
        return view('welcome', compact('products', 'banners'));
    }
}
