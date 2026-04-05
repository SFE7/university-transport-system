<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

/**
 * Rating Model
 *
 * Represents a student's rating and review of a driver after completing a ride.
 * A rating can only be created after the ride is completed.
 */
class Rating extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'student_id',
        'driver_id',
        'booking_id',
        'rating',
        'comment',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: A rating belongs to one ride
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * Relationship: A rating is created by a student (user)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Relationship: A rating is for a driver (user)
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Relationship: A rating belongs to one booking
     */
    public function booking(): BelongsTo
    {
        return $this->belongsTo(Booking::class);
    }

    /**
     * Check if rating is valid (1-5 stars)
     */
    public function isValidRating(): bool
    {
        return $this->rating >= 1 && $this->rating <= 5;
    }
}
