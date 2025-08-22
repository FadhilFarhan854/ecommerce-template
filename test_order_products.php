<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test the controller logic
$orders = \App\Models\Order::with(['items.product', 'user'])
    ->orderBy('created_at', 'desc')
    ->paginate(10);

$orderData = $orders->map(function ($order) {
    $products = $order->items->map(function ($item) {
        return [
            'id' => $item->product->id,
            'name' => $item->product->name,
            'quantity' => $item->quantity,
            'price' => $item->price,
        ];
    });

    return [
        'id' => $order->id,
        'order_number' => $order->id,
        'customer_name' => $order->user->name ?? 'Guest',
        'products' => $products->toArray(),
        'total_quantity' => $order->items->sum('quantity'),
        'status' => $order->status,
        'address' => $order->shipping_address,
        'total_price' => $order->total_price,
        'created_at' => $order->created_at->format('d M Y H:i'),
    ];
});

echo "Order Data Test:\n";
echo "================\n";
foreach ($orderData as $order) {
    echo "Order ID: " . $order['id'] . "\n";
    echo "Customer: " . $order['customer_name'] . "\n";
    echo "Products count: " . count($order['products']) . "\n";
    if (count($order['products']) > 0) {
        foreach ($order['products'] as $product) {
            echo "  - Product: " . $product['name'] . " (Qty: " . $product['quantity'] . ")\n";
        }
    } else {
        echo "  No products found\n";
    }
    echo "Total Price: " . $order['total_price'] . "\n";
    echo "---\n";
}
