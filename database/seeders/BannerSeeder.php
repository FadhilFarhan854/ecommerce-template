<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Banner;

class BannerSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $banners = [
            [
                'image' => 'banners/sample-banner-1.jpg',
                'status' => true,
            ],
            [
                'image' => 'banners/sample-banner-2.jpg',
                'status' => true,
            ],
            [
                'image' => 'banners/sample-banner-3.jpg',
                'status' => false,
            ]
        ];

        foreach ($banners as $banner) {
            Banner::create($banner);
        }
    }
}
