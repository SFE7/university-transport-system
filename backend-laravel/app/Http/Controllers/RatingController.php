<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * RatingController
 *
 * Handles rating and review operations:
 * - Create a rating/review (student, after ride completion)
 * - Get a rating
 * - List all ratings for a driver
 * - List all ratings given by a student
 * - Update a rating (student, own ratings only)
 * - Delete a rating (student, own ratings only)
 */
class RatingController extends Controller
{
    /**
     * Create a new rating/review for a driver (student only)
     *
     * Body parameters:
     * - booking_id (required): the booking from which student got the ride
     * - rating (required): star rating (1-5)
     * - comment (optional): review text
     *
     * Returns: created rating details
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'booking_id' => 'required|exists:bookings,id',
                'rating' => 'required|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            // Get the booking
            $booking = Booking::with('ride')->findOrFail($validated['booking_id']);

            // Check if user is the student who made this booking
            if ($booking->student_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: you can only rate rides that you booked',
                ], 403);
            }

            // Check if booking is confirmed (ride was completed)
            if ($booking->status !== 'confirmed') {
                return response()->json([
                    'success' => false,
                    'message' => 'You can only rate confirmed bookings',
                ], 400);
            }

            // Check if rating already exists for this booking
            $existingRating = Rating::where('booking_id', $validated['booking_id'])->first();
            if ($existingRating) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already rated this ride',
                ], 400);
            }

            // Create the rating
            $rating = Rating::create([
                'booking_id' => $validated['booking_id'],
                'ride_id' => $booking->ride_id,
                'student_id' => $user->id,
                'driver_id' => $booking->ride->driver_id,
                'rating' => $validated['rating'],
                'comment' => $validated['comment'] ?? null,
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Rating created successfully',
                'data' => [
                    'id' => $rating->id,
                    'booking_id' => $rating->booking_id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'created_at' => $rating->created_at,
                ],
            ], 201);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Get a specific rating's details
     *
     * Parameters:
     * - rating: the rating ID
     *
     * Returns: rating details with driver and student info
     */
    public function show(Rating $rating): JsonResponse
    {
        // Load relationships
        $rating->load('student', 'driver', 'ride', 'booking');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $rating->id,
                'booking_id' => $rating->booking_id,
                'ride' => [
                    'id' => $rating->ride->id,
                    'starting_point' => $rating->ride->starting_point,
                    'destination' => $rating->ride->destination,
                    'departure_date' => $rating->ride->departure_date,
                    'departure_time' => $rating->ride->departure_time,
                ],
                'driver' => [
                    'id' => $rating->driver->id,
                    'name' => $rating->driver->name,
                ],
                'student' => [
                    'id' => $rating->student->id,
                    'name' => $rating->student->name,
                ],
                'rating' => $rating->rating,
                'comment' => $rating->comment,
                'created_at' => $rating->created_at,
            ],
        ]);
    }

    /**
     * Get all ratings for a specific driver
     *
     * Parameters:
     * - driver_id: the driver's user ID
     *
     * Returns: list of all ratings for the driver
     */
    public function driverRatings($driverId): JsonResponse
    {
        $ratings = Rating::where('driver_id', $driverId)
            ->with('student', 'ride')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'student_name' => $rating->student->name,
                    'ride' => [
                        'starting_point' => $rating->ride->starting_point,
                        'destination' => $rating->ride->destination,
                        'departure_date' => $rating->ride->departure_date,
                    ],
                    'created_at' => $rating->created_at,
                ];
            });

        // Calculate statistics
        $averageRating = $ratings->avg('rating');
        $totalRatings = $ratings->count();

        return response()->json([
            'success' => true,
            'data' => [
                'ratings' => $ratings,
                'statistics' => [
                    'average' => round($averageRating, 1),
                    'total' => $totalRatings,
                ],
            ],
        ]);
    }

    /**
     * Get all ratings given by the authenticated student
     *
     * Returns: list of all ratings by the student
     */
    public function myRatings(): JsonResponse
    {
        $user = auth()->user();

        $ratings = Rating::where('student_id', $user->id)
            ->with('driver', 'ride')
            ->orderBy('created_at', 'desc')
            ->get()
            ->map(function ($rating) {
                return [
                    'id' => $rating->id,
                    'ride_id' => $rating->ride_id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'driver' => [
                        'id' => $rating->driver->id,
                        'name' => $rating->driver->name,
                    ],
                    'ride' => [
                        'starting_point' => $rating->ride->starting_point,
                        'destination' => $rating->ride->destination,
                        'departure_date' => $rating->ride->departure_date,
                    ],
                    'created_at' => $rating->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $ratings,
            'count' => $ratings->count(),
        ]);
    }

    /**
     * Update a rating (student only, own ratings)
     *
     * Parameters:
     * - rating: the rating ID
     *
     * Body parameters:
     * - rating (optional): new star rating (1-5)
     * - comment (optional): updated review text
     *
     * Returns: updated rating details
     */
    public function update(Request $request, Rating $rating): JsonResponse
    {
        try {
            $user = auth()->user();

            // Check if user is the student who made this rating
            if ($rating->student_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: you can only update your own ratings',
                ], 403);
            }

            // Validate input
            $validated = $request->validate([
                'rating' => 'nullable|integer|min:1|max:5',
                'comment' => 'nullable|string|max:1000',
            ]);

            // Update rating with only provided fields
            $rating->update(array_filter($validated, fn($value) => $value !== null));

            return response()->json([
                'success' => true,
                'message' => 'Rating updated successfully',
                'data' => [
                    'id' => $rating->id,
                    'rating' => $rating->rating,
                    'comment' => $rating->comment,
                    'updated_at' => $rating->updated_at,
                ],
            ]);

        } catch (ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Validation error',
                'errors' => $e->errors(),
            ], 422);
        }
    }

    /**
     * Delete a rating (student only, own ratings)
     *
     * Parameters:
     * - rating: the rating ID
     *
     * Returns: success message
     */
    public function destroy(Rating $rating): JsonResponse
    {
        $user = auth()->user();

        // Check if user is the student who made this rating
        if ($rating->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: you can only delete your own ratings',
            ], 403);
        }

        $rating->delete();

        return response()->json([
            'success' => true,
            'message' => 'Rating deleted successfully',
        ]);
    }
}
