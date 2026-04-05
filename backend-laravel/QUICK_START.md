# MobilITé Carpooling - Quick Start Guide

> **Sprint 1:** Carpooling Feature (Search, Book, Rate)

## Quick Navigation

- 📚 **Full API Documentation:** See [CARPOOLING_API.md](CARPOOLING_API.md)
- 🗄️ **Database Schema:** See bottom of this file
- 💻 **Code:** Check `/app/Http/Controllers/` and `/app/Models/`

---

## Installation & Setup (2 minutes)

### 1. Install Dependencies
```bash
cd c:\laravel\projects\projet_integration
composer install
npm install
```

### 2. Create Environment File
```bash
cp .env.example .env
php artisan key:generate
```

### 3. Setup Database
```bash
# Create database (if using SQLite, skip - it's created automatically)
php artisan migrate
```

### 4. Create Test Users
```bash
php artisan tinker
```

Then run in Tinker:
```php
// Create a driver
$driver = \App\Models\User::create([
    'name' => 'Ali Ben Ahmed',
    'email' => 'driver@example.com',
    'password' => bcrypt('password123'),
    'role' => 'driver'
]);

// Create a student
$student = \App\Models\User::create([
    'name' => 'Mohamed Salah',
    'email' => 'student@example.com',
    'password' => bcrypt('password123'),
    'role' => 'student'
]);

// Get authentication tokens (if using Sanctum)
$driverToken = $driver->createToken('api-token')->plainTextToken;
$studentToken = $student->createToken('api-token')->plainTextToken;

echo "Driver token: " . $driverToken . "\n";
echo "Student token: " . $studentToken;

exit
```

### 5. Start Server
```bash
php artisan serve
```

Server runs at: **http://localhost:8000**

---

## Complete User Journey (5 minutes)

### As a Driver:

**1. Post a ride offer**
```bash
curl -X POST "http://localhost:8000/api/rides" \
  -H "Authorization: Bearer DRIVER_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "starting_point": "ISET Bizerte",
    "destination": "Sfax",
    "departure_date": "2026-04-10",
    "departure_time": "08:00:00",
    "available_seats": 3,
    "price_per_seat": 15.50,
    "vehicle_description": "Red Toyota Corolla"
  }'
```

Response shows ride ID (e.g., `id: 1`)

**2. View pending bookings**
```bash
curl -X GET "http://localhost:8000/api/bookings?status=pending" \
  -H "Authorization: Bearer DRIVER_TOKEN"
```

**3. Accept a booking**
```bash
curl -X PUT "http://localhost:8000/api/bookings/1/accept" \
  -H "Authorization: Bearer DRIVER_TOKEN"
```

---

### As a Student:

**1. Search for a ride**
```bash
curl -X GET "http://localhost:8000/api/rides/search?destination=Sfax&departure_date=2026-04-10" \
  -H "Authorization: Bearer STUDENT_TOKEN"
```

**2. Book a seat in a ride**
```bash
curl -X POST "http://localhost:8000/api/bookings" \
  -H "Authorization: Bearer STUDENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ride_id": 1}'
```

**3. After ride, rate the driver**
```bash
curl -X POST "http://localhost:8000/api/ratings" \
  -H "Authorization: Bearer STUDENT_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "booking_id": 1,
    "rating": 5,
    "comment": "Great driver!"
  }'
```

---

## File Structure

```
app/
├── Http/Controllers/
│   ├── RideController.php              ← Ride search and management
│   ├── BookingController.php           ← Booking requests
│   └── RatingController.php            ← Driver ratings
└── Models/
    ├── User.php                        ← Updated with role + relationships
    ├── Ride.php                        ← Ride model
    ├── Booking.php                     ← Booking model
    └── Rating.php                      ← Rating model

database/
└── migrations/
    ├── 2026_04_05_000001_create_rides_table.php
    ├── 2026_04_05_000002_create_bookings_table.php
    ├── 2026_04_05_000003_create_ratings_table.php
    └── 2026_04_05_000004_add_role_to_users_table.php

routes/
└── api.php                             ← All API routes

CARPOOLING_API.md                       ← Full documentation
```

---

## Key API Endpoints

| Method | Endpoint | Purpose | Role |
|--------|----------|---------|------|
| GET | `/api/rides/search` | Search rides | Student |
| POST | `/api/rides` | Post a ride | Driver |
| GET | `/api/rides/{id}` | View ride details | Any |
| PUT | `/api/rides/{id}` | Update ride | Driver |
| DELETE | `/api/rides/{id}` | Cancel ride | Driver |
| GET | `/api/rides/my-rides` | My rides | Driver |
| POST | `/api/bookings` | Book a seat | Student |
| GET | `/api/bookings` | My bookings | Any |
| PUT | `/api/bookings/{id}/accept` | Accept booking | Driver |
| PUT | `/api/bookings/{id}/reject` | Reject booking | Driver |
| PUT | `/api/bookings/{id}/cancel` | Cancel booking | Student |
| POST | `/api/ratings` | Rate driver | Student |
| GET | `/api/drivers/{id}/ratings` | View driver ratings | Any |
| GET | `/api/ratings/my-ratings` | My ratings | Student |

