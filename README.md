# Enjah - E-Learning Platform

A simple PHP learning platform using XAMPP + MySQL.

## Quick Start

1. **Copy environment file:**
   ```
   takizip/taki/database/.env.example → takizip/taki/database/.env
   ```

2. **Start MySQL:** Open XAMPP Control Panel and start MySQL

3. **Run setup script:**
   ```powershell
   cd c:\Users\YourUsername\e-learning-project
   powershell -ExecutionPolicy Bypass -File takizip\taki\database\setup_mysql.ps1
   ```

4. **Start the app:**
   ```
   cd takizip/taki
   php -S localhost:8000
   ```

5. **Open in browser:** http://localhost:8000

## Features

- User login & register
- Task management board (3 columns)
- Course content
- Support reclamation form
- User profile & settings

## Folder Structure

```
takizip/taki/
├── pages/              (web pages)
├── backend/
│   ├── actions/        (form processing)
│   ├── includes/       (auth, helpers)
│   └── uploads/        (file uploads)
└── database/           (MySQL setup, schema)
```

## Tech Stack

- PHP (vanilla, no framework)
- MySQL / MariaDB
- HTML + CSS
```

3. In another terminal, verify health endpoint:

```powershell
Invoke-WebRequest -UseBasicParsing -Uri http://localhost:8000/pages/health.php
```

Expected: JSON with `"status": "success"`.

4. Manual backend flow test in browser:
- Register a new account.
- Login with that account.
- Create a task, then update status to `en_cours` and `terminee`.
- Submit a reclamation with 1+ attachments.
- Open profile and change language + name.

If setup script fails with `ERROR 2002`, start MySQL in XAMPP Control Panel first.

## Team Workflow (Free, No Paid Services)

Use GitHub + local XAMPP for each teammate.

1. Each teammate pulls latest code.
2. Each teammate uses own local MySQL database with same schema.
3. Any DB change must be added to `schema.sql` (and optionally `seed.sql`) in a branch.
4. Open Pull Request, review, merge.
5. Everyone pulls and re-imports updated schema if needed.

Quick teammate onboarding:

1. `git pull`
2. Copy `takizip/taki/database/.env.example` to `takizip/taki/database/.env`
3. Start MySQL in XAMPP
4. Run:

```powershell
powershell -ExecutionPolicy Bypass -File .\takizip\taki\database\setup_mysql.ps1
```

5. Run app:

```powershell
& 'C:\xampp\php\php.exe' -S localhost:8000 -t .\takizip\taki
```

6. Verify `http://localhost:8000/pages/health.php` returns success JSON.

Recommended branch model:

- `main` : stable
- feature branches: `feature/tasks-filter`, `feature/profile-settings`, etc.

## Notes

- Uploaded files are saved in `takizip/taki/backend/uploads/reclamations/`.
- Keep `.env` files out of git.
