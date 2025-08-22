<?php

namespace App\Http\Controllers;

use App\Models\OrderItem;
use App\Models\Order;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

class OrderItemController extends Controller
{
    /**
     * Display a listing of order items (Web & API)
     */
    public function index(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiIndex($request);
        }

        // Handle web request
        $orderId = $request->get('order_id');
        
        $query = OrderItem::with(['order', 'product']);
        
        if ($orderId) {
            $query->whereHas('order', function ($q) use ($orderId) {
                $q->where('id', $orderId)->where('user_id', Auth::id());
            });
        } else {
            $query->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate(15);

        return view('order-items.index', compact('orderItems'));
    }

    /**
     * Handle API index request
     */
    private function handleApiIndex(Request $request): JsonResponse
    {
        $perPage = $request->get('per_page', 15);
        $orderId = $request->get('order_id');

        $query = OrderItem::with(['order', 'product']);
        
        if ($orderId) {
            $query->whereHas('order', function ($q) use ($orderId) {
                $q->where('id', $orderId)->where('user_id', Auth::id());
            });
        } else {
            $query->whereHas('order', function ($q) {
                $q->where('user_id', Auth::id());
            });
        }

        $orderItems = $query->orderBy('created_at', 'desc')->paginate($perPage);

        return response()->json([
            'success' => true,
            'data' => $orderItems,
            'message' => 'Order items retrieved successfully'
        ]);
    }

    /**
     * Show the form for creating a new order item (Web & API)
     */
    public function create(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiCreate($request);
        }

        // Handle web request
        $orderId = $request->get('order_id');
        $order = null;
        
