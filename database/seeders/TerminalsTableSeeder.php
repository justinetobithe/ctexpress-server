<?php

namespace Database\Seeders;

use App\Models\Terminal;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TerminalsTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run()
    {
        Terminal::create([
            'name' => 'Calinan Transit Terminal Bankerohan Davao City',
            'longitude' => 125.60219281097703,
            'latitude' => 7.0688543495375304,
        ]);

        Terminal::create([
            'name' => 'All Aboard Transport Services Group Calinan Davao City',
            'longitude' => 125.45309623126747,
            'latitude' => 7.187582924638457,
        ]);
    }
}
