# MobilITé - Sprint 1 Carpooling API Documentation

## Overview

This is the complete API documentation for **Sprint 1** of the MobilITé app, which implements a carpooling system. The API allows students to:
- Search for carpool rides
- Book a seat in a ride
- Cancel their bookings
- Rate drivers after completing a ride

And allows drivers to:
- Post new ride offers
- Manage their ride listings
- Accept or reject booking requests

---

## Setup Instructions

### 1. Run Migrations

Before using the API, you need to run the database migrations to create the necessary tables:

```bash
php artisan migrate
```

This will create:
- `users` table (with added `role` column: student/driver)
- `rides` table (driver's ride offers)
- `bookings` table (student booking requests)
- `ratings` table (student reviews of drivers)

### 2. Seed Sample Data (Optional)

You can create sample data for testing:

```bash
php artisan tinker
```

Then in the Tinker console:

```php
// Create sample users
$driver = \App\Models\User::create([
    'name' => 'Ali Ben Ahmed',
    'email' => 'driver@example.com',
    'password' => bcrypt('password'),
    'role' => 'driver'
]);

$student = \App\Models\User::create([
    'name' => 'Mohamed Salah',
    'email' => 'student@example.com',
    'password' => bcrypt('password'),
    'role' => 'student'
]);

exit
```

### 3. Start the Development Server

```bash
php artisan serve
```

The API will be available at: `http://localhost:8000/api`

---

## Authentication

All endpoints (except login/register) require authentication using **Laravel Sanctum** tokens.

### Getting an Auth Token

**Setup Sanctum (first time only):**
```bash
php artisan vendor:publish --provider="Laravel\Sanctum\SanctumServiceProvider"
php artisan migrate
```

**Create token after login:**

For now, you can manually create a token in Tinker:

```php
php artisan tinker
$user = User::find(1);
$token = $user->createToken('api-token')->plainTextToken;
echo $token;
```

**Use token in requests:** Include it in the Authorization header:

```
Authorization: Bearer {token}
```

---

## API Endpoints

### 1. RIDE ENDPOINTS

#### Search for Rides

Allows students to search available carpools by destination and date.

**Endpoint:** `GET /api/rides/search`

**Query Parameters:**
- `destination` (required): destination city/location
- `departure_date` (required): date in format YYYY-MM-DD
- `starting_point` (optional): starting location to filter by

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/rides/search?destination=Sfax&departure_date=2026-04-10" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "starting_point": "ISET Bizerte",
      "destination": "Sfax",
      "departure_time": "08:00:00",
      "departure_date": "2026-04-10",
      "available_seats": 2,
      "price_per_seat": 15.50,
      "vehicle_description": "Red Toyota Corolla",
      "driver": {
        "id": 1,
        "name": "Ali Ben Ahmed",
        "rating": 4.5
      }
    }
  ],
  "count": 1
}
```

---

#### Post a New Ride (Driver)

**Endpoint:** `POST /api/rides`

**Required Role:** driver

**Request Body:**
```json
{
  "starting_point": "ISET Bizerte",
  "destination": "Sfax",
  "departure_date": "2026-04-10",
  "departure_time": "08:00:00",
  "available_seats": 3,
  "price_per_seat": 15.50,
  "vehicle_description": "Red Toyota Corolla, Plate ABC-123"
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/api/rides" \
  -H "Authorization: Bearer YOUR_TOKEN" \
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

**Example Response (201 Created):**
```json
{
  "success": true,
  "message": "Ride created successfully",
  "data": {
    "id": 1,
    "starting_point": "ISET Bizerte",
    "destination": "Sfax",
    "departure_date": "2026-04-10",
    "departure_time": "08:00:00",
    "available_seats": 3,
    "price_per_seat": 15.50,
    "vehicle_description": "Red Toyota Corolla"
  }
}
```

---

#### View Ride Details

**Endpoint:** `GET /api/rides/{ride_id}`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/rides/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "starting_point": "ISET Bizerte",
    "destination": "Sfax",
    "departure_date": "2026-04-10",
    "departure_time": "08:00:00",
    "available_seats": 3,
    "booked_seats": 1,
    "price_per_seat": 15.50,
    "vehicle_description": "Red Toyota Corolla",
    "status": "active",
    "driver": {
      "id": 1,
      "name": "Ali Ben Ahmed",
      "email": "driver@example.com",
      "rating": 4.5
    },
    "created_at": "2026-04-05T10:30:00"
  }
}
```

---

#### Update Ride Details (Driver Only)

Can only update rides that are still active and before departure.

**Endpoint:** `PUT /api/rides/{ride_id}`

**Request Body (all optional):**
```json
{
  "departure_time": "09:00:00",
  "available_seats": 2,
  "price_per_seat": 16.00,
  "vehicle_description": "Updated car info"
}
```

**Example Request:**
```bash
curl -X PUT "http://localhost:8000/api/rides/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"available_seats": 2}'
```

---

#### Cancel a Ride (Driver Only)

**Endpoint:** `DELETE /api/rides/{ride_id}`

**Example Request:**
```bash
curl -X DELETE "http://localhost:8000/api/rides/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Ride cancelled successfully"
}
```

---

#### Get My Rides (Driver)

Get all rides posted by the authenticated driver.

**Endpoint:** `GET /api/rides/my-rides`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/rides/my-rides" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "starting_point": "ISET Bizerte",
      "destination": "Sfax",
      "departure_date": "2026-04-10",
      "departure_time": "08:00:00",
      "available_seats": 3,
      "confirmed_bookings": 1,
      "pending_bookings": 2,
      "price_per_seat": 15.50,
      "status": "active"
    }
  ]
}
```

