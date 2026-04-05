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
        /**
         * Rides table - stores carpool ride offers posted by drivers
         *
         * Columns:
         * - id: unique identifier
         * - driver_id: FK to users table (the user who posted the ride)
         * - starting_point: departure location
         * - destination: arrival location
         * - departure_date: date of the ride (e.g., 2026-04-05)
         * - departure_time: time of departure (e.g., 08:00:00)
         * - available_seats: number of seats available in the vehicle
         * - price_per_seat: cost per seat in currency units
         * - status: active/completed/cancelled
         * - vehicle_description: optional info about the car (color, plate, model)
         * - created_at, updated_at: timestamps
         */
        Schema::create('rides', function (Blueprint $table) {
            $table->id();
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');

            // Ride details
            $table->string('starting_point');
            $table->string('destination');
            $table->date('departure_date');
            $table->time('departure_time');
            $table->integer('available_seats');
            $table->decimal('price_per_seat', 8, 2);

            // Status tracking
            $table->enum('status', ['active', 'completed', 'cancelled'])->default('active');

            // Optional vehicle info
            $table->string('vehicle_description')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('rides');
    }
};
