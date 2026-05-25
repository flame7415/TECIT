<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Barangay;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // Create barangays
        $barangays = [
            ['name' => 'Licod', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'San Miguel', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Buntay', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'San Roque', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Solano', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Magay', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Calogcog', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Sta. Cruz', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Mohon', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Camire', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cabuynan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Bislig', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Sto. Nino', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Sacme', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Limbuhan Dako', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Limbuhan Guti', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Lapay', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Tugop', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Pago', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Amanluran', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Pasil', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Binolo', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Arado', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Talolora', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Linao', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cogon', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Maghulod', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Atipolo', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Baras', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Sta. Elena', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Malaguicay', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Guindag-an', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'San Isidro', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cabarasan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Maribi', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Kiling', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Salvador', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Binongto-an', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cahumayhumayan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Catigbian', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Guingawan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Bantagan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Bangon', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Calsadahay', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'San Victor', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cabonga-an', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Ada', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Canbalisara', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Pikas', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Hilagpad', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Cabalagnan', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
            ['name' => 'Balud', 'captain_name' => '', 'contact_number' => '', 'population' => 0],
        ];

        foreach ($barangays as $barangay) {
            Barangay::create($barangay);
        }

        // Create categories with weights
        $categories = [
            ['name' => 'Infrastructure', 'description' => 'Roads, bridges, streetlights, etc.', 'weight' => 8],
            ['name' => 'Sanitation', 'description' => 'Garbage, drainage, sewage issues', 'weight' => 7],
            ['name' => 'Noise Pollution', 'description' => 'Loud music, construction noise, etc.', 'weight' => 4],
            ['name' => 'Public Safety', 'description' => 'Crime, street lighting, safety concerns', 'weight' => 9],
            ['name' => 'Water Supply', 'description' => 'Water outage, quality issues', 'weight' => 8],
            ['name' => 'Health & Sanitation', 'description' => 'Health hazards, disease outbreaks', 'weight' => 9],
            ['name' => 'Traffic & Transport', 'description' => 'Traffic congestion, parking issues', 'weight' => 6],
            ['name' => 'Environmental', 'description' => 'Illegal logging, pollution, wildlife issues', 'weight' => 7],
        ];

        foreach ($categories as $category) {
            Category::create($category);
        }

        // Create Municipal Admin
        User::create([
            'name' => 'Municipal Admin',
            'email' => 'admin@mcims.com',
            'password' => Hash::make('password123'),
            'role' => 'municipal_admin',
            'barangay_id' => null,
            'contact_number' => '09999999999',
        ]);

        // Create sample resident
        User::create([
            'name' => 'Juan Resident',
            'email' => 'juan@email.com',
            'password' => Hash::make('password123'),
            'role' => 'resident',
            'barangay_id' => 1,
            'contact_number' => '09222222222',
        ]);
    }
}

