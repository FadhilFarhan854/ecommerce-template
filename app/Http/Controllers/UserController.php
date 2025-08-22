<?php

namespace App\Http\Controllers;

use App\Http\Requests\UserRequest;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Database\Eloquent\ModelNotFoundException;

class UserController extends Controller
{
    /**
     * Display a listing of the users.
     */
    public function index(Request $request)
    {
        $query = User::with(['addresses', 'orders']);
        
        // Apply filters if provided
        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }
        
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone', 'like', "%{$search}%");
            });
        }
        
        // Pagination
        $perPage = $request->get('per_page', 15);
        $users = $query->paginate($perPage);
        
        // Jika request dari API (berdasarkan Accept header atau path)
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $users->items(),
                'pagination' => [
                    'current_page' => $users->currentPage(),
                    'last_page' => $users->lastPage(),
                    'per_page' => $users->perPage(),
                    'total' => $users->total()
                ]
            ]);
        }
        
        // Jika request dari web (monolith)
        return view('users.index', compact('users'));
    }

    /**
     * Show the form for creating a new user.
     */
    public function create(Request $request)
    {
        // Hanya untuk web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('users.create');
    }
    
    /**
     * Store a newly created user in storage.
     */
    public function store(UserRequest $request)
    {
        $user = User::create($request->validatedWithHashedPassword());

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'User created successfully',
                'data' => $user->load(['addresses', 'orders'])
            ], 201);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('users.index')
                        ->with('success', 'User created successfully');
    }

    /**
     * Display the specified user.
     */
    public function show(User $user, Request $request)
    {
        $user->load(['addresses', 'orders.orderItems.product', 'carts.product']);
        
        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $user
            ]);
        }
        
        // Jika request dari web (monolith)
        return view('users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified user.
     */
    public function edit(User $user, Request $request)
    {
        // Hanya untuk web requests
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json(['message' => 'Not supported for API'], 404);
        }
        
        return view('users.edit', compact('user'));
    }

    /**
     * Update the specified user in storage.
     */
    public function update(UserRequest $request, User $user)
    {
        $user->update($request->validatedWithHashedPassword());

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'User updated successfully',
                'data' => $user->load(['addresses', 'orders'])
            ]);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('users.index')
                        ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified user from storage.
     */
    public function destroy(User $user, Request $request)
    {
        // Check if user has orders
        if ($user->orders()->count() > 0) {
            $errorMessage = 'Cannot delete user with existing orders';
            
            // Jika request dari API
            if ($request->wantsJson() || $request->is('api/*')) {
                return response()->json([
                    'success' => false,
                    'message' => $errorMessage
                ], 422);
            }
            
            // Jika request dari web (monolith)
            return redirect()->route('users.index')
                            ->with('error', $errorMessage);
        }

        // Delete related data first
        $user->addresses()->delete();
        $user->carts()->delete();
        $user->delete();

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'message' => 'User deleted successfully'
            ]);
        }
        
        // Jika request dari web (monolith)
        return redirect()->route('users.index')
                        ->with('success', 'User deleted successfully');
    }

    /**
     * Get user statistics for admin dashboard.
     */
    public function statistics(Request $request)
    {
        $stats = [
            'total_users' => User::count(),
            'admin_users' => User::where('role', 'admin')->count(),
            'customer_users' => User::where('role', 'customer')->count(),
            'verified_users' => User::whereNotNull('email_verified_at')->count(),
            'recent_users' => User::where('created_at', '>=', now()->subDays(30))->count(),
        ];

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => true,
                'data' => $stats
            ]);
        }

        // Jika request dari web (monolith)
        return view('users.statistics', compact('stats'));
    }

    /**
     * Bulk actions for users.
     */
    public function bulkAction(Request $request)
    {
        $request->validate([
            'action' => 'required|in:delete,activate,deactivate',
            'user_ids' => 'required|array|min:1',
            'user_ids.*' => 'exists:users,id'
        ]);

        $userIds = $request->user_ids;
        $action = $request->action;
        $message = '';
        $success = true;

        try {
            switch ($action) {
                case 'delete':
                    // Check if any users have orders
                    $usersWithOrders = User::whereIn('id', $userIds)
                        ->whereHas('orders')
                        ->count();
                    
                    if ($usersWithOrders > 0) {
                        $success = false;
                        $message = 'Cannot delete users with existing orders';
                    } else {
                        // Delete related data first
                        User::whereIn('id', $userIds)->each(function ($user) {
                            $user->addresses()->delete();
                            $user->carts()->delete();
                            $user->delete();
                        });
                        $message = 'Selected users deleted successfully';
                    }
                    break;
                    
                case 'activate':
                    User::whereIn('id', $userIds)
                        ->update(['email_verified_at' => now()]);
                    $message = 'Selected users activated successfully';
                    break;
                    
                case 'deactivate':
                    User::whereIn('id', $userIds)
                        ->update(['email_verified_at' => null]);
                    $message = 'Selected users deactivated successfully';
                    break;
            }
        } catch (\Exception $e) {
            $success = false;
            $message = 'An error occurred: ' . $e->getMessage();
        }

        // Jika request dari API
        if ($request->wantsJson() || $request->is('api/*')) {
            return response()->json([
                'success' => $success,
                'message' => $message
            ], $success ? 200 : 422);
        }

        // Jika request dari web (monolith)
        return redirect()->route('users.index')
                        ->with($success ? 'success' : 'error', $message);
    }
}
