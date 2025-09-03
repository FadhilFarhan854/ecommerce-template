# 🔄 Midtrans Webhook Implementation

## Overview
Webhook Midtrans telah diimplementasikan untuk otomatis mengupdate status pembayaran order ketika terjadi perubahan status di Midtrans.

## Features
- ✅ **Automatic Status Update**: Status order otomatis terupdate berdasarkan notifikasi dari Midtrans
- ✅ **Security Verification**: Signature verification untuk memastikan request dari Midtrans
- ✅ **Stock Management**: Otomatis mengembalikan stok jika pembayaran gagal
- ✅ **Comprehensive Logging**: Logging detail untuk debugging dan monitoring
- ✅ **Error Handling**: Robust error handling untuk berbagai skenario

## Webhook Endpoint
```
POST /midtrans/callback
```

## Status Mapping

### Payment Status
| Midtrans Status | Order Payment Status | Order Status | Action |
|----------------|---------------------|--------------|---------|
| `settlement` | `paid` | `processing` | ✅ Payment success |
| `capture` (approved) | `paid` | `processing` | ✅ Credit card approved |
| `capture` (challenge) | `pending` | `pending` | ⏳ Fraud challenge |
| `capture` (denied) | `failed` | `cancelled` | ❌ Fraud denied |
| `pending` | `pending` | `pending` | ⏳ Awaiting payment |
| `deny` | `failed` | `cancelled` | ❌ Payment denied |
| `cancel` | `failed` | `cancelled` | ❌ Payment cancelled |
| `expire` | `failed` | `cancelled` | ❌ Payment expired |
| `failure` | `failed` | `cancelled` | ❌ Payment failed |

## Configuration

### 1. Environment Variables
Pastikan environment variables Midtrans sudah dikonfigurasi:
```env
MIDTRANS_SERVER_KEY=SB-Mid-server-your-server-key
MIDTRANS_CLIENT_KEY=SB-Mid-client-your-client-key
MIDTRANS_MERCHANT_ID=your-merchant-id
MIDTRANS_IS_PRODUCTION=false
```

### 2. Midtrans Dashboard Configuration
Di Midtrans Dashboard, set notification URL ke:
```
https://yourdomain.com/midtrans/callback
```

### 3. CSRF Protection
Webhook endpoint dikecualikan dari CSRF protection di `bootstrap/app.php`:
```php
$middleware->validateCsrfTokens(except: [
    'midtrans/callback',
]);
```

## Security Features

### Signature Verification
Setiap request webhook diverifikasi dengan signature SHA512:
```php
$hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
```

### Request Validation
- Validasi signature key
- Validasi keberadaan order
- Validasi format data

## Logging

### Success Logs
```log
[INFO] Order payment settled {"order_id":"ORDER-123"}
[INFO] Order status updated {"order_id":"ORDER-123","old_payment_status":"pending","new_payment_status":"paid"}
```

### Error Logs
```log
[WARNING] Invalid Midtrans signature {"order_id":"ORDER-123"}
[ERROR] Midtrans webhook error {"error":"message","trace":"..."}
```

## Manual Status Check

### Endpoint
```
GET /midtrans/check/{orderId}
```

### Response
```json
{
    "status": "success",
    "order": {
        "id": 1,
        "midtrans_order_id": "ORDER-123",
        "payment_status": "paid",
        "status": "processing"
    },
    "midtrans_status": {
        "transaction_status": "settlement",
        "payment_type": "bank_transfer",
        "gross_amount": "100000.00"
    }
}
```

## Testing

### 1. Sandbox Testing
Gunakan Midtrans Simulator untuk test:
- https://simulator.sandbox.midtrans.com

### 2. Webhook Testing
Gunakan tools seperti ngrok untuk expose local server:
```bash
ngrok http 8000
```

### 3. Manual Testing
Test manual status check:
```bash
curl -X GET "https://yourdomain.com/midtrans/check/ORDER-123" \
     -H "Authorization: Bearer your-token"
```

### 4. Simulate Webhook (Development Only)
Untuk testing di development environment:
```bash
curl -X POST "https://yourdomain.com/midtrans/simulate" \
     -H "Content-Type: application/json" \
     -H "Authorization: Bearer your-token" \
     -d '{"order_id":"ORDER-123","transaction_status":"settlement"}'
```

Available transaction statuses for simulation:
- `settlement` - Payment success
- `pending` - Payment pending
- `cancel` - Payment cancelled
- `expire` - Payment expired
- `failure` - Payment failed

## Implementation Details

### Order Status Flow
```
pending → processing (payment success)
pending → cancelled (payment failed)
```

### Stock Management
- Stock dikurangi saat order dibuat
- Stock dikembalikan jika payment gagal/cancelled/expired

### Error Handling
- Invalid signature → 401 Unauthorized
- Order not found → 404 Not Found
- Server error → 500 Internal Server Error

## Best Practices

### 1. Monitoring
- Monitor webhook logs regularly
- Set up alerts for failed webhooks
- Check payment status mismatches

### 2. Error Recovery
- Implement retry mechanism if needed
- Manual reconciliation for failed webhooks
- Regular status sync with Midtrans API

### 3. Security
- Never expose server key in client-side code
- Validate all incoming webhook requests
- Use HTTPS for all webhook endpoints

## Troubleshooting

### Common Issues

1. **Webhook not received**
   - Check Midtrans notification URL configuration
   - Verify server accessibility
   - Check firewall settings

2. **Invalid signature**
   - Verify server key configuration
   - Check request data format
   - Ensure proper encoding

3. **Order not found**
   - Check midtrans_order_id format
   - Verify order creation process
   - Check database records

### Debug Commands
```bash
# Check webhook logs
tail -f storage/logs/laravel.log | grep -i midtrans

# Test webhook endpoint
curl -X POST "https://yourdomain.com/midtrans/callback" \
     -H "Content-Type: application/json" \
     -d '{"order_id":"test","status_code":"200","gross_amount":"100000","signature_key":"test"}'
```

## Related Files
- `app/Http/Controllers/CheckoutController.php` - Webhook implementation
- `config/midtrans.php` - Midtrans configuration
- `routes/web.php` - Webhook routes
- `bootstrap/app.php` - CSRF exclusion
