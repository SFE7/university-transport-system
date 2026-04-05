@extends('layouts.app')

@section('title', 'Search Rides - MobilITé')

@section('content')
<div class="content">
    <h1>🔍 Search Carpool Rides</h1>

    <h2 style="margin-top: 0;">Find the perfect ride</h2>

    <!-- Search Form -->
    <form method="GET" action="/rides/search" style="background: #ecf0f1; padding: 2rem; border-radius: 6px; margin-bottom: 2rem;">
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1.5rem;">
            <div class="form-group" style="margin-bottom: 0;">
                <label for="destination">🏙️ Destination *</label>
                <input
                    type="text"
                    id="destination"
                    name="destination"
                    placeholder="e.g., Sfax, Tunis, Sousse"
                    value="{{ request('destination') }}"
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="departure_date">📅 Departure Date *</label>
                <input
                    type="date"
                    id="departure_date"
                    name="departure_date"
                    value="{{ request('departure_date') }}"
                    required
                >
            </div>

            <div class="form-group" style="margin-bottom: 0;">
                <label for="starting_point">📍 Starting Point (Optional)</label>
                <input
                    type="text"
                    id="starting_point"
                    name="starting_point"
                    placeholder="e.g., ISET Bizerte, Downtown"
                    value="{{ request('starting_point') }}"
                >
            </div>
        </div>

        <button type="submit" style="margin-top: 1rem; width: 100%;">Search Rides</button>
    </form>

    @if (request('destination'))
        <h2>Search Results</h2>

        @if ($rides->count() > 0)
            <p style="color: #7f8c8d; margin-bottom: 1.5rem;">
                Found <strong>{{ $rides->count() }}</strong> rides available
            </p>

            @foreach ($rides as $ride)
                <div class="ride-card">
                    <div class="ride-header">
                        <div>
                            <div class="ride-route">
                                {{ $ride->starting_point }} → {{ $ride->destination }}
                            </div>
                            <div style="color: #7f8c8d; margin-top: 0.3rem; font-size: 0.95rem;">
                                📅 {{ $ride->departure_date }} at ⏰ {{ $ride->departure_time }}
                            </div>
                        </div>
                        <div style="text-align: right;">
                            <div class="price">{{ $ride->price_per_seat }} TND</div>
                            <div style="color: #7f8c8d; font-size: 0.9rem; margin-top: 0.3rem;">per seat</div>
                        </div>
                    </div>

                    <!-- Driver Info -->
                    <div class="driver-info">
                        <span class="driver-name">👤 {{ $ride->driver->name }}</span>
                        <span style="float: right;">
                            <span class="rating">⭐ {{ number_format($ride->driver->getAverageRating(), 1) }}</span>
                            <span style="color: #7f8c8d; font-size: 0.9rem;"> ({{ \App\Models\Rating::where('driver_id', $ride->driver->id)->count() }} reviews)</span>
                        </span>
                    </div>

                    <!-- Ride Details -->
                    <div class="ride-details">
                        <div class="detail-item">
                            <span class="detail-label">Available Seats</span>
                            <span class="detail-value" style="font-weight: bold; color: #27ae60;">{{ $ride->available_seats }} available</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Booked</span>
                            <span class="detail-value">{{ \App\Models\Booking::where('ride_id', $ride->id)->where('status', 'confirmed')->count() }} / {{ \App\Models\Booking::where('ride_id', $ride->id)->where('status', 'confirmed')->count() + $ride->available_seats }} seats</span>
                        </div>
                        <div class="detail-item">
                            <span class="detail-label">Vehicle</span>
                            <span class="detail-value">{{ $ride->vehicle_description ?? 'Not specified' }}</span>
                        </div>
                    </div>

                    <!-- Book Button -->
                    <form method="POST" action="/bookings/store" style="margin-top: 1rem;">
                        @csrf
                        <input type="hidden" name="ride_id" value="{{ $ride->id }}">
                        <button type="submit" style="width: 100%;" onclick="return confirm('Book this ride?');">
                            ✅ Book a Seat
                        </button>
                    </form>
                </div>
            @endforeach
        @else
            <div class="empty-state">
                <h3>😔 No rides found</h3>
                <p>Try searching with different criteria or check back later!</p>
            </div>
        @endif
    @else
        <div class="info-card">
            <strong>💡 How to search:</strong>
            <ol style="margin-top: 0.5rem; margin-left: 1rem;">
                <li>Enter your destination city</li>
                <li>Select your preferred date</li>
                <li>Optionally specify your starting point</li>
                <li>Click "Search Rides" to see available options</li>
                <li>Click "Book a Seat" to reserve your spot</li>
            </ol>
        </div>

        <div class="empty-state" style="padding-top: 2rem;">
            <h3>👇 Start searching above</h3>
            <p>Enter your travel destination to find available carpool rides</p>
        </div>
    @endif
</div>
@endsection
