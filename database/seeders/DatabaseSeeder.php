<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;
use App\Models\User;
use App\Models\AssetCategory;
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
            'name' => 'Denchuu',
            'email' => 'admin@hotel.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_ADMIN,
            'department' => 'IT',
        ]);

        $tech = User::create([
            'name' => 'Den',
            'email' => 'tech@hotel.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_TEKNISI,
            'department' => 'engineering',
        ]);

        $staff = User::create([
            'name' => 'Chuu',
            'email' => 'staff@hotel.com',
            'password' => Hash::make('password'),
            'role' => User::ROLE_STAFF,
            'department' => 'front_office',
        ]);

        $this->call(MasterDataSeeder::class);
        $this->call(DummyDataSeeder::class);
    }
}