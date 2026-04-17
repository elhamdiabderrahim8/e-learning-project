<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';

if (is_logged_in()) {
    header('Location: cours.php');
    exit;
}

header('Location: login.php');
exit;
