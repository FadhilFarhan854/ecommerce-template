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
    $profitToday = Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->sum('total_price') ?? 0;
    $orderedToday = Order::whereDate('created_at', today())
        ->where('status', 'completed')
        ->count() ?? 0;
    
    $totalUsers = User::count() ?? 0;
    $totalProducts = Product::count() ?? 0;
    $lowStockProducts = Product::where('stock', '<=', 5)->count() ?? 0;
    $yearlyProfit = Order::whereYear('created_at', now()->year)
        ->where('status', 'finished')
        ->sum('total_price') ?? 0;
    $monthlyProfit = Order::whereYear('created_at', now()->year)
        ->whereMonth('created_at', now()->month)
        ->where('status', 'finished')
        ->sum('total_price') ?? 0;
    
    
    $sellingChartData = OrderItem::selectRaw('DATE(created_at) as date, SUM(quantity) as total_quantity')
        ->whereDate('created_at', '>=', now()->subDays(30))
        ->groupBy('date')
        ->orderBy('date')
        ->get()
        ->mapWithKeys(function ($item) {
            return [$item->date => $item->total_quantity];
        });
        
    // Monthly sales data (last 12 months)
    $monthlySalesData = [];
    for ($i = 11; $i >= 0; $i--) {
        $date = now()->subMonths($i);
        $monthSales = OrderItem::whereYear('created_at', $date->year)
            ->whereMonth('created_at', $date->month)
            ->sum('quantity') ?? 0;
        $monthlySalesData[$date->format('M Y')] = $monthSales;
    }
    
    // Yearly sales data (last 5 years)
    $yearlySalesData = [];
    for ($i = 4; $i >= 0; $i--) {
        $year = now()->subYears($i)->year;
        $yearSales = OrderItem::whereYear('created_at', $year)
            ->sum('quantity') ?? 0;
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
