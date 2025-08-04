<?php

/**
 * Test script to verify Hermes database connection
 * Save as test-hermes-connection.php in your Laravel root directory
 * Run with: php test-hermes-connection.php
 */

require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';

$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\DB;

echo "Testing Hermes Database Connection...\n";
echo "=====================================\n\n";

try {
    // Test connection
    DB::connection('hermes')->getPdo();
    echo "✅ Database connection successful!\n\n";

    // Get connection config
    $config = config('database.connections.hermes');
    echo "Connection Details:\n";
    echo "- Host: " . $config['host'] . "\n";
    echo "- Port: " . $config['port'] . "\n";
    echo "- Database: " . $config['database'] . "\n";
    echo "- Username: " . $config['username'] . "\n\n";

    // Check if tables exist
    $tables = DB::connection('hermes')->select('SHOW TABLES');
    echo "Tables in database:\n";

    if (empty($tables)) {
        echo "⚠️  No tables found. Run migrations with:\n";
        echo "   php artisan migrate --database=hermes --path=apex/hermes/database/migrations\n";
    } else {
        foreach ($tables as $table) {
            $tableName = array_values((array)$table)[0];
            echo "- " . $tableName . "\n";

            // Count records in hermes_api_keys if it exists
            if ($tableName === 'hermes_api_keys') {
                $count = DB::connection('hermes')->table('hermes_api_keys')->count();
                echo "  Records: " . $count . "\n";
            }
        }
    }
} catch (\Exception $e) {
    echo "❌ Database connection failed!\n";
    echo "Error: " . $e->getMessage() . "\n\n";
    echo "Please check:\n";
    echo "1. Database 'apex-hermes' exists in MySQL\n";
    echo "2. Your .env file has correct HERMES_DB_* settings\n";
    echo "3. MySQL service is running\n";
    echo "4. User has proper permissions\n";
}
