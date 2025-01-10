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
                'first_name' => 'Admin',
                'last_name' => 'admin',
                'email' => 'admin@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'James',
                'last_name' => 'Brown',
                'email' => 'jamesbrown@example.com',
                'password' => Hash::make('password'),
                'role' => 'operator',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Jazel',
                'last_name' => 'Endriga',
                'email' => 'jazel.endriga@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Cherrie Anne',
                'last_name' => 'Paclibar',
                'email' => 'cherrie.paclibar@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Steve Julian',
                'last_name' => 'Villegas',
                'email' => 'steve.villegas@example.com',
                'password' => Hash::make('password'),
                'role' => 'admin',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Driver',
                'last_name' => 'One',
                'email' => 'driver.one@example.com',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Driver',
                'last_name' => 'Two',
                'email' => 'driver.two@example.com',
                'password' => Hash::make('password'),
                'role' => 'driver',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'One',
                'email' => 'passenger.one@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Two',
                'email' => 'passenger.two@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Three',
                'email' => 'passenger.three@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Four',
                'email' => 'passenger.four@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Five',
                'email' => 'passenger.five@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Six',
                'email' => 'passenger.six@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Seven',
                'email' => 'passenger.seven@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Eight',
                'email' => 'passenger.eight@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Nine',
                'email' => 'passenger.nine@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
            [
                'first_name' => 'Passenger',
                'last_name' => 'Ten',
                'email' => 'passenger.ten@example.com',
                'password' => Hash::make('password'),
                'role' => 'passenger',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
                'status' => 1
            ],
        ]);
    }
}
