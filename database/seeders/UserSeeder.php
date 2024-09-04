<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        DB::table('users')->insert([
            [
                'name' => 'Jazel Endriga',
                'email' => 'jazel.endriga@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Cherrie Anne Paclibar',
                'email' => 'cherrie.paclibar@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Steve Julian Villegas',
                'email' => 'steve.villegas@example.com',
                'password' => Hash::make('password'),
                'role' => 'Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Driver One',
                'email' => 'driver.one@example.com',
                'password' => Hash::make('password'),
                'role' => 'Driver',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Driver Two',
                'email' => 'driver.two@example.com',
                'password' => Hash::make('password'),
                'role' => 'Driver',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Passenger One',
                'email' => 'passenger.one@example.com',
                'password' => Hash::make('password'),
                'role' => 'Passenger',
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'name' => 'Passenger Two',
                'email' => 'passenger.two@example.com',
                'password' => Hash::make('password'),
                'role' => 'Passenger',
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    }
}
