<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

if (is_authenticated()) {
    redirect('pages/cours.php');
}

redirect('pages/login.php');
