# OrderItem Controller Implementation

## Overview
This document describes the implementation of the OrderItem controller that handles both web and API requests in a single monolithic controller approach.

## Features Implemented

### Controller: `App\Http\Controllers\OrderItemController`

#### Dual Interface Approach
The controller uses conditional logic (`$request->wantsJson() || $request->is('api/*')`) to determine whether to return JSON responses (for API) or traditional web responses (redirects/views).

#### CRUD Operations

1. **Index** - List order items
   - Web: Returns paginated view of order items
   - API: Returns JSON with paginated order items
   - Supports filtering by `order_id`
   - Only shows items from user's own orders

2. **Create** - Show create form / get form data
   - Web: Returns create form view
   - API: Returns available products and order data as JSON
   - Validates user access to the specified order

3. **Store** - Create new order item
   - Web: Redirects with success/error messages
   - API: Returns JSON response with created item
   - Validates stock availability
   - Updates product stock automatically
   - Recalculates order total
   - Uses product price if not specified

4. **Show** - Display single order item
   - Web: Returns detail view
   - API: Returns JSON with order item data
   - Includes related order and product data

5. **Edit** - Show edit form / get edit data
   - Web: Returns edit form view
   - API: Returns order item and products as JSON
   - Prevents editing of shipped/delivered/cancelled orders

6. **Update** - Update existing order item
   - Web: Redirects with success/error messages
   - API: Returns JSON response with updated item
   - Handles stock management for product changes
   - Recalculates order total
   - Prevents updates to non-editable orders

7. **Destroy** - Delete order item
   - Web: Redirects with success/error messages
   - API: Returns JSON confirmation
   - Restores product stock
   - Recalculates order total
   - Prevents deletion from non-editable orders

#### Security Features
- Authentication required for all operations
- User can only access their own order items
- Order ownership verification on all operations
- Prevents modifications to shipped/delivered/cancelled orders

#### Business Logic
- Automatic stock management (decrement on create, restore on delete)
- Order total recalculation after any item changes
- Product price defaults when not specified
- Stock validation before creating/updating items

## Routes

### Web Routes
```php
Route::middleware('auth')->resource('order-items', OrderItemController::class);
```

### API Routes
```php
Route::middleware('auth:sanctum')->prefix('order-items')->group(function () {
    Route::get('/', [OrderItemController::class, 'index']);
    Route::post('/', [OrderItemController::class, 'store']);
    Route::get('/create', [OrderItemController::class, 'create']);
    Route::get('/{orderItem}', [OrderItemController::class, 'show']);
    Route::put('/{orderItem}', [OrderItemController::class, 'update']);
    Route::delete('/{orderItem}', [OrderItemController::class, 'destroy']);
    Route::get('/{orderItem}/edit', [OrderItemController::class, 'edit']);
});
```

## Tests

### Test Coverage: `Tests\Feature\OrderItemTest`
- **32 total tests** (28 passed, 4 skipped)
- **148 assertions**
- Complete API test coverage
- Partial web test coverage (view tests skipped as views not implemented)

#### API Test Categories
- Authentication and authorization
- CRUD operations
- Data validation
- Stock management
- Order total calculations
- Access control (user isolation)
- Business rule enforcement (order status checks)

#### Web Test Categories
- Form submissions
- Redirects and sessions
- Validation errors
- Access control

## Model Updates

### OrderItem Model
- Added `HasFactory` trait for testing
- Maintains existing relationships with Order and Product models

## Usage Examples

### API Usage

#### Get order items for a specific order:
```bash
GET /api/order-items?order_id=123
Authorization: Bearer {token}
```

#### Create a new order item:
```bash
POST /api/order-items
Authorization: Bearer {token}
Content-Type: application/json

{
    "order_id": 123,
    "product_id": 456,
    "quantity": 2,
    "price": 99.99
}
```

#### Update an order item:
```bash
PUT /api/order-items/789
Authorization: Bearer {token}
Content-Type: application/json

{
    "product_id": 456,
    "quantity": 3,
    "price": 89.99
}
```

### Web Usage
Standard Laravel resource routes work for web interface (though views are not implemented in this version).

## Benefits of This Approach

1. **Single Controller**: One controller handles both web and API requests
2. **Code Reuse**: Common business logic shared between interfaces
3. **Consistent Behavior**: Both interfaces follow the same validation and business rules
4. **Maintainability**: Changes to business logic only need to be made in one place
5. **Flexibility**: Easy to switch between response types based on request type

## Future Enhancements

1. **Views**: Implement actual Blade templates for web interface
2. **Bulk Operations**: Add bulk create/update/delete capabilities
3. **Advanced Filtering**: Add more filtering options (date ranges, product categories, etc.)
4. **Pagination Options**: Add customizable page sizes and sorting
5. **Order Item Notes**: Add optional notes/comments to order items
