<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>@yield('title', 'MobilITé Carpooling')</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f5f5;
            color: #333;
        }

        /* Navigation */
        nav {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 0;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }

        nav .container {
            max-width: 1200px;
            margin: 0 auto;
            display: flex;
            justify-content: flex-start;
            align-items: center;
            padding: 15px 20px;
        }

        nav .logo {
            color: white;
            font-size: 24px;
            font-weight: bold;
            text-decoration: none;
            margin-right: 40px;
        }

        nav .menu {
            display: flex;
            gap: 30px;
            list-style: none;
            align-items: center;
            margin: 0;
        }

        nav a {
            color: white;
            text-decoration: none;
            font-size: 14px;
            transition: opacity 0.3s;
        }

        nav a:hover {
            opacity: 0.8;
        }

        /* Main Container */
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }

        /* Content */
        .content {
            background: white;
            padding: 2rem;
            border-radius: 8px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            margin-bottom: 2rem;
        }

        h1 {
            color: #2c3e50;
            margin-bottom: 1.5rem;
            font-size: 2rem;
        }

        h2 {
            color: #34495e;
            margin-bottom: 1rem;
            margin-top: 1.5rem;
            font-size: 1.5rem;
        }

        .info-card {
            background: #ecf0f1;
            padding: 1rem;
            border-left: 4px solid #2c3e50;
            margin-bottom: 1rem;
            border-radius: 4px;
        }

        /* Form Styles */
        .form-group {
            margin-bottom: 1.5rem;
        }

        label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #2c3e50;
        }

        input, textarea, select {
            width: 100%;
            padding: 0.8rem;
            border: 1px solid #bdc3c7;
            border-radius: 4px;
            font-size: 1rem;
            font-family: inherit;
        }

        input:focus, textarea:focus, select:focus {
            outline: none;
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }

        button {
            background-color: #3498db;
            color: white;
            padding: 0.8rem 1.5rem;
            border: none;
            border-radius: 4px;
            font-size: 1rem;
            cursor: pointer;
            transition: background-color 0.3s;
        }

        button:hover {
            background-color: #2980b9;
        }

        button.secondary {
            background-color: #95a5a6;
        }

        button.secondary:hover {
            background-color: #7f8c8d;
        }

        button.danger {
            background-color: #e74c3c;
        }

        button.danger:hover {
            background-color: #c0392b;
        }

        button.success {
            background-color: #27ae60;
        }

        button.success:hover {
            background-color: #229954;
        }

        /* Ride Card */
        .ride-card, .booking-card {
            background: #f9f9f9;
            border: 1px solid #ecf0f1;
            border-radius: 6px;
            padding: 1.5rem;
            margin-bottom: 1.5rem;
            transition: box-shadow 0.3s;
        }

        .ride-card:hover, .booking-card:hover {
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
        }

        .ride-header {
            display: flex;
            justify-content: space-between;
            align-items: start;
            margin-bottom: 1rem;
        }

        .ride-route {
            font-size: 1.2rem;
            font-weight: bold;
            color: #2c3e50;
        }

        .ride-details {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 1rem;
            margin-bottom: 1rem;
        }

        .detail-item {
            display: flex;
            flex-direction: column;
        }

        .detail-label {
            font-size: 0.9rem;
            color: #7f8c8d;
            font-weight: 600;
        }

        .detail-value {
            font-size: 1rem;
            color: #2c3e50;
            margin-top: 0.3rem;
        }

        .driver-info {
            background: #ecf0f1;
            padding: 1rem;
            border-radius: 6px;
            margin-bottom: 1rem;
        }

        .driver-name {
            font-weight: bold;
            color: #2c3e50;
        }

        .rating {
            color: #f39c12;
            font-weight: bold;
        }

        .price {
            font-size: 1.3rem;
            color: #27ae60;
            font-weight: bold;
        }

        .status {
            display: inline-block;
            padding: 0.4rem 0.8rem;
            border-radius: 20px;
            font-size: 0.9rem;
            font-weight: 600;
            margin-top: 0.5rem;
        }

        .status.pending {
            background-color: #f39c12;
            color: white;
        }

        .status.confirmed {
            background-color: #27ae60;
            color: white;
        }

        .status.cancelled {
            background-color: #e74c3c;
            color: white;
        }

        .status.active {
            background-color: #3498db;
            color: white;
        }

        /* Buttons Group */
        .button-group {
            display: flex;
            gap: 0.5rem;
            margin-top: 1rem;
        }

        .button-group button {
            flex: 1;
        }

        /* Grid */
        .grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(300px, 1fr));
            gap: 1.5rem;
        }

        /* Alerts */
        .alert {
            padding: 1rem;
            border-radius: 4px;
            margin-bottom: 1rem;
        }

        .alert-success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .alert-error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .alert-info {
            background-color: #d1ecf1;
            color: #0c5460;
            border: 1px solid #bee5eb;
        }

        /* Footer */
        footer {
            background-color: #2c3e50;
            color: white;
            text-align: center;
            padding: 2rem;
            margin-top: 4rem;
        }

        /* Responsive */
        @media (max-width: 768px) {
            nav .menu {
                gap: 1rem;
            }

            .ride-details {
                grid-template-columns: 1fr;
            }

            .grid {
                grid-template-columns: 1fr;
            }

            .button-group {
                flex-direction: column;
            }
        }

        /* Empty state */
        .empty-state {
            text-align: center;
            padding: 3rem 1rem;
            color: #7f8c8d;
        }

        .empty-state h3 {
            margin: 1rem 0;
            color: #2c3e50;
        }

        /* Rating stars */
        .stars {
            color: #f39c12;
            font-size: 1.2rem;
            letter-spacing: 0.2rem;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav>
        <div class="container">
            <a href="/dashboard" class="logo">🚗 MobilITé</a>

            <ul class="menu">
                <li><a href="/dashboard">Dashboard</a></li>
                @if (auth()->user()->role === 'driver')
                    <li><a href="/rides/create">Post Ride</a></li>
                @else
                    <li><a href="/rides/search">Search Rides</a></li>
                @endif
                <li><a href="/bookings">My Bookings</a></li>
                <li><a href="/ratings">My Ratings</a></li>

                <li style="margin-left: auto; display: flex; gap: 15px; align-items: center; border-left: 1px solid rgba(255,255,255,0.2); padding-left: 20px;">
                    <span>{{ auth()->user()->name }}</span>
                    <span style="background: rgba(255,255,255,0.2); padding: 5px 12px; border-radius: 20px; font-size: 12px;">
                        {{ auth()->user()->role === 'driver' ? '👨‍💼 Driver' : '👩‍🎓 Student' }}
                    </span>
                    <form method="POST" action="{{ route('logout') }}" style="display: inline;">
                        @csrf
                        <button type="submit" style="background: rgba(255,255,255,0.3); border: none; padding: 8px 15px; border-radius: 5px; color: white; cursor: pointer; font-size: 13px;">Logout</button>
                    </form>
                </li>
            </ul>
        </div>
    </nav>

    <!-- Main Content -->
    <div class="container">
        @if (session('success'))
            <div class="alert alert-success">
                ✅ {{ session('success') }}
            </div>
        @endif

        @if (session('error'))
            <div class="alert alert-error">
                ❌ {{ session('error') }}
            </div>
        @endif

        @yield('content')
    </div>

    <!-- Footer -->
    <footer>
        <p>&copy; 2026 MobilITé - ISET Bizerte. All rights reserved.</p>
    </footer>
</body>
</html>
