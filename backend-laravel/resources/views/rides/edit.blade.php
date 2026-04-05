@extends('layouts.app')

@section('title', 'Edit Ride - MobilITé')

@section('content')
<div class="content">
    <h1>✏️ Edit Your Ride</h1>

    <div class="info-card">
        <strong>📝 Update ride details</strong> for your carpool ride.
    </div>

    <form method="POST" action="/rides/{{ $ride->id }}/update" style="max-width: 600px;">
        @csrf

        <!-- Starting Point -->
        <div class="form-group">
            <label for="starting_point">📍 Starting Point *</label>
            <input
                type="text"
                id="starting_point"
                name="starting_point"
                placeholder="e.g., ISET Bizerte, Downtown, Main Station"
                value="{{ $ride->starting_point }}"
                required
            >
        </div>

        <!-- Destination -->
        <div class="form-group">
            <label for="destination">🏙️ Destination *</label>
            <input
                type="text"
                id="destination"
                name="destination"
                placeholder="e.g., Sfax, Tunis, Sousse"
                value="{{ $ride->destination }}"
                required
            >
        </div>

        <!-- Departure Date -->
        <div class="form-group">
            <label for="departure_date">📅 Departure Date *</label>
            <input
                type="date"
                id="departure_date"
                name="departure_date"
                value="{{ $ride->departure_date }}"
                required
            >
        </div>

        <!-- Departure Time -->
        <div class="form-group">
            <label for="departure_time">⏰ Departure Time *</label>
            <input
                type="time"
                id="departure_time"
                name="departure_time"
                value="{{ $ride->departure_time }}"
                required
            >
        </div>

        <!-- Available Seats -->
        <div class="form-group">
            <label for="available_seats">👥 Available Seats *</label>
            <select id="available_seats" name="available_seats" required>
                <option value="">Select number of seats...</option>
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" {{ $ride->available_seats == $i ? 'selected' : '' }}>{{ $i }} seat{{ $i > 1 ? 's' : '' }}</option>
                @endfor
            </select>
        </div>

        <!-- Price per Seat -->
        <div class="form-group">
            <label for="price_per_seat">💰 Price per Seat (TND) *</label>
            <input
                type="number"
                id="price_per_seat"
                name="price_per_seat"
                placeholder="e.g., 20"
                value="{{ $ride->price_per_seat }}"
                step="0.5"
                min="0"
                required
            >
        </div>

        <!-- Vehicle Description -->
        <div class="form-group">
            <label for="vehicle_description">🚗 Vehicle Description</label>
            <textarea
                id="vehicle_description"
                name="vehicle_description"
                placeholder="e.g., Red Toyota Corolla, License Plate: 123 TN 01"
                style="min-height: 100px;"
            >{{ $ride->vehicle_description }}</textarea>
        </div>

        <!-- Buttons -->
        <div class="button-group">
            <button type="submit" class="success" style="flex: 1;">💾 Save Changes</button>
            <a href="/myrides" style="text-decoration: none; flex: 1;">
                <button type="button" class="secondary" style="width: 100%;">Cancel</button>
            </a>
        </div>
    </form>
</div>
@endsection
