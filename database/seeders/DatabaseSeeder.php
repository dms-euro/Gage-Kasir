<?php

namespace Database\Seeders;

use App\Models\AbsensiConfig;
use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::create([
            'username' => 'admin',
            'nama' => 'Admin',
            'level' => 'admin',
            'password' => Hash::make('admin123'),
        ]);

        AbsensiConfig::create([
            'latitude' => -7.5800,
            'longitude' => 110.9300,
            'radius' => 100,
            'jam_masuk' => '08:00:00',
            'jam_pulang' => '17:00:00',
        ]);
    }
}
