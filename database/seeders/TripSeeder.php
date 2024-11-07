<?php

namespace Database\Seeders;

use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TripSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $currentDate = now()->toDateString();

        Trip::create([
            'driver_id' => 4,
            'vehicle_id' => 1,
            'from_terminal_id' => 1,
            'to_terminal_id' => 2,
            'passenger_capacity' => 20,
            'start_time' => '09:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);

        Trip::create([
            'driver_id' => 5,
            'vehicle_id' => 2,
            'from_terminal_id' => 2,
            'to_terminal_id' => 1,
            'passenger_capacity' => 20,
            'start_time' => '11:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);

        Trip::create([
            'driver_id' => 4,
            'vehicle_id' => 1,
            'from_terminal_id' => 1,
            'to_terminal_id' => 2,
            'passenger_capacity' => 20,
            'start_time' => '13:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);

        Trip::create([
            'driver_id' => 5,
            'vehicle_id' => 2,
            'from_terminal_id' => 2,
            'to_terminal_id' => 1,
            'passenger_capacity' => 20,
            'start_time' => '15:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);

        Trip::create([
            'driver_id' => 4,
            'vehicle_id' => 1,
            'from_terminal_id' => 1,
            'to_terminal_id' => 2,
            'passenger_capacity' => 20,
            'start_time' => '17:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);

        Trip::create([
            'driver_id' => 5,
            'vehicle_id' => 2,
            'from_terminal_id' => 2,
            'to_terminal_id' => 1,
            'passenger_capacity' => 20,
            'start_time' => '19:00:00',
            'trip_date' => $currentDate,
            'fare_amount' => 50.00,
            'status' => 'pending',
        ]);
    }
}
