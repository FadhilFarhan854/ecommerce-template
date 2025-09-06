<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Outcome;
use App\Models\Order;


class FinanceController extends Controller
{
    public function index()
    {
        $outcomes = Outcome::orderBy('created_at', 'desc')->get();
        $totalOutcome = Outcome::sum('amount');
        $totalIncome = Order::where('status', 'finished')->sum('total_price');
        $netProfit = $totalIncome - $totalOutcome;
        
        // Current month and year data
        $monthlyIncome = Order::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->where('status', 'finished')
            ->sum('total_price') ?? 0;
            
        $monthlyOutcome = Outcome::whereYear('created_at', now()->year)
            ->whereMonth('created_at', now()->month)
            ->sum('amount') ?? 0;
            
        $monthlyProfit = $monthlyIncome - $monthlyOutcome;
        
        $yearlyIncome = Order::whereYear('created_at', now()->year)
            ->where('status', 'finished')
            ->sum('total_price') ?? 0;
            
        $yearlyOutcome = Outcome::whereYear('created_at', now()->year)
            ->sum('amount') ?? 0;
            
        $yearlyProfit = $yearlyIncome - $yearlyOutcome;

        // Monthly data for chart (last 12 months)
        $monthlyData = [];
        for ($i = 11; $i >= 0; $i--) {
            $date = now()->subMonths($i);
            $income = Order::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->where('status', 'finished')
                ->sum('total_price') ?? 0;
            $outcome = Outcome::whereYear('created_at', $date->year)
                ->whereMonth('created_at', $date->month)
                ->sum('amount') ?? 0;
            $monthlyData[] = [
                'month' => $date->format('M Y'),
                'income' => $income,
                'outcome' => $outcome,
                'profit' => $income - $outcome
            ];
        }

        return view('finance.index', compact(
            'outcomes', 'totalOutcome', 'totalIncome', 'netProfit', 
            'monthlyIncome', 'monthlyOutcome', 'monthlyProfit',
            'yearlyIncome', 'yearlyOutcome', 'yearlyProfit', 'monthlyData'
        ));
    }

  

    public function store(Request $request)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        Outcome::create($request->all());

        return redirect()->route('finance.index')
                         ->with('success', 'Outcome recorded successfully.');
    }

    public function edit($id)
    {
        $outcome = Outcome::findOrFail($id);
        return view('finance.edit', compact('outcome'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0',
        ]);

        $outcome = Outcome::findOrFail($id);
        $outcome->update($request->all());

        return redirect()->route('finance.index')
                         ->with('success', 'Outcome updated successfully.');
    }

    public function destroy($id)
    {
        $outcome = Outcome::findOrFail($id);
        $outcome->delete();

        return redirect()->route('finance.index')
                         ->with('success', 'Outcome deleted successfully.');
    }
    
}
