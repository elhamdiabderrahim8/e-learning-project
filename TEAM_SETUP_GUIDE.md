# E-Learning Project - Complete Team Setup & Testing Guide

This guide is for you and your teammates to set up and test the project locally.

---

## Prerequisites

Before starting, make sure you have:

1. **XAMPP installed** (includes PHP, MySQL/MariaDB, Apache)
   - Download from: https://www.apachefriends.org
   - Default install path: C:\xampp

2. **Git installed** (to clone/pull code)
   - Download from: https://git-scm.com

3. **Any web browser** (Chrome, Firefox, Edge)

---

## Step 1: Get the Latest Code

### For the first time:
```powershell
git clone <your-repo-url> e-learning-project
cd e-learning-project
```

### For subsequent updates:
```powershell
cd c:\Users\YourUsername\e-learning-project
git pull origin main
```

---

## Step 2: Create Your Local Environment File

### Do once per machine:

1. Go to: `takizip/taki/database/`
2. Copy `.env.example` and paste in same folder
3. Rename the new copy to `.env` (exactly `.env`, not `.env.txt`)
4. Open `.env` and verify values:

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=elearning
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

**If your MySQL has a password**, update `DB_PASS`:
```env
DB_PASS=your_mysql_password
```
tafha 7kayt ili password

**Important Windows Note:** In File Explorer, enable View → Show → File name extensions so you can see real file names.

---

## Step 3: Start MySQL & Run Database Setup

### Step 3A: Start MySQL

1. Open XAMPP Control Panel
2. Click **Start** next to MySQL
3. Wait for it to turn green
4. Leave it running

### Step 3B: Run Setup Script

1. Open PowerShell
2. Go to project root:
```powershell
cd C:\Users\YourUsername\e-learning-project
```

3. Run setup script:
```powershell
powershell -ExecutionPolicy Bypass -File .\takizip\taki\database\setup_mysql.ps1
```

4. Expected output:
```
Using mysql client: C:\xampp\mysql\bin\mysql.exe
Target DB: elearning on 127.0.0.1:3306
Seed imported successfully.
MySQL setup completed successfully.
```

**If you get ERROR 2002:**
- MySQL is not running. Go back and start MySQL in XAMPP

**If script succeeds but says "Failed to import schema.sql":**
- Check DB_HOST/DB_PORT/DB_USER/DB_PASS in `.env` match your MySQL config

---

## Step 4: View Database Tables (Optional But Recommended)

### Option A: phpMyAdmin (GUI - easiest)

1. Start Apache in XAMPP
2. Open browser: http://localhost/phpmyadmin
3. In left sidebar, click database: **elearning**
4. View tables: users, tasks, reclamations, courses, enrollments, certificates

### Option B: Command Line

```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -h 127.0.0.1 -P 3306 -u root -e "USE elearning; SHOW TABLES; SELECT COUNT(*) FROM users; SELECT COUNT(*) FROM tasks; SELECT COUNT(*) FROM courses;"
```

---

## Step 5: Start the PHP Web Server

1. Open PowerShell at project root:
```powershell
cd C:\Users\YourUsername\e-learning-project
```

2. Run PHP server:
```powershell
& 'C:\xampp\php\php.exe' -S localhost:8000 -t .\takizip\taki
```

3. Expected output:
```
[TIME] PHP 8.2.12 Development Server (http://localhost:8000) started
```

4. **Keep this PowerShell window open** while testing. If you close it, the server stops.

**If port 8000 is busy**, use 8001 instead:
```powershell
& 'C:\xampp\php\php.exe' -S localhost:8001 -t .\takizip\taki
```

---

## Step 6: Verify Backend Health

1. Open browser: http://localhost:8000/pages/health.php
2. You should see JSON response:
```json
{
    "status": "success",
    "message": "Database connection successful.",
    "details": {
        "driver": "mysql",
        "current_time": "2026-03-24 18:25:04",
        "db_version": "10.4.32-MariaDB"
    }
}
```

**If you see status: error:**
- Check MySQL is running
- Check .env DB_HOST/DB_PORT/DB_USER/DB_PASS are correct
- Run setup script again

