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
                'name' => 'Parfum Pria',
                'slug' => 'parfum-pria'
            ],
            [
                'name' => 'Parfum Wanita',
                'slug' => 'parfum-wanita'
            ],
            [
                'name' => 'Parfum Unisex',
                'slug' => 'parfum-unisex'
            ],
            [
                'name' => 'Minyak Wangi',
                'slug' => 'minyak-wangi'
            ],
            [
                'name' => 'Body Care',
                'slug' => 'body-care'
            ]
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }
    }
}
