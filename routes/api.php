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

// Shipment Routes
Route::prefix('shipment')->group(function () {
    Route::get('/provinces', [App\Http\Controllers\ShipmentController::class, 'getProvinces']);
    Route::get('/cities/{province_id}', [App\Http\Controllers\ShipmentController::class, 'getCities']);
    Route::post('/calculate-cost', [App\Http\Controllers\ShipmentController::class, 'calculateShippingCost']);
    Route::post('/compare-costs', [App\Http\Controllers\ShipmentController::class, 'compareShippingCosts']);
    Route::get('/couriers', [App\Http\Controllers\ShipmentController::class, 'getAvailableCouriers']);
});
