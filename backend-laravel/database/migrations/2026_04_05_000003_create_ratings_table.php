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
         * Ratings table - stores ratings and reviews from passengers about drivers
         *
         * Columns:
         * - id: unique identifier
         * - ride_id: FK to rides table (the ride being reviewed)
         * - student_id: FK to users table (the student leaving the review)
         * - driver_id: FK to users table (the driver being reviewed)
         * - booking_id: FK to bookings table (the booking associated with this rating)
         * - rating: star rating (1-5)
         * - comment: review text from the student
         * - created_at, updated_at: timestamps
         */
        Schema::create('ratings', function (Blueprint $table) {
            $table->id();
            $table->foreignId('ride_id')->constrained('rides')->onDelete('cascade');
            $table->foreignId('student_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('driver_id')->constrained('users')->onDelete('cascade');
            $table->foreignId('booking_id')->constrained('bookings')->onDelete('cascade');

            // Rating details
            $table->integer('rating'); // 1-5 stars
            $table->text('comment')->nullable();

            // Constraint: one student can only rate a driver once per ride
            $table->unique(['ride_id', 'student_id']);

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('ratings');
    }
};
