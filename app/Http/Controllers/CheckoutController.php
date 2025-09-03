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

        // Hitung total harga
        $totalPrice = $cartItems->sum(function ($item) {
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


            // Buat order dengan UUID
            $order = Order::create([
                'user_id' => Auth::id(),
                'status' => 'pending',
                'total_price' => $totalPrice,
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
                'total_price' => $totalPrice,
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
            $serverKey = config('midtrans.server_key');
            $hashed = hash("sha512", $request->order_id . $request->status_code . $request->gross_amount . $serverKey);
            
            if ($hashed === $request->signature_key) {
                // Cari order berdasarkan midtrans_order_id
                $order = Order::where('midtrans_order_id', $request->order_id)->first();
                
                if ($order) {
                    switch ($request->transaction_status) {
                        case 'capture':
                        case 'settlement':
                            $order->update(['payment_status' => 'paid', 'status' => 'processing']);
                            break;
                        case 'pending':
                            $order->update(['payment_status' => 'pending']);
                            break;
                        case 'deny':
                        case 'cancel':
                        case 'expire':
                        case 'failure':
                            $order->update(['payment_status' => 'failed', 'status' => 'cancelled']);
                            break;
                    }
                }
            }
            
            return response()->json(['status' => 'ok']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error'], 500);
        }
    }
}
