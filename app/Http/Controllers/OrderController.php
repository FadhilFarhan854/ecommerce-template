<?php

namespace App\Http\Controllers;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderController extends Controller
{
    /**
     * Display a listing of orders (Web & API)
     */
    public function index(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiIndex($request);
        }

        // Handle web request
        $orders = Order::with(['items.product', 'user'])
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

        return view('orders.index', compact('orders', 'orderData'));
    }

    

    /**
     * Handle API index request
     */
    private function handleApiIndex(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $status = $request->get('status');

        $query = Order::with(['items.product', 'user'])
            ->where('user_id', Auth::id());

        if ($status) {
            $query->where('status', $status);
        }

        $orders = $query->orderBy('created_at', 'desc')
            ->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orders,
            'message' => 'Orders retrieved successfully'
        ]);
    }

    /**
     * Show the form for creating a new order (Web & API)
     */
    public function create(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiCreate($request);
        }

        // Handle web request
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->route('cart.index')
                ->with('error', 'Your cart is empty. Add items to cart before creating an order.');
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('orders.create', compact('cartItems', 'total'));
    }

    /**
     * Handle API create request (get cart preview)
     */
    private function handleApiCreate(Request $request): JsonResponse
    {
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return response()->json([
                'success' => false,
                'message' => 'Your cart is empty. Add items to cart before creating an order.'
            ], 400);
        }

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'cart_items' => $cartItems,
                'total_price' => $total
            ],
            'message' => 'Cart items retrieved for order creation'
        ]);
    }

    /**
     * Store a newly created order (Web & API)
     */
    public function store(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiStore($request);
        }

        // Handle web request
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery',
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $order = $this->createOrderFromCart($request);

            return redirect()->route('orders.show', $order)
                ->with('success', 'Order created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create order. Please try again.')
                ->withInput();
        }
    }

    /**
     * Handle API store request
     */
    private function handleApiStore(Request $request): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            $order = $this->createOrderFromCart($request);
            $order->load(['items.product', 'user']);

            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order created successfully!'
            ], 201);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => 'Failed to create order. Please try again.',
                'error' => $e->getMessage()
            ], 500);
        }
    }

    /**
     * Common method to create order from cart
     */
    private function createOrderFromCart(Request $request)
    {
        DB::beginTransaction();

        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            throw new \Exception('Cart is empty');
        }

        $totalPrice = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        // Create order
        $order = Order::create([
            'user_id' => Auth::id(),
            'status' => 'pending',
            'total_price' => $totalPrice,
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
            'payment_status' => 'pending',
        ]);

        // Create order items
        foreach ($cartItems as $cartItem) {
            OrderItem::create([
                'order_id' => $order->id,
                'product_id' => $cartItem->product_id,
                'quantity' => $cartItem->quantity,
                'price' => $cartItem->product->price,
            ]);

            // Update product stock
            $product = $cartItem->product;
            $product->stock -= $cartItem->quantity;
            $product->save();
        }

        // Clear cart
        Cart::where('user_id', Auth::id())->delete();

        DB::commit();

        return $order;
    }

    /**
     * Display the specified order (Web & API)
     */
    public function show(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to view this order.'
                ], 403);
            }
            abort(403, 'Unauthorized to view this order.');
        }

        $order->load(['items.product', 'user', 'shipment']);

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order retrieved successfully'
            ]);
        }

        // Handle web request
        return view('orders.show', compact('order'));
    }

    /**
     * Show the form for editing the order (Web & API)
     */
    public function edit(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to edit this order.'
                ], 403);
            }
            abort(403, 'Unauthorized to edit this order.');
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be edited.'
                ], 400);
            }
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be edited.');
        }

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order data for editing'
            ]);
        }

        // Handle web request
        return view('orders.edit', compact('order'));
    }

    /**
     * Update the specified order (Web & API)
     */
    public function update(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to update this order.'
                ], 403);
            }
            abort(403, 'Unauthorized to update this order.');
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be updated.'
                ], 400);
            }
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be updated.');
        }

        $validator = Validator::make($request->all(), [
            'shipping_address' => 'required|string|max:500',
            'payment_method' => 'required|string|in:credit_card,debit_card,paypal,bank_transfer,cash_on_delivery',
        ]);

        if ($validator->fails()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $order->update([
            'shipping_address' => $request->shipping_address,
            'payment_method' => $request->payment_method,
        ]);

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            $order->load(['items.product', 'user', 'shipment']);
            return response()->json([
                'success' => true,
                'data' => $order,
                'message' => 'Order updated successfully!'
            ]);
        }

        // Handle web request
        return redirect()->route('orders.show', $order)
            ->with('success', 'Order updated successfully!');
    }

    /**
     * Cancel the specified order (Web & API)
     */
    public function destroy(Request $request, Order $order)
    {
        if ($order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized to cancel this order.'
                ], 403);
            }
            abort(403, 'Unauthorized to cancel this order.');
        }

        if (!in_array($order->status, ['pending', 'confirmed'])) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'This order cannot be cancelled.'
                ], 400);
            }
            return redirect()->route('orders.show', $order)
                ->with('error', 'This order cannot be cancelled.');
        }

        try {
            DB::beginTransaction();

            // Restore product stock
            foreach ($order->items as $item) {
                $product = $item->product;
                $product->stock += $item->quantity;
                $product->save();
            }

            $order->update(['status' => 'cancelled']);

            DB::commit();

            // Check if this is an API request
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order cancelled successfully!'
                ]);
            }

            // Handle web request
            return redirect()->route('orders.index')
                ->with('success', 'Order cancelled successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to cancel order. Please try again.',
                    'error' => $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to cancel order. Please try again.');
        }
    }

    /**
     * Update order status (API only - Admin function)
     */
    public function updateStatus(Request $request, Order $order): JsonResponse
    {
        $validator = Validator::make($request->all(), [
            'status' => 'required|string|in:pending,confirmed,processing,shipped,delivered,cancelled',
            'payment_status' => 'sometimes|string|in:pending,paid,failed,refunded',
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $updateData = ['status' => $request->status];
        
        if ($request->has('payment_status')) {
            $updateData['payment_status'] = $request->payment_status;
        }

        $order->update($updateData);

        $order->load(['items.product', 'user', 'shipment']);

        return response()->json([
            'success' => true,
            'data' => $order,
            'message' => 'Order status updated successfully!'
        ]);
    }

    /**
     * Get order statistics (API only)
     */
    public function statistics(): JsonResponse
    {
        $userId = Auth::id();

        $stats = [
            'total_orders' => Order::where('user_id', $userId)->count(),
            'pending_orders' => Order::where('user_id', $userId)->where('status', 'pending')->count(),
            'confirmed_orders' => Order::where('user_id', $userId)->where('status', 'confirmed')->count(),
            'processing_orders' => Order::where('user_id', $userId)->where('status', 'processing')->count(),
            'shipped_orders' => Order::where('user_id', $userId)->where('status', 'shipped')->count(),
            'delivered_orders' => Order::where('user_id', $userId)->where('status', 'delivered')->count(),
            'cancelled_orders' => Order::where('user_id', $userId)->where('status', 'cancelled')->count(),
            'total_spent' => Order::where('user_id', $userId)->where('status', '!=', 'cancelled')->sum('total_price'),
        ];

        return response()->json([
            'success' => true,
            'data' => $stats,
            'message' => 'Order statistics retrieved successfully'
        ]);
    }

    public function history(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiIndex($request);
        }

        // Handle web request
        $orders = Order::with(['items.product', 'user'])
            ->where('user_id', Auth::id())
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

        return view('orders.history', compact('orders', 'orderData'));
    }
}
