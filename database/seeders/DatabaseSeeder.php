<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(UserSeeder::class);
        $this->call(VehicleSeeder::class);
        $this->call(TerminalsTableSeeder::class);
        $this->call(RouteSeeder::class);
        $this->call(RouteSeeder::class);
    }
}