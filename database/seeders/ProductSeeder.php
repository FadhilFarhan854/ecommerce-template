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
            // Parfum Pria
            [
                'name' => 'Dior Sauvage EDT 100ml',
                'slug' => 'dior-sauvage-edt-100ml',
                'description' => 'Parfum pria dengan aroma segar dan maskulin. Notes bergamot, lada, dan ambroxan yang tahan lama.',
                'price' => 1599000,
                'stock' => 25,
                'weight' => 0.35,
                'category_name' => 'Parfum Pria'
            ],
            [
                'name' => 'Chanel Bleu de Chanel EDP 100ml',
                'slug' => 'chanel-bleu-de-chanel-edp-100ml',
                'description' => 'Parfum mewah dengan karakter woody aromatic yang elegan dan sophisticated.',
                'price' => 2200000,
                'stock' => 15,
                'weight' => 0.35,
                'category_name' => 'Parfum Pria'
            ],
            [
                'name' => 'Tom Ford Oud Wood EDP 50ml',
                'slug' => 'tom-ford-oud-wood-edp-50ml',
                'description' => 'Parfum premium dengan aroma oud yang misterius dan sensual.',
                'price' => 3500000,
                'stock' => 10,
                'weight' => 0.25,
                'category_name' => 'Parfum Pria'
            ],
            [
                'name' => 'Versace Eros EDT 100ml',
                'slug' => 'versace-eros-edt-100ml',
                'description' => 'Parfum dengan aroma mint, green apple, dan vanilla yang penuh gairah.',
                'price' => 899000,
                'stock' => 30,
                'weight' => 0.35,
                'category_name' => 'Parfum Pria'
            ],

            // Parfum Wanita
            [
                'name' => 'Chanel No.5 EDP 100ml',
                'slug' => 'chanel-no5-edp-100ml',
                'description' => 'Parfum ikonik dengan bunga aldehyde yang timeless dan elegan.',
                'price' => 2800000,
                'stock' => 20,
                'weight' => 0.35,
                'category_name' => 'Parfum Wanita'
            ],
            [
                'name' => 'Dior Miss Dior EDP 100ml',
                'slug' => 'dior-miss-dior-edp-100ml',
                'description' => 'Parfum feminin dengan aroma rose dan patchouli yang romantis.',
                'price' => 1899000,
                'stock' => 25,
                'weight' => 0.35,
                'category_name' => 'Parfum Wanita'
            ],
            [
                'name' => 'Yves Saint Laurent Black Opium EDP 90ml',
                'slug' => 'ysl-black-opium-edp-90ml',
                'description' => 'Parfum sensual dengan aroma coffee, vanilla, dan white flowers.',
                'price' => 1650000,
                'stock' => 18,
                'weight' => 0.32,
                'category_name' => 'Parfum Wanita'
            ],
            [
                'name' => 'Lancôme La Vie Est Belle EDP 100ml',
                'slug' => 'lancome-la-vie-est-belle-edp-100ml',
                'description' => 'Parfum manis dengan iris, patchouli, dan gourmand accord.',
                'price' => 1450000,
                'stock' => 22,
                'weight' => 0.35,
                'category_name' => 'Parfum Wanita'
            ],

            // Parfum Unisex
            [
                'name' => 'Creed Aventus EDP 100ml',
                'slug' => 'creed-aventus-edp-100ml',
                'description' => 'Parfum mewah unisex dengan aroma fruity dan smoky yang legendary.',
                'price' => 4200000,
                'stock' => 8,
                'weight' => 0.35,
                'category_name' => 'Parfum Unisex'
            ],
            [
                'name' => 'Maison Margiela REPLICA Beach Walk EDT 100ml',
                'slug' => 'maison-margiela-replica-beach-walk-edt-100ml',
                'description' => 'Parfum yang mengingatkan suasana pantai dengan aroma coconut dan solar.',
                'price' => 1750000,
                'stock' => 15,
                'weight' => 0.35,
                'category_name' => 'Parfum Unisex'
            ],
            [
                'name' => 'Le Labo Santal 33 EDP 100ml',
                'slug' => 'le-labo-santal-33-edp-100ml',
                'description' => 'Parfum niche dengan aroma sandalwood yang unique dan addictive.',
                'price' => 3200000,
                'stock' => 12,
                'weight' => 0.35,
                'category_name' => 'Parfum Unisex'
            ],

            // Minyak Wangi
            [
                'name' => 'Attar Oud Premium 12ml',
                'slug' => 'attar-oud-premium-12ml',
                'description' => 'Minyak wangi oud murni dengan aroma yang kuat dan tahan lama.',
                'price' => 350000,
                'stock' => 40,
                'weight' => 0.05,
                'category_name' => 'Minyak Wangi'
            ],
            [
                'name' => 'Minyak Wangi Mawar 10ml',
                'slug' => 'minyak-wangi-mawar-10ml',
                'description' => 'Minyak wangi mawar alami dengan aroma bunga yang segar.',
                'price' => 75000,
                'stock' => 60,
                'weight' => 0.04,
                'category_name' => 'Minyak Wangi'
            ],
            [
                'name' => 'Attar Musk White 12ml',
                'slug' => 'attar-musk-white-12ml',
                'description' => 'Minyak wangi musk putih yang lembut dan menenangkan.',
                'price' => 125000,
                'stock' => 35,
                'weight' => 0.05,
                'category_name' => 'Minyak Wangi'
            ],

            // Body Care
            [
                'name' => 'Bath & Body Works Body Mist 236ml',
                'slug' => 'bath-body-works-body-mist-236ml',
                'description' => 'Body mist dengan berbagai pilihan aroma segar untuk daily use.',
                'price' => 299000,
                'stock' => 50,
                'weight' => 0.28,
                'category_name' => 'Body Care'
            ],
            [
                'name' => 'Victoria Secret Body Spray 250ml',
                'slug' => 'victoria-secret-body-spray-250ml',
                'description' => 'Body spray dengan aroma sensual dan feminine yang menawan.',
                'price' => 385000,
                'stock' => 45,
                'weight' => 0.3,
                'category_name' => 'Body Care'
            ],
            [
                'name' => 'The Body Shop Body Butter 200ml',
                'slug' => 'the-body-shop-body-butter-200ml',
                'description' => 'Body butter dengan moisturizer dan aroma natural yang menyegarkan.',
                'price' => 275000,
                'stock' => 55,
                'weight' => 0.25,
                'category_name' => 'Body Care'
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
