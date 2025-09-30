<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User; 
use App\Models\Product;
use App\Models\FAQ;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
public function index()
{
    // Calculate profit and orders today - based on when orders were finished (updated_at)
    $profitToday = Order::whereDate('updated_at', today())
        ->where('status', 'finished')
        ->where('payment_status', 'paid')
        ->sum('total_price') ?? 0;
    $orderedToday = Order::whereDate('updated_at', today())
        ->where('status', 'finished')
        ->where('payment_status', 'paid')
        ->count() ?? 0;
    
    $totalUsers = User::count() ?? 0;
    $totalProducts = Product::count() ?? 0;
    $lowStockProducts = Product::where('stock', '<=', 5)->count() ?? 0;
    $yearlyProfit = Order::whereYear('updated_at', now()->year)
        ->where('status', 'finished')
        ->where('payment_status', 'paid')
        ->sum('total_price') ?? 0;
    $monthlyProfit = Order::whereYear('updated_at', now()->year)
        ->whereMonth('updated_at', now()->month)
        ->where('status', 'finished')
        ->where('payment_status', 'paid')
        ->sum('total_price') ?? 0;
    
    
    $sellingChartData = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
        ->selectRaw('DATE(orders.updated_at) as date, SUM(order_items.quantity) as total_quantity')
        ->whereDate('orders.updated_at', '>=', now()->subDays(30))
        ->where('orders.status', 'finished')
        ->where('orders.payment_status', 'paid')
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->date => $item->total_quantity];
        });
        
    // Monthly sales data (last 12 months) - based on when orders were finished
    $monthlySalesData = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('orders.updated_at', $date->year)
            ->whereMonth('orders.updated_at', $date->month)
            ->where('orders.status', 'finished')
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.quantity') ?? 0;
        $monthlySalesData[$date->format('M Y')] = $monthSales;
    }
    
    // Yearly sales data (last 5 years) - based on when orders were finished
    $yearlySalesData = [];
    for ($i = 4; $i >= 0; $i--) {
        $year = now()->subYears($i)->year;
        $yearSales = OrderItem::join('orders', 'order_items.order_id', '=', 'orders.id')
            ->whereYear('orders.updated_at', $year)
            ->where('orders.status', 'finished')
            ->where('orders.payment_status', 'paid')
            ->sum('order_items.quantity') ?? 0;
        $yearlySalesData[$year] = $yearSales;
    }
        
    // Ensure we have data for the chart, even if empty
    if ($sellingChartData->isEmpty()) {
        $sellingChartData = collect([]);
    }
    $totalFaqs = FAQ::count() ?? 0;
        
    $statusOrderChartData = Order::selectRaw('status, COUNT(*) as total')
        ->groupBy('status')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->status => $item->total];
        });
        
    // Ensure we have data for the chart, even if empty
    if ($statusOrderChartData->isEmpty()) {
        $statusOrderChartData = collect([]);
    }
    
    return view('admin.dashboard', compact(
        'profitToday', 
        'orderedToday', 
        'totalUsers', 
        'totalProducts', 
        'lowStockProducts',
        'sellingChartData',
        'monthlySalesData',
        'yearlySalesData',
        'statusOrderChartData',
        'totalFaqs'
    ));
}
}
