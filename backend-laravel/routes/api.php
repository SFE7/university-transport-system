<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\RideController;
use App\Http\Controllers\BookingController;
use App\Http\Controllers\RatingController;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

/**
 * ============================================================================
 * PUBLIC AUTHENTICATION ROUTES (no auth required)
 * ============================================================================
 */

// For this sprint, you should add your login/register endpoints here
// Example: Route::post('/auth/login', [AuthController::class, 'login']);

/**
 * ============================================================================
 * PROTECTED ROUTES (require authentication)
 * ============================================================================
 */

Route::middleware('auth:sanctum')->group(function () {

    /**
     * ========================================================================
     * RIDE ROUTES
     * ========================================================================
     *
     * Features:
     * - Search for available rides
     * - Post a new ride (driver)
     * - View ride details
     * - Update ride info (driver only)
     * - Cancel a ride (driver only)
     * - View driver's rides
     */

    // Search for rides
    // GET /api/rides/search?destination=Sfax&departure_date=2026-04-10&starting_point=Bizerte
    Route::get('/rides/search', [RideController::class, 'search']);

    // Get all rides posted by the authenticated driver
    // GET /api/rides/my-rides
    Route::get('/rides/my-rides', [RideController::class, 'myRides']);

    // Get a specific ride
    // GET /api/rides/{id}
    Route::get('/rides/{ride}', [RideController::class, 'show']);

    // Create a new ride (driver posts a ride)
    // POST /api/rides
    // Body: { starting_point, destination, departure_date, departure_time, available_seats, price_per_seat, vehicle_description? }
    Route::post('/rides', [RideController::class, 'store']);

    // Update a ride (driver only)
    // PUT /api/rides/{id}
    // Body: { departure_time?, available_seats?, price_per_seat?, vehicle_description? }
    Route::put('/rides/{ride}', [RideController::class, 'update']);

    // Cancel a ride (driver only)
    // DELETE /api/rides/{id}
    Route::delete('/rides/{ride}', [RideController::class, 'destroy']);


    /**
     * ========================================================================
     * BOOKING ROUTES
     * ========================================================================
     *
     * Features:
     * - Student books a ride
     * - View booking details
     * - List bookings (student: their own, driver: for their rides)
     * - Driver accepts/rejects booking
     * - Student cancels booking
     */

    // List all bookings
    // For students: their own bookings
    // For drivers: bookings for their rides
    // GET /api/bookings?status=pending
    Route::get('/bookings', [BookingController::class, 'index']);

    // Get a specific booking
    // GET /api/bookings/{id}
    Route::get('/bookings/{booking}', [BookingController::class, 'show']);

    // Create a new booking (student books a ride)
    // POST /api/bookings
    // Body: { ride_id }
    Route::post('/bookings', [BookingController::class, 'store']);

    // Accept a booking (driver only)
    // PUT /api/bookings/{id}/accept
    Route::put('/bookings/{booking}/accept', [BookingController::class, 'accept']);

    // Reject a booking (driver only)
    // PUT /api/bookings/{id}/reject
    Route::put('/bookings/{booking}/reject', [BookingController::class, 'reject']);

    // Cancel a booking (student only)
    // PUT /api/bookings/{id}/cancel
    Route::put('/bookings/{booking}/cancel', [BookingController::class, 'cancel']);


    /**
     * ========================================================================
     * RATING ROUTES
     * ========================================================================
     *
     * Features:
     * - Student rates a driver after the ride
     * - View rating details
     * - List ratings for a driver
     * - List ratings given by student
     * - Update a rating (student only)
     * - Delete a rating (student only)
     */

    // Get all ratings given by the authenticated student
    // GET /api/ratings/my-ratings
    Route::get('/ratings/my-ratings', [RatingController::class, 'myRatings']);

    // Get all ratings for a specific driver
    // GET /api/drivers/{driver_id}/ratings
    Route::get('/drivers/{driverId}/ratings', [RatingController::class, 'driverRatings']);

    // Get a specific rating
    // GET /api/ratings/{id}
    Route::get('/ratings/{rating}', [RatingController::class, 'show']);

    // Create a new rating (student rates a driver)
    // POST /api/ratings
    // Body: { booking_id, rating (1-5), comment? }
    Route::post('/ratings', [RatingController::class, 'store']);

    // Update a rating (student only)
    // PUT /api/ratings/{id}
    // Body: { rating?, comment? }
    Route::put('/ratings/{rating}', [RatingController::class, 'update']);

    // Delete a rating (student only)
    // DELETE /api/ratings/{id}
    Route::delete('/ratings/{rating}', [RatingController::class, 'destroy']);

});
