<?php

namespace App\Http\Controllers;

use App\Http\Requests\ProductRequest;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class ProductController extends Controller
{
    /**
     * Display a listing of the products.
     */
    public function index(Request $request)
    {
        $query = Product::with('category');
        
        // Filter by category if provided
        if ($request->filled('category_id')) {
            $query->where('category_id', $request->category_id);
        }
        
        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }
        
        // Pagination for API
        if ($request->wantsJson() || $request->is('api/*')) {
            $products = $query->paginate(15);
            return response()->json([
                'success' => true,
                'data' => $products
            ]);
        }
        
        // For web views
        $products = $query->paginate(12);
        $categories = Category::all();
        
        return view('products.index', compact('products', 'categories'));
    }

    /**
     * Show the form for creating a new product.
     */
    public function create(Request $request)
    {
        // Only for web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        $categories = Category::all();
        return view('products.create', compact('categories'));
    }

    /**
     * Store a newly created product in storage.
     */
    public function store(ProductRequest $request)
    {
        $productData = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        // Handle image upload or URL
        if ($request->hasFile('image')) {
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = '/storage/' . $imagePath;
        } elseif ($request->filled('image_url')) {
            $productData['image'] = $request->image_url;
        }

        $product = Product::create($productData);
        $product->load('category');

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Product created successfully',
                'data' => $product
            ], 201);
        }
        
        // If web request (monolith)
        return redirect()->route('products.index')
                        ->with('success', 'Product created successfully');
    }

    /**
     * Display the specified product.
     */
    public function show(Product $product, Request $request)
    {
        $product->load('category');
        
        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        }
        
        // If web request (monolith)
        return view('products.show', compact('product'));
    }

    /**
     * Show the form for editing the specified product.
     */
    public function edit(Product $product, Request $request)
    {
        // Only for web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        $categories = Category::all();
        return view('products.edit', compact('product', 'categories'));
    }

    /**
     * Update the specified product in storage.
     */
    public function update(ProductRequest $request, Product $product)
    {
        $productData = [
            'category_id' => $request->category_id,
            'name' => $request->name,
            'slug' => $request->slug,
            'description' => $request->description,
            'price' => $request->price,
            'stock' => $request->stock,
        ];

        // Handle image upload or URL
        if ($request->hasFile('image')) {
            // Delete old image if it exists and is stored locally
            if ($product->image && str_starts_with($product->image, '/storage/')) {
                $oldImagePath = str_replace('/storage/', '', $product->image);
                if (file_exists(storage_path('app/public/' . $oldImagePath))) {
                    unlink(storage_path('app/public/' . $oldImagePath));
                }
            }
            
            $imagePath = $request->file('image')->store('products', 'public');
            $productData['image'] = '/storage/' . $imagePath;
        } elseif ($request->filled('image_url')) {
            $productData['image'] = $request->image_url;
        }

        $product->update($productData);
        $product->load('category');

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Product updated successfully',
                'data' => $product
            ]);
        }
        
        // If web request (monolith)
        return redirect()->route('products.index')
                        ->with('success', 'Product updated successfully');
    }

    /**
     * Remove the specified product from storage.
     */
    public function destroy(Product $product, Request $request)
    {
        // Check if product has cart items or order items
        $hasCartItems = $product->carts()->count() > 0;
        $hasOrderItems = $product->orderItems()->count() > 0;
        
        if ($hasCartItems || $hasOrderItems) {
            $errorMessage = 'Cannot delete product that has been added to carts or orders';
            
            // If API request
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            // If web request (monolith)
            return redirect()->route('products.index')
                            ->with('error', $errorMessage);
        }

        $product->delete();

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Product deleted successfully'
            ]);
        }
        
        // If web request (monolith)
        return redirect()->route('products.index')
                        ->with('success', 'Product deleted successfully');
    }

    /**
     * Display a catalog of products for customers with pagination
     */
    public function catalog(Request $request)
    {
        $perPage = $request->get('per_page', 12); // Default 12 produk per halaman
        $search = $request->get('search');
        $category = $request->get('category');
        $sort = $request->get('sort', 'created_at');
        $order = $request->get('order', 'desc');

        $query = Product::query();

        // Filter berdasarkan pencarian
        if ($search) {
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', '%' . $search . '%')
                  ->orWhere('description', 'like', '%' . $search . '%');
            });
        }

        // Filter berdasarkan kategori
        if ($category) {
            $query->whereHas('category', function($q) use ($category) {
                $q->where('name', $category);
            });
        }

        // Sorting
        $allowedSorts = ['name', 'price', 'created_at'];
        if (in_array($sort, $allowedSorts)) {
            $query->orderBy($sort, $order);
        }

        $products = $query->with(['category'])->paginate($perPage);
        
        // Ambil semua kategori untuk filter
        $categories = Category::all();

        return view('products.catalog', compact('products', 'categories', 'search', 'category', 'sort', 'order'));
    }

    /**
     * Display the specified product
     */
    public function showProduct(Product $product)
    {
        $product->load(['category']);
        $relatedProducts = Product::where('category_id', $product->category_id)
                                ->where('id', '!=', $product->id)
                                ->limit(4)
                                ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
