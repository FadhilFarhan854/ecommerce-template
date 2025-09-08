<?php

require 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

use App\View\Composers\PageDataComposer;
use Illuminate\View\View;

// Test View Composer
$composer = new PageDataComposer();

// Create a mock view
$view = new class {
    public $data = [];
    
    public function with($key, $value) {
        $this->data[$key] = $value;
        return $this;
    }
};

$composer->compose($view);

echo "PageData from View Composer:\n";
echo "About title: " . ($view->data['pageData']['about']['title'] ?? 'NOT SET') . "\n";
echo "About vision: " . ($view->data['pageData']['about']['vision'] ?? 'NOT SET') . "\n";
echo "About mission: " . ($view->data['pageData']['about']['mission'] ?? 'NOT SET') . "\n";
echo "Site name: " . ($view->data['pageData']['site']['name'] ?? 'NOT SET') . "\n";
