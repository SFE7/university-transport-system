<?php

namespace App\Http\Controllers;

use App\Models\Booking;
use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

/**
 * BookingController
 *
 * Handles booking operations:
 * - Create a booking request (student)
 * - Get booking details
 * - List all bookings for a student
 * - List all bookings for a ride (driver)
 * - Accept a booking (driver)
 * - Reject a booking (driver)
 * - Cancel a booking (student)
 */
class BookingController extends Controller
{
    /**
     * Create a new booking request (student only)
     *
     * Body parameters:
     * - ride_id (required): the ride to book
     *
     * Returns: created booking details
     */
    public function store(Request $request): JsonResponse
    {
        try {
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'ride_id' => 'required|exists:rides,id',
            ]);

            $rideId = $validated['ride_id'];

            // Get the ride
            $ride = Ride::findOrFail($rideId);

            // Check if ride is active
            if ($ride->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'This ride is no longer available',
                ], 400);
            }

            // Check if ride has available seats
            if ($ride->available_seats <= 0) {
                return response()->json([
                    'success' => false,
                    'message' => 'No available seats in this ride',
                ], 400);
            }

            // Check if student already booked this ride
            $existingBooking = Booking::where('ride_id', $rideId)
                ->where('student_id', $user->id)
                ->first();

            if ($existingBooking) {
                return response()->json([
                    'success' => false,
                    'message' => 'You have already booked this ride',
                ], 400);
            }

            // Check if student is not the driver of this ride
            if ($ride->driver_id === $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'You cannot book your own ride',
                ], 400);
            }

            // Create the booking
            $booking = Booking::create([
                'ride_id' => $rideId,
                'student_id' => $user->id,
                'status' => 'pending', // Initially pending driver approval
            ]);

            // Load relationships
            $booking->load('ride:id,starting_point,destination,departure_date,departure_time,price_per_seat');

            return response()->json([
                'success' => true,
                'message' => 'Booking request created successfully',
                'data' => [
                    'id' => $booking->id,
                    'ride_id' => $booking->ride_id,
                    'ride' => [
                        'starting_point' => $booking->ride->starting_point,
                        'destination' => $booking->ride->destination,
                        'departure_date' => $booking->ride->departure_date,
                        'departure_time' => $booking->ride->departure_time,
                        'price_per_seat' => $booking->ride->price_per_seat,
                    ],
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                ],
            ], 201);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error creating booking: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Get a specific booking's details
     *
     * Parameters:
     * - booking: the booking ID
     *
     * Returns: booking details with ride and student info
     */
    public function show(Booking $booking): JsonResponse
    {
        // Load relationships
        $booking->load('ride', 'student', 'rating');

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $booking->id,
                'ride_id' => $booking->ride_id,
                'ride' => [
                    'starting_point' => $booking->ride->starting_point,
                    'destination' => $booking->ride->destination,
                    'departure_date' => $booking->ride->departure_date,
                    'departure_time' => $booking->ride->departure_time,
                    'price_per_seat' => $booking->ride->price_per_seat,
                    'driver_id' => $booking->ride->driver_id,
                ],
                'student' => [
                    'id' => $booking->student->id,
                    'name' => $booking->student->name,
                    'email' => $booking->student->email,
                ],
                'status' => $booking->status,
                'has_rating' => $booking->rating ? true : false,
                'created_at' => $booking->created_at,
                'updated_at' => $booking->updated_at,
            ],
        ]);
    }

    /**
     * Get all bookings for the authenticated user
     * For drivers: get all bookings for their rides
     * For students: get all their own bookings
     *
     * Query parameters:
     * - status (optional): filter by status (pending, confirmed, cancelled)
     *
     * Returns: list of bookings
     */
    public function index(Request $request): JsonResponse
    {
        $user = auth()->user();

        $query = Booking::with('ride', 'student');

        // If user is a driver, show bookings for their rides
        if ($user->isDriver()) {
            $query->whereHas('ride', function ($q) use ($user) {
                $q->where('driver_id', $user->id);
            });
        }
        // If user is a student, show only their bookings
        else {
            $query->where('student_id', $user->id);
        }

        // Filter by status if provided
        if ($request->has('status')) {
            $query->where('status', $request->get('status'));
        }

        $bookings = $query->orderBy('created_at', 'desc')->get()
            ->map(function ($booking) {
                return [
                    'id' => $booking->id,
                    'ride_id' => $booking->ride_id,
                    'ride' => [
                        'starting_point' => $booking->ride->starting_point,
                        'destination' => $booking->ride->destination,
                        'departure_date' => $booking->ride->departure_date,
                        'departure_time' => $booking->ride->departure_time,
                        'price_per_seat' => $booking->ride->price_per_seat,
                    ],
                    'student_name' => $booking->student->name,
                    'student_id' => $booking->student->id,
                    'status' => $booking->status,
                    'created_at' => $booking->created_at,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $bookings,
            'count' => $bookings->count(),
        ]);
    }

    /**
     * Accept a booking request (driver only)
     *
     * This endpoint is called when a driver approves a student's booking.
     * The student's booking status changes from 'pending' to 'confirmed'.
     *
     * Parameters:
     * - booking: the booking ID
     *
     * Returns: updated booking details
     */
    public function accept(Booking $booking): JsonResponse
    {
        $user = auth()->user();

        // Get the ride
        $ride = $booking->ride;

        // Check if user is the driver of this ride
        if ($ride->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: only the driver can accept bookings for this ride',
            ], 403);
        }

        // Check if booking is pending
        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be accepted',
            ], 400);
        }

        // Check if there are still available seats
        if ($ride->available_seats <= 0) {
            return response()->json([
                'success' => false,
                'message' => 'No available seats left in this ride',
            ], 400);
        }

        // Update booking status
        $booking->update(['status' => 'confirmed']);

        // TODO: Send notification to student that booking is confirmed

        return response()->json([
            'success' => true,
            'message' => 'Booking accepted successfully',
            'data' => [
                'id' => $booking->id,
                'status' => $booking->status,
                'student_name' => $booking->student->name,
            ],
        ]);
    }

    /**
     * Reject a booking request (driver only)
     *
     * This endpoint is called when a driver refuses a student's booking.
     * The booking status changes to 'cancelled'.
     *
     * Parameters:
     * - booking: the booking ID
     *
     * Returns: success message
     */
    public function reject(Booking $booking): JsonResponse
    {
        $user = auth()->user();

        // Get the ride
        $ride = $booking->ride;

        // Check if user is the driver of this ride
        if ($ride->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: only the driver can reject bookings for this ride',
            ], 403);
        }

        // Check if booking is pending
        if ($booking->status !== 'pending') {
            return response()->json([
                'success' => false,
                'message' => 'Only pending bookings can be rejected',
            ], 400);
        }

        // Update booking status
        $booking->update(['status' => 'cancelled']);

        // TODO: Send notification to student that booking was rejected

        return response()->json([
            'success' => true,
            'message' => 'Booking rejected successfully',
        ]);
    }

    /**
     * Cancel a booking (student only)
     *
     * This endpoint allows a student to cancel their booking.
     * Only pending and confirmed bookings can be cancelled.
     *
     * Parameters:
     * - booking: the booking ID
     *
     * Returns: success message
     */
    public function cancel(Booking $booking): JsonResponse
    {
        $user = auth()->user();

        // Check if user is the student who made this booking
        if ($booking->student_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: you can only cancel your own bookings',
            ], 403);
        }

        // Check if booking can be cancelled
        if ($booking->status === 'cancelled') {
            return response()->json([
                'success' => false,
                'message' => 'This booking is already cancelled',
            ], 400);
        }

        // Update booking status
        $booking->update(['status' => 'cancelled']);

        // TODO: Send notification to driver that student cancelled their booking

        return response()->json([
            'success' => true,
            'message' => 'Booking cancelled successfully',
        ]);
    }
}
