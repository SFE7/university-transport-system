<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\Ride;
use App\Models\Booking;
use App\Models\Rating;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

/**
 * DatabaseSeeder
 *
 * This seeder creates sample data for testing the carpooling system.
 *
 * Data created:
 * - 5 drivers
 * - 10 students
 * - 20 rides
 * - 30 bookings (mix of pending and confirmed)
 * - 15 ratings
 *
 * Run with: php artisan db:seed
 */
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // Create 5 drivers
        $drivers = [
            [
                'name' => 'Ali Ben Ahmed',
                'email' => 'ali@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
            [
                'name' => 'Fatima Saleh',
                'email' => 'fatima@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
            [
                'name' => 'Mohamed Khoury',
                'email' => 'mohamed@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
            [
                'name' => 'Aida Ben Salim',
                'email' => 'aida@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
            [
                'name' => 'Karim Gharbi',
                'email' => 'karim@example.com',
                'password' => bcrypt('password'),
                'role' => 'driver'
            ],
        ];

        foreach ($drivers as $driverData) {
            User::create($driverData);
        }

        echo "✅ Created 5 drivers\n";

        // Create 10 students
        $students = [
            ['name' => 'Sarah Mansouri', 'email' => 'sarah@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Hassan Amri', 'email' => 'hassan@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Leila Bouaziz', 'email' => 'leila@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Omar Naceur', 'email' => 'omar@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Nadia Zahra', 'email' => 'nadia@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Bilel Haddad', 'email' => 'bilel@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Yasmin Ferjani', 'email' => 'yasmin@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Rami Turki', 'email' => 'rami@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Amina Souissi', 'email' => 'amina@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
            ['name' => 'Tarek Maamouri', 'email' => 'tarek@student.com', 'password' => bcrypt('password'), 'role' => 'student'],
        ];

        foreach ($students as $studentData) {
            User::create($studentData);
        }

        echo "✅ Created 10 students\n";

        // Get created users
        $drivers = User::where('role', 'driver')->get();
        $students = User::where('role', 'student')->get();

        // Popular routes
        $routes = [
            [
                'starting_point' => 'ISET Bizerte',
                'destination' => 'Sfax',
                'vehicle' => 'White Toyota Corolla, Plate TUN-123'
            ],
            [
                'starting_point' => 'ISET Bizerte',
                'destination' => 'Tunis',
                'vehicle' => 'Red Mercedes C-Class, Plate TUN-456'
            ],
            [
                'starting_point' => 'Downtown Bizerte',
                'destination' => 'Sfax',
                'vehicle' => 'Blue Hyundai i30, Plate TUN-789'
            ],
            [
                'starting_point' => 'ISET Bizerte',
                'destination' => 'Sousse',
                'vehicle' => 'Silver Renault Clio, Plate TUN-321'
            ],
            [
                'starting_point' => 'Bizerte Station',
                'destination' => 'Tunis',
                'vehicle' => 'Black Honda CR-V, Plate TUN-654'
            ],
        ];

        // Create 20 rides
        $rides = [];
        for ($i = 0; $i < 20; $i++) {
            $route = $routes[$i % count($routes)];
            $driver = $drivers[$i % count($drivers)];

            $ride = Ride::create([
                'driver_id' => $driver->id,
                'starting_point' => $route['starting_point'],
                'destination' => $route['destination'],
                'departure_date' => now()->addDays(rand(1, 30))->format('Y-m-d'),
                'departure_time' => sprintf('%02d:%02d:00', rand(6, 20), [0, 30][rand(0, 1)]),
                'available_seats' => rand(2, 4),
                'price_per_seat' => rand(15, 35) + (rand(0, 1) ? 0.5 : 0),
                'vehicle_description' => $route['vehicle'],
                'status' => 'active'
            ]);

            $rides[] = $ride;
        }

        echo "✅ Created 20 rides\n";

        // Create 30 bookings
        $bookings = [];
        for ($i = 0; $i < 30; $i++) {
            $ride = $rides[$i % count($rides)];
            $student = $students[$i % count($students)];

            // Check if student already booked this ride
            $existingBooking = Booking::where('ride_id', $ride->id)
                ->where('student_id', $student->id)
                ->first();

            if (!$existingBooking) {
                $booking = Booking::create([
                    'ride_id' => $ride->id,
                    'student_id' => $student->id,
                    'status' => rand(1, 3) === 1 ? 'pending' : 'confirmed', // 2/3 confirmed, 1/3 pending
                ]);

                $bookings[] = $booking;
            }
        }

        echo "✅ Created bookings\n";

        // Create 15 ratings (only for confirmed bookings)
        $confirmedBookings = Booking::where('status', 'confirmed')->take(15)->get();

        foreach ($confirmedBookings as $booking) {
            Rating::create([
                'ride_id' => $booking->ride->id,
                'student_id' => $booking->student->id,
                'driver_id' => $booking->ride->driver->id,
                'booking_id' => $booking->id,
                'rating' => rand(3, 5), // 3-5 stars
                'comment' => $this->getRandomComment(),
            ]);
        }

        echo "✅ Created 15 ratings\n";
        echo "\n";
        echo "════════════════════════════════════════════════════════\n";
        echo "✅ DATABASE SEEDING COMPLETED!\n";
        echo "════════════════════════════════════════════════════════\n\n";
        echo "📋 SAMPLE DATA CREATED:\n";
        echo "  • Drivers: " . $drivers->count() . "\n";
        echo "  • Students: " . $students->count() . "\n";
        echo "  • Rides: " . Ride::count() . "\n";
        echo "  • Bookings: " . Booking::count() . "\n";
        echo "  • Ratings: " . Rating::count() . "\n\n";
        echo "👤 TEST ACCOUNTS:\n";
        echo "  Driver: ali@example.com / password\n";
        echo "  Student: sarah@student.com / password\n\n";
    }

    /**
     * Get random positive comment
     */
    private function getRandomComment(): string
    {
        $comments = [
            'Great driver! Very safe and friendly. Car was clean and comfortable.',
            'Excellent experience! The driver was punctual and professional.',
            'Really nice ride. Good conversation and smooth driving.',
            'Perfect! Driver was helpful and the car was in great condition.',
            'Very satisfied with this carpool. Will book again!',
            'Friendly atmosphere and comfortable car. Great value for money.',
            'Outstanding service! Highly recommend this driver.',
            'The driver was very careful on the road. Safe and reliable.',
            'Beautiful journey! Driver was accommodating and courteous.',
            'Excellent! Clean car, good music, professional driver.',
            'Amazing experience. Driver knew the best routes and was very helpful.',
            'Very happy with the booking. Smooth and pleasant ride.',
            'Great driver with beautiful car. Very professional service.',
            'Highly recommend! Safety and comfort were the priority.',
            'Wonderful ride! Driver is very attentive and punctual.',
        ];

        return $comments[array_rand($comments)];
    }
}
