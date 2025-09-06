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
            'weight' => $request->weight,
        ];

        $product = Product::create($productData);

        // Handle multiple image uploads
        if ($request->hasFile('images')) {
            foreach ($request->file('images') as $imageFile) {
                $imagePath = $imageFile->store('products', 'public');
                $product->images()->create(['url' => '/storage/' . $imagePath]);
            }
        }

        // Handle multiple image URLs
        if ($request->filled('image_urls')) {
            $urls = $request->input('image_urls');
            if (is_array($urls)) {
                foreach ($urls as $url) {
                    if (trim($url)) {
                        $product->images()->create(['url' => trim($url)]);
                    }
                }
            } else {
                // Split by comma and create images for each URL
                $urlArray = array_filter(array_map('trim', explode(',', $urls)));
                foreach ($urlArray as $url) {
                    if (filter_var($url, FILTER_VALIDATE_URL)) {
                        $product->images()->create(['url' => $url]);
                    }
                }
            }
        }

        $product->load(['category', 'images']);

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
        $product->load(['category', 'images', 'reviews.user']);
        
        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $product
            ]);
        }

        // Get related products
        $relatedProducts = Product::with(['category', 'images'])
                    ->where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->limit(4)
                    ->get();
        
        // If web request (monolith)
        return view('products.show', compact('product', 'relatedProducts'));
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
        try {
            $productData = [
                'category_id' => $request->category_id,
                'name' => $request->name,
                'slug' => $request->slug,
                'description' => $request->description,
                'price' => $request->price,
                'stock' => $request->stock,
                'weight' => $request->weight,
            ];

            $product->update($productData);

            // Optionally, remove old images if requested
            if ($request->has('remove_image_ids')) {
                $ids = $request->input('remove_image_ids');
                if (is_array($ids)) {
                    foreach ($ids as $id) {
                        $image = $product->images()->find($id);
                        if ($image) {
                            // Delete file if stored locally
                            if (str_starts_with($image->url, '/storage/')) {
                                $oldImagePath = str_replace('/storage/', '', $image->url);
                                if (file_exists(storage_path('app/public/' . $oldImagePath))) {
                                    unlink(storage_path('app/public/' . $oldImagePath));
                                }
                            }
                            $image->delete();
                        }
                    }
                }
            }

            // Handle new image uploads
            if ($request->hasFile('images')) {
                foreach ($request->file('images') as $imageFile) {
                    if ($imageFile->isValid()) {
                        try {
                            $imagePath = $imageFile->store('products', 'public');
                            $product->images()->create(['url' => '/storage/' . $imagePath]);
                        } catch (\Exception $e) {
                            \Log::error('Failed to upload image: ' . $e->getMessage());
                            // Continue with other images
                        }
                    }
                }
            }

            // Handle new image URLs (comma-separated string)
            if ($request->filled('image_urls')) {
                $imageUrls = $request->input('image_urls');
                // Split by comma and clean up
                $urls = array_map('trim', explode(',', $imageUrls));
                $urls = array_filter($urls, function($url) {
                    return !empty($url) && filter_var($url, FILTER_VALIDATE_URL);
                });
                
                foreach ($urls as $url) {
                    try {
                        $product->images()->create(['url' => $url]);
                    } catch (\Exception $e) {
                        \Log::error('Failed to save image URL: ' . $e->getMessage());
                        // Continue with other URLs
                    }
                }
            }

            $product->load(['category', 'images']);

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
                            
        } catch (\Exception $e) {
            \Log::error('Product update failed: ' . $e->getMessage());
            
            // If API request
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Failed to update product: ' . $e->getMessage()
                ], 500);
            }

            // If web request (monolith)
            return redirect()->back()
                            ->withInput()
                            ->with('error', 'Failed to update product. Please try again.');
        }
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
        $allowedSorts = ['name', 'price', 'created_at', 'popular'];
        if (in_array($sort, $allowedSorts)) {
            if ($sort === 'popular') {
                // Sort berdasarkan popularitas (jumlah item terjual)
                $query->withCount('orderItems')
                      ->orderBy('order_items_count', $order);
            } else {
                $query->orderBy($sort, $order);
            }
        }

        $products = $query->with(['category', 'images'])->paginate($perPage);
        
        // Ambil semua kategori untuk filter
        $categories = Category::all();

        return view('products.catalog', compact('products', 'categories', 'search', 'category', 'sort', 'order'));
    }

    /**
     * Display the specified product
     */
    public function showProduct(Product $product)
    {
        $product->load(['category', 'images', 'reviews.user']);
        $relatedProducts = Product::with(['category', 'images'])
                    ->where('category_id', $product->category_id)
                    ->where('id', '!=', $product->id)
                    ->limit(4)
                    ->get();

        return view('products.show', compact('product', 'relatedProducts'));
    }
}
