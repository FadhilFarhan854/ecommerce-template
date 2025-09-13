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
use App\Http\Controllers\BannerController;
use App\Http\Controllers\FAQController;
use App\Http\Controllers\FinanceController;
use App\Http\Controllers\DiscountController;
use App\Http\Controllers\ManualVerificationController;
use Illuminate\Support\Facades\Route;

Route::get('/', [LandingPageController::class, 'index'])->name('home');

// Debug route for checking pageData
Route::get('/debug-pagedata', function () {
    $pageData = [
        'site' => config('landing.site'),
        'hero' => config('landing.hero'),
        'about' => config('landing.about'),
        'contact' => config('landing.contact'),
        'footer' => [
            'company_info' => [
                'name' => config('landing.site.name'),
                'description' => config('landing.site.description'),
            ],
            'links' => config('landing.footer.links'),
            'copyright' => config('landing.footer.copyright'),
        ],
        'navigation' => config('landing.navigation'),
    ];
    
    return response()->json($pageData);
});

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
    
    // Simple Forgot Password (Manual)
    Route::get('/forgot-password', [AuthController::class, 'showForgotPasswordForm'])->name('password.request');
    Route::post('/forgot-password', [AuthController::class, 'sendResetInstructions'])->name('password.email');
});

// Manual Verification Routes (Public - tidak perlu auth)
Route::get('/verify-email', [ManualVerificationController::class, 'verifyEmail'])->name('manual.verify.email');
Route::get('/verification-success', [ManualVerificationController::class, 'showVerificationSuccess'])->name('verification.success');
Route::post('/resend-verification', [ManualVerificationController::class, 'resendVerification'])->name('manual.resend.verification');

// Email Verification Routes (New integrated system)
Route::get('/email/verify', [AuthController::class, 'verifyEmail'])->name('verify.email');
Route::post('/email/resend', [AuthController::class, 'resendVerification'])->name('verification.send');

Route::middleware('auth')->group(function () {
    Route::post('/logout', [AuthController::class, 'webLogout'])->name('logout');
    
    // Routes yang memerlukan email verification
    Route::middleware(['verified'])->group(function () {
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
});

// Public routes
Route::get('/catalog', [ProductController::class, 'catalog'])->name('products.catalog');
Route::get('/product/{product}', [ProductController::class, 'show'])->name('products.show-detail');
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

// Web routes untuk banner management - requires authentication
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::resource('banners', BannerController::class);
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

// Route untuk review produk
Route::middleware('auth')->post('/reviews', [\App\Http\Controllers\ReviewController::class, 'store'])->name('reviews.store');

// Route untuk admin review management
Route::middleware('auth')->prefix('admin')->name('admin.')->group(function () {
    Route::put('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'update'])->name('reviews.update');
    Route::delete('/reviews/{review}', [\App\Http\Controllers\ReviewController::class, 'destroy'])->name('reviews.destroy');
});

// Web routes untuk order items (monolith approach) - requires authentication
Route::middleware('auth')->resource('order-items', OrderItemController::class);

// Shipment example route
Route::get('/shipment/example', function () {
    return view('shipment.example');
})->name('shipment.example');

Route::resource('faqs', FAQController::class);

// Finance routes - Admin only
Route::middleware(['auth', 'admin'])->group(function () {
    Route::resource('finance', FinanceController::class)->except(['show', 'create']);
});

// Finance view for admin (read-only access)
Route::middleware(['auth'])->group(function () {
    Route::get('/finance', [FinanceController::class, 'index'])->name('finance.index')->middleware('admin');
});

// Discount routes - Admin only
Route::middleware(['auth', 'admin'])->prefix('admin')->name('admin.')->group(function () {
    Route::resource('discounts', DiscountController::class);
});