<?php
// Safe migration: only adds image_path column if it doesn't exist
// This preserves all existing data and login credentials

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Checking complaints table...\n";

if (!Schema::hasColumn('complaints', 'image_path')) {
    Schema::table('complaints', function (Blueprint $table) {
        $table->string('image_path')->nullable()->after('description');
    });
    echo "Added 'image_path' column to complaints table.\n";
} else {
    echo "'image_path' column already exists. No changes made.\n";
}

echo "Done! All existing data and user accounts are preserved.\n";
