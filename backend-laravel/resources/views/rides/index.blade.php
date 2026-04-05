@extends('layouts.app')

@section('title', 'My Rides - MobilITé')

@section('content')
<div class="content">
    <h1>🚗 My Rides</h1>

    <div style="margin-bottom: 2rem; display: flex; gap: 1rem;">
        <a href="/rides/create" style="text-decoration: none;">
            <button>➕ Post a New Ride</button>
        </a>
    </div>

    @if ($rides->count() > 0)
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
                    <span class="status {{ $ride->status }}">{{ ucfirst($ride->status) }}</span>
                </div>

                <div class="ride-details">
                    <div class="detail-item">
                        <span class="detail-label">Available Seats</span>
                        <span class="detail-value">{{ $ride->available_seats }} seats</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Booked Seats</span>
                        <span class="detail-value">
                            {{ \App\Models\Booking::where('ride_id', $ride->id)->where('status', 'confirmed')->count() }} / {{ $ride->available_seats }}
                        </span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Price per Seat</span>
                        <span class="detail-value price">{{ $ride->price_per_seat }} TND</span>
                    </div>
                    <div class="detail-item">
                        <span class="detail-label">Vehicle</span>
                        <span class="detail-value">{{ $ride->vehicle_description ?? 'Not specified' }}</span>
                    </div>
                </div>

                <div class="button-group" style="margin-top: 1rem;">
                    @if ($ride->status === 'active')
                        <a href="/rides/{{ $ride->id }}/edit" style="text-decoration: none; flex: 1;">
                            <button class="secondary">✏️ Edit</button>
                        </a>
                        <form method="POST" action="/rides/{{ $ride->id }}/cancel" style="flex: 1;">
                            @csrf
                            <button type="submit" class="danger" style="width: 100%;">❌ Cancel Ride</button>
                        </form>
                    @else
                        <p style="color: #7f8c8d; font-size: 0.9rem;">
                            This ride is {{ $ride->status }}.
                        </p>
                    @endif
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <h3>No rides posted yet</h3>
            <p>Start by <a href="/rides/create" style="color: #3498db;">posting a new ride</a></p>
        </div>
    @endif
</div>
@endsection
