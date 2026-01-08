<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
