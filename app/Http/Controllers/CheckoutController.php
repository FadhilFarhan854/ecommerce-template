<?php

namespace App\Http\Controllers;

use App\Models\Address;
use App\Models\Cart;
use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Validator;

class CheckoutController extends Controller
{
    public function index()
    {
        // Ambil cart items untuk user yang sedang login
        $cartItems = Cart::with('product')
            ->where('user_id', Auth::id())
            ->get();

        if ($cartItems->isEmpty()) {
            return redirect()->back()->with('error', 'Keranjang Anda kosong');
        }

        // Hitung total harga (pastikan integer)
        $totalPrice = (int) $cartItems->sum(function ($item) {
            return $item->product->price * $item->quantity;
        });

        // Hitung total berat
        $totalWeight = $cartItems->sum(function ($item) {
            return $item->product->weight * $item->quantity;
        });

        // Ambil alamat user yang sudah terdaftar
        $addresses = Address::where('user_id', Auth::id())->get();

        return view('checkout.index', compact('cartItems', 'totalPrice', 'totalWeight', 'addresses'));
    }

    public function processCheckout(Request $request)
    {
        // Validasi input
        $rules = [
            'address_option' => 'required|in:existing,new',
            'shipping_cost' => 'required|numeric|min:0',
            'shipping_courier' => 'required|string',
            'shipping_service' => 'required|string',
            'grand_total' => 'required|numeric|min:0',
        ];

        // Tambahkan validasi berdasarkan pilihan alamat
        if ($request->address_option === 'existing') {
            $rules['address_id'] = 'required|exists:addresses,id';
        } elseif ($request->address_option === 'new') {
            $rules = array_merge($rules, [
                'nama_depan' => 'required|string|max:255',
                'nama_belakang' => 'required|string|max:255',
                'alamat' => 'required|string',
                'kode_pos' => 'required|string|max:10',
                'kecamatan' => 'required|string|max:255',
                'kelurahan' => 'required|string|max:255',
                'kota' => 'required|string|max:255',
                'provinsi' => 'required|string|max:255',
                'hp' => 'required|string|max:20',
            ]);
        }

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            return response()->json([
                'success' => false,
                'message' => 'Validasi gagal',
                'errors' => $validator->errors()
            ], 422);
        }

