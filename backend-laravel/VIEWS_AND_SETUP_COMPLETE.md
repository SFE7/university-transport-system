# MobilITé - Complete Setup & Views Guide

> Everything you need to get the carpooling app running with views and SQLite

---

## 📋 What's Included

✅ **7 Blade Views** - Professional UI with styling  
✅ **Database Seeder** - 15 test users + 20 rides + 30 bookings + 15 ratings  
✅ **SQLite Setup** - Zero-configuration file-based database  
✅ **Complete Documentation** - Step-by-step instructions  

---

## 🚀 Get Running in 5 Minutes

### 1️⃣ Configure SQLite (.env)

Edit `.env` file (or create from `.env.example`):

```env
DB_CONNECTION=sqlite
# Remove or comment out:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

### 2️⃣ Run These Commands

```bash
# Navigate to project
cd c:\laravel\projects\projet_integration

# Create database file
New-Item -Path database/database.sqlite -ItemType File

# Create database tables
php artisan migrate

# Fill with sample data (5 drivers, 10 students, 20 rides, etc.)
php artisan db:seed

# Start the server
php artisan serve
```

Server starts at: **http://localhost:8000**

### 3️⃣ Login & Test

**Test Accounts Created:**
- **Driver:** ali@example.com / password
- **Student:** sarah@student.com / password

---

## 📱 Views Created

### 1. **Layout** (`resources/views/layouts/app.blade.php`)
- Beautiful responsive design
- Navigation menu
- Consistent styling for all pages

### 2. **Dashboard** (`resources/views/dashboard.blade.php`)
Shows different content for drivers vs students:
- **Students:** Recent bookings, stats, book-ride button
- **Drivers:** My rides, pending requests, driver rating

### 3. **Search Rides** (`resources/views/rides/search.blade.php`)
- Search form (destination, date, starting point)
- Display available rides
- Show driver's rating
- Book button

### 4. **Post Ride** (`resources/views/rides/create.blade.php`)
- Form to post a new ride
- Starting point, destination, time, seats, price
- Vehicle description
- Tips for drivers

### 5. **My Bookings** (`resources/views/bookings/index.blade.php`)
- Filter tabs (All, Pending, Confirmed, Cancelled)
- Show ride details and driver info
- Accept/Reject for drivers
- Cancel/Rate buttons for students

### 6. **Rate Driver** (`resources/views/ratings/create.blade.php`)
- Star rating selector (1-5)
- Comment textarea with character counter
- Submit rating

### 7. **My Ratings** (`resources/views/ratings/index.blade.php`)
- List of all ratings given
- Average rating
- Edit/Delete options

---

## 🗄️ Sample Data Seeded

### 👥 Users (15 total)

**Drivers (5):**
```
1. Ali Ben Ahmed (ali@example.com)
2. Fatima Saleh (fatima@example.com)
3. Mohamed Khoury (mohamed@example.com)
4. Aida Ben Salim (aida@example.com)
5. Karim Gharbi (karim@example.com)
```

**Students (10):**
```
1. Sarah Mansouri (sarah@student.com)
2. Hassan Amri (hassan@student.com)
3. Leila Bouaziz (leila@student.com)
... (and 7 more)
```

### 🚗 Rides (20 total)

Created with various routes:
- ISET Bizerte → Sfax
- ISET Bizerte → Tunis
- Downtown Bizerte → Sfax
- ISET Bizerte → Sousse
- Bizerte Station → Tunis

Each with:
- Random departure dates (1-30 days from now)
- Random times
- 2-4 available seats
- 15-35 TND per seat

### 📋 Bookings (30 total)

Mix of:
- ⏳ Pending (1/3) - Waiting for driver approval
- ✅ Confirmed (2/3) - Driver accepted

### ⭐ Ratings (15 total)

Only for confirmed bookings:
- Random ratings (3-5 stars)
- Positive comments from sample phrases
- Links student to driver review

---

## 🎯 Full User Journey Example

### As a **Driver** (ali@example.com):

```
1. Login with ali@example.com / password
2. Click "Dashboard" → See "Post a Ride" button
3. Click "Post a Ride" → Fill form:
   - Starting: ISET Bizerte
   - Destination: Sfax
   - Date: 2026-04-15
   - Time: 08:00
   - Seats: 3
   - Price: 15.50 TND
   - Click "Post Ride"
4. View "My Rides" → See new ride listed
5. See "Pending Bookings" count
6. Click ride → "View Bookings"
7. Accept/Reject student requests
8. See your rating increase as students rate you
```

### As a **Student** (sarah@student.com):

```
1. Login with sarah@student.com / password
2. Click "Dashboard" → See stats
3. Click "Search Rides" → Fill form:
   - Destination: Sfax
   - Date: 2026-04-15
   - Starting: (leave empty or fill)
   - Click "Search Rides"
4. Results show available rides
5. See driver "Ali Ben Ahmed" with ⭐ 4.5 rating
6. Click "Book a Seat" → Status: Pending
7. View "My Bookings" → See booking status
8. Wait for driver to accept (or driver accepts immediately)
9. Once Confirmed → "Rate Driver" button appears
10. Click "Rate Driver" → Give 5 stars + comment
11. View "My Ratings" → See your review
```

---

## 🔄 Database Schema

```
USERS (15 total)
├── 5 Drivers (role = 'driver')
└── 10 Students (role = 'student')

RIDES (20 total)
├── By Driver 1: 4 rides
├── By Driver 2: 4 rides
└── ... (distributed among 5 drivers)

