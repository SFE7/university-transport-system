@extends('layouts.app')

@section('title', 'My Bookings - MobilITé')

@section('content')
<div class="content">
    <h1>📋 My Bookings</h1>

    <!-- Filter Tabs -->
    <div style="display: flex; gap: 1rem; margin-bottom: 2rem; flex-wrap: wrap;">
        <a href="/mybookings" style="text-decoration: none;">
            <button @if (!request('status')) style="background-color: #2c3e50;" @else class="secondary" @endif>
                All {{ $bookingsCount }}
            </button>
        </a>
        <a href="/mybookings?status=pending" style="text-decoration: none;">
            <button @if (request('status') === 'pending') style="background-color: #f39c12;" @else class="secondary" @endif>
                ⏳ Pending {{ $pendingCount }}
            </button>
        </a>
        <a href="/mybookings?status=confirmed" style="text-decoration: none;">
            <button @if (request('status') === 'confirmed') style="background-color: #27ae60;" @else class="secondary" @endif>
                ✅ Confirmed {{ $confirmedCount }}
            </button>
        </a>
        <a href="/mybookings?status=cancelled" style="text-decoration: none;">
            <button @if (request('status') === 'cancelled') style="background-color: #e74c3c;" @else class="secondary" @endif>
                ❌ Cancelled {{ $cancelledCount }}
            </button>
        </a>
    </div>

    @if ($bookings->count() > 0)
        @foreach ($bookings as $booking)
            <div class="booking-card">
                <!-- Header: Route and Status -->
                <div class="ride-header">
                    <div>
                        <div class="ride-route">
                            {{ $booking->ride->starting_point }} → {{ $booking->ride->destination }}
                        </div>
                        <div style="color: #7f8c8d; margin-top: 0.3rem; font-size: 0.95rem;">
                            📅 {{ $booking->ride->departure_date }} at ⏰ {{ $booking->ride->departure_time }}
                        </div>
                    </div>
                    <span class="status {{ $booking->status }}">{{ ucfirst($booking->status) }}</span>
                </div>

                <!-- Driver Info -->
                <div class="driver-info">
                    <span class="driver-name">👤 {{ $booking->ride->driver->name }}</span>
                    <span style="float: right;">
                        <span class="rating">⭐ {{ number_format($booking->ride->driver->getAverageRating(), 1) }}</span>
                        <span style="color: #7f8c8d; font-size: 0.9rem;"> ({{ \App\Models\Rating::where('driver_id', $booking->ride->driver->id)->count() }} reviews)</span>
                    </span>
                </div>

                <!-- Booking Details -->
                <div class="ride-details">
                    <div class="detail-item">
                        <span class="detail-label">Price per Seat</span>
                        <span class="detail-value price">{{ $booking->ride->price_per_seat }} TND</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Vehicle</span>
                        <span class="detail-value">{{ $booking->ride->vehicle_description ?? 'Not specified' }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booked On</span>
                        <span class="detail-value">{{ $booking->created_at->format('d/m/Y H:i') }}</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booking ID</span>
                        <span class="detail-value" style="font-family: monospace;">#{{ $booking->id }}</span>
                    </div>
                </div>

                <!-- Actions -->
                <div class="button-group">
                    @if ($booking->status === 'pending')
                        <p style="margin: 0; color: #f39c12; font-weight: 600; flex: 1;">
                            ⏳ Waiting for driver approval...
                        </p>
                        <form method="POST" action="/bookings/{{ $booking->id }}/cancel" style="flex: 1;">
                            @csrf
                            <button type="submit" class="danger" style="width: 100%;" onclick="return confirm('Cancel this booking?');">
                                ❌ Cancel Request
                            </button>
                        </form>
                    @elseif ($booking->status === 'confirmed')
                        <p style="margin: 0; color: #27ae60; font-weight: 600; flex: 1;">
                            ✅ Your booking is confirmed!
                        </p>
                        @if (!$booking->rating)
                            <a href="/ratings/create/{{ $booking->id }}" style="text-decoration: none; flex: 1;">
                                <button style="width: 100%;">⭐ Rate Driver</button>
                            </a>
                        @else
                            <p style="margin: 0; color: #7f8c8d; text-align: center; flex: 1;">
                                You rated: <strong>{{ $booking->rating->rating }} ⭐</strong>
                            </p>
                        @endif
                        <form method="POST" action="/bookings/{{ $booking->id }}/cancel" style="flex: 1;">
                            @csrf
                            <button type="submit" class="secondary" style="width: 100%;" onclick="return confirm('Cancel this booking?');">
                                ❌ Cancel
                            </button>
                        </form>
                    @elseif ($booking->status === 'cancelled')
                        <p style="margin: 0; color: #e74c3c; font-weight: 600; flex: 1;">
                            ❌ This booking was cancelled
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <h3>📭 No bookings yet</h3>
            <p>Start by <a href="/rides/search" style="color: #3498db;">searching for rides</a></p>
        </div>
    @endif
</div>
@endsection
