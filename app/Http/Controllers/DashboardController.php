<?php

namespace App\Http\Controllers;
use App\Models\OrderItem;
use App\Models\Order;
use App\Models\User; 
use App\Models\Product;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
public function index()
{
    $profitToday = Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_price') ?? 0;
    $orderedToday = Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->count() ?? 0;
    
    $totalUsers = User::count() ?? 0;
    $totalProducts = Product::count() ?? 0;
    $lowStockProducts = Product::where('stock', '<=', 5)->count() ?? 0;
    
    $sellingChartData = OrderItem::selectRaw('DATE(created_at) as date, SUM(quantity) as total_quantity')
        ->whereDate('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->date => $item->total_quantity];
        });
        
    // Ensure we have data for the chart, even if empty
    if ($sellingChartData->isEmpty()) {
        $sellingChartData = collect([]);
    }
        
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
        'statusOrderChartData'
    ));
}
}
