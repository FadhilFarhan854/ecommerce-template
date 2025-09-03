<?php
// Test shipping cost calculation
require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(\Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use Illuminate\Support\Facades\Http;

$apiKey = config('services.rajaongkir.api_key');
$baseUrl = config('services.rajaongkir.base_url');

echo "Testing Shipping Cost Calculation...\n";
echo "Origin: 139 (Jakarta Timur)\n";
echo "Destination: 135 (Jakarta Barat)\n";
echo "Weight: 1000g\n";
echo "Courier: JNE\n\n";

$response = Http::withHeaders([
    'key' => $apiKey,
    'content-type' => 'application/x-www-form-urlencoded'
])->asForm()->post($baseUrl . '/calculate/district/domestic-cost', [
    'origin' => 139,
    'destination' => 135,
    'weight' => 1000,
    'courier' => 'jne'
]);

if ($response->successful()) {
    $data = $response->json();
    echo "✅ Success!\n";
    echo "Response: " . json_encode($data, JSON_PRETTY_PRINT) . "\n";
} else {
    echo "❌ Failed!\n";
    echo "Status: " . $response->status() . "\n";
    echo "Response: " . $response->body() . "\n";
}
