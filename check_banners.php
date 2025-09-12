<?php

require_once 'vendor/autoload.php';
require_once 'bootstrap/app.php';

use App\Models\Banner;

echo "=== CHECKING BANNERS ===\n\n";

try {
    $banners = Banner::all();
    
    if ($banners->count() > 0) {
        echo "Found " . $banners->count() . " banners:\n";
        foreach ($banners as $banner) {
            echo "- ID: " . $banner->id . "\n";
            echo "  Image: " . $banner->image . "\n";
            echo "  Status: " . $banner->status . " (" . ($banner->status ? 'Active' : 'Inactive') . ")\n";
            echo "  Created: " . $banner->created_at . "\n\n";
        }
        
        $activeBanners = Banner::where('status', 1)->get();
        echo "Active banners: " . $activeBanners->count() . "\n";
        
    } else {
        echo "No banners found in database.\n";
        echo "Creating sample banner...\n";
        
        $banner = Banner::create([
            'image' => 'banners/sample-banner.jpg',
            'status' => 1
        ]);
        
        echo "Sample banner created with ID: " . $banner->id . "\n";
    }
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}

?>
