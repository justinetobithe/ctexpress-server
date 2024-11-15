<?php

namespace Database\Seeders;

use App\Models\Booking;
use App\Models\Trip;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class BookingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $trips = Trip::all();

        Booking::create([
            'user_id' => 8,
            'trip_id' => $trips[0]->id,
            'booked_at' => now(),
            'status' => 'pending',
            'paid' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Booking::create([
            'user_id' => 9,
            'trip_id' => $trips[1]->id,
            'booked_at' => now(),
            'status' => 'pending',
            'paid' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Booking::create([
            'user_id' => 10,
            'trip_id' => $trips[2]->id,
            'booked_at' => now(),
            'status' => 'pending',
            'paid' => 0,
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
