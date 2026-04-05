<?php

use Illuminate\Support\Facades\Route;
use App\Models\User;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\Rating;

// Redirect root to dashboard
Route::get('/', function () {
    if (auth()->check()) {
        return redirect('/dashboard');
    }
    return redirect('/login');
});

// Dashboard (shows different content for driver vs student)
Route::get('/dashboard', function () {
    $user = auth()->user();

    if ($user->role === 'driver') {
        // Driver dashboard
        $myRidesCount = Ride::where('driver_id', $user->id)->count();
        $activeRides = Ride::where('driver_id', $user->id)->where('status', 'active')->get();
        $pendingBookingsCount = Booking::whereHas('ride', function ($q) use ($user) {
            $q->where('driver_id', $user->id);
        })->where('status', 'pending')->count();
        $driverRating = $user->getAverageRating();

        return view('dashboard', [
            'myRidesCount' => $myRidesCount,
            'activeRides' => $activeRides,
            'pendingBookingsCount' => $pendingBookingsCount,
            'driverRating' => $driverRating
        ]);
    } else {
        // Student dashboard
        $myBookingsCount = Booking::where('student_id', $user->id)->count();
        $confirmedBookingsCount = Booking::where('student_id', $user->id)->where('status', 'confirmed')->count();
        $myRatingsCount = Rating::where('student_id', $user->id)->count();
        $recentBookings = Booking::where('student_id', $user->id)->latest()->take(5)->get();

        return view('dashboard', [
            'myBookingsCount' => $myBookingsCount,
            'confirmedBookingsCount' => $confirmedBookingsCount,
            'myRatingsCount' => $myRatingsCount,
            'recentBookings' => $recentBookings
        ]);
    }
})->middleware(['auth'])->name('dashboard');

// Search rides
Route::get('/rides/search', function () {
    $destination = request('destination');
    $date = request('departure_date');

    $rides = Ride::where('status', 'active');

    if ($destination) {
        $rides = $rides->where('destination', 'like', "%{$destination}%");
    }
    if ($date) {
        $rides = $rides->whereDate('departure_date', $date);
    }

    $rides = $rides->with('driver')->get();

    return view('rides.search', ['rides' => $rides]);
})->middleware(['auth'])->name('rides.search');

// Post ride (driver only)
Route::get('/rides/create', function () {
    if (auth()->user()->role !== 'driver') {
        return abort(403);
    }
    return view('rides.create');
})->middleware(['auth'])->name('rides.create');

Route::post('/rides/store', function () {
    return abort_if(auth()->user()->role !== 'driver', 403);

    $ride = Ride::create([
        'driver_id' => auth()->id(),
        'starting_point' => request('starting_point'),
        'destination' => request('destination'),
        'departure_date' => request('departure_date'),
        'departure_time' => request('departure_time'),
        'available_seats' => request('available_seats'),
        'price_per_seat' => request('price_per_seat'),
        'vehicle_description' => request('vehicle_description'),
        'status' => 'active'
    ]);

    return redirect('/myrides')->with('success', 'Ride posted successfully!');
})->middleware(['auth'])->name('rides.store');

// Edit ride
Route::get('/rides/{ride}/edit', function ($ride) {
    $ride = Ride::findOrFail($ride);
    return abort_if(auth()->id() !== $ride->driver_id, 403);
    return view('rides.edit', ['ride' => $ride]);
})->middleware(['auth'])->name('rides.edit');

Route::post('/rides/{ride}/update', function ($ride) {
    $ride = Ride::findOrFail($ride);
    return abort_if(auth()->id() !== $ride->driver_id, 403);

    $ride->update([
        'starting_point' => request('starting_point'),
        'destination' => request('destination'),
        'departure_date' => request('departure_date'),
        'departure_time' => request('departure_time'),
        'available_seats' => request('available_seats'),
        'price_per_seat' => request('price_per_seat'),
        'vehicle_description' => request('vehicle_description'),
    ]);

    return redirect('/myrides')->with('success', 'Ride updated successfully!');
})->middleware(['auth'])->name('rides.update');

// Cancel ride
Route::post('/rides/{ride}/cancel', function ($ride) {
    $ride = Ride::findOrFail($ride);
    return abort_if(auth()->id() !== $ride->driver_id, 403);
    $ride->update(['status' => 'cancelled']);
    return redirect('/myrides')->with('success', 'Ride cancelled!');
})->middleware(['auth'])->name('rides.cancel');
// Create booking (student books a ride)
Route::post('/bookings/store', function () {
    return abort_if(auth()->user()->role !== 'student', 403);

    $ride = Ride::findOrFail(request('ride_id'));

    // Check if already booked
    $existing = Booking::where('ride_id', $ride->id)
        ->where('student_id', auth()->id())
        ->first();

    if ($existing) {
        return back()->with('error', 'You already booked this ride!');
    }

    // Check if seats available
    $bookedSeats = Booking::where('ride_id', $ride->id)
        ->where('status', '!=', 'cancelled')
        ->count();

    if ($bookedSeats >= $ride->available_seats) {
        return back()->with('error', 'No seats available!');
    }

    Booking::create([
        'ride_id' => $ride->id,
        'student_id' => auth()->id(),
        'status' => 'pending'
    ]);

    return back()->with('success', 'Booking request sent! Wait for driver approval.');
})->middleware(['auth'])->name('bookings.store');

