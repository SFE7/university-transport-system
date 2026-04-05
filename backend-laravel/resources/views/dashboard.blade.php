@extends('layouts.app')

@section('title', 'Dashboard - MobilITé')

@section('content')
<div class="content">
    <h1>Welcome, {{ auth()->user()->name }}! 👋</h1>

    @if (auth()->user()->isStudent())
        <!-- Student Dashboard -->
        <div class="info-card">
            <strong>You are logged in as a Student</strong><br>
            You can search for rides, book seats, and rate drivers.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <!-- Statistics -->
            <div style="background: #3498db; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">MY BOOKINGS</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $myBookingsCount }}</p>
                <a href="/mybookings" style="color: white; text-decoration: none; font-size: 0.9rem;">View all →</a>
            </div>

            <div style="background: #27ae60; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">CONFIRMED RIDES</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $confirmedBookingsCount }}</p>
                <a href="/mybookings?status=confirmed" style="color: white; text-decoration: none; font-size: 0.9rem;">View all →</a>
            </div>

            <div style="background: #f39c12; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">MY RATINGS</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $myRatingsCount }}</p>
                <a href="/ratings" style="color: white; text-decoration: none; font-size: 0.9rem;">View all →</a>
            </div>
        </div>

        <h2>Quick Actions</h2>
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <a href="/rides/search" style="text-decoration: none;">
                <button style="width: 200px;">🔍 Search Rides</button>
            </a>
            <a href="/mybookings" style="text-decoration: none;">
                <button class="secondary" style="width: 200px;">📋 My Bookings</button>
            </a>
            <a href="/ratings" style="text-decoration: none;">
                <button class="secondary" style="width: 200px;">⭐ My Ratings</button>
            </a>
        </div>

        <h2>Recent Bookings</h2>
        @if ($recentBookings->count() > 0)
            @foreach ($recentBookings as $booking)
                <div class="booking-card">
                    <div class="ride-header">
                        <div>
                            <div class="ride-route">{{ $booking->ride->starting_point }} → {{ $booking->ride->destination }}</div>
                            <div style="color: #7f8c8d; margin-top: 0.3rem;">{{ $booking->ride->departure_date }} at {{ $booking->ride->departure_time }}</div>
                        </div>
                        <span class="status {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                    </div>
                    <div class="ride-details">
                        <div class="detail-item">
                            <span class="detail-label">Driver</span>
                            <span class="detail-value">{{ $booking->ride->driver->name }}</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Price per Seat</span>
                            <span class="detail-value price">{{ $booking->ride->price_per_seat }} TND</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Booked On</span>
                            <span class="detail-value">{{ $booking->created_at->format('d/m/Y') }}</span>
                        </div>
                    </div>
                    @if ($booking->status === 'confirmed' && !$booking->rating)
                        <a href="/ratings/create/{{ $booking->id }}" style="text-decoration: none;">
                            <button class="success">⭐ Rate this Ride</button>
                        </a>
                    @endif
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <h3>No bookings yet</h3>
                <p>Start by <a href="/rides/search" style="color: #3498db;">searching for rides</a></p>
            </div>
        @endif

    @elseif (auth()->user()->isDriver())
        <!-- Driver Dashboard -->
        <div class="info-card">
            <strong>You are logged in as a Driver</strong><br>
            You can post rides, manage bookings, and build your reputation.
        </div>

        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <!-- Statistics -->
            <div style="background: #3498db; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">MY RIDES</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $myRidesCount }}</p>
                <a href="/myrides" style="color: white; text-decoration: none; font-size: 0.9rem;">View all →</a>
            </div>

            <div style="background: #f39c12; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">PENDING BOOKINGS</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $pendingBookingsCount }}</p>
                <a href="/mybookings?status=pending" style="color: white; text-decoration: none; font-size: 0.9rem;">View all →</a>
            </div>

            <div style="background: #e74c3c; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">YOUR RATING</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ number_format($driverRating, 1) }} ⭐</p>
                <a href="/profile" style="color: white; text-decoration: none; font-size: 0.9rem;">View profile →</a>
            </div>
        </div>

        <h2>Quick Actions</h2>
        <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
            <a href="/rides/create" style="text-decoration: none;">
                <button style="width: 200px;">➕ Post a Ride</button>
            </a>
            <a href="/myrides" style="text-decoration: none;">
                <button class="secondary" style="width: 200px;">🚗 My Rides</button>
            </a>
            <a href="/mybookings?status=pending" style="text-decoration: none;">
                <button class="secondary" style="width: 200px;">📬 Pending Requests</button>
            </a>
        </div>

        <h2>Your Active Rides</h2>
        @if ($activeRides->count() > 0)
            @foreach ($activeRides as $ride)
                <div class="ride-card">
                    <div class="ride-header">
                        <div>
                            <div class="ride-route">{{ $ride->starting_point }} → {{ $ride->destination }}</div>
                            <div style="color: #7f8c8d; margin-top: 0.3rem;">{{ $ride->departure_date }} at {{ $ride->departure_time }}</div>
                        </div>
                        <span class="status {{ $ride->status }}">{{ ucfirst($ride->status) }}</span>
                    </div>
                    <div class="ride-details">
                        <div class="detail-item">
                            <span class="detail-label">Available Seats</span>
                            <span class="detail-value">{{ $ride->available_seats }} seats</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Booked Seats</span>
                            <span class="detail-value">{{ \App\Models\Booking::where('ride_id', $ride->id)->where('status', 'confirmed')->count() }} seats</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Price per Seat</span>
                            <span class="detail-value price">{{ $ride->price_per_seat }} TND</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Pending Requests</span>
                            <span class="detail-value">{{ \App\Models\Booking::where('ride_id', $ride->id)->where('status', 'pending')->count() }}</span>
                        </div>
                    </div>
                    <div class="button-group">
                        <a href="/rides/edit/{{ $ride->id }}" style="text-decoration: none; flex: 1;">
                            <button style="width: 100%;">✏️ Edit</button>
                        </a>
                        <a href="/rides/bookings/{{ $ride->id }}" style="text-decoration: none; flex: 1;">
                            <button class="secondary" style="width: 100%;">📋 View Bookings</button>
                        </a>
                        <form method="POST" action="/rides/{{ $ride->id }}/delete" style="flex: 1;">
                            @csrf
                            <button type="submit" class="danger" style="width: 100%;" onclick="return confirm('Are you sure?');">❌ Cancel</button>
                        </form>
                    </div>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <h3>No active rides</h3>
                <p><a href="/rides/create" style="color: #3498db;">Post your first ride</a> to start earning!</p>
            </div>
        @endif
    @endif
</div>
@endsection