---

### 2. BOOKING ENDPOINTS

#### Search and Book a Ride (Student)

**Endpoint:** `POST /api/bookings`

**Request Body:**
```json
{
  "ride_id": 1
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/api/bookings" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"ride_id": 1}'
```

**Example Response (201 Created):**
```json
{
  "success": true,
  "message": "Booking request created successfully",
  "data": {
    "id": 1,
    "ride_id": 1,
    "ride": {
      "starting_point": "ISET Bizerte",
      "destination": "Sfax",
      "departure_date": "2026-04-10",
      "departure_time": "08:00:00",
      "price_per_seat": 15.50
    },
    "status": "pending",
    "created_at": "2026-04-05T11:00:00"
  }
}
```

**Validation Checks:**
- Ride must exist and be active
- Must have available seats
- Student cannot book the same ride twice
- Student cannot book their own ride

---

#### View Booking Details

**Endpoint:** `GET /api/bookings/{booking_id}`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/bookings/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "ride_id": 1,
    "ride": {
      "starting_point": "ISET Bizerte",
      "destination": "Sfax",
      "departure_date": "2026-04-10",
      "departure_time": "08:00:00",
      "price_per_seat": 15.50,
      "driver_id": 1
    },
    "student": {
      "id": 2,
      "name": "Mohamed Salah",
      "email": "student@example.com"
    },
    "status": "pending",
    "has_rating": false,
    "created_at": "2026-04-05T11:00:00"
  }
}
```

---

#### List My Bookings

**Endpoint:** `GET /api/bookings`

**Query Parameters (optional):**
- `status`: filter by status (pending, confirmed, cancelled)

**For Students:** Returns their own bookings  
**For Drivers:** Returns bookings for their rides

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/bookings?status=confirmed" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ride_id": 1,
      "ride": {
        "starting_point": "ISET Bizerte",
        "destination": "Sfax",
        "departure_date": "2026-04-10",
        "departure_time": "08:00:00",
        "price_per_seat": 15.50
      },
      "student_id": 2,
      "student_name": "Mohamed Salah",
      "status": "confirmed",
      "created_at": "2026-04-05T11:00:00"
    }
  ],
  "count": 1
}
```

---

#### Accept a Booking (Driver Only)

When a driver accepts a booking, the student's booking status changes to "confirmed".

**Endpoint:** `PUT /api/bookings/{booking_id}/accept`

**Example Request:**
```bash
curl -X PUT "http://localhost:8000/api/bookings/1/accept" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Booking accepted successfully",
  "data": {
    "id": 1,
    "status": "confirmed",
    "student_name": "Mohamed Salah"
  }
}
```

**Validation Checks:**
- Only the driver of the ride can accept
- Booking must be in "pending" status
- Ride must have available seats

---

#### Reject a Booking (Driver Only)

When a driver rejects a booking, the booking status changes to "cancelled".

**Endpoint:** `PUT /api/bookings/{booking_id}/reject`

**Example Request:**
```bash
curl -X PUT "http://localhost:8000/api/bookings/1/reject" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Booking rejected successfully"
}
```

---

#### Cancel a Booking (Student Only)

**Endpoint:** `PUT /api/bookings/{booking_id}/cancel`

**Example Request:**
```bash
curl -X PUT "http://localhost:8000/api/bookings/1/cancel" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Booking cancelled successfully"
}
```

---

### 3. RATING ENDPOINTS

#### Rate a Driver (Student)

