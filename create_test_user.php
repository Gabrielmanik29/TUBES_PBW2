<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\User;

echo "Creating test user...\n";

try {
    // Check if test user already exists
    $existingUser = User::where('email', 'test@example.com')->first();

    if ($existingUser) {
        echo "Test user already exists:\n";
        echo "Email: " . $existingUser->email . "\n";
        echo "Name: " . $existingUser->name . "\n";
        echo "Role: " . $existingUser->role . "\n";
        echo "Email verified: " . ($existingUser->email_verified_at ? 'Yes' : 'No') . "\n";
        echo "Password: password\n";
    } else {
        // Create new test user
        $user = User::create([
            'name' => 'Test User',
            'email' => 'test@example.com',
            'email_verified_at' => now(), // Auto-verify email
            'password' => Hash::make('password'),
            'role' => 'user',
        ]);

        echo "Test user created successfully!\n";
        echo "Email: test@example.com\n";
        echo "Password: password\n";
        echo "Role: user\n";
        echo "Email verified: Yes\n";
    }

    // Also create an admin test user
    $existingAdmin = User::where('email', 'admin@example.com')->first();

    if ($existingAdmin) {
        echo "\nAdmin user already exists:\n";
        echo "Email: " . $existingAdmin->email . "\n";
        echo "Name: " . $existingAdmin->name . "\n";
        echo "Role: " . $existingAdmin->role . "\n";
        echo "Email verified: " . ($existingAdmin->email_verified_at ? 'Yes' : 'No') . "\n";
        echo "Password: password\n";
    } else {
        $admin = User::create([
            'name' => 'Test Admin',
            'email' => 'admin@example.com',
            'email_verified_at' => now(), // Auto-verify email
            'password' => Hash::make('password'),
            'role' => 'admin',
        ]);

        echo "\nAdmin test user created successfully!\n";
        echo "Email: admin@example.com\n";
        echo "Password: password\n";
        echo "Role: admin\n";
        echo "Email verified: Yes\n";
    }

    echo "\nTotal users in database: " . User::count() . "\n";

} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
}
