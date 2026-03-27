# Setup Guide

## Prerequisites

- XAMPP (https://www.apachefriends.org)
- Git (https://git-scm.com)
- Any browser

## Installation

### 1. Clone the project
```powershell
git clone <your-repo-url> e-learning-project
cd e-learning-project
```

### 2. Create .env file
Copy `takizip/taki/database/.env.example` to `takizip/taki/database/.env`

Edit `.env` if your MySQL has a password:
```
DB_PASS=your_password_here
```

### 3. Start MySQL
Open XAMPP Control Panel and click **Start** next to MySQL (wait for green)

### 4. Run setup
```powershell
powershell -ExecutionPolicy Bypass -File takizip\taki\database\setup_mysql.ps1
```

### 5. Start the app
```powershell
cd takizip\taki
php -S localhost:8000
```

Open: http://localhost:8000

## Troubleshooting

| Problem | Solution |
|---------|----------|
| ERROR 2002 | Start MySQL in XAMPP Control Panel |
| Port 8000 in use | Use `php -S localhost:8001` instead |
| Cannot connect | Check MySQL is running + .env values are correct |
| File extensions hidden | Windows: View → Show → File name extensions |
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
