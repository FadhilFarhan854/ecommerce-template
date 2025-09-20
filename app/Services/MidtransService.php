<?php

namespace App\Services;

use Midtrans\Config;
use Midtrans\Snap;

class MidtransService
{
	public function __construct()
	{
		Config::$serverKey = config('midtrans.server_key');
		Config::$isProduction = config('midtrans.is_production');
		Config::$clientKey = config('midtrans.client_key');
	}

	public function createSnapToken($order, $generateNewOrderId = false)
	{
		// Load order items untuk detail produk
		$order->load('items.product');
		
		// Generate new order ID jika diminta (untuk continue payment)
		if ($generateNewOrderId || empty($order->midtrans_order_id)) {
			$newOrderId = 'ORDER-' . time() . '-' . \Illuminate\Support\Str::random(8);
			$order->update(['midtrans_order_id' => $newOrderId]);
			\Log::info('Generated new Midtrans order ID', [
				'order_id' => $order->id,
				'old_midtrans_id' => $order->getOriginal('midtrans_order_id'),
				'new_midtrans_id' => $newOrderId
			]);
		}
		
		// Pastikan order memiliki midtrans_order_id
		if (empty($order->midtrans_order_id)) {
			\Log::error('Order missing midtrans_order_id', ['order_id' => $order->id]);
			throw new \Exception('Order missing Midtrans order ID');
		}
		
		// Item details untuk Midtrans
		$itemDetails = [];
		$subtotal = 0;
		
		foreach ($order->items as $item) {
			if (!$item->product) {
				\Log::error('Order item missing product', [
					'order_id' => $order->id,
					'item_id' => $item->id,
					'product_id' => $item->product_id
				]);
				throw new \Exception('Product not found for order item');
			}
			
			$itemDetails[] = [
				'id' => $item->product_id,
				'price' => (int) $item->price, // Konversi ke integer untuk IDR
				'quantity' => $item->quantity,
				'name' => $item->product->name,
			];
			$subtotal += (int) $item->price * $item->quantity;
		}
		
		// Tambahkan ongkir sebagai item terpisah jika ada
		$shippingCost = (int) $order->total_price - $subtotal;
		if ($shippingCost > 0) {
			$itemDetails[] = [
				'id' => 'SHIPPING',
				'price' => $shippingCost,
				'quantity' => 1,
				'name' => 'Ongkos Kirim',
			];
		}

		$params = [
			'transaction_details' => [
				'order_id' => $order->midtrans_order_id, // Gunakan midtrans_order_id yang unik
				'gross_amount' => (int) $order->total_price, // Konversi ke integer untuk IDR
			],
			'customer_details' => [
				'first_name' => auth()->user()->name,
				'email' => auth()->user()->email,
			],
			'item_details' => $itemDetails,
			'enabled_payments' => ['credit_card', 'cimb_clicks', 'bca_klikbca', 'bca_klikpay', 'bri_epay', 'echannel', 'permata_va', 'bca_va', 'bni_va', 'other_va', 'gopay', 'indomaret', 'danamon_online', 'akulaku'],
		];
		
		// Debug logging
		\Log::info('Midtrans payment params:', [
			'order_id' => $order->midtrans_order_id,
			'total_price' => $order->total_price,
			'subtotal' => $subtotal,
			'shipping_cost' => $shippingCost,
			'item_details' => $itemDetails
		]);
		
		try {
			$snapToken = Snap::getSnapToken($params);
			\Log::info('Snap token generated successfully', [
				'order_id' => $order->midtrans_order_id,
				'token_length' => strlen($snapToken)
			]);
			return $snapToken;
		} catch (\Exception $e) {
			\Log::error('Failed to generate snap token', [
				'order_id' => $order->midtrans_order_id,
				'error' => $e->getMessage(),
				'params' => $params
			]);
			throw $e;
		}
	}
}