// Accept booking (driver)
Route::post('/bookings/{booking}/accept', function ($booking) {
    $booking = Booking::findOrFail($booking);
    return abort_if(auth()->id() !== $booking->ride->driver_id, 403);
    $booking->update(['status' => 'confirmed']);
    return back()->with('success', 'Booking accepted!');
})->middleware(['auth'])->name('bookings.accept');

// Reject booking (driver)
Route::post('/bookings/{booking}/reject', function ($booking) {
    $booking = Booking::findOrFail($booking);
    return abort_if(auth()->id() !== $booking->ride->driver_id, 403);
    $booking->update(['status' => 'cancelled']);
    return back()->with('success', 'Booking rejected!');
})->middleware(['auth'])->name('bookings.reject');

// Cancel booking (student)
Route::post('/bookings/{booking}/cancel', function ($booking) {
    $booking = Booking::findOrFail($booking);
    return abort_if(auth()->id() !== $booking->student_id, 403);
    $booking->update(['status' => 'cancelled']);
    return back()->with('success', 'Booking cancelled!');
})->middleware(['auth'])->name('bookings.cancel');
// My bookings
Route::get('/bookings', function () {
    $user = auth()->user();
    $status = request('status');

    if ($user->role === 'driver') {
        // Driver sees bookings for their rides
        $bookings = Booking::whereHas('ride', function ($q) use ($user) {
            $q->where('driver_id', $user->id);
        });
    } else {
        // Student sees their own bookings
        $bookings = Booking::where('student_id', $user->id);
    }

    if ($status) {
        $bookings = $bookings->where('status', $status);
    }

    $bookings = $bookings->with('ride', 'ride.driver')->get();

    return view('bookings.index', [
        'bookings' => $bookings,
        'bookingsCount' => $bookings->count(),
        'pendingCount' => Booking::where('status', 'pending')->count(),
        'confirmedCount' => Booking::where('status', 'confirmed')->count(),
        'cancelledCount' => Booking::where('status', 'cancelled')->count(),
    ]);
})->middleware(['auth'])->name('bookings.index');

// Alias: /mybookings routes to /bookings for view compatibility
Route::get('/mybookings', function () {
    return redirect('/bookings' . (request('status') ? '?status=' . request('status') : ''));
})->middleware(['auth']);

// My rides
Route::get('/myrides', function () {
    $user = auth()->user();
    if ($user->role !== 'driver') {
        return abort(403);
    }

    $rides = Ride::where('driver_id', $user->id)->get();

    return view('rides.index', ['rides' => $rides]);
})->middleware(['auth'])->name('rides.myrides');

// Rate driver
Route::get('/ratings/create/{booking}', function ($booking) {
    $booking = Booking::findOrFail($booking);
    return abort_if(auth()->id() !== $booking->student_id, 403);
    return view('ratings.create', ['booking' => $booking]);
})->middleware(['auth'])->name('ratings.create');

Route::post('/ratings/store', function () {
    Rating::create([
        'booking_id' => request('booking_id'),
        'ride_id' => request('ride_id'),
        'student_id' => auth()->id(),
        'driver_id' => request('driver_id'),
        'rating' => request('rating'),
        'comment' => request('comment')
    ]);

    return redirect('/bookings')->with('success', 'Rating submitted!');
})->middleware(['auth'])->name('ratings.store');

// Edit rating
Route::get('/ratings/edit/{rating}', function ($rating) {
    $rating = Rating::findOrFail($rating);
    return abort_if(auth()->id() !== $rating->student_id, 403);
    return view('ratings.edit', ['rating' => $rating]);
})->middleware(['auth'])->name('ratings.edit');

Route::post('/ratings/{rating}/update', function ($rating) {
    $rating = Rating::findOrFail($rating);
    return abort_if(auth()->id() !== $rating->student_id, 403);

    $rating->update([
        'rating' => request('rating'),
        'comment' => request('comment')
    ]);

    return redirect('/ratings')->with('success', 'Rating updated!');
})->middleware(['auth'])->name('ratings.update');

// Delete rating
Route::post('/ratings/{rating}/delete', function ($rating) {
    $rating = Rating::findOrFail($rating);
    return abort_if(auth()->id() !== $rating->student_id, 403);
    $rating->delete();

    return redirect('/ratings')->with('success', 'Rating deleted!');
})->middleware(['auth'])->name('ratings.delete');

// My ratings
Route::get('/ratings', function () {
    $ratings = Rating::where('student_id', auth()->id())
        ->with('ride', 'ride.driver')
        ->get();

    return view('ratings.index', ['ratings' => $ratings]);
})->middleware(['auth'])->name('ratings.index');

// Simple login for testing
Route::get('/login', function () {
    return view('login');
})->name('login');

Route::post('/login', function () {
    $email = request('email');
    $user = User::where('email', $email)->first();

    if (!$user) {
        return back()->with('error', 'User not found');
    }

    auth()->login($user);
    return redirect('/dashboard');
})->name('login.post');

// Logout
Route::post('/logout', function () {
    auth()->logout();
    return redirect('/login');
})->name('logout');