After a ride is completed (booking confirmed), a student can leave a rating and review.

**Endpoint:** `POST /api/ratings`

**Request Body:**
```json
{
  "booking_id": 1,
  "rating": 5,
  "comment": "Great driver! Very safe and friendly."
}
```

**Example Request:**
```bash
curl -X POST "http://localhost:8000/api/ratings" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{
    "booking_id": 1,
    "rating": 5,
    "comment": "Great driver! Very safe and friendly."
  }'
```

**Example Response (201 Created):**
```json
{
  "success": true,
  "message": "Rating created successfully",
  "data": {
    "id": 1,
    "booking_id": 1,
    "rating": 5,
    "comment": "Great driver! Very safe and friendly.",
    "created_at": "2026-04-10T18:00:00"
  }
}
```

**Validation Checks:**
- Booking must exist and belong to the student
- Booking must be "confirmed"
- Student can only rate once per ride
- Rating must be between 1-5 stars

---

#### View Rating Details

**Endpoint:** `GET /api/ratings/{rating_id}`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/ratings/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "id": 1,
    "booking_id": 1,
    "ride": {
      "id": 1,
      "starting_point": "ISET Bizerte",
      "destination": "Sfax",
      "departure_date": "2026-04-10",
      "departure_time": "08:00:00"
    },
    "driver": {
      "id": 1,
      "name": "Ali Ben Ahmed"
    },
    "student": {
      "id": 2,
      "name": "Mohamed Salah"
    },
    "rating": 5,
    "comment": "Great driver! Very safe and friendly.",
    "created_at": "2026-04-10T18:00:00"
  }
}
```

---

#### Get All Ratings for a Driver

**Endpoint:** `GET /api/drivers/{driver_id}/ratings`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/drivers/1/ratings" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": {
    "ratings": [
      {
        "id": 1,
        "rating": 5,
        "comment": "Great driver!",
        "student_name": "Mohamed Salah",
        "ride": {
          "starting_point": "ISET Bizerte",
          "destination": "Sfax",
          "departure_date": "2026-04-10"
        },
        "created_at": "2026-04-10T18:00:00"
      }
    ],
    "statistics": {
      "average": 4.8,
      "total": 5
    }
  }
}
```

---

#### Get My Ratings (Student)

Get all ratings given by the authenticated student.

**Endpoint:** `GET /api/ratings/my-ratings`

**Example Request:**
```bash
curl -X GET "http://localhost:8000/api/ratings/my-ratings" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "data": [
    {
      "id": 1,
      "ride_id": 1,
      "rating": 5,
      "comment": "Great experience!",
      "driver": {
        "id": 1,
        "name": "Ali Ben Ahmed"
      },
      "ride": {
        "starting_point": "ISET Bizerte",
        "destination": "Sfax",
        "departure_date": "2026-04-10"
      },
      "created_at": "2026-04-10T18:00:00"
    }
  ],
  "count": 1
}
```

---

#### Update a Rating (Student Only)

Update your own rating.

**Endpoint:** `PUT /api/ratings/{rating_id}`

**Request Body (all optional):**
```json
{
  "rating": 4,
  "comment": "Updated comment..."
}
```

**Example Request:**
```bash
curl -X PUT "http://localhost:8000/api/ratings/1" \
  -H "Authorization: Bearer YOUR_TOKEN" \
  -H "Content-Type: application/json" \
  -d '{"rating": 4}'
```

---

#### Delete a Rating (Student Only)

**Endpoint:** `DELETE /api/ratings/{rating_id}`

**Example Request:**
```bash
curl -X DELETE "http://localhost:8000/api/ratings/1" \
  -H "Authorization: Bearer YOUR_TOKEN"
```

**Example Response:**
```json
{
  "success": true,
  "message": "Rating deleted successfully"
}
```

---

## Database Schema

### Users Table (Modified)
```sql
id | role | name | email | password | email_verified_at | created_at | updated_at
```

**Columns:**
- `role`: enum('student', 'driver') - user type
- Other columns: standard Laravel user fields

---

### Rides Table
```sql
id | driver_id | starting_point | destination | departure_date | departure_time | 
available_seats | price_per_seat | status | vehicle_description | created_at | updated_at
```

**Columns:**
- `driver_id`: foreign key to users (who posted the ride)
- `status`: enum('active', 'completed', 'cancelled')
- `available_seats`: number of free seats
- `price_per_seat`: cost per seat in currency

---

### Bookings Table
```sql
id | ride_id | student_id | status | created_at | updated_at
```

