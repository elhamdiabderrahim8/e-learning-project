# e-learning-project

Native PHP learning platform using XAMPP + MySQL (or MariaDB).

## Stack

- PHP (no framework)
- MySQL / MariaDB (XAMPP)
- HTML/CSS/JS templates

## Project Structure

- `takizip/taki` : app root
- `takizip/taki/pages` : page routes (`*.php`)
- `takizip/taki/backend/actions` : form and feature actions
- `takizip/taki/backend/includes` : auth/session/helpers/bootstrap
- `takizip/taki/database/database.php` : PDO connection (MySQL)
- `takizip/taki/database/schema.sql` : MySQL schema
- `takizip/taki/database/seed.sql` : optional demo data

## Main Features

- Authentication (register/login/logout)
- Protected pages with session checks
- Tasks board with statuses (`a_faire`, `en_cours`, `terminee`)
- Reclamation form with multiple file uploads
- Profile settings (name, language, account deletion)

## XAMPP Local Setup

1. Start `Apache` and `MySQL` in XAMPP Control Panel.
2. Create database (example: `elearning`) from phpMyAdmin.
3. Create `.env` from the example and configure DB credentials:
	- copy `takizip/taki/database/.env.example` -> `takizip/taki/database/.env`

```env
DB_CONNECTION=mysql
DB_HOST=127.0.0.1
DB_PORT=3306
DB_NAME=elearning
DB_USER=root
DB_PASS=
DB_CHARSET=utf8mb4
```

4. Import schema in phpMyAdmin SQL tab:
	- `takizip/taki/database/schema.sql`
5. Optional: import seed data:
	- `takizip/taki/database/seed.sql`

### One-click setup (Windows PowerShell)

From repository root:

```powershell
powershell -ExecutionPolicy Bypass -File .\takizip\taki\database\setup_mysql.ps1
```

Options:

- Skip seed import:

```powershell
powershell -ExecutionPolicy Bypass -File .\takizip\taki\database\setup_mysql.ps1 -SkipSeed
```

- Custom mysql.exe path:

```powershell
powershell -ExecutionPolicy Bypass -File .\takizip\taki\database\setup_mysql.ps1 -MysqlExe "C:\xampp\mysql\bin\mysql.exe"
```

## Run App

From repository root:

```bash
cd takizip/taki
php -S localhost:8000
```

Open:

- App: `http://localhost:8000`
- Health check: `http://localhost:8000/pages/health.php`

## Backend Test Checklist

Run these checks from repository root after setup:

1. Syntax check all PHP files:

```powershell
Get-ChildItem -Path .\takizip\taki -Recurse -Filter *.php | ForEach-Object { & 'C:\xampp\php\php.exe' -l $_.FullName }
```

2. Start server:

```powershell
& 'C:\xampp\php\php.exe' -S localhost:8000 -t .\takizip\taki
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
