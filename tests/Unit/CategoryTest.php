<?php

namespace Tests\Unit;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test category can be created.
     */
    public function test_category_can_be_created(): void
    {
        $category = Category::create([
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);

        $this->assertInstanceOf(Category::class, $category);
        $this->assertEquals('Electronics', $category->name);
        $this->assertEquals('electronics', $category->slug);
        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);
    }

    /**
     * Test category has fillable attributes.
     */
    public function test_category_has_correct_fillable_attributes(): void
    {
        $category = new Category();
        $expectedFillable = ['name', 'slug'];

        $this->assertEquals($expectedFillable, $category->getFillable());
    }

    /**
     * Test category has products relationship.
     */
    public function test_category_has_products_relationship(): void
    {
        $category = Category::factory()->create();
        
        // Create products for this category
        Product::factory()->count(3)->create(['category_id' => $category->id]);

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->products);
        $this->assertCount(3, $category->products);
        $this->assertInstanceOf(Product::class, $category->products->first());
    }

    /**
     * Test category can exist without products.
     */
    public function test_category_can_exist_without_products(): void
    {
        $category = Category::factory()->create();

        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Collection::class, $category->products);
        $this->assertCount(0, $category->products);
    }

    /**
     * Test category factory creates valid data.
     */
    public function test_category_factory_creates_valid_data(): void
    {
        $category = Category::factory()->create();

        $this->assertNotEmpty($category->name);
        $this->assertNotEmpty($category->slug);
        $this->assertIsString($category->name);
        $this->assertIsString($category->slug);
        $this->assertDoesNotMatchRegularExpression('/\s/', $category->slug); // No spaces in slug
    }

    /**
     * Test multiple categories can be created with unique names and slugs.
     */
    public function test_multiple_categories_have_unique_names_and_slugs(): void
    {
        $categories = Category::factory()->count(5)->create();

        $names = $categories->pluck('name')->toArray();
        $slugs = $categories->pluck('slug')->toArray();

        $this->assertEquals($names, array_unique($names));
        $this->assertEquals($slugs, array_unique($slugs));
    }
}
