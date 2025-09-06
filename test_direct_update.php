<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Banner;

echo "=== TEST DIRECT BANNER UPDATE ===\n\n";

try {
    // Test Banner ID 3 (yang bermasalah di log)
    $banner = Banner::find(3);
    if (!$banner) {
        echo "Banner ID 3 tidak ditemukan!\n";
        exit(1);
    }
    
    echo "BANNER ID 3 TEST:\n";
    echo "- Before: status = {$banner->status}, updated_at = {$banner->updated_at}\n";
    
    // Test 1: Update langsung dengan integer
    $result = $banner->update(['status' => 1]);
    echo "- Update result: " . ($result ? 'SUCCESS' : 'FAILED') . "\n";
    
    $banner->refresh();
    echo "- After: status = {$banner->status}, updated_at = {$banner->updated_at}\n\n";
    
    // Test 2: Force update dengan save()
    echo "TEST FORCE UPDATE:\n";
    $banner->status = 0;
    $saveResult = $banner->save();
    echo "- Save result: " . ($saveResult ? 'SUCCESS' : 'FAILED') . "\n";
    
    $banner->refresh();
    echo "- After save: status = {$banner->status}, updated_at = {$banner->updated_at}\n\n";
    
    // Test 3: Raw query
    echo "TEST RAW QUERY:\n";
    $rawResult = DB::table('banners')->where('id', 3)->update(['status' => 1]);
    echo "- Raw update affected rows: {$rawResult}\n";
    
    $banner->refresh();
    echo "- After raw: status = {$banner->status}, updated_at = {$banner->updated_at}\n\n";
    
    // Test 4: Check fillable
    echo "MODEL INFO:\n";
    echo "- Fillable fields: " . implode(', ', $banner->getFillable()) . "\n";
    echo "- Table name: " . $banner->getTable() . "\n";
    
} catch (Exception $e) {
    echo "ERROR: " . $e->getMessage() . "\n";
    echo "Stack trace:\n" . $e->getTraceAsString() . "\n";
}

?>