        if ($orderId) {
            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$order) {
                return redirect()->route('orders.index')
                    ->with('error', 'Order not found or access denied.');
            }
        }

        $products = Product::where('stock', '>', 0)->get();

        return view('order-items.create', compact('order', 'products'));
    }

    /**
     * Handle API create request (get form data)
     */
    private function handleApiCreate(Request $request): JsonResponse
    {
        $orderId = $request->get('order_id');
        $order = null;
        
        if ($orderId) {
            $order = Order::where('id', $orderId)
                ->where('user_id', Auth::id())
                ->first();
                
            if (!$order) {
                return response()->json([
                    'success' => false,
                    'message' => 'Order not found or access denied.'
                ], 404);
            }
        }

        $products = Product::where('stock', '>', 0)->get();

        return response()->json([
            'success' => true,
            'data' => [
                'order' => $order,
                'products' => $products
            ],
            'message' => 'Form data retrieved successfully'
        ]);
    }

    /**
     * Store a newly created order item (Web & API)
     */
    public function store(Request $request)
    {
        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiStore($request);
        }

        // Handle web request
        $validator = Validator::make($request->all(), [
            'order_id' => 'required|exists:orders,id',
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $orderItem = $this->createOrderItem($request);

            return redirect()->route('order-items.show', $orderItem)
                ->with('success', 'Order item created successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to create order item. ' . $e->getMessage())
                ->withInput();
        }
    }

  

    /**
     * Common method to create order item
     */
    private function createOrderItem(Request $request)
    {
        DB::beginTransaction();

        // Verify order belongs to authenticated user
        $order = Order::where('id', $request->order_id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        // Get product and validate stock
        $product = Product::findOrFail($request->product_id);
        
        if ($product->stock < $request->quantity) {
            throw new \Exception('Insufficient stock. Available: ' . $product->stock);
        }

        // Use product price if not provided
        $price = $request->price ?? $product->price;

        // Create order item
        $orderItem = OrderItem::create([
            'order_id' => $request->order_id,
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $price,
        ]);

        // Update product stock
        $product->decrement('stock', $request->quantity);

        // Update order total
        $this->updateOrderTotal($order);

        DB::commit();

        return $orderItem;
    }

    /**
     * Display the specified order item (Web & API)
     */
    public function show(Request $request, OrderItem $orderItem)
    {
        // Verify access
        if ($orderItem->order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('order-items.index')
                ->with('error', 'Access denied');
        }

        $orderItem->load(['order', 'product']);

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $orderItem,
                'message' => 'Order item retrieved successfully'
            ]);
        }

        // Handle web request
        return view('order-items.show', compact('orderItem'));
    }

    /**
     * Show the form for editing the specified order item (Web & API)
     */
    public function edit(Request $request, OrderItem $orderItem)
    {
        // Verify access
        if ($orderItem->order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('order-items.index')
                ->with('error', 'Access denied');
        }

        // Check if order is still editable
        if (in_array($orderItem->order->status, ['shipped', 'delivered', 'cancelled'])) {
            $message = 'Cannot edit order item. Order status is: ' . $orderItem->order->status;
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            
            return redirect()->route('order-items.show', $orderItem)
                ->with('error', $message);
        }

        $orderItem->load(['order', 'product']);
        $products = Product::where('stock', '>', 0)->get();

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => [
                    'order_item' => $orderItem,
                    'products' => $products
                ],
                'message' => 'Order item edit data retrieved successfully'
            ]);
        }

        // Handle web request
        return view('order-items.edit', compact('orderItem', 'products'));
    }

    /**
     * Update the specified order item (Web & API)
     */
    public function update(Request $request, OrderItem $orderItem)
    {
        // Verify access
        if ($orderItem->order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('order-items.index')
                ->with('error', 'Access denied');
        }

        // Check if this is an API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return $this->handleApiUpdate($request, $orderItem);
        }

        // Handle web request
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1',
            'price' => 'nullable|numeric|min:0'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        try {
            $this->updateOrderItem($request, $orderItem);

            return redirect()->route('order-items.show', $orderItem)
                ->with('success', 'Order item updated successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            return redirect()->back()
                ->with('error', 'Failed to update order item. ' . $e->getMessage())
                ->withInput();
        }
    }

    /**
     * Handle API update request
     */
   

    /**
     * Common method to update order item
     */
    private function updateOrderItem(Request $request, OrderItem $orderItem)
    {
        DB::beginTransaction();

        // Check if order is still editable
        if (in_array($orderItem->order->status, ['shipped', 'delivered', 'cancelled'])) {
            throw new \Exception('Cannot update order item. Order status is: ' . $orderItem->order->status);
        }

        $oldProduct = $orderItem->product;
        $oldQuantity = $orderItem->quantity;

        // Get new product and validate stock
        $newProduct = Product::findOrFail($request->product_id);
        
        // Calculate stock needed
        $stockNeeded = $request->quantity;
        if ($newProduct->id === $oldProduct->id) {
            $stockNeeded = $request->quantity - $oldQuantity;
        }
        
        if ($newProduct->stock < $stockNeeded) {
            throw new \Exception('Insufficient stock. Available: ' . $newProduct->stock);
        }

        // Restore old product stock if product changed
        if ($newProduct->id !== $oldProduct->id) {
            $oldProduct->increment('stock', $oldQuantity);
        }

        // Use product price if not provided
        $price = $request->price ?? $newProduct->price;

        // Update order item
        $orderItem->update([
            'product_id' => $request->product_id,
            'quantity' => $request->quantity,
            'price' => $price,
        ]);

        // Update new product stock
        if ($newProduct->id === $oldProduct->id) {
            if ($stockNeeded > 0) {
                $newProduct->decrement('stock', $stockNeeded);
            } elseif ($stockNeeded < 0) {
                $newProduct->increment('stock', abs($stockNeeded));
            }
        } else {
            $newProduct->decrement('stock', $request->quantity);
        }

        // Update order total
        $this->updateOrderTotal($orderItem->order);

        DB::commit();
    }

    /**
     * Remove the specified order item (Web & API)
     */
    public function destroy(Request $request, OrderItem $orderItem)
    {
        // Verify access
        if ($orderItem->order->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('order-items.index')
                ->with('error', 'Access denied');
        }

        // Check if order is still editable
        if (in_array($orderItem->order->status, ['shipped', 'delivered', 'cancelled'])) {
            $message = 'Cannot delete order item. Order status is: ' . $orderItem->order->status;
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $message
                ], 422);
            }
            
            return redirect()->route('order-items.show', $orderItem)
                ->with('error', $message);
        }

        try {
            DB::beginTransaction();

            // Restore product stock
            $orderItem->product->increment('stock', $orderItem->quantity);
            
            // Store order for total update
            $order = $orderItem->order;
            
            // Delete order item
            $orderItem->delete();

            // Update order total
            $this->updateOrderTotal($order);

            DB::commit();

            // Check if this is an API request
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => true,
                    'message' => 'Order item deleted successfully'
                ]);
            }

            // Handle web request
            return redirect()->route('order-items.index')
                ->with('success', 'Order item deleted successfully!');

        } catch (\Exception $e) {
            DB::rollback();
            
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to delete order item. ' . $e->getMessage()
                ], 500);
            }
            
            return redirect()->back()
                ->with('error', 'Failed to delete order item. ' . $e->getMessage());
        }
    }

    /**
     * Update order total price
     */
    private function updateOrderTotal(Order $order)
    {
        $total = $order->items()->sum(DB::raw('quantity * price'));
        $order->update(['total_price' => $total]);
    }
    
}
