# Authentication API Documentation

This document outlines the authentication endpoints available in the ecommerce Laravel application.

## Base URL
```
/api/auth
```

## Endpoints

### 1. Register User
**POST** `/api/auth/register`

Register a new user account.

**Request Body:**
```json
{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "phone": "1234567890",
    "role": "customer"
}
```

**Response (201):**
```json
{
    "success": true,
    "message": "User registered successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "1234567890",
            "role": "customer",
            "created_at": "2025-08-20T...",
            "updated_at": "2025-08-20T..."
        },
        "token": "1|abc123...",
        "token_type": "Bearer"
    }
}
```

### 2. Login User
**POST** `/api/auth/login`

Authenticate a user and receive an access token.

**Request Body:**
```json
{
    "email": "john@example.com",
    "password": "password123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Login successful",
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "1234567890",
            "role": "customer"
        },
        "token": "2|def456...",
        "token_type": "Bearer"
    }
}
```

### 3. Get User Profile
**GET** `/api/auth/profile`

Get the authenticated user's profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "data": {
        "user": {
            "id": 1,
            "name": "John Doe",
            "email": "john@example.com",
            "phone": "1234567890",
            "role": "customer"
        }
    }
}
```

### 4. Update User Profile
**PUT** `/api/auth/profile`

Update the authenticated user's profile information.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "name": "John Smith",
    "email": "johnsmith@example.com",
    "phone": "9876543210"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Profile updated successfully",
    "data": {
        "user": {
            "id": 1,
            "name": "John Smith",
            "email": "johnsmith@example.com",
            "phone": "9876543210",
            "role": "customer"
        }
    }
}
```

### 5. Change Password
**POST** `/api/auth/change-password`

Change the authenticated user's password.

**Headers:**
```
Authorization: Bearer {token}
```

**Request Body:**
```json
{
    "current_password": "oldpassword123",
    "password": "newpassword123",
    "password_confirmation": "newpassword123"
}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Password changed successfully"
}
```

### 6. Logout
**POST** `/api/auth/logout`

Logout the current session (revoke current token).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out successfully"
}
```

### 7. Logout All Devices
**POST** `/api/auth/logout-all`

Logout from all devices (revoke all tokens).

**Headers:**
```
Authorization: Bearer {token}
```

**Response (200):**
```json
{
    "success": true,
    "message": "Logged out from all devices successfully"
}
```

## Error Responses

### Validation Error (422)
```json
{
    "success": false,
    "message": "Validation errors",
    "errors": {
        "email": ["The email field is required."],
        "password": ["The password field is required."]
    }
}
```

### Unauthorized (401)
```json
{
    "success": false,
    "message": "Invalid credentials"
}
```

### Unauthenticated (401)
```json
{
    "message": "Unauthenticated."
}
```

## Usage Example

### Using cURL

1. **Register a new user:**
```bash
curl -X POST http://localhost:8000/api/auth/register \
  -H "Content-Type: application/json" \
  -d '{
    "name": "John Doe",
    "email": "john@example.com",
    "password": "password123",
    "password_confirmation": "password123",
    "role": "customer"
  }'
```

2. **Login:**
```bash
curl -X POST http://localhost:8000/api/auth/login \
  -H "Content-Type: application/json" \
  -d '{
    "email": "john@example.com",
    "password": "password123"
  }'
```

3. **Access protected route:**
```bash
curl -X GET http://localhost:8000/api/auth/profile \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

## Notes

- All endpoints return JSON responses
- Authentication tokens are required for protected routes
- Tokens are generated using Laravel Sanctum
- The `role` field accepts either "admin" or "customer" (defaults to "customer")
- Password must be at least 8 characters long
- Email must be unique across all users
