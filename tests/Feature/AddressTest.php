<?php

namespace Tests\Feature;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUser;

    protected function setUp(): void
    {
        parent::setUp();
        
        // Create users for testing
        $this->user = User::factory()->create([
            'role' => 'customer'
        ]);

        $this->otherUser = User::factory()->create([
            'role' => 'customer'
        ]);
    }

    /**
     * Test fetching user's addresses via API.
     */
    public function test_user_can_fetch_their_addresses_via_api(): void
    {
        // Create addresses for the user
        Address::factory()->count(3)->create(['user_id' => $this->user->id]);
        
        // Create addresses for another user (should not be included)
        Address::factory()->count(2)->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/addresses');

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
                                 'user_id',
                                 'nama_depan',
                                 'nama_belakang',
                                 'alamat',
                                 'kode_pos',
                                 'kecamatan',
                                 'provinsi',
                                 'hp',
                                 'kelurahan',
                                 'kota',
                                 'created_at',
                                 'updated_at',
                                 'user'
                             ]
                         ]
                     ]
                 ]);

        // Should only return user's addresses
        $addresses = $response->json('data.data');
        $this->assertCount(3, $addresses);
        
        foreach ($addresses as $address) {
            $this->assertEquals($this->user->id, $address['user_id']);
        }
    }

    /**
     * Test fetching addresses without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_fetch_addresses(): void
    {
        $response = $this->getJson('/api/addresses');

        $response->assertStatus(401);
    }

    /**
     * Test creating a new address with authentication.
     */
    public function test_authenticated_user_can_create_address(): void
    {
        $addressData = [
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'alamat' => 'Jl. Sudirman No. 123',
            'kode_pos' => '12345',
            'kecamatan' => 'Menteng',
            'provinsi' => 'DKI Jakarta',
            'hp' => '081234567890',
            'kelurahan' => 'Menteng',
            'kota' => 'Jakarta Pusat'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/addresses', $addressData);

        $response->assertStatus(201)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address created successfully',
                     'data' => [
                         'user_id' => $this->user->id,
                         'nama_depan' => 'John',
                         'nama_belakang' => 'Doe',
                         'alamat' => 'Jl. Sudirman No. 123'
                     ]
                 ]);

        $this->assertDatabaseHas('addresses', array_merge($addressData, [
            'user_id' => $this->user->id
        ]));
    }

    /**
     * Test creating address without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_create_address(): void
    {
        $addressData = [
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'alamat' => 'Jl. Sudirman No. 123',
            'kode_pos' => '12345',
            'kecamatan' => 'Menteng',
            'provinsi' => 'DKI Jakarta',
            'hp' => '081234567890',
            'kelurahan' => 'Menteng',
            'kota' => 'Jakarta Pusat'
        ];

        $response = $this->postJson('/api/addresses', $addressData);

        $response->assertStatus(401);
    }

    /**
     * Test creating address with invalid data returns validation errors.
     */
    public function test_address_creation_validates_required_fields(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/addresses', []);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nama_depan',
                     'nama_belakang',
                     'alamat',
                     'kode_pos',
                     'kecamatan',
                     'provinsi',
                     'hp',
                     'kelurahan',
                     'kota'
                 ]);
    }

    /**
     * Test fetching a specific address.
     */
    public function test_user_can_fetch_their_specific_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson("/api/addresses/{$address->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'data' => [
                         'id' => $address->id,
                         'user_id' => $this->user->id,
                         'nama_depan' => $address->nama_depan,
                         'nama_belakang' => $address->nama_belakang
                     ]
                 ]);
    }

    /**
     * Test user cannot access another user's address.
     */
    public function test_user_cannot_access_other_users_address(): void
    {
        $otherUserAddress = Address::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson("/api/addresses/{$otherUserAddress->id}");

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Access denied'
                 ]);
    }

    /**
     * Test fetching non-existent address returns 404.
     */
    public function test_fetch_non_existent_address_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->getJson('/api/addresses/999');

        $response->assertStatus(404);
    }

    /**
     * Test updating an address.
     */
    public function test_user_can_update_their_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'nama_depan' => 'Jane',
            'nama_belakang' => 'Smith',
            'alamat' => 'Jl. Thamrin No. 456',
            'kode_pos' => '54321',
            'kecamatan' => 'Tanah Abang',
            'provinsi' => 'DKI Jakarta',
            'hp' => '087654321098',
            'kelurahan' => 'Bendungan Hilir',
            'kota' => 'Jakarta Pusat'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/addresses/{$address->id}", $updateData);

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address updated successfully',
                     'data' => [
                         'id' => $address->id,
                         'nama_depan' => 'Jane',
                         'nama_belakang' => 'Smith',
                         'alamat' => 'Jl. Thamrin No. 456'
                     ]
                 ]);

        $this->assertDatabaseHas('addresses', array_merge($updateData, [
            'id' => $address->id,
            'user_id' => $this->user->id
        ]));
    }

    /**
     * Test user cannot update another user's address.
     */
    public function test_user_cannot_update_other_users_address(): void
    {
        $otherUserAddress = Address::factory()->create(['user_id' => $this->otherUser->id]);

        $updateData = [
            'nama_depan' => 'Jane',
            'nama_belakang' => 'Smith',
            'alamat' => 'Updated Address',
            'kode_pos' => '54321',
            'kecamatan' => 'Updated Kecamatan',
            'provinsi' => 'Updated Provinsi',
            'hp' => '087654321098',
            'kelurahan' => 'Updated Kelurahan',
            'kota' => 'Updated Kota'
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->putJson("/api/addresses/{$otherUserAddress->id}", $updateData);

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Access denied'
                 ]);
    }

    /**
     * Test updating address without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_update_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $updateData = [
            'nama_depan' => 'Updated Name'
        ];

        $response = $this->putJson("/api/addresses/{$address->id}", $updateData);

        $response->assertStatus(401);
    }

    /**
     * Test deleting an address.
     */
    public function test_user_can_delete_their_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(200)
                 ->assertJson([
                     'success' => true,
                     'message' => 'Address deleted successfully'
                 ]);

        $this->assertDatabaseMissing('addresses', [
            'id' => $address->id
        ]);
    }

    /**
     * Test user cannot delete another user's address.
     */
    public function test_user_cannot_delete_other_users_address(): void
    {
        $otherUserAddress = Address::factory()->create(['user_id' => $this->otherUser->id]);

        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson("/api/addresses/{$otherUserAddress->id}");

        $response->assertStatus(403)
                 ->assertJson([
                     'success' => false,
                     'message' => 'Access denied'
                 ]);

        // Address should still exist
        $this->assertDatabaseHas('addresses', [
            'id' => $otherUserAddress->id
        ]);
    }

    /**
     * Test deleting address without authentication returns 401.
     */
    public function test_unauthenticated_user_cannot_delete_address(): void
    {
        $address = Address::factory()->create(['user_id' => $this->user->id]);

        $response = $this->deleteJson("/api/addresses/{$address->id}");

        $response->assertStatus(401);
    }

    /**
     * Test deleting non-existent address returns 404.
     */
    public function test_delete_non_existent_address_returns_404(): void
    {
        $response = $this->actingAs($this->user, 'sanctum')
                         ->deleteJson('/api/addresses/999');

        $response->assertStatus(404);
    }

    /**
     * Test address validation for maximum length fields.
     */
    public function test_address_validation_for_field_lengths(): void
    {
        $addressData = [
            'nama_depan' => str_repeat('a', 256), // Too long
            'nama_belakang' => str_repeat('b', 256), // Too long
            'alamat' => 'Valid address',
            'kode_pos' => str_repeat('1', 101), // Too long
            'kecamatan' => str_repeat('c', 101), // Too long
            'provinsi' => str_repeat('d', 101), // Too long
            'hp' => str_repeat('0', 101), // Too long
            'kelurahan' => str_repeat('e', 101), // Too long
            'kota' => str_repeat('f', 101), // Too long
        ];

        $response = $this->actingAs($this->user, 'sanctum')
                         ->postJson('/api/addresses', $addressData);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors([
                     'nama_depan',
                     'nama_belakang',
                     'kode_pos',
                     'kecamatan',
                     'provinsi',
                     'hp',
                     'kelurahan',
                     'kota'
                 ]);
    }
}
