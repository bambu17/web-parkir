<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\ParkingArea;
use App\Models\ParkingRate;
use Illuminate\Support\Facades\Hash;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        User::updateOrCreate(['email' => 'admin@parkir.test'], ['name' => 'Administrator', 'password' => Hash::make('password'), 'role' => 'admin']);
        User::updateOrCreate(['email' => 'petugas@parkir.test'], ['name' => 'Petugas Parkir', 'password' => Hash::make('password'), 'role' => 'petugas']);
        User::updateOrCreate(['email' => 'owner@parkir.test'], ['name' => 'Owner', 'password' => Hash::make('password'), 'role' => 'owner']);
        ParkingArea::firstOrCreate(['code' => 'A1'], ['name' => 'Area Utama', 'capacity' => 100]);
        ParkingRate::firstOrCreate(['vehicle_type' => 'motor'], ['first_hour_rate' => 3000, 'next_hour_rate' => 2000]);
        ParkingRate::firstOrCreate(['vehicle_type' => 'mobil'], ['first_hour_rate' => 5000, 'next_hour_rate' => 3000]);
    }
}
