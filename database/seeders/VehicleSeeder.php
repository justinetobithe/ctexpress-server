<?php

namespace Database\Seeders;

use App\Models\Vehicle;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class VehicleSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Vehicle::create([
            'driver_id' => 6,
            'license_plate' => 'L300-001',
            'brand' => 'Mitsubishi',
            'model' => 'L300',
            'year' => 2022,
            'capacity' => 20,
        ]);

        Vehicle::create([
            'driver_id' => 7,
            'license_plate' => 'L300-002',
            'brand' => 'Mitsubishi',
            'model' => 'L300',
            'year' => 2023,
            'capacity' => 20,
        ]);
    }
}
