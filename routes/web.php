<?php

use App\Http\Controllers\CategoryController;
use App\Http\Controllers\ProductController;
use App\Http\Controllers\AddressController;
use App\Http\Controllers\DashboardController;
use App\Http\Controllers\UserController;
use App\Http\Controllers\CartController;
use App\Http\Controllers\OrderController;
use App\Http\Controllers\OrderItemController;
use App\Http\Controllers\CheckoutController;
use App\Http\Controllers\LandingPageController;
use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Authentication Routes
Route::middleware('guest')->group(function () {
    Route::get('/login', function () {
        return view('auth.login');
    })->name('login');
    
    Route::get('/register', function () {
        return view('auth.register');
    })->name('register');
    
    Route::post('/login', [AuthController::class, 'webLogin'])->name('login.post');
    Route::post('/register', [AuthController::class, 'webRegister'])->name('register.post');
});

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');
    Route::get('/dashboard', function () {
        return view('dashboard');
        
    })->name('dashboard');

    Route::get('/admin-dashboard',[DashboardController::class, 'index'] )->name('admin.dashboard');

    // Profile routes
    Route::get('/profile', [App\Http\Controllers\ProfileController::class, 'show'])->name('profile.show');
    Route::get('/profile/edit', [App\Http\Controllers\ProfileController::class, 'edit'])->name('profile.edit');
    Route::put('/profile', [App\Http\Controllers\ProfileController::class, 'update'])->name('profile.update');
    Route::get('/profile/change-password', [App\Http\Controllers\ProfileController::class, 'editPassword'])->name('profile.change-password');
    Route::put('/profile/password', [App\Http\Controllers\ProfileController::class, 'updatePassword'])->name('profile.update-password');
});

// Public routes
Route::get('/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
Route::get('/product/{product}', [ProductController::class, 'showProduct'])->name('products.show-detail');
Route::get('/test-cart', function () {
    return view('test-cart');
})->name('test.cart');

// Debug route untuk testing
Route::middleware('auth')->get('/debug-cart', function () {
    $user = auth()->user();
    $cartItems = \App\Models\Cart::where('user_id', $user->id)->with('product')->get();
    return response()->json([
        'user' => $user->name,
        'cart_items' => $cartItems,
        'total_items' => $cartItems->sum('quantity')
    ]);
});

// Web routes untuk kategori (monolith approach)
Route::resource('categories', CategoryController::class);

// Web routes untuk produk (monolith approach)
Route::resource('products', ProductController::class);

// Web routes untuk alamat (monolith approach) - requires authentication
Route::middleware('auth')->resource('addresses', AddressController::class);

// Web routes untuk user management (monolith approach) - requires authentication
Route::middleware('auth')->group(function () {
    Route::resource('users', UserController::class);
    Route::get('/users-statistics', [UserController::class, 'statistics'])->name('users.statistics');
    Route::post('/users-bulk-action', [UserController::class, 'bulkAction'])->name('users.bulk-action');
});

// Web routes untuk cart (monolith approach) - requires authentication
Route::middleware('auth')->group(function () {
    Route::get('/cart', [CartController::class, 'index'])->name('cart.index');
    Route::post('/cart', [CartController::class, 'store'])->name('cart.store');
    Route::put('/cart/{cart}', [CartController::class, 'update'])->name('cart.update');
    Route::delete('/cart/{cart}', [CartController::class, 'destroy'])->name('cart.destroy');
    Route::delete('/cart', [CartController::class, 'clear'])->name('cart.clear');
    Route::get('/cart/count', [CartController::class, 'webCount'])->name('cart.count');
});

// Web routes untuk checkout (monolith approach) - requires authentication
Route::middleware('auth')->group(function () {
    Route::get('/checkout', [CheckoutController::class, 'index'])->name('checkout.index');
    Route::post('/checkout/process', [CheckoutController::class, 'processCheckout'])->name('checkout.process');
    Route::get('/checkout/addresses', [CheckoutController::class, 'getUserAddresses'])->name('checkout.addresses');
});

// Midtrans callback (no auth required)
Route::post('/midtrans/callback', [CheckoutController::class, 'midtransCallback'])->name('midtrans.callback');

// Test route untuk verify webhook accessibility
Route::get('/midtrans/test', function () {
    return response()->json([
        'status' => 'Webhook endpoint is accessible',
        'timestamp' => now(),
        'environment' => config('app.env'),
        'url' => config('app.url'),
        'midtrans_mode' => config('midtrans.is_production') ? 'production' : 'sandbox'
    ]);
})->name('midtrans.test');

// Check payment status (optional, for admin or debugging)
Route::get('/midtrans/check/{orderId}', [CheckoutController::class, 'checkPaymentStatus'])
    ->name('midtrans.check')
    ->middleware('auth');

// Simulate webhook for testing (development only)
Route::post('/midtrans/simulate', [CheckoutController::class, 'simulateWebhook'])
    ->name('midtrans.simulate')
    ->middleware('auth');

// Web routes untuk orders (monolith approach) - requires authentication
Route::middleware('auth')->resource('orders', OrderController::class);
Route::middleware('auth')->get('/history', [OrderController::class, 'history'])->name('orders.history');

// Order status management routes
Route::middleware('auth')->group(function () {
    Route::post('/orders/{id}/update-to-sending', [OrderController::class, 'updateToSending'])->name('orders.update-to-sending');
    Route::post('/orders/{id}/finish', [OrderController::class, 'finishOrder'])->name('orders.finish');
    Route::post('/orders/{id}/retry-payment', [OrderController::class, 'retryPayment'])->name('orders.retry-payment');
});

// Auto cancel pending orders (for scheduler)
Route::get('/orders/auto-cancel-pending', [OrderController::class, 'autoCancelPendingOrders'])->name('orders.auto-cancel-pending');

// Web routes untuk order items (monolith approach) - requires authentication
Route::middleware('auth')->resource('order-items', OrderItemController::class);

// Shipment example route
Route::get('/shipment/example', function () {
    return view('shipment.example');
})->name('shipment.example');
