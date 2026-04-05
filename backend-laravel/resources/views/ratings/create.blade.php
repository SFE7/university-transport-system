@extends('layouts.app')

@section('title', 'Rate Driver - MobilITé')

@section('content')
<div class="content">
    <h1>⭐ Rate Your Ride</h1>

    <div style="max-width: 600px;">
        <!-- Ride Info -->
        <div class="ride-card">
            <div class="ride-header">
                <div>
                    <div class="ride-route">
                        {{ $booking->ride->starting_point }} → {{ $booking->ride->destination }}
                    </div>
                    <div style="color: #7f8c8d; margin-top: 0.3rem; font-size: 0.95rem;">
                        📅 {{ $booking->ride->departure_date }} at ⏰ {{ $booking->ride->departure_time }}
                    </div>
                </div>
            </div>

            <div class="driver-info">
                <span class="driver-name">👤 {{ $booking->ride->driver->name }}</span>
                <span style="float: right;">
                    <span class="rating">⭐ {{ number_format($booking->ride->driver->getAverageRating(), 1) }}</span>
                </span>
            </div>

            <div class="ride-details">
                <div class="detail-item">
                    <span class="detail-label">Driver Email</span>
                    <span class="detail-value">{{ $booking->ride->driver->email }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Vehicle</span>
                    <span class="detail-value">{{ $booking->ride->vehicle_description ?? 'Not specified' }}</span>
                </div>
            </div>
        </div>

        <!-- Rating Form -->
        <div style="margin-top: 2rem; background: #ecf0f1; padding: 1.5rem; border-radius: 6px;">
            <h2 style="margin-top: 0;">How was your experience?</h2>

            <form method="POST" action="/ratings/store">
                @csrf
                <input type="hidden" name="booking_id" value="{{ $booking->id }}">
                <input type="hidden" name="ride_id" value="{{ $booking->ride->id }}">
                <input type="hidden" name="driver_id" value="{{ $booking->ride->driver->id }}">

                <!-- Star Rating -->
                <div class="form-group">
                    <label>⭐ Your Rating *</label>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        @for ($i = 1; $i <= 5; $i++)
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal;">
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $i }}"
                                    style="width: auto; margin-right: 0.5rem;"
                                    @required(true)
                                    @checked(old('rating') == $i)
                                >
                                <span style="font-size: 2rem; color: #f39c12;">
                                    {{ str_repeat('⭐', $i) }}{{ str_repeat('☆', 5 - $i) }}
                                </span>
                                <span style="margin-left: 0.5rem; color: #7f8c8d;">{{ $i }}</span>
                            </label>
                        @endfor
                    </div>
                    @error('rating')
                        <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.5rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Comment -->
                <div class="form-group">
                    <label for="comment">💬 Your Review (Optional)</label>
                    <textarea
                        id="comment"
                        name="comment"
                        placeholder="Share your experience... Was the driver friendly? Was the car clean? etc."
                        rows="5"
                        maxlength="1000"
                    >{{ old('comment') }}</textarea>
                    <div style="color: #7f8c8d; font-size: 0.85rem; margin-top: 0.3rem;">
                        <span id="char-count">0</span>/1000 characters
                    </div>
                    @error('comment')
                        <div style="color: #e74c3c; font-size: 0.9rem; margin-top: 0.3rem;">{{ $message }}</div>
                    @enderror
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 1rem; margin-top: 2rem;">
                    <button type="submit" class="success" style="flex: 1;">✅ Submit Rating</button>
                    <a href="/mybookings" style="text-decoration: none; flex: 1;">
                        <button type="button" class="secondary" style="width: 100%;">❌ Skip</button>
                    </a>
                </div>
            </form>
        </div>

        <!-- Criteria Tips -->
        <div style="margin-top: 2rem; padding: 1.5rem; background: #d1ecf1; border-radius: 6px; border-left: 4px solid #0c5460;">
            <h3 style="color: #0c5460; margin-bottom: 1rem;">📋 What to Rate:</h3>
            <div style="color: #0c5460; line-height: 1.8;">
                <p><strong>⭐⭐⭐⭐⭐ (5 stars):</strong> Excellent! Friendly, safe, on-time, clean car</p>
                <p><strong>⭐⭐⭐⭐ (4 stars):</strong> Good experience, minor issues</p>
                <p><strong>⭐⭐⭐ (3 stars):</strong> Average, some concerns</p>
                <p><strong>⭐⭐ (2 stars):</strong> Below expectations</p>
                <p><strong>⭐ (1 star):</strong> Poor experience</p>
            </div>
        </div>
    </div>
</div>

<script>
    const commentField = document.getElementById('comment');
    const charCount = document.getElementById('char-count');

    commentField.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });

    // Initialize char count
    charCount.textContent = commentField.value.length;
</script>
@endsection
