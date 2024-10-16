<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('routes', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('vehicle_id')->constrained('vehicles')->onDelete('cascade');
            $table->foreignId('from_terminal_id')->constrained('terminals')->onDelete('cascade');
            $table->foreignId('to_terminal_id')->constrained('terminals')->onDelete('cascade');
            $table->integer('passenger_capacity');
            $table->time('start_time');
            $table->date('route_date');
            $table->decimal('fare_amount', 8, 2);
            $table->enum('status', ['pending', 'completed', 'canceled', 'in_progress', 'failed'])->default('pending');
            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('routes');
    }
};
