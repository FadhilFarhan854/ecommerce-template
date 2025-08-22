<?php

namespace Database\Factories;

use App\Models\Address;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Address>
 */
class AddressFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Address::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'nama_depan' => fake()->firstName(),
            'nama_belakang' => fake()->lastName(),
            'alamat' => fake()->address(),
            'kode_pos' => fake()->postcode(),
            'kecamatan' => fake()->city(),
            'provinsi' => fake()->state(),
            'hp' => fake()->phoneNumber(),
            'kelurahan' => fake()->streetName(),
            'kota' => fake()->city(),
        ];
    }
}
