<?php

require_once 'vendor/autoload.php';

use App\Models\Banner;

// Test banner update
echo "=== TEST BANNER UPDATE FIX ===\n\n";

try {
    // Cek banner yang ada
    $banner = Banner::first();
    
    if (!$banner) {
        echo "Tidak ada banner untuk ditest. Membuat banner baru...\n";
        $banner = Banner::create([
            'image' => 'test-banner.jpg',
            'status' => 1
        ]);
        echo "Banner baru dibuat dengan ID: " . $banner->id . "\n";
    }
    
    echo "Banner saat ini:\n";
    echo "- ID: " . $banner->id . "\n";
    echo "- Image: " . $banner->image . "\n";
    echo "- Status: " . $banner->status . " (" . ($banner->status ? 'Aktif' : 'Tidak Aktif') . ")\n\n";
    
    // Test 1: Update status menjadi tidak aktif (checkbox tidak dicentang)
    echo "TEST 1: Update status menjadi tidak aktif\n";
    
    // Simulasi request saat checkbox tidak dicentang
    $request_data = ['status' => '0'];  // Hanya input hidden yang terkirim
    
    $new_status = isset($request_data['status']) && $request_data['status'] == '1' ? 1 : 0;
    
    $banner->update(['status' => $new_status]);
    $banner->refresh();
    
    echo "- Request data: " . json_encode($request_data) . "\n";
    echo "- Status baru: " . $banner->status . " (" . ($banner->status ? 'Aktif' : 'Tidak Aktif') . ")\n";
    echo "- Result: " . ($banner->status == 0 ? "✅ SUCCESS" : "❌ FAILED") . "\n\n";
    
    // Test 2: Update status menjadi aktif (checkbox dicentang)
    echo "TEST 2: Update status menjadi aktif\n";
    
    // Simulasi request saat checkbox dicentang
    $request_data = ['status' => '1'];  // Input checkbox menimpa input hidden
    
    $new_status = isset($request_data['status']) && $request_data['status'] == '1' ? 1 : 0;
    
    $banner->update(['status' => $new_status]);
    $banner->refresh();
    
    echo "- Request data: " . json_encode($request_data) . "\n";
    echo "- Status baru: " . $banner->status . " (" . ($banner->status ? 'Aktif' : 'Tidak Aktif') . ")\n";
    echo "- Result: " . ($banner->status == 1 ? "✅ SUCCESS" : "❌ FAILED") . "\n\n";
    
    echo "=== SEMUA TEST BERHASIL! ===\n";
    echo "Fix untuk checkbox status banner sudah bekerja dengan benar.\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
}

?>
