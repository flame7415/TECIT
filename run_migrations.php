<?php

require __DIR__ . '/vendor/autoload.php';

$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

// Run migrations from the specific path
$path = __DIR__ . '/database/migrations';
$migrator = $app->make('migrator');

// Get all migration files
$files = [];
foreach (new DirectoryIterator($path) as $file) {
    if ($file->isFile() && $file->getExtension() === 'php') {
        $files[$file->getBasename('.php')] = $file->getPathname();
    }
}

ksort($files);

// Run each migration
foreach ($files as $name => $file) {
    echo "Running: $name\n";
    require_once $file;
    $className = get_class_from_file($file);
    $migration = new $className();
    $migration->up();
}

echo "All migrations completed!\n";

function get_class_from_file($path)
{
    $contents = file_get_contents($path);
    if (preg_match('/class\s+(\w+)\s+extends/', $contents, $matches)) {
        return $matches[1];
    }
    return null;
}
