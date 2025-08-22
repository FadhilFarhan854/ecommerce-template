<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make('Illuminate\Contracts\Console\Kernel')->bootstrap();

// Test with search parameter
echo "Testing search with 'tas':\n";
$request = \Illuminate\Http\Request::create('/products', 'GET', ['search' => 'tas']);
$query = \App\Models\Product::with('category');

if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
    });
    echo "Search condition applied with term: '{$search}'\n";
} else {
    echo "No search condition applied\n";
}

$products = $query->get();
echo "Found " . $products->count() . " products\n\n";

// Test with empty search parameter
echo "Testing search with empty string:\n";
$request = \Illuminate\Http\Request::create('/products', 'GET', ['search' => '']);
$query = \App\Models\Product::with('category');

if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
    });
    echo "Search condition applied with term: '{$search}'\n";
} else {
    echo "No search condition applied\n";
}

$products = $query->get();
echo "Found " . $products->count() . " products\n\n";

// Test with no search parameter
echo "Testing with no search parameter:\n";
$request = \Illuminate\Http\Request::create('/products', 'GET', []);
$query = \App\Models\Product::with('category');

if ($request->filled('search')) {
    $search = $request->search;
    $query->where(function($q) use ($search) {
        $q->where('name', 'like', "%{$search}%")
          ->orWhere('description', 'like', "%{$search}%");
    });
    echo "Search condition applied with term: '{$search}'\n";
} else {
    echo "No search condition applied\n";
}

$products = $query->get();
echo "Found " . $products->count() . " products\n";
