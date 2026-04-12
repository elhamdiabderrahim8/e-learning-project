# Enjah Project Overview

This workspace contains three main application areas:

- `admin/` for the administration panel.
- `professeur/` for the teacher portal.
- `kmr/student/` for the student portal and its backend.

## Main entry points

- Root hub: `index.php`
- Admin login: `admin/login.php`
- Professor landing page: `professeur/index.php`
- Student landing page: `kmr/student/home.php`

## Structure notes

- `kmr/student/backend/` already has a shared bootstrap/auth/helper layer.
- `admin/` and `professeur/` still keep more of their navigation and page layout inline.
- `Projet/` and `taki/` appear to be alternate copies or mirrors of the same project family, so they should be treated as secondary until you decide which copy is the source of truth.

## Cleanup direction

1. Keep one canonical root entry point for the workspace.
2. Extract shared navigation and layout pieces into reusable partials.
3. Standardize routes and file names across the three app areas.
4. Remove or archive duplicate project copies once the canonical tree is confirmed.