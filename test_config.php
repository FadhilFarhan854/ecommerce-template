<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Testing config access:\n";
echo "Site name: " . config('landing.site.name') . "\n";
echo "About title: " . config('landing.about.title') . "\n";
echo "About description: " . config('landing.about.description') . "\n";
echo "Vision: " . config('landing.about.vision') . "\n";
echo "Mission: " . config('landing.about.mission') . "\n";
echo "Additional info: " . config('landing.about.additional_info') . "\n";
