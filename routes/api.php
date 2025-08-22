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

/*
|--------------------------------------------------------------------------
| Future API Routes
|--------------------------------------------------------------------------
|
| When you need to add API functionality (mobile app, third-party integration),
| you can uncomment and modify the routes below:
|
| // Authentication routes
| Route::prefix('auth')->group(function () {
|     Route::post('/register', [AuthController::class, 'register']);
|     Route::post('/login', [AuthController::class, 'login']);
|     Route::middleware('auth:sanctum')->group(function () {
|         Route::get('/profile', [AuthController::class, 'profile']);
|         Route::post('/logout', [AuthController::class, 'logout']);
|     });
| });
|
| // Cart API routes  
| Route::middleware('auth:sanctum')->prefix('cart')->group(function () {
|     Route::get('/', [CartController::class, 'apiIndex']);
|     Route::post('/', [CartController::class, 'apiStore']);
|     Route::put('/{cart}', [CartController::class, 'apiUpdate']);
|     Route::delete('/{cart}', [CartController::class, 'apiDestroy']);
|     Route::delete('/', [CartController::class, 'apiClear']);
|     Route::get('/count', [CartController::class, 'apiCount']);
| });
|
*/
