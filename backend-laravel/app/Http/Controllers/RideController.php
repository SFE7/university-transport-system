<?php

namespace App\Http\Controllers;

use App\Models\Ride;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

/**
 * RideController
 *
 * Handles ride operations:
 * - Search for available rides
 * - Post a new ride (driver only)
 * - Get ride details
 * - Update ride details (driver only)
 * - Delete/cancel a ride (driver only)
 */
class RideController extends Controller
{
    /**
     * Search for available carpool rides
     *
     * Query parameters:
     * - destination (required): where the student wants to go
     * - departure_date (required): date of the ride (YYYY-MM-DD)
     * - starting_point (optional): starting location to filter by
     *
     * Returns: list of available rides matching the criteria
     */
    public function search(Request $request): JsonResponse
    {
        try {
            // Validate search parameters
            $validated = $request->validate([
                'destination' => 'required|string|max:255',
                'departure_date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'starting_point' => 'nullable|string|max:255',
            ]);

            // Start building the query
            $query = Ride::where('status', 'active')
                ->where('destination', $validated['destination'])
                ->where('departure_date', $validated['departure_date']);

            // Filter by starting point if provided
            if ($validated['starting_point'] ?? null) {
                $query->where('starting_point', $validated['starting_point']);
            }

            // Get rides with driver info and available seats
            $rides = $query->with('driver:id,name,email')
                ->where('available_seats', '>', 0)
                ->get()
                ->map(function ($ride) {
                    return [
                        'id' => $ride->id,
                        'starting_point' => $ride->starting_point,
                        'destination' => $ride->destination,
                        'departure_time' => $ride->departure_time,
                        'departure_date' => $ride->departure_date,
                        'available_seats' => $ride->available_seats,
                        'price_per_seat' => $ride->price_per_seat,
                        'vehicle_description' => $ride->vehicle_description,
                        'driver' => [
                            'id' => $ride->driver->id,
                            'name' => $ride->driver->name,
                            'rating' => round($ride->driver->getAverageRating(), 1),
                        ],
                    ];
                });

            return response()->json([
                'success' => true,
                'data' => $rides,
                'count' => $rides->count(),
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
     * Get a specific ride's details
     *
     * Parameters:
     * - ride: the ride ID
     *
     * Returns: ride details with driver info and bookings count
     */
    public function show(Ride $ride): JsonResponse
    {
        // Load relationships
        $ride->load('driver:id,name,email', 'bookings');

        $confirmedBookings = $ride->confirmedBookings()->count();

        return response()->json([
            'success' => true,
            'data' => [
                'id' => $ride->id,
                'starting_point' => $ride->starting_point,
                'destination' => $ride->destination,
                'departure_date' => $ride->departure_date,
                'departure_time' => $ride->departure_time,
                'available_seats' => $ride->available_seats,
                'booked_seats' => $confirmedBookings,
                'price_per_seat' => $ride->price_per_seat,
                'vehicle_description' => $ride->vehicle_description,
                'status' => $ride->status,
                'driver' => [
                    'id' => $ride->driver->id,
                    'name' => $ride->driver->name,
                    'email' => $ride->driver->email,
                    'rating' => round($ride->driver->getAverageRating(), 1),
                ],
                'created_at' => $ride->created_at,
            ],
        ]);
    }

    /**
     * Create a new ride (driver only)
     *
     * Body parameters:
     * - starting_point (required): departure location
     * - destination (required): arrival location
     * - departure_date (required): date (YYYY-MM-DD)
     * - departure_time (required): time (HH:MM:SS)
     * - available_seats (required): number of available seats
     * - price_per_seat (required): price per seat
     * - vehicle_description (optional): car description
     *
     * Returns: created ride details
     */
    public function store(Request $request): JsonResponse
    {
        try {
            // Authenticate user - for API, you would use middleware
            $user = auth()->user();

            // Validate input
            $validated = $request->validate([
                'starting_point' => 'required|string|max:255',
                'destination' => 'required|string|max:255',
                'departure_date' => 'required|date_format:Y-m-d|after_or_equal:today',
                'departure_time' => 'required|date_format:H:i:s',
                'available_seats' => 'required|integer|min:1|max:10',
                'price_per_seat' => 'required|numeric|min:0|max:1000',
                'vehicle_description' => 'nullable|string|max:500',
            ]);

            // Create the ride
            $ride = Ride::create([
                'driver_id' => $user->id,
                'starting_point' => $validated['starting_point'],
                'destination' => $validated['destination'],
                'departure_date' => $validated['departure_date'],
                'departure_time' => $validated['departure_time'],
                'available_seats' => $validated['available_seats'],
                'price_per_seat' => $validated['price_per_seat'],
                'vehicle_description' => $validated['vehicle_description'] ?? null,
                'status' => 'active',
            ]);

            return response()->json([
                'success' => true,
                'message' => 'Ride created successfully',
                'data' => [
                    'id' => $ride->id,
                    'starting_point' => $ride->starting_point,
                    'destination' => $ride->destination,
                    'departure_date' => $ride->departure_date,
                    'departure_time' => $ride->departure_time,
                    'available_seats' => $ride->available_seats,
                    'price_per_seat' => $ride->price_per_seat,
                    'vehicle_description' => $ride->vehicle_description,
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
     * Update a ride (driver only, before departure)
     *
     * Parameters:
     * - ride: the ride ID
     *
     * Body parameters:
     * - departure_time (optional)
     * - available_seats (optional)
     * - price_per_seat (optional)
     * - vehicle_description (optional)
     *
     * Returns: updated ride details
     */
    public function update(Request $request, Ride $ride): JsonResponse
    {
        try {
            $user = auth()->user();

            // Check if user is the driver of this ride
            if ($ride->driver_id !== $user->id) {
                return response()->json([
                    'success' => false,
                    'message' => 'Unauthorized: only the driver can update this ride',
                ], 403);
            }

            // Check if ride is still active
            if ($ride->status !== 'active') {
                return response()->json([
                    'success' => false,
                    'message' => 'Cannot update a ride that is not active',
                ], 400);
            }

            // Validate input
            $validated = $request->validate([
                'departure_time' => 'nullable|date_format:H:i:s',
                'available_seats' => 'nullable|integer|min:1|max:10',
                'price_per_seat' => 'nullable|numeric|min:0|max:1000',
                'vehicle_description' => 'nullable|string|max:500',
            ]);

            // Update only provided fields
            $ride->update(array_filter($validated, fn($value) => $value !== null));

            return response()->json([
                'success' => true,
                'message' => 'Ride updated successfully',
                'data' => [
                    'id' => $ride->id,
                    'starting_point' => $ride->starting_point,
                    'destination' => $ride->destination,
                    'departure_date' => $ride->departure_date,
                    'departure_time' => $ride->departure_time,
                    'available_seats' => $ride->available_seats,
                    'price_per_seat' => $ride->price_per_seat,
                    'vehicle_description' => $ride->vehicle_description,
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
     * Cancel a ride (driver only)
     *
     * Parameters:
     * - ride: the ride ID
     *
     * Returns: success message
     */
    public function destroy(Ride $ride): JsonResponse
    {
        $user = auth()->user();

        // Check if user is the driver of this ride
        if ($ride->driver_id !== $user->id) {
            return response()->json([
                'success' => false,
                'message' => 'Unauthorized: only the driver can cancel this ride',
            ], 403);
        }

        // Update ride status to cancelled
        $ride->update(['status' => 'cancelled']);

        return response()->json([
            'success' => true,
            'message' => 'Ride cancelled successfully',
        ]);
    }

    /**
     * Get all rides offered by the authenticated driver
     *
     * Returns: list of rides with booking counts
     */
    public function myRides(): JsonResponse
    {
        $user = auth()->user();

        // Get all rides by the driver
        $rides = Ride::where('driver_id', $user->id)
            ->with('bookings')
            ->orderBy('departure_date', 'asc')
            ->get()
            ->map(function ($ride) {
                return [
                    'id' => $ride->id,
                    'starting_point' => $ride->starting_point,
                    'destination' => $ride->destination,
                    'departure_date' => $ride->departure_date,
                    'departure_time' => $ride->departure_time,
                    'available_seats' => $ride->available_seats,
                    'confirmed_bookings' => $ride->confirmedBookings()->count(),
                    'pending_bookings' => $ride->bookings()->where('status', 'pending')->count(),
                    'price_per_seat' => $ride->price_per_seat,
                    'status' => $ride->status,
                ];
            });

        return response()->json([
            'success' => true,
            'data' => $rides,
        ]);
    }
}
