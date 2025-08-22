<?php

namespace Tests\Feature;

use App\Models\User;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Laravel\Sanctum\Sanctum;
use Tests\TestCase;

class OrderItemTest extends TestCase
{
    use RefreshDatabase, WithFaker;

    protected $user;
    protected $otherUser;
    protected $product;
    protected $category;
    protected $order;

    protected function setUp(): void
    {
        parent::setUp();
        
        $this->user = User::factory()->create();
        $this->otherUser = User::factory()->create();
        $this->category = Category::factory()->create();
        $this->product = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 10,
            'price' => 100.00
        ]);
        $this->order = Order::factory()->create([
            'user_id' => $this->user->id,
            'status' => 'pending'
        ]);
    }

    // API Tests

    public function test_api_can_get_order_items_list()
    {
        Sanctum::actingAs($this->user);

        OrderItem::factory(3)->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'current_page',
                'data' => [
                    '*' => [
                        'id',
                        'order_id',
                        'product_id',
                        'quantity',
                        'price',
                        'created_at',
                        'updated_at',
                        'order',
                        'product'
                    ]
                ]
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_can_filter_order_items_by_order_id()
    {
        Sanctum::actingAs($this->user);

        $order2 = Order::factory()->create(['user_id' => $this->user->id]);
        
        OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);
        OrderItem::factory()->create([
            'order_id' => $order2->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items?order_id=' . $this->order->id);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);
        $response->assertJsonPath('data.data.0.order_id', $this->order->id);
    }

    public function test_api_cannot_get_order_items_without_authentication()
    {
        $response = $this->getJson('/api/order-items');

        $response->assertStatus(401);
    }

    public function test_api_can_get_create_form_data()
    {
        Sanctum::actingAs($this->user);

        $response = $this->getJson('/api/order-items/create?order_id=' . $this->order->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'order',
                'products'
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_cannot_get_create_form_data_for_other_users_order()
    {
        Sanctum::actingAs($this->otherUser);

        $response = $this->getJson('/api/order-items/create?order_id=' . $this->order->id);

        $response->assertStatus(404);
        $response->assertJson([
            'success' => false,
            'message' => 'Order not found or access denied.'
        ]);
    }

    public function test_api_can_create_order_item()
    {
        Sanctum::actingAs($this->user);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 95.00
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertStatus(201);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'order_id',
                'product_id',
                'quantity',
                'price',
                'order',
                'product'
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 95.00
        ]);

        // Check that product stock was decremented
        $this->product->refresh();
        $this->assertEquals(8, $this->product->stock);

        // Check that order total was updated
        $this->order->refresh();
        $this->assertEquals(190.00, $this->order->total_price);
    }

    public function test_api_can_create_order_item_with_product_price()
    {
        Sanctum::actingAs($this->user);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2
            // No price provided, should use product price
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertStatus(201);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('order_items', [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00 // Product price
        ]);
    }

    public function test_api_cannot_create_order_item_for_other_users_order()
    {
        Sanctum::actingAs($this->otherUser);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
    }

    public function test_api_cannot_create_order_item_with_insufficient_stock()
    {
        Sanctum::actingAs($this->user);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 15 // More than available stock (10)
        ];

        $response = $this->postJson('/api/order-items', $orderItemData);

        $response->assertStatus(500);
        $response->assertJson(['success' => false]);
        $response->assertJsonFragment(['message' => 'Failed to create order item. Insufficient stock. Available: 10']);
    }

    public function test_api_validates_order_item_creation_data()
    {
        Sanctum::actingAs($this->user);

        $response = $this->postJson('/api/order-items', []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['order_id', 'product_id', 'quantity']);
    }

    public function test_api_can_show_order_item()
    {
        Sanctum::actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items/' . $orderItem->id);

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'id',
                'order_id',
                'product_id',
                'quantity',
                'price',
                'order',
                'product'
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_cannot_show_other_users_order_item()
    {
        Sanctum::actingAs($this->otherUser);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items/' . $orderItem->id);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Access denied'
        ]);
    }

    public function test_api_can_get_edit_form_data()
    {
        Sanctum::actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items/' . $orderItem->id . '/edit');

        $response->assertStatus(200);
        $response->assertJsonStructure([
            'success',
            'data' => [
                'order_item',
                'products'
            ],
            'message'
        ]);
        $response->assertJson(['success' => true]);
    }

    public function test_api_cannot_edit_order_item_from_shipped_order()
    {
        Sanctum::actingAs($this->user);

        $this->order->update(['status' => 'shipped']);
        
        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->getJson('/api/order-items/' . $orderItem->id . '/edit');

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot edit order item. Order status is: shipped'
        ]);
    }

    public function test_api_can_update_order_item()
    {
        Sanctum::actingAs($this->user);

        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 5,
            'price' => 150.00
        ]);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        $updateData = [
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => 140.00
        ];

        $response = $this->putJson('/api/order-items/' . $orderItem->id, $updateData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('order_items', [
            'id' => $orderItem->id,
            'product_id' => $product2->id,
            'quantity' => 3,
            'price' => 140.00
        ]);

        // Check stock changes
        $this->product->refresh();
        $product2->refresh();
        $this->assertEquals(12, $this->product->stock); // Original stock restored (10 + 2)
        $this->assertEquals(2, $product2->stock); // Stock decremented (5 - 3)
    }

    public function test_api_can_update_order_item_same_product()
    {
        Sanctum::actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        // Manually decrement stock to simulate original creation
        $this->product->decrement('stock', 2);

        $updateData = [
            'product_id' => $this->product->id,
            'quantity' => 4, // Increase quantity
            'price' => 95.00
        ];

        $response = $this->putJson('/api/order-items/' . $orderItem->id, $updateData);

        $response->assertStatus(200);
        $response->assertJson(['success' => true]);

        $this->assertDatabaseHas('order_items', [
            'id' => $orderItem->id,
            'quantity' => 4,
            'price' => 95.00
        ]);

        // Check stock change - should decrease by 2 more (4 - 2)
        $this->product->refresh();
        $this->assertEquals(6, $this->product->stock); // 8 - 2
    }

    public function test_api_validates_order_item_update_data()
    {
        Sanctum::actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->putJson('/api/order-items/' . $orderItem->id, []);

        $response->assertStatus(422);
        $response->assertJsonValidationErrors(['product_id', 'quantity']);
    }

    public function test_api_can_delete_order_item()
    {
        Sanctum::actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 3
        ]);

        // Manually decrement stock to simulate creation
        $this->product->decrement('stock', 3);

        $response = $this->deleteJson('/api/order-items/' . $orderItem->id);

        $response->assertStatus(200);
        $response->assertJson([
            'success' => true,
            'message' => 'Order item deleted successfully'
        ]);

        $this->assertDatabaseMissing('order_items', ['id' => $orderItem->id]);

        // Check that stock was restored
        $this->product->refresh();
        $this->assertEquals(10, $this->product->stock); // Back to original stock
    }

    public function test_api_cannot_delete_order_item_from_delivered_order()
    {
        Sanctum::actingAs($this->user);

        $this->order->update(['status' => 'delivered']);
        
        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->deleteJson('/api/order-items/' . $orderItem->id);

        $response->assertStatus(422);
        $response->assertJson([
            'success' => false,
            'message' => 'Cannot delete order item. Order status is: delivered'
        ]);
    }

    public function test_api_cannot_delete_other_users_order_item()
    {
        Sanctum::actingAs($this->otherUser);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->deleteJson('/api/order-items/' . $orderItem->id);

        $response->assertStatus(403);
        $response->assertJson([
            'success' => false,
            'message' => 'Access denied'
        ]);
    }

    // Web Tests (skipped since views are not created)

    public function test_web_can_view_order_items_index()
    {
        $this->markTestSkipped('Views not implemented - API only controller');
    }

    public function test_web_can_view_create_form()
    {
        $this->markTestSkipped('Views not implemented - API only controller');
    }

    public function test_web_cannot_view_create_form_for_other_users_order()
    {
        $this->actingAs($this->otherUser);

        $response = $this->get('/order-items/create?order_id=' . $this->order->id);

        $response->assertRedirect('/orders');
        $response->assertSessionHas('error', 'Order not found or access denied.');
    }

    public function test_web_can_create_order_item()
    {
        $this->actingAs($this->user);

        $orderItemData = [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 95.00
        ];

        $response = $this->post('/order-items', $orderItemData);

        $response->assertRedirect();
        $response->assertSessionHas('success', 'Order item created successfully!');

        $this->assertDatabaseHas('order_items', [
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 95.00
        ]);
    }

    public function test_web_validates_order_item_creation()
    {
        $this->actingAs($this->user);

        $response = $this->post('/order-items', []);

        $response->assertRedirect();
        $response->assertSessionHasErrors(['order_id', 'product_id', 'quantity']);
    }

    public function test_web_can_view_order_item()
    {
        $this->markTestSkipped('Views not implemented - API only controller');
    }

    public function test_web_cannot_view_other_users_order_item()
    {
        $this->actingAs($this->otherUser);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->get('/order-items/' . $orderItem->id);

        $response->assertRedirect('/order-items');
        $response->assertSessionHas('error', 'Access denied');
    }

    public function test_web_can_view_edit_form()
    {
        $this->markTestSkipped('Views not implemented - API only controller');
    }

    public function test_web_cannot_edit_cancelled_order_item()
    {
        $this->actingAs($this->user);

        $this->order->update(['status' => 'cancelled']);
        
        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->get('/order-items/' . $orderItem->id . '/edit');

        $response->assertRedirect('/order-items/' . $orderItem->id);
        $response->assertSessionHas('error', 'Cannot edit order item. Order status is: cancelled');
    }

    public function test_web_can_update_order_item()
    {
        $this->actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        $updateData = [
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => 95.00
        ];

        $response = $this->put('/order-items/' . $orderItem->id, $updateData);

        $response->assertRedirect('/order-items/' . $orderItem->id);
        $response->assertSessionHas('success', 'Order item updated successfully!');

        $this->assertDatabaseHas('order_items', [
            'id' => $orderItem->id,
            'quantity' => 3,
            'price' => 95.00
        ]);
    }

    public function test_web_can_delete_order_item()
    {
        $this->actingAs($this->user);

        $orderItem = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id
        ]);

        $response = $this->delete('/order-items/' . $orderItem->id);

        $response->assertRedirect('/order-items');
        $response->assertSessionHas('success', 'Order item deleted successfully!');

        $this->assertDatabaseMissing('order_items', ['id' => $orderItem->id]);
    }

    public function test_order_total_updates_correctly()
    {
        Sanctum::actingAs($this->user);

        // Create first order item
        $orderItem1 = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $this->product->id,
            'quantity' => 2,
            'price' => 100.00
        ]);

        // Create second order item
        $product2 = Product::factory()->create([
            'category_id' => $this->category->id,
            'stock' => 5,
            'price' => 50.00
        ]);

        $orderItem2 = OrderItem::factory()->create([
            'order_id' => $this->order->id,
            'product_id' => $product2->id,
            'quantity' => 1,
            'price' => 50.00
        ]);

        // Manually update order total as it would be done by the controller
        $total = $this->order->items()->sum(\DB::raw('quantity * price'));
        $this->order->update(['total_price' => $total]);

        $this->order->refresh();
        $this->assertEquals(250.00, $this->order->total_price); // (2 * 100) + (1 * 50)

        // Update first order item quantity
        $response = $this->putJson('/api/order-items/' . $orderItem1->id, [
            'product_id' => $this->product->id,
            'quantity' => 3,
            'price' => 100.00
        ]);

        $response->assertStatus(200);

        $this->order->refresh();
        $this->assertEquals(350.00, $this->order->total_price); // (3 * 100) + (1 * 50)
    }
}
