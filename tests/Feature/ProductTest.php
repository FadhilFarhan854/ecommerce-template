<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class ProductTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authenticated requests
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);

        // Create a category for products
        $this->category = Category::factory()->create();
    }

    /**
     * Test fetching all products via API.
     */
    public function test_can_fetch_all_products_via_api(): void
    {
        // Create some products
        Product::factory()->count(5)->create(['category_id' => $this->category->id]);

        $response = $this->getJson('/api/products');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'data' => [
                             '*' => [
                                 'id',
                                 'name',
                                 'slug',
                                 'description',
                                 'price',
                                 'stock',
                                 'image',
                                 'category_id',
                                 'created_at',
                                 'updated_at',
                                 'category'
                             ]
                         ]
                     ]
                 ]);
    }

    /**
     * Test fetching products with category filter.
     */
    public function test_can_filter_products_by_category(): void
    {
        $category2 = Category::factory()->create();
        
        Product::factory()->count(3)->create(['category_id' => $this->category->id]);
        Product::factory()->count(2)->create(['category_id' => $category2->id]);

        $response = $this->getJson("/api/products?category_id={$this->category->id}");

        $response->assertStatus(200);
        $products = $response->json('data.data');
        $this->assertCount(3, $products);
        
        foreach ($products as $product) {
            $this->assertEquals($this->category->id, $product['category_id']);
        }
    }

    /**
     * Test fetching products with search functionality.
     */
    public function test_can_search_products(): void
    {
        Product::factory()->create([
            'name' => 'iPhone 14',
            'description' => 'Latest Apple smartphone',
            'category_id' => $this->category->id
        ]);
        
        Product::factory()->create([
            'name' => 'Samsung Galaxy',
            'description' => 'Android smartphone',
            'category_id' => $this->category->id
        ]);

        $response = $this->getJson('/api/products?search=iPhone');

        $response->assertStatus(200);
        $products = $response->json('data.data');
        $this->assertCount(1, $products);
        $this->assertStringContainsString('iPhone', $products[0]['name']);
    }

    /**
     * Test fetching a single product.
     */
    public function test_can_fetch_single_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->getJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $product->id,
                         'name' => $product->name,
                         'slug' => $product->slug,
                         'price' => $product->price
                     ]
                 ]);
    }

    /**
     * Test fetching non-existent product returns 404.
     */
    public function test_fetch_non_existent_product_returns_404(): void
    {
        $response = $this->getJson('/api/products/999');

        $response->assertStatus(404);
    }

    /**
     * Test creating a new product with authentication.
     */
    public function test_authenticated_user_can_create_product(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'slug' => 'test-product',
            'description' => 'This is a test product description',
            'price' => 99.99,
            'stock' => 10,
            'image' => 'https://example.com/image.jpg'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Product created successfully',
                     'data' => [
                         'name' => 'Test Product',
                         'price' => 99.99,
                         'stock' => 10
                     ]
                 ]);

        $this->assertDatabaseHas('products', [
            'name' => 'Test Product',
            'price' => 99.99,
            'stock' => 10
        ]);
    }

    /**
     * Test creating a product without slug generates slug from name.
     */
    public function test_product_creation_generates_slug_from_name(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Amazing Product Name',
            'description' => 'Test description',
            'price' => 50.00,
            'stock' => 5
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Amazing Product Name',
                         'slug' => 'amazing-product-name'
                     ]
                 ]);
    }

    /**
     * Test creating a product without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_create_product(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 99.99,
            'stock' => 10
        ];

        $response = $this->postJson('/api/products', $productData);

        $response->assertStatus(401);
    }

    /**
     * Test creating a product with invalid data returns validation errors.
     */
    public function test_product_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'category_id',
                     'name',
                     'description',
                     'price',
                     'stock'
                 ]);
    }

    /**
     * Test creating a product with invalid category returns validation error.
     */
    public function test_product_creation_validates_category_exists(): void
    {
        $productData = [
            'category_id' => 999, // Non-existent category
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 99.99,
            'stock' => 10
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['category_id']);
    }

    /**
     * Test creating a product with duplicate name returns validation error.
     */
    public function test_product_creation_prevents_duplicate_names(): void
    {
        Product::factory()->create([
            'name' => 'Unique Product',
            'category_id' => $this->category->id
        ]);

        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Unique Product',
            'description' => 'Test description',
            'price' => 99.99,
            'stock' => 10
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test updating a product.
     */
    public function test_authenticated_user_can_update_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $updateData = [
            'category_id' => $this->category->id,
            'name' => 'Updated Product Name',
            'slug' => 'updated-product-name',
            'description' => 'Updated description',
            'price' => 149.99,
            'stock' => 20,
            'image' => 'https://example.com/updated-image.jpg'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Product updated successfully',
                     'data' => [
                         'id' => $product->id,
                         'name' => 'Updated Product Name',
                         'price' => 149.99,
                         'stock' => 20
                     ]
                 ]);

        $this->assertDatabaseHas('products', [
            'id' => $product->id,
            'name' => 'Updated Product Name',
            'price' => 149.99,
            'stock' => 20
        ]);
    }

    /**
     * Test updating a product without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_update_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $updateData = [
            'name' => 'Updated Product'
        ];

        $response = $this->putJson("/api/products/{$product->id}", $updateData);

        $response->assertStatus(401);
    }

    /**
     * Test deleting a product.
     */
    public function test_authenticated_user_can_delete_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Product deleted successfully'
                 ]);

        $this->assertDatabaseMissing('products', [
            'id' => $product->id
        ]);
    }

    /**
     * Test deleting a product without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_delete_product(): void
    {
        $product = Product::factory()->create(['category_id' => $this->category->id]);

        $response = $this->deleteJson("/api/products/{$product->id}");

        $response->assertStatus(401);
    }

    /**
     * Test deleting non-existent product returns 404.
     */
    public function test_delete_non_existent_product_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson('/api/products/999');

        $response->assertStatus(404);
    }

    /**
     * Test product price validation.
     */
    public function test_product_price_must_be_positive(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => -10.00, // Negative price
            'stock' => 10
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['price']);
    }

    /**
     * Test product stock validation.
     */
    public function test_product_stock_must_be_non_negative(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10.00,
            'stock' => -5 // Negative stock
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['stock']);
    }

    /**
     * Test product image URL validation.
     */
    public function test_product_image_must_be_valid_url(): void
    {
        $productData = [
            'category_id' => $this->category->id,
            'name' => 'Test Product',
            'description' => 'Test description',
            'price' => 10.00,
            'stock' => 5,
            'image' => 'not-a-valid-url'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/products', $productData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['image']);
    }
}
