<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
| Note: Currently this template uses web-based authentication with sessions.
| API routes are kept minimal for future extensibility (mobile app, etc.)
|
*/

Route::get('/user', function (Request $request) {
    return $request->user();
})->middleware('auth:sanctum');

// Health check endpoint
Route::get('/health', function () {
    return response()->json([
        'status' => 'ok',
        'timestamp' => now(),
        'version' => '1.0.0'
    ]);
});

// Authentication API Routes
Route::prefix('auth')->group(function () {
    Route::post('/register', [App\Http\Controllers\AuthController::class, 'register']);
    Route::post('/login', [App\Http\Controllers\AuthController::class, 'login']);
    Route::post('/resend-verification', [App\Http\Controllers\AuthController::class, 'resendVerification']);
    
    Route::middleware('auth:sanctum')->group(function () {
        Route::get('/profile', [App\Http\Controllers\AuthController::class, 'profile']);
        Route::put('/profile', [App\Http\Controllers\AuthController::class, 'updateProfile']);
        Route::post('/change-password', [App\Http\Controllers\AuthController::class, 'changePassword']);
        Route::post('/logout', [App\Http\Controllers\AuthController::class, 'logout']);
        Route::post('/logout-all', [App\Http\Controllers\AuthController::class, 'logoutAll']);
    });
});

// Wilayah.id API proxy routes
Route::prefix('wilayah')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\WilayahController::class, 'getProvinces']);
    Route::get('/regencies/{provinceCode}', [App\Http\Controllers\WilayahController::class, 'getRegencies']);
    Route::get('/districts/{regencyCode}', [App\Http\Controllers\WilayahController::class, 'getDistricts']);
    Route::delete('/cache', [App\Http\Controllers\WilayahController::class, 'clearCache'])->middleware('auth');
});

// Shipment Routes
Route::prefix('shipment')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\ShipmentController::class, 'getProvinces']);
    Route::get('/cities/{province_id}', [App\Http\Controllers\ShipmentController::class, 'getCities']);
    Route::post('/calculate-cost', [App\Http\Controllers\ShipmentController::class, 'calculateShippingCost']);
    Route::post('/compare-costs', [App\Http\Controllers\ShipmentController::class, 'compareShippingCosts']);
    Route::get('/couriers', [App\Http\Controllers\ShipmentController::class, 'getAvailableCouriers']);
});

// Midtrans webhook (no auth required)
Route::post('/midtrans/callback', [App\Http\Controllers\CheckoutController::class, 'midtransCallback']);

// Midtrans management routes (auth required)
Route::middleware('auth:sanctum')->group(function () {
    Route::get('/midtrans/check/{orderId}', [App\Http\Controllers\CheckoutController::class, 'checkPaymentStatus']);
    Route::post('/midtrans/simulate', [App\Http\Controllers\CheckoutController::class, 'simulateWebhook']);
});
