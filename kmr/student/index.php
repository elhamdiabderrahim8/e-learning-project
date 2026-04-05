<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

if (is_authenticated()) {
    logout_user();
}

require __DIR__ . '/home.html';
