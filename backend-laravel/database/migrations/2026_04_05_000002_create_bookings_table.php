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
         * Bookings table - stores ride booking requests from students
         *
         * Columns:
         * - id: unique identifier
         * - ride_id: FK to rides table (which ride is being booked)
         * - student_id: FK to users table (student making the booking)
         * - status: pending/confirmed/cancelled
         *   - pending: waiting for driver's approval
         *   - confirmed: driver accepted the booking
         *   - cancelled: student cancelled or driver refused
         * - created_at, updated_at: timestamps
         */
        Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('rides')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');

            // Booking status tracking
            $table->enum('status', ['pending', 'confirmed', 'cancelled'])->default('pending');

            // Unique constraint: one student can only book one seat per ride
            $table->unique(['ride_id', 'student_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('bookings');
    }
};
