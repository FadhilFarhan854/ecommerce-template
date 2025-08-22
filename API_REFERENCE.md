# 🔧 API Reference - Cart Endpoints

## Base URL
```
Web: http://127.0.0.1:8000
API: http://127.0.0.1:8000/api
```

## Authentication
- **Web Routes**: Session-based authentication dengan CSRF protection
- **API Routes**: Sanctum token-based authentication

## Web Endpoints

### 🛒 Cart Management

#### Get Cart Page
```http
GET /cart
```
**Response**: HTML page dengan daftar cart items

**Headers Required**:
```
Cookie: laravel_session=xxx
```

---

#### Add Item to Cart
```http
POST /cart
```

**Headers**:
```
Content-Type: multipart/form-data
Accept: application/json
X-Requested-With: XMLHttpRequest
X-CSRF-TOKEN: {csrf_token}
```

**Body** (FormData):
```
_token: {csrf_token}
product_id: 1
quantity: 2
```

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Produk berhasil ditambahkan ke keranjang.",
    "data": {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "quantity": 2,
        "product": {
            "id": 1,
            "name": "iPhone 14 Pro",
            "price": 999.99,
            "stock": 48
        }
    }
}
```

**Response Error (400)**:
```json
{
    "success": false,
    "message": "Stok tidak mencukupi."
}
```

---

#### Update Cart Item
```http
PUT /cart/{cartId}
```

**Headers**:
```
Content-Type: multipart/form-data
Accept: application/json
X-Requested-With: XMLHttpRequest
```

**Body** (FormData):
```
_method: PUT
_token: {csrf_token}
quantity: 3
```

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Keranjang berhasil diperbarui.",
    "data": {
        "id": 1,
        "user_id": 1,
        "product_id": 1,
        "quantity": 3,
        "product": {
            "id": 1,
            "name": "iPhone 14 Pro",
            "price": 999.99
        }
    }
}
```

---

#### Remove Cart Item
```http
DELETE /cart/{cartId}
```

**Headers**:
```
Content-Type: multipart/form-data
Accept: application/json
X-Requested-With: XMLHttpRequest
```

**Body** (FormData):
```
_method: DELETE
_token: {csrf_token}
```

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Item berhasil dihapus dari keranjang."
}
```

---

#### Clear Cart
```http
DELETE /cart
```

**Headers**:
```
Content-Type: multipart/form-data
Accept: application/json
X-Requested-With: XMLHttpRequest
```

**Body** (FormData):
```
_method: DELETE
_token: {csrf_token}
```

**Response Success (200)**:
```json
{
    "success": true,
    "message": "Keranjang berhasil dikosongkan."
}
```

---

#### Get Cart Count
```http
GET /cart/count
```

**Headers**:
```
Accept: application/json
X-Requested-With: XMLHttpRequest
```

**Response Success (200)**:
```json
{
    "success": true,
    "count": 5
}
```

## API Endpoints (Sanctum)

### 🔑 Authentication Required
Semua API endpoints memerlukan Sanctum token:

```http
Authorization: Bearer {token}
```

### 📋 Get Cart Items
```http
GET /api/cart
```

**Response Success (200)**:
```json
{
    "success": true,
    "data": {
        "items": [
            {
                "id": 1,
                "user_id": 1,
                "product_id": 1,
                "quantity": 2,
                "product": {
                    "id": 1,
                    "name": "iPhone 14 Pro",
                    "price": 999.99,
                    "category": {
                        "name": "Electronics"
                    }
                }
            }
        ],
        "total": 1999.98,
        "count": 2
    }
}
```

### ➕ Add Item (API)
```http
POST /api/cart
```

**Headers**:
```
Content-Type: application/json
Accept: application/json
Authorization: Bearer {token}
```

**Body**:
```json
{
    "product_id": 1,
    "quantity": 2
}
```

### 🔄 Update Item (API)
```http
PUT /api/cart/{cartId}
```

**Body**:
```json
{
    "quantity": 3
}
```

### 🗑️ Remove Item (API)
```http
DELETE /api/cart/{cartId}
```

### 🧹 Clear Cart (API)
```http
DELETE /api/cart
```

### 🔢 Get Count (API)
```http
GET /api/cart/count
```

## Error Responses

### 401 Unauthorized
```json
{
    "message": "Unauthenticated."
}
```

### 403 Forbidden
```json
{
    "success": false,
    "message": "Unauthorized action."
}
```

### 404 Not Found
```json
{
    "message": "No query results for model [App\\Models\\Cart] {id}"
}
```

### 422 Validation Error
```json
{
    "message": "The given data was invalid.",
    "errors": {
        "product_id": [
            "The product id field is required."
        ],
        "quantity": [
            "The quantity must be at least 1."
        ]
    }
}
```

### 419 Page Expired (CSRF)
```json
{
    "message": "CSRF token mismatch."
}
```

## Rate Limiting

- **Web Routes**: 60 requests per minute
- **API Routes**: 60 requests per minute per user

## Examples

### cURL Examples

#### Add to Cart
```bash
curl -X POST "http://127.0.0.1:8000/cart" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -H "X-CSRF-TOKEN: your_csrf_token" \
  -F "_token=your_csrf_token" \
  -F "product_id=1" \
  -F "quantity=2"
```

#### Get Cart Count
```bash
curl -X GET "http://127.0.0.1:8000/cart/count" \
  -H "Accept: application/json" \
  -H "X-Requested-With: XMLHttpRequest" \
  -b "laravel_session=your_session_cookie"
```

### JavaScript Examples

#### Add to Cart with Error Handling
```javascript
async function addToCart(productId, quantity) {
    try {
        const formData = new FormData();
        formData.append('_token', document.querySelector('meta[name="csrf-token"]').content);
        formData.append('product_id', productId);
        formData.append('quantity', quantity);

        const response = await fetch('/cart', {
            method: 'POST',
            headers: {
                'Accept': 'application/json',
                'X-Requested-With': 'XMLHttpRequest'
            },
            body: formData
        });

        const data = await response.json();

        if (response.ok) {
            console.log('Success:', data.message);
            // Update UI
        } else {
            console.error('Error:', data.message);
        }
    } catch (error) {
        console.error('Network Error:', error);
    }
}
```

## Status Codes

| Code | Description |
|------|-------------|
| 200  | Success |
| 201  | Created |
| 400  | Bad Request (validation error, insufficient stock) |
| 401  | Unauthorized |
| 403  | Forbidden |
| 404  | Not Found |
| 419  | Page Expired (CSRF) |
| 422  | Unprocessable Entity (validation) |
| 500  | Internal Server Error |

---

**Version**: 1.0  
**Last Updated**: 22 Agustus 2025
