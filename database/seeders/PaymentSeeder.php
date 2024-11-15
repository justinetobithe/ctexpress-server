<?php

namespace Database\Seeders;

use App\Models\Payment;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class PaymentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    { 
        Payment::create([
            'user_id' => 8,
            'booking_id' => 1,
            'payment_method' => 'cash',
            'amount' => 150.00,
            'reference_no' => 'REF12345',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Payment::create([
            'user_id' => 9,
            'booking_id' => 2,
            'payment_method' => 'cash',
            'amount' => 200.00,
            'reference_no' => 'REF67890',
            'created_at' => now(),
            'updated_at' => now(),
        ]);

        Payment::create([
            'user_id' => 10,
            'booking_id' => 3,
            'payment_method' => 'cash',
            'amount' => 250.00,
            'reference_no' => 'REF11223',
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }
}
