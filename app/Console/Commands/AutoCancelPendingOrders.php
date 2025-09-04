<?php

namespace App\Console\Commands;

use App\Models\Order;
use Illuminate\Console\Command;

class AutoCancelPendingOrders extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'orders:auto-cancel-pending';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Auto cancel pending orders older than 24 hours';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Starting auto-cancel pending orders...');
        
        try {
            $cutoffTime = now()->subDay(); // 24 hours ago
            
            $ordersToCancel = Order::where('status', 'pending')
                ->where('created_at', '<', $cutoffTime)
                ->get();

            $cancelledCount = 0;
            
            foreach ($ordersToCancel as $order) {
                $this->info("Processing order ID: {$order->id}");
                
                // Kembalikan stok produk
                foreach ($order->items as $item) {
                    $item->product->increment('stock', $item->quantity);
                    $this->info("Restored {$item->quantity} stock for product: {$item->product->name}");
                }
                
                // Update status order
                $order->update([
                    'status' => 'cancelled',
                    'payment_status' => 'failed'
                ]);
                
                $cancelledCount++;
                $this->info("Cancelled order ID: {$order->id}");
            }

            $this->info("Auto-cancelled {$cancelledCount} pending orders older than 24 hours");
            \Log::info("Auto-cancelled {$cancelledCount} pending orders older than 24 hours");
            
            return 0;
        } catch (\Exception $e) {
            $this->error('Error auto-cancelling pending orders: ' . $e->getMessage());
            \Log::error('Error auto-cancelling pending orders: ' . $e->getMessage());
            
            return 1;
        }
    }
}
