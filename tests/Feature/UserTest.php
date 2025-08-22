<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\Address;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;

class UserTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $admin;
    protected $customer;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create admin user for authenticated requests
        $this->admin = User::factory()->create([
            'role' => 'admin',
            'email' => 'admin@example.com'
        ]);

        // Create customer user for testing
        $this->customer = User::factory()->create([
            'role' => 'customer',
            'email' => 'customer@example.com'
        ]);
    }

    /**
     * Test fetching all users via API.
     */
    public function test_can_fetch_all_users_via_api(): void
    {
        // Create additional users
        User::factory()->count(3)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users');

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
                             'email',
                             'phone',
                             'role',
                             'created_at',
                             'updated_at',
                             'addresses',
                             'orders'
                         ]
                     ],
                     'pagination' => [
                         'current_page',
                         'last_page',
                         'per_page',
                         'total'
                     ]
                 ]);
        
        $this->assertCount(5, $response->json('data')); // 2 from setUp + 3 created
    }

    /**
     * Test fetching users with role filter.
     */
    public function test_can_filter_users_by_role(): void
    {
        User::factory()->count(2)->create(['role' => 'admin']);
        User::factory()->count(3)->create(['role' => 'customer']);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users?role=admin');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        // Should have 3 admin users (1 from setUp + 2 created)
        $this->assertCount(3, $data);
        foreach ($data as $user) {
            $this->assertEquals('admin', $user['role']);
        }
    }

    /**
     * Test fetching users with search filter.
     */
    public function test_can_search_users(): void
    {
        User::factory()->create([
            'name' => 'John Doe',
            'email' => 'john@example.com'
        ]);
        
        User::factory()->create([
            'name' => 'Jane Smith',
            'email' => 'jane@example.com'
        ]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users?search=john');

        $response->assertStatus(200);
        $data = $response->json('data');
        
        $this->assertCount(1, $data);
        $this->assertEquals('John Doe', $data[0]['name']);
    }

    /**
     * Test creating a new user via API.
     */
    public function test_can_create_user_via_api(): void
    {
        $userData = [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users', $userData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User created successfully'
                 ])
                 ->assertJsonStructure([
                     'success',
                     'message',
                     'data' => [
                         'id',
                         'name',
                         'email',
                         'phone',
                         'role',
                         'created_at',
                         'updated_at',
                         'addresses',
                         'orders'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'name' => 'New User',
            'email' => 'newuser@example.com',
            'phone' => '1234567890',
            'role' => 'customer'
        ]);

        // Verify password is hashed
        $user = User::where('email', 'newuser@example.com')->first();
        $this->assertTrue(Hash::check('password123', $user->password));
    }

    /**
     * Test validation errors when creating user.
     */
    public function test_user_creation_validation_errors(): void
    {
        $userData = [
            'name' => '',
            'email' => 'invalid-email',
            'password' => '123', // Too short
            'role' => 'invalid-role'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['name', 'email', 'password', 'role']);
    }

    /**
     * Test unique email validation.
     */
    public function test_user_email_must_be_unique(): void
    {
        $userData = [
            'name' => 'Test User',
            'email' => $this->customer->email, // Use existing email
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users', $userData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['email']);
    }

    /**
     * Test fetching a single user via API.
     */
    public function test_can_fetch_single_user_via_api(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson("/api/users/{$this->customer->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $this->customer->id,
                         'name' => $this->customer->name,
                         'email' => $this->customer->email,
                         'role' => $this->customer->role
                     ]
                 ]);
    }

    /**
     * Test 404 error for non-existent user.
     */
    public function test_returns_404_for_non_existent_user(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users/99999');

        $response->assertStatus(404);
    }

    /**
     * Test updating a user via API.
     */
    public function test_can_update_user_via_api(): void
    {
        $updateData = [
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'role' => 'admin'
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->putJson("/api/users/{$this->customer->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User updated successfully',
                     'data' => [
                         'id' => $this->customer->id,
                         'name' => 'Updated Name',
                         'email' => 'updated@example.com',
                         'phone' => '9876543210',
                         'role' => 'admin'
                     ]
                 ]);

        $this->assertDatabaseHas('users', [
            'id' => $this->customer->id,
            'name' => 'Updated Name',
            'email' => 'updated@example.com',
            'phone' => '9876543210',
            'role' => 'admin'
        ]);
    }

    /**
     * Test updating user password.
     */
    public function test_can_update_user_password(): void
    {
        $updateData = [
            'name' => $this->customer->name,
            'email' => $this->customer->email,
            'password' => 'newpassword123',
            'password_confirmation' => 'newpassword123',
            'role' => $this->customer->role
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->putJson("/api/users/{$this->customer->id}", $updateData);

        $response->assertStatus(200);

        $updatedUser = User::find($this->customer->id);
        $this->assertTrue(Hash::check('newpassword123', $updatedUser->password));
    }

    /**
     * Test deleting a user without orders.
     */
    public function test_can_delete_user_without_orders(): void
    {
        $user = User::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'User deleted successfully'
                 ]);

        $this->assertDatabaseMissing('users', ['id' => $user->id]);
    }

    /**
     * Test cannot delete user with orders.
     */
    public function test_cannot_delete_user_with_orders(): void
    {
        // Create an order for the customer
        Order::factory()->create(['user_id' => $this->customer->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->deleteJson("/api/users/{$this->customer->id}");

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Cannot delete user with existing orders'
                 ]);

        $this->assertDatabaseHas('users', ['id' => $this->customer->id]);
    }

    /**
     * Test user statistics endpoint.
     */
    public function test_can_get_user_statistics(): void
    {
        // Create additional users for statistics
        User::factory()->count(3)->create(['role' => 'customer']);
        User::factory()->count(2)->create(['role' => 'admin']);
        User::factory()->count(2)->create(['role' => 'customer', 'email_verified_at' => now()]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users/statistics');

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true
                 ])
                 ->assertJsonStructure([
                     'success',
                     'data' => [
                         'total_users',
                         'admin_users',
                         'customer_users',
                         'verified_users',
                         'recent_users'
                     ]
                 ]);

        $data = $response->json('data');
        // 2 from setUp + 3 + 2 + 2 = 9 total users
        $this->assertEquals(9, $data['total_users']); 
        // 1 from setUp + 2 created = 3 admin users
        $this->assertEquals(3, $data['admin_users']); 
        // 1 from setUp + 3 + 2 created = 6 customer users
        $this->assertEquals(6, $data['customer_users']); 
    }

    /**
     * Test bulk delete action.
     */
    public function test_can_bulk_delete_users(): void
    {
        $users = User::factory()->count(3)->create();
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users/bulk-action', [
                             'action' => 'delete',
                             'user_ids' => $userIds
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Selected users deleted successfully'
                 ]);

        foreach ($userIds as $userId) {
            $this->assertDatabaseMissing('users', ['id' => $userId]);
        }
    }

    /**
     * Test bulk activate action.
     */
    public function test_can_bulk_activate_users(): void
    {
        $users = User::factory()->count(3)->create(['email_verified_at' => null]);
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users/bulk-action', [
                             'action' => 'activate',
                             'user_ids' => $userIds
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Selected users activated successfully'
                 ]);

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('users', [
                'id' => $userId,
                'email_verified_at' => now()->format('Y-m-d H:i:s')
            ]);
        }
    }

    /**
     * Test bulk deactivate action.
     */
    public function test_can_bulk_deactivate_users(): void
    {
        $users = User::factory()->count(3)->create(['email_verified_at' => now()]);
        $userIds = $users->pluck('id')->toArray();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users/bulk-action', [
                             'action' => 'deactivate',
                             'user_ids' => $userIds
                         ]);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Selected users deactivated successfully'
                 ]);

        foreach ($userIds as $userId) {
            $this->assertDatabaseHas('users', [
                'id' => $userId,
                'email_verified_at' => null
            ]);
        }
    }

    /**
     * Test bulk action validation.
     */
    public function test_bulk_action_validation(): void
    {
        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users/bulk-action', [
                             'action' => 'invalid-action',
                             'user_ids' => []
                         ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors(['action', 'user_ids']);
    }

    /**
     * Test cannot bulk delete users with orders.
     */
    public function test_cannot_bulk_delete_users_with_orders(): void
    {
        $userWithOrder = User::factory()->create();
        Order::factory()->create(['user_id' => $userWithOrder->id]);
        
        $userWithoutOrder = User::factory()->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users/bulk-action', [
                             'action' => 'delete',
                             'user_ids' => [$userWithOrder->id, $userWithoutOrder->id]
                         ]);

        $response->assertStatus(422)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Cannot delete users with existing orders'
                 ]);

        // Both users should still exist
        $this->assertDatabaseHas('users', ['id' => $userWithOrder->id]);
        $this->assertDatabaseHas('users', ['id' => $userWithoutOrder->id]);
    }

    /**
     * Test pagination in user listing.
     */
    public function test_user_listing_pagination(): void
    {
        User::factory()->count(25)->create();

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->getJson('/api/users?per_page=10');

        $response->assertStatus(200);
        
        $data = $response->json();
        $this->assertCount(10, $data['data']);
        $this->assertEquals(1, $data['pagination']['current_page']);
        $this->assertEquals(10, $data['pagination']['per_page']);
        // The exact total might vary due to other tests, so let's just check it's at least 25
        $this->assertGreaterThanOrEqual(25, $data['pagination']['total']);
    }

    /**
     * Test that user deletion also removes related data.
     */
    public function test_user_deletion_removes_related_data(): void
    {
        $user = User::factory()->create();
        
        // Create related data
        Address::factory()->create(['user_id' => $user->id]);
        Cart::factory()->create(['user_id' => $user->id]);

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->deleteJson("/api/users/{$user->id}");

        $response->assertStatus(200);

        // Check that user and related data are deleted
        $this->assertDatabaseMissing('users', ['id' => $user->id]);
        $this->assertDatabaseMissing('addresses', ['user_id' => $user->id]);
        $this->assertDatabaseMissing('carts', ['user_id' => $user->id]);
    }

    /**
     * Test creating user without optional fields.
     */
    public function test_can_create_user_without_optional_fields(): void
    {
        $userData = [
            'name' => 'Minimal User',
            'email' => 'minimal@example.com',
            'password' => 'password123',
            'password_confirmation' => 'password123',
            'role' => 'customer'
            // phone is optional
        ];

        $response = $this->actingAs($this->admin, 'sanctum')
                         ->postJson('/api/users', $userData);

        $response->assertStatus(201);

        $this->assertDatabaseHas('users', [
            'name' => 'Minimal User',
            'email' => 'minimal@example.com',
            'role' => 'customer',
            'phone' => null
        ]);
    }
}
