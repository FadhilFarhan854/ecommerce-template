# Order Flow Implementation Summary

## 🔄 New Order Status Flow

### Order Status:
1. **`unpaid`** - Pesanan dibuat setelah checkout, menunggu pembayaran
2. **`paid`** - Pembayaran berhasil dikonfirmasi via webhook Midtrans  
3. **`sending`** - Admin menandai pesanan sedang dikirim
4. **`finished`** - User menandai pesanan sudah diterima
5. **`cancelled`** - Pesanan dibatalkan

### Payment Status:
1. **`unpaid`** - Belum dibayar
2. **`paid`** - Sudah dibayar 
3. **`failed`** - Pembayaran gagal

## 🎯 Implementation Flow

### 1. User Checkout
- **File**: `CheckoutController.php`
- **Action**: Membuat order dengan status `unpaid` dan payment_status `unpaid`
- **Changes**: Order dibuat langsung tanpa konfirmasi admin

### 2. Payment Status Update via Webhook
- **File**: `CheckoutController.php` - `midtransCallback()`
- **Success**: Status → `paid`, payment_status → `paid`
- **Failed/Cancel**: Status tetap `unpaid`, payment_status → `failed`

### 3. Retry Payment (User)
- **File**: `CheckoutController.php` - `retryPayment()`
- **Route**: `POST /orders/{order}/retry-payment`
- **Condition**: Order status = `unpaid`
- **Action**: Generate token Midtrans baru untuk pembayaran ulang

### 4. Mark as Sending (Admin)
- **File**: `OrderController.php` - `markAsSending()`
- **Route**: `POST /orders/{order}/mark-sending` (middleware: admin)
- **Condition**: Order status = `paid`
- **Action**: Update status → `sending`

### 5. Mark as Finished (User)
- **File**: `OrderController.php` - `markAsFinished()`
- **Route**: `POST /orders/{order}/mark-finished`
- **Condition**: Order status = `sending`
- **Action**: Update status → `finished`

### 6. Product Review Restriction
- **File**: `ReviewController.php`, `Review.php`
- **Condition**: User hanya bisa review produk dari order dengan status `finished`
- **Validation**: Method `Review::canUserReviewProduct()`

## 🎨 Frontend Updates

### Order History Page (`orders/history.blade.php`)
- Status badges dengan warna sesuai
- Tombol "Bayar Ulang" untuk status `unpaid`
- Tombol "Selesaikan" untuk status `sending`
- JavaScript untuk retry payment dengan Midtrans Snap

### Admin Order Management (`orders/index.blade.php`)
- Status labels dalam bahasa Indonesia
- Tombol "Kirim" untuk order dengan status `paid`

### Order Detail Page (`orders/show.blade.php`)
- Status dan payment status dengan label Indonesia
- Action buttons berdasarkan status order
- Retry payment functionality

## 🛠️ Technical Details

### Model Updates
- **Order.php**: Added constants, helper methods, and labels
- **Review.php**: Added validation method for review eligibility

### Route Updates
- Web routes dengan proper middleware (admin untuk mark-sending)
- API routes untuk mobile/future integration

### Middleware
- **AdminMiddleware**: Protects admin-only endpoints

### Factory Updates
- **OrderFactory.php**: Updated to use new status values

## 🔧 Database Schema
No new migrations needed - existing `status` and `payment_status` fields used with new values.

## 📋 Test Scenarios

1. **Complete Happy Path**:
   - User checkout → `unpaid`
   - Payment success → `paid`
   - Admin mark sending → `sending`
   - User finish order → `finished`
   - User can review products

2. **Payment Retry Path**:
   - User checkout → `unpaid`
   - Payment fails → still `unpaid`
   - User retries payment → `paid`
   - Continue normal flow

3. **Admin Workflow**:
   - Admin sees orders with `paid` status
   - Admin can mark as `sending`
   - Admin cannot modify `finished` orders

4. **Review Restriction**:
   - Users cannot review until order is `finished`
   - Prevents fake reviews from unpaid/undelivered orders

## 🚀 Next Steps

1. Test complete flow end-to-end
2. Add notification system for status changes
3. Consider adding tracking number for `sending` status
4. Add order cancellation logic for `unpaid` orders
5. Implement automatic stock restoration for failed payments