---

## Testing with Postman

### Import Collection

1. Open Postman
2. Create new collection: "MobilITé Carpooling"
3. Add requests for each endpoint
4. Add authorization header: `Authorization: Bearer YOUR_TOKEN`

### Postman Environment Variables

Create an environment with:
```
{
  "base_url": "http://localhost:8000/api",
  "driver_token": "YOUR_DRIVER_TOKEN",
  "student_token": "YOUR_STUDENT_TOKEN",
  "ride_id": "1",
  "booking_id": "1"
}
```

Then use in requests: `{{base_url}}/rides/search`

---

## Common Tasks

### Create a test scenario

```bash
# 1. Create driver and get token
php artisan tinker
$driver = User::create(['name' => 'Driver', 'email' => 'driver@test', 'password' => bcrypt('pass'), 'role' => 'driver']);
$driverToken = $driver->createToken('token')->plainTextToken;
exit

# 2. Create student and get token
php artisan tinker
$student = User::create(['name' => 'Student', 'email' => 'student@test', 'password' => bcrypt('pass'), 'role' => 'student']);
$studentToken = $student->createToken('token')->plainTextToken;
exit

# 3. Driver posts ride
# 4. Student searches and books
# 5. Driver accepts
# 6. Student rates
```

### Reset database
```bash
php artisan migrate:fresh
```

### View all data
```bash
php artisan tinker
Ride::with('driver')->get()
Booking::with('ride', 'student')->get()
Rating::with('driver', 'student')->get()
```

---

## Database Schema Overview

### ✅ Rides Table
- Stores driver's ride offers
- Links to drivers via `driver_id`
- Status: active/completed/cancelled

### ✅ Bookings Table
- Student booking requests
- Links to student and ride
- Status: pending/confirmed/cancelled
- Unique constraint: can't book same ride twice

### ✅ Ratings Table
- Student reviews of drivers
- 1-5 star rating + comment
- Only for confirmed bookings
- Unique constraint: can't rate same ride twice

### ✅ Users Table (Modified)
- Added `role` column: student/driver
- Can now have relationships to rides, bookings, ratings

---

## Validation Rules

### Ride Creation
- `starting_point`: required, max 255 chars
- `destination`: required, max 255 chars
- `departure_date`: required, must be today or later
- `departure_time`: required, format HH:MM:SS
- `available_seats`: required, 1-10 seats
- `price_per_seat`: required, 0-1000 currency units

### Booking
- `ride_id`: must exist and be active
- Must have available seats
- Student can't book same ride twice
- Student can't book own ride

### Rating
- `booking_id`: must exist and be confirmed
- `rating`: required, 1-5 stars
- `comment`: optional, max 1000 chars
- Student can't rate same ride twice

---

## HTTP Status Codes Used

| Code | Meaning | Example |
|------|---------|---------|
| 200 | OK (GET/PUT successful) | Get ride details |
| 201 | Created (POST successful) | Ride created |
| 400 | Bad Request (validation error) | Missing destination |
| 403 | Forbidden (unauthorized) | Driver accepting another driver's booking |
| 404 | Not Found | Ride doesn't exist |
| 422 | Unprocessable Entity | Validation errors returned |

---

## Next Steps

After Sprint 1, consider:

1. **Sprint 2:** User Authentication & Profiles
   - Login/Register endpoints
   - User profile management
   - Real notifications

2. **Sprint 3:** Payment Integration
   - Payment gateway integration
   - Booking confirmation with payment

3. **Sprint 4:** Advanced Features
   - Chat/Messaging
   - Reviews & Reports
   - GPS Tracking

---

## Useful Commands

```bash
# Run migrations
php artisan migrate

# Reset database
php artisan migrate:fresh

# Create new model with migration
php artisan make:model ModelName -m

# Create controller
php artisan make:controller MyController

# Run Tinker
php artisan tinker

# Start server
php artisan serve

# Run tests
php artisan test

# View all routes
php artisan route:list
```

---

## Troubleshooting

**"Unauthenticated" error?**
- Include Authorization header with token
- Use format: `Authorization: Bearer YOUR_TOKEN`

**"Ride not found"?**
- Check ride ID is correct
- Ensure ride is still active

**"Validation error"?**
- Check all required fields are included
- Verify date format (YYYY-MM-DD)
- Verify time format (HH:MM:SS)

**Database connection error?**
- Check `.env` file database configuration
- Run `php artisan migrate`

---

## Support & Documentation

- **Full API Docs:** [CARPOOLING_API.md](CARPOOLING_API.md)
- **Laravel Docs:** https://laravel.com/docs
- **Sanctum Docs:** https://laravel.com/docs/11.x/sanctum

---

**Last Updated:** April 5, 2026  
**Sprint:** 1 - Carpooling System  
**Status:** ✅ Complete
