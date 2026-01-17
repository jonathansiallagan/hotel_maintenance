<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Sparepart;
use App\Models\AssetCategory;
use App\Models\SparepartCategory;
use App\Models\MaintenanceSchedule;
use App\Models\User;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    public function run(): void
    {
        // ==========================================
        // 1. DATA LOKASI (LOCATIONS)
        // ==========================================
        $loc_lobby = Location::firstOrCreate(['name' => 'Lobby Utama - Lt 1']);
        $loc_kamar101 = Location::firstOrCreate(['name' => 'Kamar 101 - Deluxe']);
        $loc_kamar102 = Location::firstOrCreate(['name' => 'Kamar 102 - Suite']);
        $loc_server = Location::firstOrCreate(['name' => 'Ruang Server IT']);
        $loc_dapur = Location::firstOrCreate(['name' => 'Kitchen / Pantry']);
        $loc_toilet = Location::firstOrCreate(['name' => 'Toilet Umum Pria']);

        // ==========================================
        // 2. DATA ASET (ASSETS)
        // ==========================================
        $cat_hvac = AssetCategory::where('code', 'CAT-HVAC')->first();
        $cat_elektronik = AssetCategory::where('code', 'CAT-ELC')->first();
        $cat_plumbing = AssetCategory::where('code', 'CAT-PLB')->first();
        $cat_kitchen = AssetCategory::where('code', 'CAT-KIT')->first();

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
        ];

        foreach ($assets as $assetData) {
            $assetData['uuid'] = (string) Str::uuid();

            $newAsset = Asset::create($assetData);

            $this->command->info("Created: {$newAsset->name} | UUID: {$newAsset->uuid}");
        }

        // ==========================================
        // 3. DATA SPAREPART (UNTUK TEKNISI)
        // ==========================================
        $spareparts = [
            [
                'name' => 'Freon R32 (Kaleng)',
                'sku_code' => 'FR-260115-A1B2',
                'sparepart_category_id' => SparepartCategory::where('name', 'HVAC')->first()->id,
                'stock' => 20,
                'unit' => 'kaleng',
                'price_per_unit' => 125000
            ],
            [
                'name' => 'Freon R410 (Tabung Besar)',
                'sku_code' => 'FR-260115-B3C4',
                'sparepart_category_id' => SparepartCategory::where('name', 'HVAC')->first()->id,
                'stock' => 5,
                'unit' => 'tabung',
                'price_per_unit' => 850000
            ],
            [
                'name' => 'Lampu LED Philips 12W Putih',
                'sku_code' => 'LLP-260115-D5E6',
                'sparepart_category_id' => SparepartCategory::where('name', 'Lighting')->first()->id,
                'stock' => 50,
                'unit' => 'pcs',
                'price_per_unit' => 45000
            ],
            [
                'name' => 'Lampu LED Philips 18W Kuning',
                'sku_code' => 'LLP-260115-F7G8',
                'sparepart_category_id' => SparepartCategory::where('name', 'Lighting')->first()->id,
                'stock' => 30,
                'unit' => 'pcs',
                'price_per_unit' => 65000
            ],
            [
                'name' => 'Kabel LAN Belden Cat6',
                'sku_code' => 'KL-260115-H9I0',
                'sparepart_category_id' => SparepartCategory::where('name', 'Elektrikal')->first()->id,
                'stock' => 100,
                'unit' => 'meter',
                'price_per_unit' => 5000
            ],
            [
                'name' => 'Konektor RJ45 AMP',
                'sku_code' => 'KR-260115-J1K2',
                'sparepart_category_id' => SparepartCategory::where('name', 'Elektrikal')->first()->id,
                'stock' => 200,
                'unit' => 'pcs',
                'price_per_unit' => 2500
            ],
            [
                'name' => 'Seal Tape Pipa (Onda)',
                'sku_code' => 'STP-260115-L3M4',
                'sparepart_category_id' => SparepartCategory::where('name', 'Plumbing')->first()->id,
                'stock' => 25,
                'unit' => 'roll',
                'price_per_unit' => 5000
            ],
            [
                'name' => 'Keran Air Stainless 1/2 Inch',
                'sku_code' => 'KAS-260115-N5O6',
                'sparepart_category_id' => SparepartCategory::where('name', 'Plumbing')->first()->id,
                'stock' => 10,
                'unit' => 'pcs',
                'price_per_unit' => 75000
            ],
            [
                'name' => 'MCB Schneider 10A',
                'sku_code' => 'MS-260115-P7Q8',
                'sparepart_category_id' => SparepartCategory::where('name', 'Elektrikal')->first()->id,
                'stock' => 15,
                'unit' => 'pcs',
                'price_per_unit' => 60000
            ],
            [
                'name' => 'Baterai AA Alkaline',
                'sku_code' => 'BAA-260115-R9S0',
                'sparepart_category_id' => SparepartCategory::where('name', 'Elektrikal')->first()->id,
                'stock' => 40,
                'unit' => 'pcs',
                'price_per_unit' => 15000
            ],
        ];

        foreach ($spareparts as $part) {
            Sparepart::create($part);
        }

        // ==========================================
        // 4. DATA JADWAL MAINTENANCE (MAINTENANCE SCHEDULES)
        // ==========================================
        $technician = User::where('role', 'technician')->first();

        $maintenanceSchedules = [
            [
                'asset_id' => Asset::where('name', 'AC Daikin 2PK')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Pembersihan Filter AC',
                'description' => 'Pembersihan filter AC secara berkala untuk menjaga kualitas udara dan efisiensi pendinginan',
                'frequency' => 'monthly',
                'priority' => 'medium',
                'last_performed_at' => now()->subDays(15),
                'next_due_date' => now()->addDays(15),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'AC Daikin 2PK')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Cek Tekanan Refrigerant AC',
                'description' => 'Pengecekan tekanan refrigerant dan kebocoran sistem pendingin',
                'frequency' => 'quarterly',
                'priority' => 'high',
                'last_performed_at' => now()->subMonths(2),
                'next_due_date' => now()->addMonth(),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Smart TV Samsung 50 Inch')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Update Firmware TV',
                'description' => 'Pengecekan dan update firmware TV untuk fitur terbaru dan perbaikan bug',
                'frequency' => 'quarterly',
                'priority' => 'low',
                'last_performed_at' => now()->subMonths(1),
                'next_due_date' => now()->addMonths(2),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Water Heater Ariston')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Pembersihan Elemen Pemanas',
                'description' => 'Pembersihan elemen pemanas dan cek keamanan water heater',
                'frequency' => 'monthly',
                'priority' => 'high',
                'last_performed_at' => now()->subDays(20),
                'next_due_date' => now()->addDays(10),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Router Mikrotik RB4011')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Backup Konfigurasi Router',
                'description' => 'Backup konfigurasi router dan pengecekan performa jaringan',
                'frequency' => 'weekly',
                'priority' => 'medium',
                'last_performed_at' => now()->subDays(3),
                'next_due_date' => now()->addDays(4),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Server Dell PowerEdge')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Update Sistem Operasi Server',
                'description' => 'Pengecekan update sistem operasi dan patch keamanan server',
                'frequency' => 'monthly',
                'priority' => 'high',
                'last_performed_at' => now()->subDays(10),
                'next_due_date' => now()->addDays(20),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Server Dell PowerEdge')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Pembersihan Dust Server',
                'description' => 'Pembersihan debu pada komponen server untuk mencegah overheating',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'last_performed_at' => now()->subMonths(1),
                'next_due_date' => now()->addMonths(2),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Kulkas 2 Pintu Sharp')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Defrost dan Pembersihan Kulkas',
                'description' => 'Defrost freezer dan pembersihan interior kulkas secara menyeluruh',
                'frequency' => 'quarterly',
                'priority' => 'medium',
                'last_performed_at' => now()->subMonths(2),
                'next_due_date' => now()->addMonth(),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Wastafel Toto')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Pembersihan dan Sanitasi Wastafel',
                'description' => 'Pembersihan kerak dan sanitasi wastafel untuk kebersihan tamu',
                'frequency' => 'weekly',
                'priority' => 'medium',
                'last_performed_at' => now()->subDays(2),
                'next_due_date' => now()->addDays(5),
                'is_active' => true
            ],
            [
                'asset_id' => Asset::where('name', 'Wastafel Toto')->first()->id,
                'technician_id' => $technician ? $technician->id : null,
                'title' => 'Cek Kebocoran Pipa Wastafel',
                'description' => 'Inspeksi kebocoran pipa dan pengencang baut wastafel',
                'frequency' => 'monthly',
                'priority' => 'high',
                'last_performed_at' => now()->subDays(25),
                'next_due_date' => now()->addDays(5),
                'is_active' => true
            ]
        ];

        foreach ($maintenanceSchedules as $schedule) {
            MaintenanceSchedule::create($schedule);
        }

        $this->command->info('Dummy maintenance schedules created successfully!');
    }
}
