<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Electronics',
                'slug' => 'electronics'
            ],
            [
                'name' => 'Clothing & Fashion',
                'slug' => 'clothing-fashion'
            ],
            [
                'name' => 'Home & Garden',
                'slug' => 'home-garden'
            ],
            [
                'name' => 'Sports & Outdoors',
                'slug' => 'sports-outdoors'
            ],
            [
                'name' => 'Books & Media',
                'slug' => 'books-media'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
