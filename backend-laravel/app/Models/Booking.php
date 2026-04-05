<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasOne;

/**
 * Booking Model
 *
 * Represents a student's booking request for a carpool ride.
 * Status progression:
 * - pending: driver hasn't responded yet
 * - confirmed: driver accepted the booking
 * - cancelled: student cancelled or driver refused
 */
class Booking extends Model
{
    use HasFactory;

    protected $fillable = [
        'ride_id',
        'student_id',
        'status',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: A booking belongs to one ride
     */
    public function ride(): BelongsTo
    {
        return $this->belongsTo(Ride::class);
    }

    /**
     * Relationship: A booking belongs to one student (user)
     */
    public function student(): BelongsTo
    {
        return $this->belongsTo(User::class, 'student_id');
    }

    /**
     * Get the driver of the ride
     */
    public function driver(): BelongsTo
    {
        return $this->ride->driver();
    }

    /**
     * Relationship: A booking has one rating (optional)
     * The student can rate the driver after the ride
     */
    public function rating(): HasOne
    {
        return $this->hasOne(Rating::class);
    }

    /**
     * Check if this booking is confirmed
     */
    public function isConfirmed(): bool
    {
        return $this->status === 'confirmed';
    }

    /**
     * Check if this booking is pending
     */
    public function isPending(): bool
    {
        return $this->status === 'pending';
    }

    /**
     * Check if this booking is cancelled
     */
    public function isCancelled(): bool
    {
        return $this->status === 'cancelled';
    }
}
