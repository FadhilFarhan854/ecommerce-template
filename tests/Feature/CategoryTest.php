<?php

namespace Tests\Feature;

use App\Models\Category;
use App\Models\Product;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class CategoryTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create a user for authenticated requests
        $this->user = User::factory()->create([
            'role' => 'admin'
        ]);
    }

    /**
     * Test fetching all categories.
     */
    public function test_can_fetch_all_categories(): void
    {
        // Create some categories
        Category::factory()->count(3)->create();

        $response = $this->getJson('/api/categories');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         '*' => [
                             'id',
                             'name',
                             'slug',
                             'created_at',
                             'updated_at',
                             'products'
                         ]
                     ]
                 ]);
        
        $this->assertCount(3, $response->json('data'));
    }

    /**
     * Test fetching a single category.
     */
    public function test_can_fetch_single_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->getJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $category->id,
                         'name' => $category->name,
                         'slug' => $category->slug
                     ]
                 ]);
    }

    /**
     * Test fetching non-existent category returns 404.
     */
    public function test_fetch_non_existent_category_returns_404(): void
    {
        $response = $this->getJson('/api/categories/999');

        $response->assertStatus(404);
    }

    /**
     * Test creating a new category with authentication.
     */
    public function test_authenticated_user_can_create_category(): void
    {
        $categoryData = [
            'name' => 'Electronics',
            'slug' => 'electronics'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category created successfully',
                     'data' => [
                         'name' => 'Electronics',
                         'slug' => 'electronics'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);
    }

    /**
     * Test creating a category without slug generates slug from name.
     */
    public function test_category_creation_generates_slug_from_name(): void
    {
        $categoryData = [
            'name' => 'Home & Garden'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/categories', $categoryData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'name' => 'Home & Garden',
                         'slug' => 'home-garden'
                     ]
                 ]);
    }

    /**
     * Test creating a category without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_create_category(): void
    {
        $categoryData = [
            'name' => 'Electronics'
        ];

        $response = $this->postJson('/api/categories', $categoryData);

        $response->assertStatus(401);
    }

    /**
     * Test creating a category with invalid data returns validation errors.
     */
    public function test_category_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/categories', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test creating a category with duplicate name returns validation error.
     */
    public function test_category_creation_prevents_duplicate_names(): void
    {
        Category::factory()->create(['name' => 'Electronics']);

        $categoryData = [
            'name' => 'Electronics'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/categories', $categoryData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test creating a category with duplicate slug returns validation error.
     */
    public function test_category_creation_prevents_duplicate_slugs(): void
    {
        Category::factory()->create(['slug' => 'electronics']);

        $categoryData = [
            'name' => 'Electronic Items',
            'slug' => 'electronics'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/categories', $categoryData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['slug']);
    }

    /**
     * Test updating a category.
     */
    public function test_authenticated_user_can_update_category(): void
    {
        $category = Category::factory()->create([
            'name' => 'Electronics',
            'slug' => 'electronics'
        ]);

        $updateData = [
            'name' => 'Electronic Devices',
            'slug' => 'electronic-devices'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category updated successfully',
                     'data' => [
                         'id' => $category->id,
                         'name' => 'Electronic Devices',
                         'slug' => 'electronic-devices'
                     ]
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'name' => 'Electronic Devices',
            'slug' => 'electronic-devices'
        ]);
    }

    /**
     * Test updating a category without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_update_category(): void
    {
        $category = Category::factory()->create();

        $updateData = [
            'name' => 'Updated Category'
        ];

        $response = $this->putJson("/api/categories/{$category->id}", $updateData);

        $response->assertStatus(401);
    }

    /**
     * Test updating a category with duplicate name returns validation error.
     */
    public function test_category_update_prevents_duplicate_names(): void
    {
        $category1 = Category::factory()->create(['name' => 'Electronics']);
        $category2 = Category::factory()->create(['name' => 'Clothing']);

        $updateData = [
            'name' => 'Electronics'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/categories/{$category2->id}", $updateData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name']);
    }

    /**
     * Test deleting a category.
     */
    public function test_authenticated_user_can_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Category deleted successfully'
                 ]);

        $this->assertDatabaseMissing('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test deleting a category without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_delete_category(): void
    {
        $category = Category::factory()->create();

        $response = $this->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(401);
    }

    /**
     * Test deleting a category with associated products returns error.
     */
    public function test_cannot_delete_category_with_products(): void
    {
        $category = Category::factory()->create();
        
        // Create a product associated with this category
        Product::factory()->create(['category_id' => $category->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/categories/{$category->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Cannot delete category with associated products'
                 ]);

        $this->assertDatabaseHas('categories', [
            'id' => $category->id
        ]);
    }

    /**
     * Test deleting non-existent category returns 404.
     */
    public function test_delete_non_existent_category_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson('/api/categories/999');

        $response->assertStatus(404);
    }
}
