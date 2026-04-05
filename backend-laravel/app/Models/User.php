<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Database\Factories\UserFactory;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<UserFactory> */
    use HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Relationship: A user can offer many rides (if they are a driver)
     */
    public function offeredRides(): HasMany
    {
        return $this->hasMany(Ride::class, 'driver_id');
    }

    /**
     * Relationship: A user can book many rides (if they are a student)
     */
    public function bookings(): HasMany
    {
        return $this->hasMany(Booking::class, 'student_id');
    }

    /**
     * Relationship: A user can give many ratings
     */
    public function givenRatings(): HasMany
    {
        return $this->hasMany(Rating::class, 'student_id');
    }

    /**
     * Relationship: A user can receive many ratings (if they are a driver)
     */
    public function receivedRatings(): HasMany
    {
        return $this->hasMany(Rating::class, 'driver_id');
    }

    /**
     * Check if user is a driver
     */
    public function isDriver(): bool
    {
        return $this->role === 'driver';
    }

    /**
     * Check if user is a student
     */
    public function isStudent(): bool
    {
        return $this->role === 'student';
    }

    /**
     * Get average rating for a driver
     */
    public function getAverageRating(): float
    {
        return $this->receivedRatings()->avg('rating') ?? 0;
    }
}
