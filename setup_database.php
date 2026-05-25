<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;

echo "Setting up database...\n";

// Drop all tables
$tables = ['barangay_admin_requests', 'complaints', 'categories', 'barangays', 'users', 'jobs', 'cache', 'cache_locks', 'password_reset_tokens', 'sessions', 'migrations'];
foreach ($tables as $table) {
    if (Schema::hasTable($table)) {
        Schema::drop($table);
        echo "Dropped: $table\n";
    }
}

// Create barangays table
Schema::create('barangays', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('captain_name')->nullable();
    $table->string('contact_number')->nullable();
    $table->integer('population')->default(0);
    $table->timestamps();
});
echo "Created: barangays\n";

// Create users table
Schema::create('users', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email')->unique();
    $table->timestamp('email_verified_at')->nullable();
    $table->string('password');
    $table->enum('role', ['resident', 'barangay_admin', 'municipal_admin'])->default('resident');
    $table->foreignId('barangay_id')->nullable()->constrained('barangays')->nullOnDelete();
    $table->string('contact_number')->nullable();
    $table->rememberToken();
    $table->timestamps();
});
echo "Created: users\n";

// Create categories table
Schema::create('categories', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->integer('weight')->default(5);
    $table->timestamps();
});
echo "Created: categories\n";

// Create complaints table
Schema::create('complaints', function (Blueprint $table) {
    $table->id();
    $table->string('complaint_id')->unique();
    $table->foreignId('user_id')->constrained('users')->cascadeOnDelete();
    $table->foreignId('barangay_id')->constrained('barangays')->cascadeOnDelete();
    $table->foreignId('category_id')->constrained('categories')->cascadeOnDelete();
    $table->text('description');
    $table->string('image_path')->nullable();
    $table->enum('vulnerability_flag', ['elderly', 'PWD', 'pregnant', 'none'])->default('none');
    $table->float('priority_score')->default(0);
    $table->enum('priority_level', ['low', 'medium', 'high', 'critical'])->default('low');
    $table->enum('status', ['pending', 'acknowledged', 'in_progress', 'resolved', 'closed'])->default('pending');
    $table->text('resolution_notes')->nullable();
    $table->timestamp('resolved_at')->nullable();
    $table->boolean('escalated_to_municipal')->default(false);
    $table->timestamps();
});
echo "Created: complaints\n";

// Create barangay_admin_requests table
Schema::create('barangay_admin_requests', function (Blueprint $table) {
    $table->id();
    $table->string('name');
    $table->string('email');
    $table->string('contact_number');
    $table->foreignId('barangay_id')->constrained('barangays')->cascadeOnDelete();
    $table->enum('status', ['pending', 'approved', 'rejected'])->default('pending');
    $table->timestamps();
});
echo "Created: barangay_admin_requests\n";

// Create cache table
Schema::create('cache', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->mediumText('value');
    $table->integer('expiration');
});
echo "Created: cache\n";

// Create cache_locks table
Schema::create('cache_locks', function (Blueprint $table) {
    $table->string('key')->primary();
    $table->string('owner');
    $table->integer('expiration');
});
echo "Created: cache_locks\n";

// Create jobs table
Schema::create('jobs', function (Blueprint $table) {
    $table->bigIncrements('id');
    $table->string('queue')->index();
    $table->longText('payload');
    $table->unsignedTinyInteger('attempts');
    $table->unsignedInteger('reserved_at')->nullable();
    $table->unsignedInteger('available_at');
    $table->unsignedInteger('created_at');
});
echo "Created: jobs\n";

// Create password_reset_tokens table
Schema::create('password_reset_tokens', function (Blueprint $table) {
    $table->string('email')->primary();
    $table->string('token');
    $table->timestamp('created_at')->nullable();
});
echo "Created: password_reset_tokens\n";

// Create sessions table
Schema::create('sessions', function (Blueprint $table) {
    $table->string('id')->primary();
    $table->foreignId('user_id')->nullable()->index();
    $table->string('ip_address', 45)->nullable();
    $table->text('user_agent')->nullable();
    $table->longText('payload');
    $table->integer('last_activity')->index();
});
echo "Created: sessions\n";

// Create migrations table
Schema::create('migrations', function (Blueprint $table) {
    $table->id();
    $table->string('migration');
    $table->integer('batch');
});
echo "Created: migrations\n";

// Seed data
echo "\nSeeding data...\n";

// Insert barangays
$barangays = [
    ['name' => 'Licod', 'captain_name' => 'Juan Dela Cruz', 'contact_number' => '09123456789', 'population' => 5000],
    ['name' => 'San Isidro', 'captain_name' => 'Maria Santos', 'contact_number' => '09123456780', 'population' => 3500],
    ['name' => 'Poblacion', 'captain_name' => 'Pedro Reyes', 'contact_number' => '09123456781', 'population' => 8000],
    ['name' => 'San Roque', 'captain_name' => 'Ana Garcia', 'contact_number' => '09123456782', 'population' => 4200],
    ['name' => 'San Antonio', 'captain_name' => 'Jose Martinez', 'contact_number' => '09123456783', 'population' => 6100],
];

foreach ($barangays as $barangay) {
    DB::table('barangays')->insert(array_merge($barangay, [
        'created_at' => now(),
        'updated_at' => now(),
    ]));
}
echo "Seeded: " . count($barangays) . " barangays\n";

// Insert categories
$categories = [
    ['name' => 'Road Repair', 'weight' => 8],
    ['name' => 'Garbage Collection', 'weight' => 6],
    ['name' => 'Street Lighting', 'weight' => 7],
    ['name' => 'Flood Control', 'weight' => 9],
    ['name' => 'Public Safety', 'weight' => 10],
];

foreach ($categories as $category) {
    DB::table('categories')->insert(array_merge($category, [
        'created_at' => now(),
        'updated_at' => now(),
    ]));
}
echo "Seeded: " . count($categories) . " categories\n";

// Insert municipal admin
DB::table('users')->insert([
    'name' => 'Municipal Admin',
    'email' => 'admin@mcims.gov',
    'password' => Hash::make('password'),
    'role' => 'municipal_admin',
    'barangay_id' => null,
    'contact_number' => '09123456789',
    'created_at' => now(),
    'updated_at' => now(),
]);
echo "Seeded: municipal admin\n";

// Insert migration records
$migrationFiles = [
    '0001_01_01_000000_create_users_table',
    '0001_01_01_000001_create_cache_table',
    '0001_01_01_000002_create_jobs_table',
    '2024_01_01_000002_create_categories_table',
    '2024_01_01_000003_create_complaints_table',
    '2024_01_02_000000_create_barangays_table',
    '2024_01_03_000000_create_barangay_admin_requests_table',
    '2025_04_27_130843_add_image_path_to_complaints_table',
];

foreach ($migrationFiles as $i => $migration) {
    DB::table('migrations')->insert([
        'migration' => $migration,
        'batch' => 1,
    ]);
}
echo "Seeded: " . count($migrationFiles) . " migration records\n";

echo "\nDatabase setup complete!\n";
