<?php

require_once 'vendor/autoload.php';

// Bootstrap Laravel
require_once 'bootstrap/app.php';

use App\Models\Banner;
use Illuminate\Http\Request;

echo "=== DEBUG BANNER UPDATE ===\n\n";

try {
    // Cek banner yang ada
    $banners = Banner::all();
    echo "Banners di database:\n";
    foreach ($banners as $banner) {
        echo "- ID: {$banner->id}, Image: {$banner->image}, Status: {$banner->status} (" . ($banner->status ? 'true' : 'false') . "), Type: " . gettype($banner->status) . "\n";
    }
    echo "\n";
    
    if ($banners->count() == 0) {
        echo "Tidak ada banner. Membuat banner test...\n";
        $banner = Banner::create([
            'image' => 'test-banner.jpg',
            'status' => true
        ]);
        echo "Banner test dibuat dengan ID: {$banner->id}\n\n";
    } else {
        $banner = $banners->first();
    }
    
    echo "Testing update dengan berbagai cara...\n\n";
    
    // Test 1: Update dengan integer 0
    echo "TEST 1: Update status ke 0 (integer)\n";
    $original_status = $banner->status;
    
    $banner->update(['status' => 0]);
    $banner->refresh();
    
    echo "- Original: " . ($original_status ? 'true' : 'false') . "\n";
    echo "- Update dengan: 0 (integer)\n";
    echo "- Result: " . ($banner->status ? 'true' : 'false') . " (type: " . gettype($banner->status) . ")\n";
    echo "- Database value: " . $banner->getAttributes()['status'] . "\n\n";
    
    // Test 2: Update dengan string '0'
    echo "TEST 2: Update status ke '0' (string)\n";
    $banner->update(['status' => '0']);
    $banner->refresh();
    
    echo "- Update dengan: '0' (string)\n";
    echo "- Result: " . ($banner->status ? 'true' : 'false') . " (type: " . gettype($banner->status) . ")\n";
    echo "- Database value: " . $banner->getAttributes()['status'] . "\n\n";
    
    // Test 3: Update dengan boolean false
    echo "TEST 3: Update status ke false (boolean)\n";
    $banner->update(['status' => false]);
    $banner->refresh();
    
    echo "- Update dengan: false (boolean)\n";
    echo "- Result: " . ($banner->status ? 'true' : 'false') . " (type: " . gettype($banner->status) . ")\n";
    echo "- Database value: " . $banner->getAttributes()['status'] . "\n\n";
    
    // Test 4: Update dengan integer 1
    echo "TEST 4: Update status ke 1 (integer)\n";
    $banner->update(['status' => 1]);
    $banner->refresh();
    
    echo "- Update dengan: 1 (integer)\n";
    echo "- Result: " . ($banner->status ? 'true' : 'false') . " (type: " . gettype($banner->status) . ")\n";
    echo "- Database value: " . $banner->getAttributes()['status'] . "\n\n";
    
    // Test 5: Simulasi exact controller code
    echo "TEST 5: Simulasi exact controller code\n";
    
    // Simulasi request dropdown value "0"
    $request_data = ['status' => '0'];
    $new_status = (int) ($request_data['status'] ?? 0);
    
    echo "- Request data: " . json_encode($request_data) . "\n";
    echo "- Processed: (int) '" . $request_data['status'] . "' = " . $new_status . "\n";
    
    $banner->update(['status' => $new_status]);
    $banner->refresh();
    
    echo "- Final result: " . ($banner->status ? 'true' : 'false') . " (type: " . gettype($banner->status) . ")\n";
    echo "- Database value: " . $banner->getAttributes()['status'] . "\n\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
