@extends('layouts.app')

@section('title', 'Edit Rating - MobilITé')

@section('content')
<div class="content">
    <h1>✏️ Edit Your Rating</h1>

    <div style="max-width: 600px;">
        <!-- Ride Info -->
        <div class="ride-card">
            <div class="ride-header">
                <div>
                    <div class="ride-route">
                        {{ $rating->ride->starting_point }} → {{ $rating->ride->destination }}
                    </div>
                    <div style="color: #7f8c8d; margin-top: 0.3rem; font-size: 0.95rem;">
                        📅 {{ $rating->ride->departure_date }} at ⏰ {{ $rating->ride->departure_time }}
                    </div>
                </div>
            </div>

            <div class="driver-info">
                <span class="driver-name">👤 {{ $rating->ride->driver->name }}</span>
                <span style="float: right;">
                    <span class="rating">⭐ {{ number_format($rating->ride->driver->getAverageRating(), 1) }}</span>
                </span>
            </div>

            <div class="ride-details">
                <div class="detail-item">
                    <span class="detail-label">Driver Email</span>
                    <span class="detail-value">{{ $rating->ride->driver->email }}</span>
                </div>
                <div class="detail-item">
                    <span class="detail-label">Vehicle</span>
                    <span class="detail-value">{{ $rating->ride->vehicle_description ?? 'Not specified' }}</span>
                </div>
            </div>
        </div>

        <!-- Rating Form -->
        <div style="margin-top: 2rem; background: #ecf0f1; padding: 1.5rem; border-radius: 6px;">
            <h2 style="margin-top: 0;">Update your rating</h2>

            <form method="POST" action="/ratings/{{ $rating->id }}/update">
                @csrf

                <!-- Star Rating -->
                <div class="form-group">
                    <label>⭐ Your Rating *</label>
                    <div style="display: flex; gap: 1rem; margin-top: 1rem;">
                        @for ($i = 1; $i <= 5; $i++)
                            <label style="display: flex; align-items: center; cursor: pointer; font-weight: normal; font-size: 2rem;">
                                <input
                                    type="radio"
                                    name="rating"
                                    value="{{ $i }}"
                                    {{ $rating->rating == $i ? 'checked' : '' }}
                                    style="width: auto; margin-right: 0.5rem;"
                                    required
                                >
                                {{ str_repeat('⭐', $i) }}
                            </label>
                        @endfor
                    </div>
                </div>

                <!-- Comment -->
                <div class="form-group">
                    <label for="comment">💬 Your Comment *</label>
                    <textarea
                        id="comment"
                        name="comment"
                        placeholder="Share your experience... (max 1000 characters)"
                        maxlength="1000"
                        required
                    >{{ $rating->comment }}</textarea>
                    <div style="color: #7f8c8d; font-size: 0.85rem; margin-top: 0.3rem;">
                        <span id="char-count">{{ strlen($rating->comment) }}</span> / 1000 characters
                    </div>
                </div>

                <!-- Buttons -->
                <div class="button-group">
                    <button type="submit" class="success" style="flex: 1;">💾 Save Changes</button>
                    <a href="/ratings" style="text-decoration: none; flex: 1;">
                        <button type="button" class="secondary" style="width: 100%;">Cancel</button>
                    </a>
                </div>
            </form>
        </div>
    </div>
</div>

<script>
    const textarea = document.getElementById('comment');
    const charCount = document.getElementById('char-count');
    textarea.addEventListener('input', function() {
        charCount.textContent = this.value.length;
    });
</script>
@endsection
