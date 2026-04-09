# Admin Panel - Enjah E-Learning

## Setup

### 1. Import SQL migration
Run `chat_migration.sql` in your MySQL database (phpMyAdmin or CLI):
```sql
SOURCE /path/to/chat_migration.sql;
```

### 2. Admin credentials
Default login is in `login.php` — change these before deploying:
```php
define('ADMIN_EMAIL',    'admin@enjah.com');
define('ADMIN_PASSWORD', 'admin123');
```

### 3. Access
Go to: `http://localhost/projet/admin/login.php`

## Features
- Login protected (email + password)
- Dashboard with stats
- Students list — search + delete
- Professors list — search + delete
- Payments — approve payments
- Support Chat — real-time chat with students/professors

## Adding chat widget to student/professor pages
Include this at the bottom of any page, after setting the user vars:
```php
<?php
$chat_user_id   = $_SESSION['CIN'];          // user's CIN
$chat_user_type = 'etudiant';                // or 'professeur'
$chat_user_name = $_SESSION['nom'] . ' ' . $_SESSION['prenom'];
require_once '/path/to/admin/chat_widget.php';
?>
```
