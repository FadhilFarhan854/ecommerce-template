<?php

namespace Tests\Unit;

use App\Models\Address;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class AddressTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test address can be created.
     */
    public function test_address_can_be_created(): void
    {
        $user = User::factory()->create();
        
        $address = Address::create([
            'user_id' => $user->id,
            'nama_depan' => 'John',
            'nama_belakang' => 'Doe',
            'alamat' => 'Jl. Sudirman No. 123',
            'kode_pos' => '12345',
            'kecamatan' => 'Menteng',
            'provinsi' => 'DKI Jakarta',
            'hp' => '081234567890',
            'kelurahan' => 'Menteng',
            'kota' => 'Jakarta Pusat'
        ]);

        $this->assertInstanceOf(Address::class, $address);
        $this->assertEquals('John', $address->nama_depan);
        $this->assertEquals('Doe', $address->nama_belakang);
        $this->assertEquals($user->id, $address->user_id);
    }

    /**
     * Test address has correct fillable attributes.
     */
    public function test_address_has_correct_fillable_attributes(): void
    {
        $address = new Address();
        
        $expectedFillable = [
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
        ];

        $this->assertEquals($expectedFillable, $address->getFillable());
    }

    /**
     * Test address belongs to user relationship.
     */
    public function test_address_belongs_to_user(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $this->assertInstanceOf(User::class, $address->user);
        $this->assertEquals($user->id, $address->user->id);
        $this->assertEquals($user->name, $address->user->name);
    }

    /**
     * Test address factory creates valid data.
     */
    public function test_address_factory_creates_valid_data(): void
    {
        $address = Address::factory()->create();

        $this->assertNotNull($address->user_id);
        $this->assertNotNull($address->nama_depan);
        $this->assertNotNull($address->nama_belakang);
        $this->assertNotNull($address->alamat);
        $this->assertNotNull($address->kode_pos);
        $this->assertNotNull($address->kecamatan);
        $this->assertNotNull($address->provinsi);
        $this->assertNotNull($address->hp);
        $this->assertNotNull($address->kelurahan);
        $this->assertNotNull($address->kota);
    }

    /**
     * Test user can have multiple addresses.
     */
    public function test_user_can_have_multiple_addresses(): void
    {
        $user = User::factory()->create();
        
        Address::factory()->count(3)->create(['user_id' => $user->id]);

        $this->assertCount(3, $user->fresh()->addresses);
    }

    /**
     * Test address is deleted when user is deleted (cascade).
     */
    public function test_address_is_deleted_when_user_is_deleted(): void
    {
        $user = User::factory()->create();
        $address = Address::factory()->create(['user_id' => $user->id]);

        $addressId = $address->id;

        // Delete the user
        $user->delete();

        // Address should be deleted due to cascade
        $this->assertDatabaseMissing('addresses', ['id' => $addressId]);
    }
}
