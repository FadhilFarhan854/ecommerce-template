<?php

namespace App\Http\Controllers;

use App\Http\Requests\CategoryRequest;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Validation\ValidationException;

class CategoryController extends Controller
{
    /**
     * Display a listing of the categories.
     */
    public function index(Request $request)
    {
        $categories = Category::with('products')->get();
        
        // Jika request dari API (berdasarkan Accept header atau path)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $categories
            ]);
        }
        
        // Jika request dari web (monolith)
        return view('categories.index', compact('categories'));
    }

    /**
     * Show the form for creating a new category.
     */
    public function create(Request $request)
    {
        // Hanya untuk web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('categories.create');
    }
    
    /**
     * Store a newly created category in storage.
     */
    public function store(CategoryRequest $request)
    {
        $category = Category::create([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Category created successfully',
                'data' => $category
            ], 201);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('categories.index')
                        ->with('success', 'Category created successfully');
    }

    /**
     * Display the specified category.
     */
    public function show(Category $category, Request $request)
    {
        $category->load('products');
        
        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $category
            ]);
        }
        
        // Jika request dari web (monolith)
        return view('categories.show', compact('category'));
    }

    /**
     * Show the form for editing the specified category.
     */
    public function edit(Category $category, Request $request)
    {
        // Hanya untuk web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('categories.edit', compact('category'));
    }

    /**
     * Update the specified category in storage.
     */
    public function update(CategoryRequest $request, Category $category)
    {
        $category->update([
            'name' => $request->name,
            'slug' => $request->slug
        ]);

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Category updated successfully',
                'data' => $category
            ]);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('categories.index')
                        ->with('success', 'Category updated successfully');
    }

    /**
     * Remove the specified category from storage.
     */
    public function destroy(Category $category, Request $request)
    {
        // Check if category has products
        if ($category->products()->count() > 0) {
            $errorMessage = 'Cannot delete category with associated products';
            
            // Jika request dari API
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            // Jika request dari web (monolith)
            return redirect()->route('categories.index')
                            ->with('error', $errorMessage);
        }

        $category->delete();

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Category deleted successfully'
            ]);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('categories.index')
                        ->with('success', 'Category deleted successfully');
    }
}
