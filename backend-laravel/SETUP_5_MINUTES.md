# 🚀 5-MINUTE SETUP GUIDE

**Just copy and paste these commands!**

---

## Step 1: Copy Environment File

```powershell
cd c:\laravel\projects\projet_integration
copy .env.example .env
```

---

## Step 2: Update `.env` Database Configuration

Open `.env` file and change:

```env
# FIND THIS LINE:
DB_CONNECTION=mysql

# CHANGE TO:
DB_CONNECTION=sqlite

# THEN COMMENT OUT THESE LINES (add # at start):
# DB_HOST=127.0.0.1
# DB_PORT=3306
# DB_DATABASE=laravel
# DB_USERNAME=root
# DB_PASSWORD=
```

**Save and close the file.**

---

## Step 3: Generate App Key

```powershell
php artisan key:generate
```

Expected output:
```
Application key set successfully.
```

---

## Step 4: Create SQLite Database File

```powershell
New-Item -Path database/database.sqlite -ItemType File
```

Expected output:
```
    Directory: C:\laravel\projects\projet_integration\database

Mode                 LastWriteTime         Length Name
----                 -----------         ------ ----
-a---           4/5/2026  10:30 AM              0 database.sqlite
```

---

## Step 5: Create Database Tables

```powershell
php artisan migrate
```

Expected output:
```
Migration table created successfully.
Migrating: 2014_...
Migrated: 2014_...
...
```

---

## Step 6: Fill Database with Sample Data

```powershell
php artisan db:seed
```

Expected output:
```
✅ Created 5 drivers
✅ Created 10 students
✅ Created 20 rides
✅ Created bookings
✅ Created 15 ratings

════════════════════════════════════════════════════════
✅ DATABASE SEEDING COMPLETED!
════════════════════════════════════════════════════════

📋 SAMPLE DATA CREATED:
  • Drivers: 5
  • Students: 10
  • Rides: 20
  • Bookings: 30
  • Ratings: 15

👤 TEST ACCOUNTS:
  Driver: ali@example.com / password
  Student: sarah@student.com / password
```

---

## Step 7: Start the Development Server

```powershell
php artisan serve
```

Expected output:
```
   2026-04-05 10:35  INFO  Server running on [http://127.0.0.1:8000].

  Press Ctrl+C to quit
```

---

## 🎉 You're Done! Open Your Browser

**Go to:** http://localhost:8000

**Login as Driver:**
- Email: `ali@example.com`
- Password: `password`

**Login as Student:**
- Email: `sarah@student.com`
- Password: `password`

---

## 📱 What to Test

### As Driver (ali@example.com):
1. View dashboard with stats
2. Go to "My Rides" - see 4 rides
3. Go to "Pending Bookings" - accept a booking
4. Check your average rating

### As Student (sarah@student.com):
1. Search for rides to "Sfax"
2. See Ali's ride with ⭐ rating
3. Click "Book a Seat"
4. Go to "My Bookings" - wait for approval
5. Once approved - "Rate Driver" button appears
6. Give 5 stars + write a comment
7. View "My Ratings"

---

## 🎯 Database File Location

After setup, your database is at:
```
c:\laravel\projects\projet_integration\database\database.sqlite
```

This is a single file containing everything! 📦

---

## 🆘 If Something Goes Wrong

### Error: "No such file or directory"
```powershell
# Create the file again:
New-Item -Path database/database.sqlite -ItemType File
```

### Error: "SQLSTATE[HY000]"
```powershell
# Rebuild everything:
php artisan migrate:fresh --seed
```

### Error: "Base table already exists"
```powershell
# Reset and rebuild:
php artisan migrate:reset
php artisan migrate
php artisan db:seed
```

### Server won't start
```powershell
# Check if port 8000 is in use, start on different port:
php artisan serve --port=8001
# Then open: http://localhost:8001
```

---

## ✅ Verification Checklist

- [ ] `.env` file exists
- [ ] `DB_CONNECTION=sqlite` in `.env`
- [ ] `database/database.sqlite` file exists
- [ ] `php artisan migrate` completed
- [ ] `php artisan db:seed` completed
- [ ] Server running on http://localhost:8000
- [ ] Can login as ali@example.com
- [ ] Can login as sarah@student.com
- [ ] Can search rides as student
- [ ] Can post ride as driver

---

## 📊 Database Contents After Seeding

```
Users:         15 (5 drivers + 10 students)
Rides:         20 (ready to search)
Bookings:      30 (10 pending + 20 confirmed)
Ratings:       15 (3-5 stars)
```

---

## 🎓 Documentation Files

Read these for more details:

| File | Purpose |
|------|---------|
| [SQLITE_SETUP.md](SQLITE_SETUP.md) | Detailed SQLite guide |
| [QUICK_START.md](QUICK_START.md) | Postman testing guide |
| [CARPOOLING_API.md](CARPOOLING_API.md) | API endpoint reference |
| [API_REFERENCE_CARD.md](API_REFERENCE_CARD.md) | Quick lookup card |
| [VIEWS_AND_SETUP_COMPLETE.md](VIEWS_AND_SETUP_COMPLETE.md) | Views & demo guide |

---

## ⏱️ Total Time: ~5 minutes

1️⃣ Copy .env (30 seconds)
2️⃣ Edit .env (1 minute)
3️⃣ Generate key (10 seconds)
4️⃣ Create database file (10 seconds)
5️⃣ Run migrations (1 minute)
6️⃣ Seed data (1 minute)
7️⃣ Start server (10 seconds)

**Total: ~5 minutes ✅**

---

## 🚀 You're Ready!

Your Laravel carpooling app is **fully setup and ready to demo** with:

✅ Beautiful responsive views  
✅ Real SQLite database  
✅ 20 sample rides  
✅ 15 test users  
✅ Ready-to-demo workflows  

Happy coding! 🎉
