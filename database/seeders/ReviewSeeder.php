<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Review;
use App\Models\Product;
use App\Models\User;

class ReviewSeeder extends Seeder
{
    public function run(): void
    {
        // Get some users and products for creating reviews
        $users = User::take(5)->get();
        $products = Product::take(3)->get();

        if ($users->isEmpty() || $products->isEmpty()) {
            return; // Skip if no users or products exist
        }

        $sampleReviews = [
            [
                'review' => 'Produk sangat bagus, kualitas sesuai ekspektasi. Pengiriman cepat dan packaging rapi. Sangat puas dengan pembelian ini!',
                'rating' => 5
            ],
            [
                'review' => 'Overall cukup baik, namun ada beberapa hal yang bisa diperbaiki. Harga sebanding dengan kualitas.',
                'rating' => 4
            ],
            [
                'review' => 'Produk standar, tidak terlalu istimewa tapi juga tidak mengecewakan. Sesuai dengan deskripsi.',
                'rating' => 3
            ],
            [
                'review' => 'Kualitas produk excellent! Sangat merekomendasikan untuk yang sedang mencari produk seperti ini. Worth every penny!',
                'rating' => 5
            ],
            [
                'review' => 'Bagus banget! Sudah beberapa kali beli dan selalu puas. Customer service juga responsive.',
                'rating' => 5
            ],
            [
                'review' => 'Lumayan bagus sih, tapi agak overpriced menurut saya. Mungkin bisa lebih murah lagi.',
                'rating' => 3
            ],
            [
                'review' => 'Perfect! Exactly what I ordered. Fast shipping and great quality. Will definitely buy again!',
                'rating' => 5
            ],
            [
                'review' => 'Good product overall. Some minor issues but customer service helped resolve them quickly.',
                'rating' => 4
            ],
            [
                'review' => 'Produk sesuai gambar dan deskripsi. Packaging aman, tidak ada kerusakan. Recommended!',
                'rating' => 4
            ],
            [
                'review' => 'Amazing quality! Exceeded my expectations. The attention to detail is impressive.',
                'rating' => 5
            ]
        ];

        foreach ($products as $product) {
            // Create 3-4 reviews per product
            $numReviews = rand(3, 4);
            $reviewsForProduct = collect($sampleReviews)->shuffle()->take($numReviews);
            
            foreach ($reviewsForProduct as $reviewData) {
                $randomUser = $users->random();
                
                Review::create([
                    'user_id' => $randomUser->id,
                    'product_id' => $product->id,
                    'review' => $reviewData['review'],
                    'rating' => $reviewData['rating'],
                    'created_at' => now()->subDays(rand(1, 30)),
                    'updated_at' => now()->subDays(rand(1, 30))
                ]);
            }
        }
    }
}
