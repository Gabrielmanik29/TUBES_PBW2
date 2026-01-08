<?php

require_once 'vendor/autoload.php';

$app = require_once 'bootstrap/app.php';
$app->make(Illuminate\Contracts\Console\Kernel::class)->bootstrap();

try {
    $connection = config('database.default');
    $dbConfig = config('database.connections.' . $connection);
    echo "Current database connection: $connection\n";
    echo "Database: " . ($dbConfig['database'] ?? 'N/A') . "\n";
    echo "Driver: " . $dbConfig['driver'] . "\n";

    // Try to connect and list tables
    $tables = DB::select("SELECT name FROM sqlite_master WHERE type='table'");
    echo "Tables in database:\n";
    foreach ($tables as $table) {
        echo "- " . $table->name . "\n";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
