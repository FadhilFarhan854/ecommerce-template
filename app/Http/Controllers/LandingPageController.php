<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Banner;

class LandingPageController extends Controller
{
    public function index()
    {
        // Jika ada produk di database, gunakan itu. Jika tidak, gunakan sample products
        $products = \App\Models\Product::with(['images', 'discount'])->take(6)->get();
        if ($products->isEmpty()) {
            $products = collect(config('landing.sample_products'))->map(function ($product) {
                return (object) $product;
            });
        }

        // Get active banners
        $banners = Banner::where('status', true)->latest()->get();

        // Tidak perlu mengirim pageData lagi karena sudah dihandle oleh PageDataComposer
        // Data akan otomatis tersedia di semua view melalui View Composer
        
        return view('welcome', compact('products', 'banners'));
    }
}
