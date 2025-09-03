<?php

namespace Database\Seeders;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Database\Seeder;

class ProductSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Get all categories
        $categories = Category::all();

        if ($categories->isEmpty()) {
            $this->command->info('No categories found. Please run CategorySeeder first.');
            return;
        }

        $products = [
            // Electronics
            [
                'name' => 'iPhone 14 Pro',
                'slug' => 'iphone-14-pro',
                'description' => 'The latest iPhone with advanced camera system and A16 Bionic chip.',
                'price' => 999.99,
                'stock' => 50,
                'weight' => 0.206,
                'category_name' => 'Electronics'
            ],
            [
                'name' => 'MacBook Air M2',
                'slug' => 'macbook-air-m2',
                'description' => 'Lightweight laptop with M2 chip and all-day battery life.',
                'price' => 1199.99,
                'stock' => 30,
                'weight' => 1.24,
                'category_name' => 'Electronics'
            ],
            [
                'name' => 'Samsung 65" 4K TV',
                'slug' => 'samsung-65-4k-tv',
                'description' => 'Smart TV with crystal clear 4K display and HDR support.',
                'price' => 799.99,
                'stock' => 15,
                'weight' => 18.5,
                'category_name' => 'Electronics'
            ],

            // Clothing & Fashion
            [
                'name' => 'Classic White T-Shirt',
                'slug' => 'classic-white-t-shirt',
                'description' => 'Premium cotton t-shirt with comfortable fit.',
                'price' => 24.99,
                'stock' => 100,
                'weight' => 0.2,
                'category_name' => 'Clothing & Fashion'
            ],
            [
                'name' => 'Blue Denim Jeans',
                'slug' => 'blue-denim-jeans',
                'description' => 'Classic blue jeans with modern fit and stretch fabric.',
                'price' => 79.99,
                'stock' => 75,
                'weight' => 0.8,
                'category_name' => 'Clothing & Fashion'
            ],
            [
                'name' => 'Leather Jacket',
                'slug' => 'leather-jacket',
                'description' => 'Genuine leather jacket with classic design.',
                'price' => 199.99,
                'stock' => 25,
                'weight' => 1.5,
                'category_name' => 'Clothing & Fashion'
            ],

            // Home & Garden
            [
                'name' => 'Coffee Maker Deluxe',
                'slug' => 'coffee-maker-deluxe',
                'description' => 'Programmable coffee maker with built-in grinder.',
                'price' => 149.99,
                'stock' => 40,
                'weight' => 4.5,
                'category_name' => 'Home & Garden'
            ],
            [
                'name' => 'Garden Tool Set',
                'slug' => 'garden-tool-set',
                'description' => 'Complete set of essential gardening tools.',
                'price' => 89.99,
                'stock' => 60,
                'weight' => 3.2,
                'category_name' => 'Home & Garden'
            ],

            // Sports & Outdoors
            [
                'name' => 'Yoga Mat Premium',
                'slug' => 'yoga-mat-premium',
                'description' => 'Non-slip yoga mat with extra cushioning.',
                'price' => 39.99,
                'stock' => 80,
                'weight' => 1.8,
                'category_name' => 'Sports & Outdoors'
            ],
            [
                'name' => 'Mountain Bike',
                'slug' => 'mountain-bike',
                'description' => '21-speed mountain bike with aluminum frame.',
                'price' => 599.99,
                'stock' => 12,
                'weight' => 15.5,
                'category_name' => 'Sports & Outdoors'
            ],

            // Books & Media
            [
                'name' => 'Programming Book Collection',
                'slug' => 'programming-book-collection',
                'description' => 'Essential programming books for developers.',
                'price' => 129.99,
                'stock' => 35,
                'weight' => 2.5,
                'category_name' => 'Books & Media'
            ],
            [
                'name' => 'Wireless Headphones',
                'slug' => 'wireless-headphones',
                'description' => 'Noise-cancelling wireless headphones with premium sound.',
                'price' => 249.99,
                'stock' => 45,
                'weight' => 0.3,
                'category_name' => 'Books & Media'
            ]
        ];

        foreach ($products as $productData) {
            // Find category by name
            $category = $categories->where('name', $productData['category_name'])->first();
            
            if ($category) {
                Product::create([
                    'category_id' => $category->id,
                    'name' => $productData['name'],
                    'slug' => $productData['slug'],
                    'description' => $productData['description'],
                    'price' => $productData['price'],
                    'stock' => $productData['stock'],
                    'weight' => $productData['weight']
                ]);
            }
        }

        $this->command->info('Products seeded successfully!');
    }
}