**Columns:**
- `ride_id`: foreign key to rides
- `student_id`: foreign key to users
- `status`: enum('pending', 'confirmed', 'cancelled')
- Unique constraint: (ride_id, student_id)

---

### Ratings Table
```sql
id | ride_id | student_id | driver_id | booking_id | rating | comment | created_at | updated_at
```

**Columns:**
- `ride_id`: foreign key to rides
- `student_id`: foreign key to users (who left the rating)
- `driver_id`: foreign key to users (who received the rating)
- `booking_id`: foreign key to bookings
- `rating`: integer (1-5)
- `comment`: text (optional review)

---

## Key Features Implemented

✅ **Feature 1:** Search for available carpool rides by destination and date
- Endpoint: `GET /api/rides/search`
- Filters by destination, departure date, and optionally starting point
- Returns available rides with driver info and ratings

✅ **Feature 2:** Driver posts a ride offer
- Endpoint: `POST /api/rides`
- Specify starting point, destination, time, seats, and price
- Only authenticated drivers can post

✅ **Feature 3:** Student books a seat in a ride
- Endpoint: `POST /api/bookings`
- Creates a booking request (initially "pending")
- Validates available seats and prevents double bookings

✅ **Feature 4:** Driver accepts or refuses booking
- Accept: `PUT /api/bookings/{id}/accept` - booking changes to "confirmed"
- Reject: `PUT /api/bookings/{id}/reject` - booking changes to "cancelled"
- Only the ride's driver can accept/refuse

✅ **Feature 5:** Student gets notification when booking is confirmed
- TODO: Implement Laravel Notifications (mentioned in controller comments)
- When driver accepts, status becomes "confirmed"

✅ **Feature 6:** Student cancels booking
- Endpoint: `PUT /api/bookings/{id}/cancel`
- Only the student who made the booking can cancel
- Changes booking status to "cancelled"

✅ **Feature 7:** Student rates driver after ride
- Endpoint: `POST /api/ratings`
- Leave 1-5 star rating with optional comment
- Only possible for "confirmed" bookings
- Can be updated or deleted later

---

## Error Handling

All endpoints return JSON responses with a `success` boolean and appropriate HTTP status codes:

**Common Status Codes:**
- `200 OK`: Successful GET/PUT request
- `201 Created`: Resource created successfully
- `400 Bad Request`: Validation error or invalid operation
- `403 Forbidden`: Unauthorized (e.g., not the right user)
- `404 Not Found`: Resource doesn't exist
- `422 Unprocessable Entity`: Validation error with field details
- `500 Internal Server Error`: Server error

**Example Error Response:**
```json
{
  "success": false,
  "message": "Validation error",
  "errors": {
    "destination": ["The destination field is required."],
    "departure_date": ["The departure date must be a valid date."]
  }
}
```

---

## Testing the API

### Using Postman

1. Create a Postman collection
2. Add each endpoint from this documentation
3. In the Authorization tab, select "Bearer Token" and add your token
4. Test each endpoint

### Using cURL

All examples in this documentation use cURL. Simply copy and modify for your needs:

```bash
curl -X GET "http://localhost:8000/api/rides/search?destination=Sfax&departure_date=2026-04-10" \
  -H "Authorization: Bearer YOUR_TOKEN_HERE"
```

---

## Next Steps for Future Sprints

1. **Authentication:** Implement login/register endpoints
2. **Notifications:** Add real-time notifications for booking status changes
3. **Payments:** Integrate payment processing for seat bookings
4. **Messaging:** Add chat between driver and student
5. **Map Integration:** Show ride routes on a map
6. **GPS Tracking:** Real-time driver location during ride
7. **Reports:** User reports and support system

---

## Code Organization

```
app/
├── Http/
│   └── Controllers/
│       ├── RideController.php        (search, post, manage rides)
│       ├── BookingController.php     (book, accept, reject, cancel)
│       └── RatingController.php      (rate driver)
└── Models/
    ├── User.php                      (users with role)
    ├── Ride.php                      (ride offers)
    ├── Booking.php                   (booking requests)
    └── Rating.php                    (driver ratings)

database/
└── migrations/
    ├── 2026_04_05_000001_create_rides_table.php
    ├── 2026_04_05_000002_create_bookings_table.php
    ├── 2026_04_05_000003_create_ratings_table.php
    └── 2026_04_05_000004_add_role_to_users_table.php

routes/
└── api.php                           (all API endpoints)
```

---

## Support

For questions or issues with the API:
1. Check the error message in the response
2. Review the validation requirements
3. Ensure you're authenticated with a valid token
4. Verify the request body matches the expected format

Happy coding! 🚗