BOOKINGS (30 total)
├── Pending: 10 (⏳ waiting approval)
└── Confirmed: 20 (✅ driver accepted)

RATINGS (15 total)
└── Only for confirmed bookings
    ├── Average rating: ~4.2 stars
    └── Sample positive comments
```

---

## 📁 File Structure

```
resources/
└── views/
    ├── layouts/
    │   └── app.blade.php                    (Master layout)
    ├── dashboard.blade.php                  (Home page)
    ├── rides/
    │   ├── search.blade.php                 (Search & result)
    │   └── create.blade.php                 (Post ride form)
    ├── bookings/
    │   └── index.blade.php                  (My bookings)
    └── ratings/
        ├── create.blade.php                 (Rate driver form)
        └── index.blade.php                  (My ratings list)

database/
├── database.sqlite                          (Created after migrate)
├── migrations/
│   ├── 2026_04_05_000001_create_rides_table.php
│   ├── 2026_04_05_000002_create_bookings_table.php
│   ├── 2026_04_05_000003_create_ratings_table.php
│   └── 2026_04_05_000004_add_role_to_users_table.php
└── seeders/
    └── DatabaseSeeder.php                   (Sample data)
```

---

## 🎨 UI Features

✨ **Professional Design:**
- Responsive grid layout
- Color-coded status badges
- Star rating display
- Driver info cards
- Booking filters
- Form validation

🎯 **User-Friendly:**
- Clear navigation
- Emoji icons
- Statistics cards
- Empty states with helpful messages
- Confirmation on delete/cancel
- Character counter for comments

📱 **Responsive:**
- Works on desktop
- Works on tablet
- Works on mobile

---

## 🧪 Testing Checklist

- [ ] Create `.env` from `.env.example`
- [ ] Update `DB_CONNECTION` to `sqlite`
- [ ] Run `php artisan migrate`
- [ ] Run `php artisan db:seed`
- [ ] Run `php artisan serve`
- [ ] Login as driver: ali@example.com / password
- [ ] View dashboard and rides
- [ ] Logout and login as student: sarah@student.com / password
- [ ] Search for rides
- [ ] Book a ride
- [ ] Switch back to driver account
- [ ] Accept the booking
- [ ] Switch back to student
- [ ] Rate the driver
- [ ] View ratings

---

## 🔧 Useful Commands

```bash
# View database
php artisan tinker
User::count()              # Should be 15
Ride::count()              # Should be 20
Booking::count()           # Should be 30
Rating::count()            # Should be 15

# Reset everything (fresh start)
php artisan migrate:fresh --seed

# View specific data
User::where('role', 'driver')->get()
Ride::with('driver')->first()
Booking::where('status', 'pending')->count()
Rating::where('rating', 5)->get()
```

---

## ⚠️ Important Notes

### SQLite Vs MySQL

| Feature | SQLite | MySQL |
|---------|--------|-------|
| Setup | ✅ Auto (file) | ❌ Needs server |
| Files | 1 `.sqlite` | Multiple |
| Size | Small | Large |
| For Dev | ✅ Perfect | Overkill |
| For Prod | ❌ Not recommended | ✅ Better |

For an **end-of-year project**, SQLite is **perfect**!

### Database File

- Location: `database/database.sqlite`
- Size: ~50KB (very small)
- Format: Binary file (not human-readable)
- Backed up with: `database/`

---

## 🎓 For Your Presentation

"Our app demonstrates:"

1. ✅ **Full-featured API** (18 endpoints)
2. ✅ **Professional Views** (7 pages with styling)
3. ✅ **Real Database** (SQLite with 20+ rides, 30+ bookings)
4. ✅ **User Roles** (Students vs Drivers)
5. ✅ **Complete Workflow** (Search → Book → Accept → Rate)
6. ✅ **Responsive Design** (Works on mobile/desktop)
7. ✅ **Sample Data** (Ready to demo immediately)

---

## 📚 Full Documentation Files

- **QUICK_START.md** - Quick setup guide
- **SQLITE_SETUP.md** - Database configuration
- **CARPOOLING_API.md** - API documentation
- **API_REFERENCE_CARD.md** - Quick API lookup
- **SPRINT_1_SUMMARY.md** - Project overview

---

## ✨ Ready to Demo!

After setup:

1. **Driver Demo** (ali@example.com)
   - Show "My Rides" with 4 rides
   - Show "Pending Bookings" count
   - Show pending booking requests

2. **Student Demo** (sarah@student.com)
   - Search "Sfax" with date "2026-04-15"
   - Show search results with driver ratings
   - Book Ali's ride (status → pending)
   - Show in "My Bookings"

3. **Back to Driver**
   - Accept Sarah's booking
   - Booking status → confirmed

4. **Back to Student**
   - Show booking now confirmed
   - Click "Rate Driver"
   - Give 5 stars + comment
   - View "My Ratings"

5. **Back to Driver**
   - Show new rating added
   - Average rating increased

---

## 🚀 Next Steps

1. ✅ Setup SQLite (DONE - follow SQLITE_SETUP.md)
2. ✅ Run migrations (DONE - creates tables)
3. ✅ Seed data (DONE - create sample users/rides)
4. ✅ Show views (DONE - 7 professional pages)
5. ⏭️ Connect authentication (Not in Sprint 1)
6. ⏭️ Test all endpoints (Use Postman or cURL)

---

**Save this file and bookmark it!**

📌 Start here: [SQLITE_SETUP.md](SQLITE_SETUP.md)  
📌 API guide: [CARPOOLING_API.md](CARPOOLING_API.md)  
📌 Views demo: Follow the user journey above  

Happy coding! 🚗✨
