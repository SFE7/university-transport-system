@extends('layouts.app')

@section('title', 'Post a Ride - MobilITé')

@section('content')
<div class="content">
    <h1>➕ Post Your Carpool Ride</h1>

    <div class="info-card">
        <strong>📝 Fill in the details below</strong> to post a new carpool ride. Students will be able to search and book your ride.
    </div>

    <form method="POST" action="/rides/store" style="max-width: 600px;">
        @csrf

        <!-- Starting Point -->
        <div class="form-group">
            <label for="starting_point">📍 Starting Point *</label>
            <input
                type="text"
                id="starting_point"
                name="starting_point"
                placeholder="e.g., ISET Bizerte, Downtown, Main Station"
                value="{{ old('starting_point') }}"
                required
            >
            @error('starting_point')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Destination -->
        <div class="form-group">
            <label for="destination">🏙️ Destination *</label>
            <input
                type="text"
                id="destination"
                name="destination"
                placeholder="e.g., Sfax, Tunis, Sousse"
                value="{{ old('destination') }}"
                required
            >
            @error('destination')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Departure Date -->
        <div class="form-group">
            <label for="departure_date">📅 Departure Date *</label>
            <input
                type="date"
                id="departure_date"
                name="departure_date"
                value="{{ old('departure_date') }}"
                required
            >
            @error('departure_date')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Departure Time -->
        <div class="form-group">
            <label for="departure_time">⏰ Departure Time *</label>
            <input
                type="time"
                id="departure_time"
                name="departure_time"
                value="{{ old('departure_time') }}"
                required
            >
            @error('departure_time')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Available Seats -->
        <div class="form-group">
            <label for="available_seats">👥 Available Seats *</label>
            <select id="available_seats" name="available_seats" required>
                <option value="">Select number of seats</option>
                @for ($i = 1; $i <= 10; $i++)
                    <option value="{{ $i }}" @selected(old('available_seats') == $i)>
                        {{ $i }} {{ $i === 1 ? 'seat' : 'seats' }}
                    </option>
                @endfor
            </select>
            @error('available_seats')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Price per Seat -->
        <div class="form-group">
            <label for="price_per_seat">💰 Price per Seat (TND) *</label>
            <input
                type="number"
                id="price_per_seat"
                name="price_per_seat"
                placeholder="e.g., 15.50"
                step="0.01"
                min="0"
                max="1000"
                value="{{ old('price_per_seat') }}"
                required
            >
            @error('price_per_seat')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Vehicle Description -->
        <div class="form-group">
            <label for="vehicle_description">🚗 Vehicle Description (Optional)</label>
            <textarea
                id="vehicle_description"
                name="vehicle_description"
                placeholder="e.g., Red Toyota Corolla, Plate ABC-123, Clean car with AC"
                rows="3"
            >{{ old('vehicle_description') }}</textarea>
            @error('vehicle_description')
                <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
            @enderror
        </div>

        <!-- Form Actions -->
        <div style="display: flex; gap: 1rem; margin-top: 2rem;">
            <button type="submit" style="flex: 1;">📤 Post Ride</button>
            <a href="/dashboard" style="text-decoration: none; flex: 1;">
                <button type="button" class="secondary" style="width: 100%;">❌ Cancel</button>
            </a>
        </div>
    </form>

    <!-- Tips -->
    <div style="margin-top: 3rem; padding: 1.5rem; background: #d1ecf1; border-radius: 6px; border-left: 4px solid #0c5460;">
        <h3 style="color: #0c5460; margin-bottom: 1rem;">💡 Tips for a Successful Ride</h3>
        <ul style="color: #0c5460; margin-left: 1.5rem; line-height: 1.8;">
            <li><strong>Be accurate:</strong> Provide precise starting point and destination</li>
            <li><strong>Be fair:</strong> Price reasonably compared to other rides</li>
            <li><strong>Be clear:</strong> Describe your vehicle so students know what to expect</li>
            <li><strong>Be responsive:</strong> Review and respond to booking requests quickly</li>
            <li><strong>Be reliable:</strong> Depart on time and treat passengers well</li>
        </ul>
    </div>
</div>
@endsection
