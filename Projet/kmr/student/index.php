<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

$requestedLang = (string) ($_GET['lang'] ?? '');
if ($requestedLang === 'en' || $requestedLang === 'fr') {
    $_SESSION['preferred_language'] = $requestedLang;
    $_SESSION['preferred_language_synced'] = false;
    redirect('index.php');
}

if (is_authenticated()) {
    logout_user();
}

require __DIR__ . '/home.php';
