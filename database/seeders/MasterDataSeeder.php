<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Location;
use App\Models\Asset;
use App\Models\Sparepart;
use App\Models\AssetCategory;
use App\Models\SparepartCategory;
use Illuminate\Support\Str;

class MasterDataSeeder extends Seeder
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
        // 2. DATA ASSET CATEGORY
        // ==========================================
        $cat_elektronik = AssetCategory::firstOrCreate(
            ['code' => 'CAT-ELC'],
            [
                'name' => 'Elektronik & IT',
                'department_handler' => 'it'
            ]
        );

        $cat_hvac = AssetCategory::firstOrCreate(
            ['code' => 'CAT-HVAC'],
            [
                'name' => 'HVAC (AC & Pendingin)',
                'department_handler' => 'engineering'
            ]
        );

        $cat_plumbing = AssetCategory::firstOrCreate(
            ['code' => 'CAT-PLB'],
            [
                'name' => 'Plumbing (Pipa & Air)',
                'department_handler' => 'engineering'
            ]
        );

        $cat_furniture = AssetCategory::firstOrCreate(
            ['code' => 'CAT-FUR'],
            [
                'name' => 'Furniture & Interior',
                'department_handler' => 'engineering'
            ]
        );

        $cat_kitchen = AssetCategory::firstOrCreate(
            ['code' => 'CAT-KIT'],
            [
                'name' => 'Peralatan Dapur',
                'department_handler' => 'engineering'
            ]
        );

        // ==========================================
        // 3. DATA SPAREPART CATEGORY
        // ==========================================
        $sparepartCategories = [
            'Elektrikal',
            'Mekanik',
            'Plumbing',
            'Umum',
            'HVAC',
            'Cleaning Supplies',
            'Furniture',
            'Kitchen Equipment',
            'Bathroom Fixtures',
            'Security Systems',
            'Lighting',
            'Carpentry',
            'Painting',
        ];

        foreach ($sparepartCategories as $category) {
            SparepartCategory::firstOrCreate(
                ['name' => $category],
                ['code' => Str::upper(Str::slug($category, '_'))]
            );
        }
    }
}
