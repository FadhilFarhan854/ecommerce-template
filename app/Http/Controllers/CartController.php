<?php

namespace App\Http\Controllers;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\JsonResponse;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class CartController extends Controller
{
    /**
     * Display the cart items for the authenticated user.
     */
    public function index(): View
    {
        $cartItems = Cart::with(['product.images', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a product to the cart.
     */
    public function store(Request $request)
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product has enough stock
        if ($product->stock < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Insufficient stock available.');
        }

        $existingCartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            // Check total quantity against stock
            if ($product->stock < $newQuantity) {
                if ($request->expectsJson()) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Stok tidak mencukupi.'
                    ], 400);
                }
                return redirect()->back()->with('error', 'Insufficient stock available.');
            }

            $existingCartItem->update(['quantity' => $newQuantity]);
            $cartItem = $existingCartItem->fresh(['product.images', 'product.category']);
        } else {
            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
            $cartItem->load(['product.images', 'product.category']);
        }

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Produk berhasil ditambahkan ke keranjang.',
                'data' => $cartItem
            ]);
        }

        return redirect()->back()->with('success', 'Product added to cart successfully.');
    }

    /**
     * Update the quantity of a cart item.
     */
    public function update(Request $request, Cart $cart)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if product has enough stock
        if ($cart->product->stock < $request->quantity) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Stok tidak mencukupi.'
                ], 400);
            }
            return redirect()->back()->with('error', 'Insufficient stock available.');
        }

        $cart->update(['quantity' => $request->quantity]);
        $cart->load(['product.images', 'product.category']);

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil diperbarui.',
                'data' => $cart
            ]);
        }

        return redirect()->back()->with('success', 'Cart updated successfully.');
    }

    /**
     * Remove a cart item.
     */
    public function destroy(Request $request, Cart $cart)
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            if ($request->expectsJson()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized action.'
                ], 403);
            }
            return redirect()->back()->with('error', 'Unauthorized action.');
        }

        $cart->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Item berhasil dihapus dari keranjang.'
            ]);
        }

        return redirect()->back()->with('success', 'Item removed from cart.');
    }

    /**
     * Clear all cart items for the authenticated user.
     */
    public function clear(Request $request)
    {
        Cart::where('user_id', Auth::id())->delete();

        if ($request->expectsJson()) {
            return response()->json([
                'success' => true,
                'message' => 'Keranjang berhasil dikosongkan.'
            ]);
        }

        return redirect()->back()->with('success', 'Cart cleared successfully.');
    }

    /**
     * Get cart count for web requests.
     */
    public function webCount(): JsonResponse
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'count' => $count
        ]);
    }

    // API Methods

    /**
     * Get cart items via API.
     */
    public function apiIndex(): JsonResponse
    {
        $cartItems = Cart::with(['product', 'product.category'])
            ->where('user_id', Auth::id())
            ->get();

        $total = $cartItems->sum(function ($item) {
            return $item->quantity * $item->product->price;
        });

        return response()->json([
            'success' => true,
            'data' => [
                'items' => $cartItems,
                'total' => $total,
                'count' => $cartItems->count()
            ]
        ]);
    }

    /**
     * Add product to cart via API.
     */
    public function apiStore(Request $request): JsonResponse
    {
        $request->validate([
            'product_id' => 'required|exists:products,id',
            'quantity' => 'required|integer|min:1'
        ]);

        $product = Product::findOrFail($request->product_id);

        // Check if product has enough stock
        if ($product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ], 400);
        }

        $existingCartItem = Cart::where('user_id', Auth::id())
            ->where('product_id', $request->product_id)
            ->first();

        if ($existingCartItem) {
            $newQuantity = $existingCartItem->quantity + $request->quantity;
            
            // Check total quantity against stock
            if ($product->stock < $newQuantity) {
                return response()->json([
                    'success' => false,
                    'message' => 'Insufficient stock available.'
                ], 400);
            }

            $existingCartItem->update(['quantity' => $newQuantity]);
            $cartItem = $existingCartItem->fresh(['product', 'product.category']);
        } else {
            $cartItem = Cart::create([
                'user_id' => Auth::id(),
                'product_id' => $request->product_id,
                'quantity' => $request->quantity,
            ]);
            $cartItem->load(['product', 'product.category']);
        }

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart successfully.',
            'data' => $cartItem
        ], 201);
    }

    /**
     * Update cart item via API.
     */
    public function apiUpdate(Request $request, Cart $cart): JsonResponse
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $request->validate([
            'quantity' => 'required|integer|min:1'
        ]);

        // Check if product has enough stock
        if ($cart->product->stock < $request->quantity) {
            return response()->json([
                'success' => false,
                'message' => 'Insufficient stock available.'
            ], 400);
        }

        $cart->update(['quantity' => $request->quantity]);
        $cart->load(['product', 'product.category']);

        return response()->json([
            'success' => true,
            'message' => 'Cart updated successfully.',
            'data' => $cart
        ]);
    }

    /**
     * Remove cart item via API.
     */
    public function apiDestroy(Cart $cart): JsonResponse
    {
        // Ensure the cart item belongs to the authenticated user
        if ($cart->user_id !== Auth::id()) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized action.'
            ], 403);
        }

        $cart->delete();

        return response()->json([
            'success' => true,
            'message' => 'Item removed from cart.'
        ]);
    }

    /**
     * Clear all cart items via API.
     */
    public function apiClear(): JsonResponse
    {
        Cart::where('user_id', Auth::id())->delete();

        return response()->json([
            'success' => true,
            'message' => 'Cart cleared successfully.'
        ]);
    }

    /**
     * Get cart count via API.
     */
    public function apiCount(): JsonResponse
    {
        $count = Cart::where('user_id', Auth::id())->sum('quantity');

        return response()->json([
            'success' => true,
            'data' => [
                'count' => $count
            ]
        ]);
    }
    
}
