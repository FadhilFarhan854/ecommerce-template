<?php

namespace App\Http\Controllers;

use App\Models\Discount;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class DiscountController extends Controller
{
    /**
     * Display a listing of discounts
     */
    public function index()
    {
        $discounts = Discount::with('product')->latest()->paginate(20);
        return view('admin.discounts.index', compact('discounts'));
    }

    /**
     * Show the form for creating a new discount
     */
    public function create()
    {
        $products = Product::whereDoesntHave('discount')->get();
        return view('admin.discounts.create', compact('products'));
    }

    /**
     * Store a newly created discount
     */
    public function store(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id|unique:discounts,product_id',
            'percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'nullable|date|after_or_equal:today',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        Discount::create([
            'product_id' => $request->product_id,
            'percentage' => $request->percentage,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount created successfully!');
    }

    /**
     * Display the specified discount
     */
    public function show(Discount $discount)
    {
        $discount->load('product');
        return view('admin.discounts.show', compact('discount'));
    }

    /**
     * Show the form for editing the discount
     */
    public function edit(Discount $discount)
    {
        $products = Product::all();
        return view('admin.discounts.edit', compact('discount', 'products'));
    }

    /**
     * Update the specified discount
     */
    public function update(Request $request, Discount $discount)
    {
        $validator = Validator::make($request->all(), [
            'percentage' => 'required|numeric|min:1|max:100',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after:start_date',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return redirect()->back()
                ->withErrors($validator)
                ->withInput();
        }

        $discount->update([
            'percentage' => $request->percentage,
            'start_date' => $request->start_date,
            'end_date' => $request->end_date,
            'is_active' => $request->has('is_active') ? true : false
        ]);

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount updated successfully!');
    }

    /**
     * Remove the specified discount
     */
    public function destroy(Discount $discount)
    {
        $discount->delete();

        return redirect()->route('admin.discounts.index')
            ->with('success', 'Discount deleted successfully!');
    }

    /**
     * Quick add/update discount for a product (AJAX)
     */
    public function quickUpdate(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'product_id' => 'required|exists:products,id',
            'percentage' => 'required|numeric|min:1|max:100',
            'is_active' => 'boolean'
        ]);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validation failed',
                'errors' => $validator->errors()
            ], 422);
        }

        $discount = Discount::updateOrCreate(
            ['product_id' => $request->product_id],
            [
                'percentage' => $request->percentage,
                'is_active' => $request->has('is_active') ? true : false
            ]
        );

        return response()->json([
            'success' => true,
            'message' => 'Discount updated successfully!',
            'discount' => $discount
        ]);
    }
}
