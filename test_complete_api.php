<?php
// Test complete API endpoints
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "🚀 Testing Complete Shipment API...\n";
echo str_repeat("=", 50) . "\n\n";

// Test 1: Provinces
echo "1. Testing Provinces...\n";
$response = file_get_contents('http://localhost:8000/api/shipment/provinces');
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "   ✅ Found " . count($data['data']) . " provinces\n";
} else {
    echo "   ❌ Failed\n";
}

// Test 2: Cities
echo "\n2. Testing Cities (Jakarta - ID: 10)...\n";
$response = file_get_contents('http://localhost:8000/api/shipment/cities?province_id=10');
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "   ✅ Found " . count($data['data']) . " cities\n";
} else {
    echo "   ❌ Failed\n";
}

// Test 3: Calculate Cost
echo "\n3. Testing Calculate Cost (JNE)...\n";
$postData = json_encode([
    'origin' => 139,
    'destination' => 135,
    'weight' => 1000,
    'courier' => 'jne'
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $postData
    ]
]);

$response = file_get_contents('http://localhost:8000/api/shipment/calculate-cost', false, $context);
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "   ✅ Success! Found shipping options\n";
    foreach ($data['data'] as $courier) {
        echo "      📦 " . $courier['courier_name'] . ":\n";
        foreach ($courier['services'] as $service) {
            echo "         • " . $service['service'] . ": Rp " . number_format($service['cost']) . " (" . $service['etd'] . ")\n";
        }
    }
} else {
    echo "   ❌ Failed\n";
    if ($data) {
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
}

// Test 4: Compare Costs
echo "\n4. Testing Compare Costs...\n";
$postData = json_encode([
    'origin' => 139,
    'destination' => 135,
    'weight' => 1000,
    'couriers' => ['jne', 'pos', 'tiki']
]);

$context = stream_context_create([
    'http' => [
        'method' => 'POST',
        'header' => "Content-Type: application/json\r\n",
        'content' => $postData
    ]
]);

$response = file_get_contents('http://localhost:8000/api/shipment/compare-costs', false, $context);
$data = json_decode($response, true);
if ($data && $data['success']) {
    echo "   ✅ Success! Found " . count($data['data']) . " couriers\n";
    
    // Show cheapest options
    $allOptions = [];
    foreach ($data['data'] as $courier) {
        foreach ($courier['services'] as $service) {
            $allOptions[] = [
                'courier' => $courier['courier_name'],
                'service' => $service['service'],
                'cost' => $service['cost'],
                'etd' => $service['etd']
            ];
        }
    }
    
    usort($allOptions, function($a, $b) {
        return $a['cost'] - $b['cost'];
    });
    
    echo "   💰 Cheapest options:\n";
    foreach (array_slice($allOptions, 0, 3) as $option) {
        echo "      🏆 " . $option['courier'] . " " . $option['service'] . ": Rp " . number_format($option['cost']) . " (" . $option['etd'] . ")\n";
    }
} else {
    echo "   ❌ Failed\n";
    if ($data) {
        echo "   Error: " . ($data['message'] ?? 'Unknown error') . "\n";
    }
}

echo "\n" . str_repeat("=", 50) . "\n";
echo "✅ API Testing Complete!\n";
