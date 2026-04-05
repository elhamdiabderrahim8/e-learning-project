<?php

declare(strict_types=1);

require_once __DIR__ . '/../backend/includes/bootstrap.php';

if (is_authenticated()) {
    redirect('cours.php');
}

redirect('login.php');
