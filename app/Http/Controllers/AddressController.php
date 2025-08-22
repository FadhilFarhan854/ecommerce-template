<?php

namespace App\Http\Controllers;

use App\Http\Requests\AddressRequest;
use App\Models\Address;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Auth;

class AddressController extends Controller
{
    /**
     * Display a listing of the addresses for the authenticated user.
     */
    public function index(Request $request)
    {
        $query = Address::where('user_id', Auth::id())->with('user');
        
        // For API, include pagination
        if ($request->wantsJson() || $request->is('api/*')) {
            $addresses = $query->paginate(15);
            return response()->json([
                'success' => true,
                'data' => $addresses
            ]);
        }
        
        // For web views
        $addresses = $query->get();
        return view('addresses.index', compact('addresses'));
    }

    /**
     * Show the form for creating a new address.
     */
    public function create(Request $request)
    {
        // Only for web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('addresses.create');
    }

    /**
     * Store a newly created address in storage.
     */
    public function store(AddressRequest $request)
    {
        $addressData = array_merge($request->validated(), [
            'user_id' => Auth::id()
        ]);

        $address = Address::create($addressData);
        $address->load('user');

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Address created successfully',
                'data' => $address
            ], 201);
        }
        
        // If web request (monolith)
        return redirect()->route('addresses.index')
                        ->with('success', 'Address created successfully');
    }

    /**
     * Display the specified address.
     */
    public function show(Address $address, Request $request)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('addresses.index')
                            ->with('error', 'Access denied');
        }

        $address->load('user');
        
        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $address
            ]);
        }
        
        // If web request (monolith)
        return view('addresses.show', compact('address'));
    }

    /**
     * Show the form for editing the specified address.
     */
    public function edit(Address $address, Request $request)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('addresses.index')
                            ->with('error', 'Access denied');
        }

        // Only for web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('addresses.edit', compact('address'));
    }

    /**
     * Update the specified address in storage.
     */
    public function update(AddressRequest $request, Address $address)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('addresses.index')
                            ->with('error', 'Access denied');
        }

        $address->update($request->validated());
        $address->load('user');

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Address updated successfully',
                'data' => $address
            ]);
        }
        
        // If web request (monolith)
        return redirect()->route('addresses.index')
                        ->with('success', 'Address updated successfully');
    }

    /**
     * Remove the specified address from storage.
     */
    public function destroy(Address $address, Request $request)
    {
        // Check if the address belongs to the authenticated user
        if ($address->user_id !== Auth::id()) {
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => 'Access denied'
                ], 403);
            }
            
            return redirect()->route('addresses.index')
                            ->with('error', 'Access denied');
        }

        $address->delete();

        // If API request
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'Address deleted successfully'
            ]);
        }
        
        // If web request (monolith)
        return redirect()->route('addresses.index')
                        ->with('success', 'Address deleted successfully');
    }
}
