<?php

namespace Database\Factories;

use App\Models\Order;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Order>
 */
class OrderFactory extends Factory
{
    protected $model = Order::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => User::factory(),
            'status' => $this->faker->randomElement(['unpaid', 'paid', 'sending', 'finished', 'cancelled']),
            'total_price' => $this->faker->randomFloat(2, 10, 1000),
            'shipping_address' => $this->faker->address,
            'payment_method' => $this->faker->randomElement(['credit_card', 'bank_transfer', 'cash_on_delivery']),
            'payment_status' => $this->faker->randomElement(['unpaid', 'paid', 'failed']),
        ];
    }

    /**
     * Indicate that the order is unpaid.
     */
    public function unpaid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'unpaid',
            'payment_status' => 'unpaid',
        ]);
    }

    /**
     * Indicate that the order is paid.
     */
    public function paid(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'paid',
            'payment_status' => 'paid',
        ]);
    }
    
    /**
     * Indicate that the order is being sent.
     */
    public function sending(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'sending',
            'payment_status' => 'paid',
        ]);
    }

    /**
     * Indicate that the order is finished.
     */
    public function finished(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'finished',
            'payment_status' => 'paid',
        ]);
    }
}
