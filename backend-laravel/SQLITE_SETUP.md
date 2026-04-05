# SQLite Database Setup Guide

## What is SQLite?

SQLite is a lightweight, file-based database that's perfect for development and testing. No server setup needed - it stores data in a single `.sqlite` file.

---

## ✅ Setup SQLite in 3 Steps

### Step 1: Update `.env` Configuration

Edit your `.env` file in the project root:

```bash
# Change this:
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_DATABASE=laravel
DB_USERNAME=root
DB_PASSWORD=

# To this:
DB_CONNECTION=sqlite
# Remove or comment out these lines:
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

SQLite will be stored at: `database/database.sqlite`

---

### Step 2: Create the SQLite Database File

```bash
# The file will be created automatically when you run migrations
# But you can create it manually if needed:

# On Windows PowerShell:
New-Item -Path database/database.sqlite -ItemType File

# On Mac/Linux:
touch database/database.sqlite
```

---

### Step 3: Run Migrations and Seed Data

```bash
# 1. Create all tables
php artisan migrate

# 2. Fill with sample data
php artisan db:seed

# 3. (Optional) Reset everything
php artisan migrate:fresh --seed
```

---

## 🚀 Complete Setup Instructions

### For beginners (copy-paste ready):

```bash
# 1. Navigate to project
cd c:\laravel\projects\projet_integration

# 2. Copy environment file
copy .env.example .env

# 3. Generate app key
php artisan key:generate

# 4. Update .env - change DB_CONNECTION to sqlite
# Edit .env file and change DB_CONNECTION=mysql to DB_CONNECTION=sqlite

# 5. Run migrations to create tables
php artisan migrate

# 6. Seed database with sample data
php artisan db:seed

# 7. Start the server
php artisan serve
```

---

## 📝 What the Seeder Creates

After running `php artisan db:seed`, your database will have:

### 👥 Users (15 total)
- **5 Drivers:**
  - ali@example.com / password
  - fatima@example.com / password
  - mohamed@example.com / password
  - aida@example.com / password
  - karim@example.com / password

- **10 Students:**
  - sarah@student.com / password
  - hassan@student.com / password
  - leila@student.com / password
  - omar@student.com / password
  - nadia@student.com / password
  - bilel@student.com / password
  - yasmin@student.com / password
  - rami@student.com / password
  - amina@student.com / password
  - tarek@student.com / password

### 🚗 Data
- 20 active rides
- 30 bookings (mix of pending and confirmed)
- 15 ratings and reviews

---

## 🔧 SQLite File Location

```
projet_integration/
├── database/
│   ├── database.sqlite          ← Your database file (auto-created)
│   ├── migrations/
│   └── seeders/
├── .env
└── artisan
```

---

## 🧪 Testing Your Setup

### 1. Check if database exists
```bash
# On Windows PowerShell:
Get-ChildItem database\*.sqlite

# Output should show: database.sqlite
```

### 2. Check database contents
```bash
# Open Tinker
php artisan tinker

# View users
User::all()

# View rides
Ride::all()

# View bookings
Booking::all()

# Exit
exit
```

### 3. Test with the web app
```bash
# Start server
php artisan serve

# Visit in browser: http://localhost:8000/dashboard
# Login with: ali@example.com / password
```

---

## 📊 Database Tables Created

### users
```
id | role | name | email | password | email_verified_at | created_at | updated_at
```

### rides
```
id | driver_id | starting_point | destination | departure_date | departure_time | 
available_seats | price_per_seat | status | vehicle_description | created_at | updated_at
```

### bookings
```
id | ride_id | student_id | status | created_at | updated_at
```

### ratings
```
id | ride_id | student_id | driver_id | booking_id | rating | comment | created_at | updated_at
```

---

## 🔄 Reset Database (Start Fresh)

If you want to erase everything and start over:

```bash
# Reset migrations and seed with new data
php artisan migrate:fresh --seed

# Or manually:
php artisan migrate:reset       # Remove all tables
php artisan migrate             # Recreate tables
php artisan db:seed            # Fill with sample data
```

---

## ⚠️ Common Issues

### Issue: "No such file or directory: database.sqlite"
**Solution:**
```bash
# Create the file
New-Item -Path database/database.sqlite -ItemType File

# Then run migrations
php artisan migrate
```

### Issue: "SQLSTATE[HY000]: General error: 2 no such table"
**Solution:** You forgot to run migrations
```bash
php artisan migrate
```

### Issue: "Base table or view already exists"
**Solution:** Reset and reseed
```bash
php artisan migrate:fresh --seed
```

### Issue: "Access denied for database.sqlite"
**Solution:** Check file permissions
```bash
# On Windows (usually automatic)
# On Mac/Linux:
chmod 666 database/database.sqlite
chmod 777 database/
```

---

## 🎯 Quick Reference

```bash
# Create blank database
php artisan migrate

# Create database with sample data
php artisan migrate --seed

# Reset everything and reseed
php artisan migrate:fresh --seed

# View all migrations
php artisan migrate:status

# Rollback last migration
php artisan migrate:rollback

# Rollback all migrations
php artisan migrate:reset

# Seed only (if tables exist)
php artisan db:seed
```

---

## 📁 SQLite Browser Tools (Optional)

To visually browse your SQLite database, you can use:

### Free Online:
- **DBBrowser (SQLite):** https://sqlitebrowser.org/ - Desktop app
- **Adminer:** Free web-based tool

### In Laravel:
```bash
# Laravel provides tinker for quick queries
php artisan tinker

# Then in Tinker:
User::count()
Ride::with('driver')->first()
Booking::where('status', 'pending')->get()
Rating::avg('rating')
```

---

## ✨ After Setup - Next Steps

1. **Start server:** `php artisan serve`
2. **Login as driver:** ali@example.com / password
3. **Post a ride:** Navigate to "Post a Ride"
4. **Open incognito:** Login as student: sarah@student.com / password
5. **Search and book:** Search the driver's ride and book it
6. **Accept booking:** Switch back to driver and accept booking
7. **Rate:** Switch to student and rate the driver

---

## 🎓 For Your Professors

Key points about SQLite setup:
- ✅ **Zero configuration** - no database server needed
- ✅ **File-based** - entire database in one `.sqlite` file
- ✅ **Development-friendly** - perfect for prototyping
- ✅ **Portable** - easy to backup and share
- ✅ **Laravel integrated** - native support

---

## 📞 Troubleshooting Commands

```bash
# Check current database config
php artisan config:show | grep -i database

# List all database tables
php artisan tinker
Schema::getTables()

# Check last migration status
php artisan migrate:status

# Test database connection
php artisan db:table users
```

---

**Last Updated:** April 5, 2026  
**SQLite Version:** Included with PHP  
**Laravel Version:** 11.x
