<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

$users = Illuminate\Support\Facades\DB::table('users')->select('id', 'name', 'email', 'role')->get();
echo 'Users in database: ' . count($users) . PHP_EOL;
foreach ($users as $u) {
    echo '  - ' . $u->name . ' (' . $u->email . ') [' . $u->role . ']' . PHP_EOL;
}
