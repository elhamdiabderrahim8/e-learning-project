# e-learning-project

Backend PHP + SQL connected to Supabase Postgres for the Smart Learning site.

## Stack

- PHP (native, no framework)
- Supabase PostgreSQL
- HTML + CSS existing frontend

## What was added

- Authentication: register, login, logout
- Protected pages with session check
- Reclamation form storage (with optional file upload)
- Tasks management (create + mark complete/incomplete)
- SQL schema and seed files

## Project paths

- App root: takizip/taki
- Backend code: takizip/taki/backend
- SQL schema: takizip/taki/backend/sql/schema.sql
- SQL seed: takizip/taki/backend/sql/seed.sql

## Database setup (Supabase)

### Quick Setup

1. **Environment variables are already configured** in `.env` file (takizip/taki/.env).

2. **Load SQL schema.** In Supabase SQL Editor:
   - Click "New Query"
   - Copy-paste entire content from: `takizip/taki/backend/sql/schema.sql`
   - Click "Run"

3. **Load demo data (optional).** In Supabase SQL Editor:
   - Click "New Query"
   - Copy-paste entire content from: `takizip/taki/backend/sql/seed.sql`
   - Click "Run"

4. **Verify connection.** Start the app and check health:
   ```
   cd takizip/taki
   php -S localhost:8000
   # Open: http://localhost:8000/health.php
   ```
   You should see: `{"status": "success", ...}`

### Current Configuration

The `.env` file contains your Supabase credentials:
- SUPABASE_DB_HOST
- SUPABASE_DB_PORT
- SUPABASE_DB_NAME
- SUPABASE_DB_USER
- SUPABASE_DB_PASS
- SUPABASE_DB_SSLMODE

Fallback variables DB_HOST, DB_PORT, etc. are also supported for local MySQL testing.

## Run locally

From repository root:

```bash
cd takizip/taki
php -S localhost:8000
```

Open in browser:

http://localhost:8000

### Health Check

To verify Supabase connection is working:

http://localhost:8000/health.php

Should return JSON with `"status": "success"`

## Main PHP pages

- login.php
- registre.php
- cours.php
- tache_a_fair.php
- reclamation.php
- offres.php
- calendrier.php
- certificats.php

## Backend actions

- backend/actions/register.php
- backend/actions/login.php
- backend/actions/logout.php
- backend/actions/create_reclamation.php
- backend/actions/create_task.php
- backend/actions/toggle_task.php

## Setup Files

- `.env` - Database credentials (DO NOT commit to git)
- `health.php` - Connection verification endpoint
- `SETUP_SUPABASE.sh` - Setup guide (Linux/Mac)
- `SETUP_SUPABASE.ps1` - Setup guide (Windows PowerShell)
- `backend/config/database.php` - PDO Postgres connection
- `backend/sql/schema.sql` - PostgreSQL schema (run in Supabase SQL Editor)
- `backend/sql/seed.sql` - Demo data (run in Supabase SQL Editor)

## Important note

This backend now targets Supabase PostgreSQL using only PHP + SQL.
Database credentials are stored in `.env` and loaded automatically by the app.