        try {
            DB::beginTransaction();

            // Ambil cart items
            $cartItems = Cart::with('product')
                ->where('user_id', Auth::id())
                ->get();

            if ($cartItems->isEmpty()) {
                throw new \Exception('Keranjang kosong');
            }

            // Cek stok produk
            foreach ($cartItems as $item) {
                if ($item->product->stock < $item->quantity) {
                    throw new \Exception("Stok produk {$item->product->name} tidak mencukupi");
                }
            }

            // Handle alamat
            if ($request->address_option === 'new') {
                // Buat alamat baru
                $address = Address::create([
                    'user_id' => Auth::id(),
                    'nama_depan' => $request->nama_depan,
                    'nama_belakang' => $request->nama_belakang,
                    'alamat' => $request->alamat,
                    'kode_pos' => $request->kode_pos,
                    'kecamatan' => $request->kecamatan,
                    'kelurahan' => $request->kelurahan,
                    'kota' => $request->kota,
                    'provinsi' => $request->provinsi,
                    'hp' => $request->hp,
                ]);
                $shippingAddress = $this->formatAddress($address);
            } else {
                // Gunakan alamat yang sudah ada
                $address = Address::findOrFail($request->address_id);
                if ($address->user_id !== Auth::id()) {
                    throw new \Exception('Alamat tidak valid');
                }
                $shippingAddress = $this->formatAddress($address);
            }

            // Hitung total harga dan berat
            $totalPrice = (int) $cartItems->sum(function ($item) {
                return $item->product->price * $item->quantity;
            });

            $totalWeight = $cartItems->sum(function ($item) {
                return $item->product->weight * $item->quantity;
            });

            // Ambil data ongkir dari request
            $shippingCost = (int) $request->shipping_cost;
            $shippingCourier = $request->shipping_courier;
            $shippingService = $request->shipping_service;
            $shippingDescription = $request->shipping_description ?? '';
            $shippingEtd = $request->shipping_etd ?? '';
            
            // Total keseluruhan (subtotal + ongkir)
            $grandTotal = $totalPrice + $shippingCost;
            
            // Validasi grand total dari frontend
            $requestGrandTotal = (int) $request->grand_total;
            
            // Debug logging
            \Log::info('Checkout validation:', [
                'subtotal' => $totalPrice,
                'shipping_cost_raw' => $request->shipping_cost,
                'shipping_cost_int' => $shippingCost,
                'calculated_grand_total' => $grandTotal,
                'request_grand_total_raw' => $request->grand_total,
                'request_grand_total_int' => $requestGrandTotal,
                'match' => $grandTotal === $requestGrandTotal
            ]);
            
            if ($grandTotal !== $requestGrandTotal) {
                throw new \Exception("Total pembayaran tidak sesuai. Backend: {$grandTotal}, Frontend: {$requestGrandTotal}. Mohon refresh halaman dan coba lagi.");
            }


            // Buat order dengan UUID
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_price' => $grandTotal, // Total keseluruhan (subtotal + ongkir)
                'total_weight' => $totalWeight,
                'shipping_address' => $shippingAddress,
                'payment_method' => 'midtrans',
                'payment_status' => 'pending',
            ]);

            // Buat order items dan kurangi stok
            foreach ($cartItems as $item) {
                OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $item->product_id,
                    'quantity' => $item->quantity,
                    'price' => (int) $item->product->price, // Pastikan integer untuk IDR
                ]);
                $item->product->decrement('stock', $item->quantity);
            }

            // Kosongkan cart
            Cart::where('user_id', Auth::id())->delete();

            // Generate Snap Token
            $midtransService = new \App\Services\MidtransService();
            $snapToken = $midtransService->createSnapToken($order);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Checkout berhasil! Silakan lanjutkan pembayaran.',
                'order_id' => $order->id,
                'subtotal' => $totalPrice,
                'shipping_cost' => $shippingCost,
                'total_price' => $grandTotal, // Grand total untuk Midtrans
                'total_weight' => $totalWeight,
                'snap_token' => $snapToken,
            ]);

        } catch (\Exception $e) {
            DB::rollback();
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 400);
        }
    }

    private function formatAddress($address)
    {
        return "{$address->nama_depan} {$address->nama_belakang}\n" .
               "{$address->alamat}\n" .
               "{$address->kelurahan}, {$address->kecamatan}\n" .
               "{$address->kota}, {$address->provinsi} {$address->kode_pos}\n" .
               "HP: {$address->hp}";
    }

    public function getUserAddresses()
    {
        $addresses = Address::where('user_id', Auth::id())->get();
        return response()->json($addresses);
    }

    public function midtransCallback(Request $request)
    {
        try {
            // Log incoming webhook untuk debugging
            \Log::info('Midtrans Webhook Received', [
                'order_id' => $request->order_id,
                'transaction_status' => $request->transaction_status,
                'payment_type' => $request->payment_type,
                'gross_amount' => $request->gross_amount,
                'fraud_status' => $request->fraud_status ?? null,
            ]);

            $serverKey = config('midtrans.server_key');
            $orderId = $request->order_id;
            $statusCode = $request->status_code;
            $grossAmount = $request->gross_amount;
            $signatureKey = $request->signature_key;
            
            // Verify signature untuk keamanan
            $hashed = hash("sha512", $orderId . $statusCode . $grossAmount . $serverKey);
            
            if ($hashed !== $signatureKey) {
                \Log::warning('Invalid Midtrans signature', [
                    'order_id' => $orderId,
                    'expected' => $hashed,
                    'received' => $signatureKey
                ]);
                return response()->json(['status' => 'error', 'message' => 'Invalid signature'], 401);
            }

            // Cari order berdasarkan midtrans_order_id
            $order = Order::where('midtrans_order_id', $orderId)->first();
            
            if (!$order) {
                \Log::warning('Order not found for Midtrans callback', ['order_id' => $orderId]);
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Update status berdasarkan transaction_status
            $transactionStatus = $request->transaction_status;
            $fraudStatus = $request->fraud_status;
            $oldPaymentStatus = $order->payment_status;
            $oldStatus = $order->status;

            switch ($transactionStatus) {
                case 'capture':
                    // Untuk credit card, perlu cek fraud_status
                    if ($fraudStatus == 'accept') {
                        $order->update([
                            'payment_status' => 'paid',
                            'status' => 'processing'
                        ]);
                        \Log::info('Order payment captured and accepted', ['order_id' => $orderId]);
                    } else if ($fraudStatus == 'challenge') {
                        $order->update([
                            'payment_status' => 'pending',
                            'status' => 'pending'
                        ]);
                        \Log::info('Order payment captured but challenged', ['order_id' => $orderId]);
                    } else {
                        $order->update([
                            'payment_status' => 'failed',
                            'status' => 'cancelled'
                        ]);
                        \Log::info('Order payment captured but denied', ['order_id' => $orderId]);
                    }
                    break;

                case 'settlement':
                    // Pembayaran berhasil (untuk non-credit card)
                    $order->update([
                        'payment_status' => 'paid',
                        'status' => 'processing'
                    ]);
                    \Log::info('Order payment settled', ['order_id' => $orderId]);
                    break;

                case 'pending':
                    // Pembayaran pending (menunggu)
                    $order->update([
                        'payment_status' => 'pending'
                    ]);
                    \Log::info('Order payment pending', ['order_id' => $orderId]);
                    break;

                case 'deny':
                case 'cancel':
                case 'expire':
                case 'failure':
                    // Pembayaran gagal atau dibatalkan
                    $order->update([
                        'payment_status' => 'failed',
                        'status' => 'cancelled'
                    ]);
                    
                    // Kembalikan stok produk jika pembayaran gagal
                    foreach ($order->items as $item) {
                        $item->product->increment('stock', $item->quantity);
                    }
                    
                    \Log::info('Order payment failed/cancelled, stock restored', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus
                    ]);
                    break;

                default:
                    \Log::warning('Unknown transaction status', [
                        'order_id' => $orderId,
                        'status' => $transactionStatus
                    ]);
                    break;
            }

            // Log perubahan status
            if ($oldPaymentStatus !== $order->payment_status || $oldStatus !== $order->status) {
                \Log::info('Order status updated', [
                    'order_id' => $orderId,
                    'old_payment_status' => $oldPaymentStatus,
                    'new_payment_status' => $order->payment_status,
                    'old_status' => $oldStatus,
                    'new_status' => $order->status
                ]);
            }
            
            return response()->json(['status' => 'ok']);
            
        } catch (\Exception $e) {
            \Log::error('Midtrans webhook error', [
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
                'request_data' => $request->all()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Cek status pembayaran secara manual (optional)
     */
    public function checkPaymentStatus($orderId)
    {
        try {
            $order = Order::where('midtrans_order_id', $orderId)->first();
            
            if (!$order) {
                return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
            }

            // Get transaction status from Midtrans API
            $serverKey = config('midtrans.server_key');
            $curl = curl_init();

            curl_setopt_array($curl, array(
                CURLOPT_URL => config('midtrans.base_url') . '/v2/' . $orderId . '/status',
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_HTTPHEADER => array(
                    'Accept: application/json',
                    'Content-Type: application/json',
                    'Authorization: Basic ' . base64_encode($serverKey . ':')
                ),
            ));

            $response = curl_exec($curl);
            $httpCode = curl_getinfo($curl, CURLINFO_HTTP_CODE);
            curl_close($curl);

            if ($httpCode === 200) {
                $result = json_decode($response, true);
                
                return response()->json([
                    'status' => 'success',
                    'order' => $order,
                    'midtrans_status' => $result
                ]);
            } else {
                return response()->json(['status' => 'error', 'message' => 'Failed to get status from Midtrans'], 400);
            }

        } catch (\Exception $e) {
            Log::error('Error checking payment status', [
                'order_id' => $orderId,
                'error' => $e->getMessage()
            ]);
            
            return response()->json(['status' => 'error', 'message' => 'Internal server error'], 500);
        }
    }

    /**
     * Simulate webhook untuk testing (development only)
     */
    public function simulateWebhook(Request $request)
    {
        if (config('app.env') !== 'local') {
            return response()->json(['status' => 'error', 'message' => 'Only available in development'], 403);
        }

        $orderId = $request->input('order_id');
        $transactionStatus = $request->input('transaction_status', 'settlement');
        
        $order = Order::where('midtrans_order_id', $orderId)->first();
        
        if (!$order) {
            return response()->json(['status' => 'error', 'message' => 'Order not found'], 404);
        }

        // Simulate webhook request
        $simulatedRequest = new Request([
            'order_id' => $orderId,
            'transaction_status' => $transactionStatus,
            'status_code' => '200',
            'gross_amount' => $order->total_price,
            'payment_type' => 'bank_transfer',
            'fraud_status' => 'accept',
            'signature_key' => hash("sha512", $orderId . '200' . $order->total_price . config('midtrans.server_key'))
        ]);

        // Process webhook
        $result = $this->midtransCallback($simulatedRequest);
        
        return response()->json([
            'status' => 'success',
            'message' => 'Webhook simulated successfully',
            'webhook_result' => $result->getData(),
            'order_status' => [
                'payment_status' => $order->fresh()->payment_status,
                'status' => $order->fresh()->status
            ]
        ]);
    }
}
