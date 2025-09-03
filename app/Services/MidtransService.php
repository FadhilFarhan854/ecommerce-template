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

	public function createSnapToken($order)
	{
		// Load order items untuk detail produk
		$order->load('items.product');
		
		// Item details untuk Midtrans
		$itemDetails = [];
		$subtotal = 0;
		
		foreach ($order->items as $item) {
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
		
		return Snap::getSnapToken($params);
	}
}
