<?php

require 'vendor/autoload.php';

use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

echo "Database Tables and Columns:\n\n";

$tables = DB::select('SHOW TABLES');
foreach ($tables as $table) {
    $tableName = array_values((array) $table)[0];
    echo "=== $tableName ===\n";
    
    $columns = DB::select("DESCRIBE $tableName");
    foreach ($columns as $column) {
        echo "  {$column->Field} ({$column->Type})";
        if ($column->Null === 'NO') echo ' NOT NULL';
        if ($column->Key === 'PRI') echo ' PRIMARY KEY';
        if ($column->Key === 'MUL') echo ' INDEX';
        if ($column->Default !== null) echo " DEFAULT {$column->Default}";
        echo "\n";
    }
    echo "\n";
}
