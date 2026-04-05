@extends('layouts.app')

@section('title', 'My Ratings - MobilITé')

@section('content')
<div class="content">
    <h1>⭐ My Ratings & Reviews</h1>

    <p style="color: #7f8c8d; margin-bottom: 2rem;">
        View and manage the ratings you've given to drivers.
    </p>

    @if ($ratings->count() > 0)
        <!-- Statistics -->
        <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 1rem; margin-bottom: 2rem;">
            <div style="background: #f39c12; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">TOTAL RATINGS</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $ratings->count() }}</p>
            </div>

            <div style="background: #27ae60; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">AVERAGE RATING</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ number_format($ratings->avg('rating'), 1) }} ⭐</p>
            </div>

            <div style="background: #3498db; color: white; padding: 1.5rem; border-radius: 6px;">
                <h3 style="margin-bottom: 0.5rem; font-size: 0.9rem; opacity: 0.9;">5-STAR RATINGS</h3>
                <p style="font-size: 2rem; font-weight: bold;">{{ $ratings->where('rating', 5)->count() }}</p>
            </div>
        </div>

        <!-- Ratings List -->
        @foreach ($ratings as $rating)
            <div style="background: white; border: 1px solid #ecf0f1; border-radius: 6px; padding: 1.5rem; margin-bottom: 1.5rem; transition: box-shadow 0.3s;">
                <!-- Header -->
                <div style="display: flex; justify-content: space-between; align-items: start; margin-bottom: 1rem;">
                    <div>
                        <div style="font-weight: bold; font-size: 1.1rem; color: #2c3e50;">
                            {{ $rating->ride->starting_point }} → {{ $rating->ride->destination }}
                        </div>
                        <div style="color: #7f8c8d; font-size: 0.9rem; margin-top: 0.3rem;">
                            📅 {{ $rating->ride->departure_date }} at ⏰ {{ $rating->ride->departure_time }}
                        </div>
                    </div>
                    <div style="text-align: right;">
                        <div style="font-size: 1.5rem; color: #f39c12; font-weight: bold;">
                            {{ str_repeat('⭐', $rating->rating) }}{{ str_repeat('☆', 5 - $rating->rating) }}
                        </div>
                        <div style="color: #7f8c8d; font-size: 0.9rem; margin-top: 0.3rem;">
                            {{ $rating->rating }} stars
                        </div>
                    </div>
                </div>

                <!-- Driver Info -->
                <div class="driver-info">
                    <span class="driver-name">👤 {{ $rating->driver->name }}</span>
                </div>

                <!-- Review Comment -->
                @if ($rating->comment)
                    <div style="background: #f9f9f9; padding: 1rem; border-radius: 4px; margin-bottom: 1rem;">
                        <p style="margin: 0; color: #2c3e50; line-height: 1.6;">
                            {{ $rating->comment }}
                        </p>
                    </div>
                @else
                    <div style="color: #95a5a6; font-style: italic; margin-bottom: 1rem;">
                        No comment provided
                    </div>
                @endif

                <!-- Rating Date -->
                <div style="color: #7f8c8d; font-size: 0.9rem; margin-bottom: 1rem;">
                    Rated on {{ $rating->created_at->format('d M Y \a\t H:i') }}
                    @if ($rating->updated_at->ne($rating->created_at))
                        • Last updated {{ $rating->updated_at->diffForHumans() }}
                    @endif
                </div>

                <!-- Actions -->
                <div style="display: flex; gap: 0.5rem;">
                    <a href="/ratings/edit/{{ $rating->id }}" style="text-decoration: none; flex: 1;">
                        <button style="width: 100%;">✏️ Edit</button>
                    </a>
                    <form method="POST" action="/ratings/{{ $rating->id }}/delete" style="flex: 1;">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="danger" style="width: 100%;" onclick="return confirm('Delete this rating?');">
                            🗑️ Delete
                        </button>
                    </form>
                </div>
            </div>
        @endforeach
    @else
        <div class="empty-state">
            <h3>⭐ No ratings yet</h3>
            <p>After completing a confirmed booking, you can rate the driver.</p>
            <a href="/mybookings?status=confirmed" style="color: #3498db; text-decoration: none;">
                View confirmed bookings →
            </a>
        </div>
    @endif
</div>
@endsection
