<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\Category;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Sparepart;
use App\Models\Ticket;

class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // 1. Buat Users (Admin, Teknisi, Staff)
        $admin = User::create([
            'name' => 'Chief Engineer',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'), // Password untuk login
            'role' => User::ROLE_ADMIN,
            'department' => 'engineering',
        ]);

        $tech = User::create([
            'name' => 'Budi Teknisi',
            'email' => 'tech@hotel.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEKNISI,
            'department' => 'engineering',
        ]);

        $staff = User::create([
            'name' => 'Siti Resepsionis',
            'email' => 'staff@hotel.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_USER,
            'department' => 'front_office',
        ]);

        // 2. Master Data (Kategori & Lokasi)
        $catHVAC = Category::create(['name' => 'HVAC (AC)', 'code' => 'HVAC', 'department_handler' => 'engineering']);
        $catElec = Category::create(['name' => 'Electronics', 'code' => 'ELEC', 'department_handler' => 'it']);
        $catPlumb = Category::create(['name' => 'Plumbing', 'code' => 'PLM', 'department_handler' => 'engineering']);

        $locLobby = Location::create(['name' => 'Main Lobby', 'floor' => '1', 'building_block' => 'Main Building']);
        $locRoom101 = Location::create(['name' => 'Room 101', 'floor' => '1', 'building_block' => 'Guest Wing']);
        $locKitchen = Location::create(['name' => 'Main Kitchen', 'floor' => '1', 'building_block' => 'Back Office']);

        // 3. Create Assets (Aset Nyata)
        $acLobby = Asset::create([
            'uuid' => Str::uuid(),
            'name' => 'AC Daikin 2PK Standing',
            'category_id' => $catHVAC->id,
            'location_id' => $locLobby->id,
            'serial_number' => 'DKN-2023-001',
            'status' => 'active',
            'purchase_date' => '2023-01-01',
        ]);

        $tvRoom = Asset::create([
            'uuid' => Str::uuid(),
            'name' => 'Samsung Smart TV 43 Inch',
            'category_id' => $catElec->id,
            'location_id' => $locRoom101->id,
            'serial_number' => 'SMG-TV-101',
            'status' => 'active',
        ]);

        // 4. Create Spareparts (Inventory)
        $freon = Sparepart::create([
            'name' => 'Freon R32 (Kaleng)',
            'sku_code' => 'SP-FREON-32',
            'stock' => 10,
            'unit' => 'kaleng',
            'price_per_unit' => 150000
        ]);

        Sparepart::create([
            'name' => 'Kabel HDMI 2m',
            'sku_code' => 'SP-HDMI-2M',
            'stock' => 5,
            'unit' => 'pcs',
            'price_per_unit' => 50000
        ]);

        // 5. Create Dummy Ticket (Contoh Kasus: AC Lobby Rusak)
        Ticket::create([
            'ticket_number' => 'TIK-' . date('Ymd') . '-001',
            'reporter_id' => $staff->id, // Siti lapor
            'technician_id' => $tech->id, // Budi ditugaskan
            'asset_id' => $acLobby->id, // AC Lobby rusak
            'title' => 'AC Lobby Panas',
            'description' => 'Suhu AC tidak dingin, hanya keluar angin biasa. Tamu complain.',
            'photo_evidence_before' => 'path/to/dummy_image.jpg',
            'priority' => 'high',
            'status' => 'in_progress',
            'reported_at' => now()->subHours(2),
            'responded_at' => now()->subHour(),
        ]);
    }
}