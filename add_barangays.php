<?php
require __DIR__ . '/vendor/autoload.php';
$app = require_once __DIR__ . '/bootstrap/app.php';
$kernel = $app->make(Illuminate\Contracts\Console\Kernel::class);
$kernel->bootstrap();

use App\Models\Barangay;

$barangayNames = [
    'Ada',
    'Amanluran',
    'Arado',
    'Atipolo',
    'Balud',
    'Bangon',
    'Bantagan',
    'Baras',
    'Binolo',
    'Binongto-an',
    'Bislig',
    'Buntay (Pob.)',
    'Cabalagnan',
    'Cabarasan Guti',
    'Cabonga-an',
    'Cabuynan',
    'Cahumayhumayan',
    'Calogcog',
    'Calsadahay',
    'Camire',
    'Canbalisara',
    'Canramos (Pob.)',
    'Catigbian',
    'Catmon',
    'Cogon',
    'Guindag-an',
    'Guingawan',
    'Hilagpad',
    'Kiling',
    'Lapay',
    'Licod (Pob.)',
    'Limbuhan Daku',
    'Limbuhan Guti',
    'Linao',
    'Magay',
    'Maghulod',
    'Malaguicay',
    'Maribi',
    'Mohon',
    'Pago',
    'Pasil',
    'Pikas',
    'Sacme',
    'Salvador',
    'San Isidro',
    'San Miguel (Pob.)',
    'San Roque (Pob.)',
    'San Victor',
    'Santa Cruz',
    'Santa Elena',
    'Santo Niño (Pob.)',
    'Solano',
    'Talolora',
    'Tugop'
];

echo "Current barangays in DB:\n";
$existing = Barangay::pluck('name')->toArray();
foreach ($existing as $name) {
    echo "  - $name\n";
}

echo "\nAdding new barangays...\n";
$added = 0;
foreach ($barangayNames as $name) {
    if (!in_array($name, $existing)) {
        Barangay::create(['name' => $name]);
        echo "  + Added: $name\n";
        $added++;
    } else {
        echo "  = Already exists: $name\n";
    }
}

echo "\nDone! Added $added new barangays.\n";
echo "Total barangays in DB: " . Barangay::count() . "\n";