**If you cannot connect to localhost:**
- Check PHP server terminal is still running (did not close it)
- If closed, restart it with the command from Step 5

---

## Step 7: Manual Functional Testing

### Test 1: Register a New Account

1. Open: http://localhost:8000/pages/registre.php
2. Fill form:
   - First name: John
   - Last name: Doe
   - Email: john@example.com
   - Password: TestPassword123
   - Confirm password: TestPassword123
3. Click Sign up
4. Expected: Redirects to offers page and shows "Account created successfully"

### Test 2: Login

1. Open: http://localhost:8000/pages/login.php
2. Email: john@example.com
3. Password: TestPassword123
4. Click Login
5. Expected: Redirects to courses page, shows "Login successful"

### Test 3: Create Task

1. Go to: http://localhost:8000/pages/tache_a_fair.php
2. Fill task form:
   - Title: Learn PHP
   - Priority: High
   - Due date: 2026-12-31
3. Click "Add task"
4. Expected: Task appears in "A faire" (To do) column

### Test 4: Update Task Status

1. In task card, click "Update" button
2. Expected: Task moves from "A faire" → "En cours" (In progress)
3. Click "Update" again
4. Expected: Task moves from "En cours" → "Terminee" (Done)
5. Click "Update" on done task
6. Expected: Confirmation popup, then task is deleted

### Test 5: Submit Reclamation with Multiple Files

1. Go to: http://localhost:8000/pages/reclamation.php
2. Fill form:
   - Subject: Issue with login
   - Description: Cannot login with my account
   - Attachments: Select 2+ files (screenshots, logs, etc.)
3. Click "Send request"
4. Expected: Shows "Your request has been sent"
5. In phpMyAdmin, check table `reclamations` for new row

### Test 6: Update Profile

1. Go to: http://localhost:8000/pages/profil.php
2. Update:
   - First name: Jane
   - Last name: Smith
   - Language: Français (French)
3. Click "Save settings"
4. Expected: Shows "Profile updated successfully" and page changes to French

### Test 7: Delete Profile

1. On profile page, click "Delete profile"
2. Confirm popup
3. Expected: Logs out, redirects to home page
4. Try opening protected page (http://localhost:8000/pages/cours.php)
5. Expected: Redirects to login

---

## Quick Troubleshooting

| Problem | Solution |
|---------|----------|
| MySQL won't start | Check port 3306 is not in use, restart XAMPP |
| Setup script fails | Verify `.env` has correct DB credentials |
| "Cannot reach this page" on localhost:8000 | Make sure PHP server terminal is still open |
| Health returns status error | Check MySQL is running, .env is correct |
| Tasks page shows no data | Check you logged in first |
| File upload on reclamation fails | Check `takizip/taki/backend/uploads/reclamations/` folder exists and is writable |

---

## For Your Teammates

Share these steps with your team. Each person should:

1. Clone/pull latest code
2. Copy `.env.example` → `.env` (personalized per machine)
3. Start MySQL in XAMPP
4. Run setup script once
5. Start PHP server
6. Verify health endpoint
7. Run manual tests

If anyone gets stuck, they can copy the exact error message and ask for help with that specific step.

---

## Advanced: Command Cheat Sheet

**Kill all PHP servers:**
```powershell
Get-Process php -ErrorAction SilentlyContinue | Stop-Process -Force
```

**Check if MySQL is running:**
```powershell
Get-Process mysqld -ErrorAction SilentlyContinue
```

**View .env file:**
```powershell
Get-Content .\takizip\taki\database\.env
```

**Query database from terminal:**
```powershell
& 'C:\xampp\mysql\bin\mysql.exe' -h 127.0.0.1 -u root -e "SHOW DATABASES; USE elearning; SHOW TABLES;"
```

**Lint all PHP files:**
```powershell
Get-ChildItem -Path .\takizip\taki -Recurse -Filter *.php | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
```

---

## Contact for Issues

If setup or tests fail:
1. Note the exact error message
2. Mention which step you are on
3. Share your PowerShell output
4. Share your `.env` values (hide password)

---

**Last Updated:** March 24, 2026  
**Project:** e-learning (MySQL/XAMPP / Native PHP)
