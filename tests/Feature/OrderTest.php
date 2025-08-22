<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use App\Models\Cart;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $product;
    protected $category;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 100.00
        ]);
    }

    // API Tests

    public function test_api_can_get_orders_list()
    {
        Sanctum::actingAs($this->user);

        $orders = Order::factory(3)->create(['user_id' => $this->user->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'user_id',
                        'status',
                        'total_price',
                        'shipping_address',
                        'payment_method',
                        'payment_status',
                        'created_at',
                        'updated_at'
                    ]
                ]
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_can_filter_orders_by_status()
    {
        Sanctum::actingAs($this->user);

        Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending']);
        Order::factory()->create(['user_id' => $this->user->id, 'status' => 'delivered']);

        $response = $this->getJson('/api/orders?status=pending');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('data.data.0.status', 'pending');
    }

    public function test_api_cannot_get_orders_without_authentication()
    {
        $response = $this->getJson('/api/orders');

        $response->assertStatus(401);
    }

    public function test_api_can_create_order_from_cart()
    {
        Sanctum::actingAs($this->user);

        // Add item to cart
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ]);

        $orderData = [
            'shipping_address' => '123 Test Street, Test City',
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'user_id',
                'status',
                'total_price',
                'shipping_address',
                'payment_method',
                'payment_status',
                'items' => [
                    '*' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'price',
                        'product'
                    ]
                ]
            ],
            'message'
        ]);
        $response->assertJson([
            'success' => true,
            'message' => 'Order created successfully!'
        ]);

        $this->assertDatabaseHas('orders', [
            'user_id' => $this->user->id,
            'status' => 'pending',
            'total_price' => 200.00
        ]);
    }

    public function test_api_cannot_create_order_with_invalid_data()
    {
        Sanctum::actingAs($this->user);

        $invalidData = [
            'shipping_address' => '', // required
            'payment_method' => 'invalid_method' // invalid value
        ];

        $response = $this->postJson('/api/orders', $invalidData);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors(['shipping_address', 'payment_method']);
    }

    public function test_api_cannot_create_order_with_empty_cart()
    {
        Sanctum::actingAs($this->user);

        $orderData = [
            'shipping_address' => '123 Test Street, Test City',
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'Your cart is empty. Add items to cart before creating an order.'
        ]);
    }

    public function test_api_can_get_own_order()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'user_id',
                'status',
                'total_price',
                'shipping_address',
                'payment_method',
                'payment_status'
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_cannot_get_other_users_order()
    {
        Sanctum::actingAs($this->user);

        $otherUser = User::factory()->create();
        $order = Order::factory()->create(['user_id' => $otherUser->id]);

        $response = $this->getJson("/api/orders/{$order->id}");

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Unauthorized to view this order.'
        ]);
    }

    public function test_api_can_update_own_pending_order()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $updateData = [
            'shipping_address' => 'Updated Address',
            'payment_method' => 'paypal'
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Order updated successfully!'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'shipping_address' => 'Updated Address',
            'payment_method' => 'paypal'
        ]);
    }

    public function test_api_cannot_update_shipped_order()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'shipped'
        ]);

        $updateData = [
            'shipping_address' => 'Updated Address',
            'payment_method' => 'paypal'
        ];

        $response = $this->putJson("/api/orders/{$order->id}", $updateData);

        $response->assertStatus(400);
        $response->assertJson([
            'success' => false,
            'message' => 'This order cannot be updated.'
        ]);
    }

    public function test_api_can_cancel_own_pending_order()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        OrderItem::factory()->create([
            'order_id' => $order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        $originalStock = $this->product->stock;

        $response = $this->deleteJson("/api/orders/{$order->id}");

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Order cancelled successfully!'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'cancelled'
        ]);

        // Check that product stock is restored
        $this->product->refresh();
        $this->assertEquals($originalStock + 2, $this->product->stock);
    }

    public function test_api_can_update_order_status()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);

        $statusData = [
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ];

        $response = $this->patchJson("/api/orders/{$order->id}/status", $statusData);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Order status updated successfully!'
        ]);

        $this->assertDatabaseHas('orders', [
            'id' => $order->id,
            'status' => 'confirmed',
            'payment_status' => 'paid'
        ]);
    }

    public function test_api_cannot_update_order_status_with_invalid_data()
    {
        Sanctum::actingAs($this->user);

        $order = Order::factory()->create(['user_id' => $this->user->id]);

        $invalidData = [
            'status' => 'invalid_status'
        ];

        $response = $this->patchJson("/api/orders/{$order->id}/status", $invalidData);

        $response->assertStatus(422);
        $response->assertJson(['success' => false]);
        $response->assertJsonValidationErrors(['status']);
    }

    public function test_api_can_get_order_statistics()
    {
        Sanctum::actingAs($this->user);

        // Create orders with different statuses
        Order::factory()->create(['user_id' => $this->user->id, 'status' => 'pending', 'total_price' => 100]);
        Order::factory()->create(['user_id' => $this->user->id, 'status' => 'delivered', 'total_price' => 200]);
        Order::factory()->create(['user_id' => $this->user->id, 'status' => 'cancelled', 'total_price' => 150]);

        $response = $this->getJson('/api/orders/statistics');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'total_orders',
                'pending_orders',
                'confirmed_orders',
                'processing_orders',
                'shipped_orders',
                'delivered_orders',
                'cancelled_orders',
                'total_spent'
            ],
            'message'
        ]);

        $response->assertJson([
            'success' => true,
            'data' => [
                'total_orders' => 3,
                'pending_orders' => 1,
                'delivered_orders' => 1,
                'cancelled_orders' => 1,
                'total_spent' => 300 // 100 + 200 (cancelled orders excluded)
            ]
        ]);
    }

    public function test_api_cannot_access_without_authentication()
    {
        $response = $this->getJson('/api/orders/statistics');
        $response->assertStatus(401);

        $response = $this->postJson('/api/orders', []);
        $response->assertStatus(401);

        $order = Order::factory()->create();
        $response = $this->getJson("/api/orders/{$order->id}");
        $response->assertStatus(401);

        $response = $this->putJson("/api/orders/{$order->id}", []);
        $response->assertStatus(401);

        $response = $this->deleteJson("/api/orders/{$order->id}");
        $response->assertStatus(401);

        $response = $this->patchJson("/api/orders/{$order->id}/status", []);
        $response->assertStatus(401);
    }

    public function test_order_creation_updates_product_stock()
    {
        Sanctum::actingAs($this->user);

        $originalStock = $this->product->stock;

        // Add item to cart
        Cart::create([
            'user_id' => $this->user->id,
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);

        $orderData = [
            'shipping_address' => '123 Test Street, Test City',
            'payment_method' => 'credit_card'
        ];

        $response = $this->postJson('/api/orders', $orderData);

        $response->assertStatus(201);

        // Check that product stock is updated
        $this->product->refresh();
        $this->assertEquals($originalStock - 3, $this->product->stock);

        // Check that cart is cleared
        $this->assertDatabaseMissing('carts', [
            'user_id' => $this->user->id,
            'product_id' => $this->product->id
        ]);
    }

    public function test_only_user_orders_are_returned()
    {
        Sanctum::actingAs($this->user);

        // Create orders for current user
        Order::factory(2)->create(['user_id' => $this->user->id]);

        // Create orders for other users
        $otherUser = User::factory()->create();
        Order::factory(3)->create(['user_id' => $otherUser->id]);

        $response = $this->getJson('/api/orders');

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        
        $orders = $response->json('data.data');
        $this->assertCount(2, $orders);
        
        foreach ($orders as $order) {
            $this->assertEquals($this->user->id, $order['user_id']);
        }
    }
}
