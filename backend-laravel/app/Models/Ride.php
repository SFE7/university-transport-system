<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

/**
 * Ride Model
 *
 * Represents a carpool ride offered by a driver.
 * A ride can have multiple bookings (one per student).
 * A ride can have multiple ratings (one per student who completed the ride).
 */
class Ride extends Model
{
    use HasFactory;

    // Specify which columns can be mass assigned
    protected $fillable = [
        'driver_id',
        'starting_point',
        'destination',
        'departure_date',
        'departure_time',
        'available_seats',
        'price_per_seat',
        'status',
        'vehicle_description',
    ];

    // Cast columns to specific data types
    protected $casts = [
        'departure_date' => 'date',
        'departure_time' => 'datetime:H:i:s',
        'price_per_seat' => 'decimal:2',
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /**
     * Relationship: A ride belongs to one driver (user)
     */
    public function driver(): BelongsTo
    {
        return $this->belongsTo(User::class, 'driver_id');
    }

    /**
     * Relationship: A ride has many bookings
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class);
    }

    /**
     * Get only confirmed bookings for this ride
     */
    public function confirmedBookings(): HasMany
    {
        return $this->hasMany(Booking::class)->where('status', 'confirmed');
    }

    /**
     * Relationship: A ride has many ratings
     */
    public function ratings(): HasMany
    {
        return $this->hasMany(Rating::class);
    }

    /**
     * Get the number of available seats accounting for confirmed bookings
     */
    public function getAvailableSeatsAttribute(): int
    {
        $bookedSeats = $this->confirmedBookings()->count();
        return $this->attributes['available_seats'] - $bookedSeats;
    }

    /**
     * Get the average rating for this ride
     */
    public function getAverageRatingAttribute(): float
    {
        return $this->ratings()->avg('rating') ?? 0;
    }
}
