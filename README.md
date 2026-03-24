# e-learning-project

Native PHP learning platform connected to Supabase PostgreSQL.

## Stack

- PHP (no framework)
- Supabase PostgreSQL
- HTML/CSS/JS templates

## Project Structure

- `takizip/taki` : application root
- `takizip/taki/pages` : route entry pages (`*.php`)
- `takizip/taki/backend/actions` : form and feature actions
- `takizip/taki/backend/includes` : auth/session/helpers bootstrap
- `takizip/taki/supabase/schema.sql` : database schema
- `takizip/taki/supabase/seed.sql` : optional demo data
- `takizip/taki/supabase/database.php` : PDO connection

## Main Features

- Authentication (register/login/logout)
- Protected pages via session checks
- Tasks board with statuses (`a_faire`, `en_cours`, `terminee`)
- Reclamation form with multiple file uploads
- Profile settings (name, language, delete account)
- Language preference persistence (`en` default, `fr` optional)

## Current Main Pages

- `pages/login.php`
- `pages/registre.php`
- `pages/cours.php`
- `pages/tache_a_fair.php`
- `pages/reclamation.php`
- `pages/offres.php`
- `pages/profil.php`
- `pages/content.php`
- `pages/health.php`

## Setup (Supabase)

1. Configure credentials in `takizip/taki/supabase/.env`.
2. Run `takizip/taki/supabase/schema.sql` in Supabase SQL Editor.
3. Optionally run `takizip/taki/supabase/seed.sql`.

## Run Locally

From repository root:

```bash
cd takizip/taki
php -S localhost:8000
```

Open:

- App: `http://localhost:8000`
- Health check: `http://localhost:8000/pages/health.php`

Expected health response includes `"status": "success"`.

## Notes

- Uploaded files are stored under `takizip/taki/backend/uploads/reclamations/`.
- Keep `.env` files out of git.
