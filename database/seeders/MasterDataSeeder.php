<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Sparepart;
use App\Models\Category;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA LOKASI (LOCATIONS)
        // ==========================================
        $loc_lobby = Location::create(['name' => 'Lobby Utama - Lt 1']);
        $loc_kamar101 = Location::create(['name' => 'Kamar 101 - Deluxe']);
        $loc_kamar102 = Location::create(['name' => 'Kamar 102 - Suite']);
        $loc_server = Location::create(['name' => 'Ruang Server IT']);
        $loc_dapur = Location::create(['name' => 'Kitchen / Pantry']);
        $loc_toilet = Location::create(['name' => 'Toilet Umum Pria']);

        // ==========================================
        // 2. DATA KATEGORI (CATEGORIES)
        // ==========================================
        // Kita buat kategori dulu agar aset punya induk
        $cat_elektronik = Category::create([
            'name' => 'Elektronik & IT',
            'code' => 'CAT-ELC',
            'department_handler' => 'it'
        ]);

        $cat_hvac = Category::create([
            'name' => 'HVAC (AC & Pendingin)',
            'code' => 'CAT-HVAC',
            'department_handler' => 'engineering'
        ]);

        $cat_plumbing = Category::create([
            'name' => 'Plumbing (Pipa & Air)',
            'code' => 'CAT-PLB',
            'department_handler' => 'engineering'
        ]);

        $cat_furniture = Category::create([
            'name' => 'Furniture & Interior',
            'code' => 'CAT-FUR',
            'department_handler' => 'engineering'
        ]);

        $cat_kitchen = Category::create([
            'name' => 'Peralatan Dapur',
            'code' => 'CAT-KIT',
            'department_handler' => 'engineering'
        ]);

        // ==========================================
        // 3. DATA ASET (ASSETS)
        // ==========================================
        $assets = [
            [
                'name' => 'AC Daikin 2PK',
                'location_id' => $loc_lobby->id,
                'category_id' => $cat_hvac->id,
                'image_path' => null,
                'serial_number' => 'DKN-2023-8882'
            ],
            [
                'name' => 'Smart TV Samsung 50 Inch',
                'location_id' => $loc_kamar101->id,
                'category_id' => $cat_elektronik->id,
                'image_path' => null,
                'serial_number' => 'SAM-TV-5500'
            ],
            [
                'name' => 'Water Heater Ariston',
                'location_id' => $loc_kamar101->id,
                'category_id' => $cat_plumbing->id,
                'image_path' => null,
                'serial_number' => 'WH-ARI-992'
            ],
            [
                'name' => 'Router Mikrotik RB4011',
                'location_id' => $loc_server->id,
                'category_id' => $cat_elektronik->id,
                'image_path' => null,
                'serial_number' => 'MT-RB-4011'
            ],
            [
                'name' => 'Server Dell PowerEdge',
                'location_id' => $loc_server->id,
                'category_id' => $cat_elektronik->id,
                'image_path' => null,
                'serial_number' => 'DEL-PE-740'
            ],
            [
                'name' => 'Kulkas 2 Pintu Sharp',
                'location_id' => $loc_dapur->id,
                'category_id' => $cat_kitchen->id,
                'image_path' => null,
                'serial_number' => 'SHP-SJ-200'
            ],
            [
                'name' => 'Wastafel Toto',
                'location_id' => $loc_toilet->id,
                'category_id' => $cat_plumbing->id,
                'image_path' => null,
                'serial_number' => 'TOT-WS-11'
            ],

            // --- ASET DUMMY KHUSUS TESTING (UUID SIMPLE) ---
            [
                'name' => 'AC Panasonic 1/2 PK (Testing)',
                'location_id' => $loc_kamar102->id,
                'category_id' => $cat_hvac->id,
                'uuid' =>   1,
                'image_path' => null,
                'serial_number' => 'TEST-AC-001'
            ],
            [
                'name' => 'TV LG 32 Inch (Testing)',
                'location_id' => $loc_lobby->id,
                'category_id' => $cat_elektronik->id,
                'uuid' => 2,
                'image_path' => null,
                'serial_number' => 'TEST-TV-002'
            ],
            [
                'name' => 'Coffee Maker Nescafe (Testing)',
                'location_id' => $loc_dapur->id,
                'category_id' => $cat_kitchen->id,
                'uuid' => 3,
                'image_path' => null,
                'serial_number' => 'TEST-COF-003'
            ],
        ];

        // // code asli
        // foreach ($assets as $assetData) {
        //     $assetData['uuid'] = (string) Str::uuid();

        //     $newAsset = Asset::create($assetData);

        //     $this->command->info("Created: {$newAsset->name} | UUID: {$newAsset->uuid}");
        // }

        // code sementara
        foreach ($assets as $assetData) {

            // LOGIC: Cek dulu, apakah di array ada key 'uuid'?
            // Jika TIDAK ADA, baru kita buatkan UUID acak.
            // Jika SUDAH ADA (seperti aset 1, 2, 3), biarkan saja.
            if (!isset($assetData['uuid'])) {
                $assetData['uuid'] = (string) Str::uuid();
            }

            $newAsset = Asset::create($assetData);

            $this->command->info("Created: {$newAsset->name} | UUID: {$newAsset->uuid}");
        }

        // ==========================================
        // 4. DATA SPAREPART (UNTUK TEKNISI)
        // ==========================================
        $spareparts = [
            [
                'name' => 'Freon R32 (Kaleng)',
                'sku_code' => 'SP-AC-R32',
                'stock' => 20,
                'unit' => 'kaleng',
                'price_per_unit' => 125000
            ],
            [
                'name' => 'Freon R410 (Tabung Besar)',
                'sku_code' => 'SP-AC-R410',
                'stock' => 5,
                'unit' => 'tabung',
                'price_per_unit' => 850000
            ],
            [
                'name' => 'Lampu LED Philips 12W Putih',
                'sku_code' => 'SP-EL-LED12',
                'stock' => 50,
                'unit' => 'pcs',
                'price_per_unit' => 45000
            ],
            [
                'name' => 'Lampu LED Philips 18W Kuning',
                'sku_code' => 'SP-EL-LED18',
                'stock' => 30,
                'unit' => 'pcs',
                'price_per_unit' => 65000
            ],
            [
                'name' => 'Kabel LAN Belden Cat6',
                'sku_code' => 'SP-NET-CAT6',
                'stock' => 100,
                'unit' => 'meter',
                'price_per_unit' => 5000
            ],
            [
                'name' => 'Konektor RJ45 AMP',
                'sku_code' => 'SP-NET-RJ45',
                'stock' => 200,
                'unit' => 'pcs',
                'price_per_unit' => 2500
            ],
            [
                'name' => 'Seal Tape Pipa (Onda)',
                'sku_code' => 'SP-PL-SEAL',
                'stock' => 25,
                'unit' => 'roll',
                'price_per_unit' => 5000
            ],
            [
                'name' => 'Keran Air Stainless 1/2 Inch',
                'sku_code' => 'SP-PL-KRAN',
                'stock' => 10,
                'unit' => 'pcs',
                'price_per_unit' => 75000
            ],
            [
                'name' => 'MCB Schneider 10A',
                'sku_code' => 'SP-EL-MCB10',
                'stock' => 15,
                'unit' => 'pcs',
                'price_per_unit' => 60000
            ],
            [
                'name' => 'Baterai AA Alkaline',
                'sku_code' => 'SP-GEN-BATT',
                'stock' => 40,
                'unit' => 'pcs',
                'price_per_unit' => 15000
            ],
        ];

        foreach ($spareparts as $part) {
            Sparepart::create($part);
        }
    }
}
