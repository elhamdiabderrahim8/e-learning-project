<?php

declare(strict_types=1);

require_once __DIR__ . '/session.php';

function shared_logout(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(
            session_name(),
            '',
            time() - 42000,
            $params['path'],
            $params['domain'] ?? '',
            $params['secure'] ?? false,
            $params['httponly'] ?? true
        );
    }

    session_destroy();
}

function require_admin_auth(): void
{
    if (empty($_SESSION['admin_logged_in'])) {
        header('Location: login.php');
        exit();
    }
}

function require_session_cin(string $loginPage = 'login.php'): void
{
    if (!isset($_SESSION['CIN'])) {
        header('Location: ' . $loginPage);
        exit();
    }
}

function get_profile_display_name(string $fallback = ''): string
{
    $name = trim(($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? ''));
    return $name !== '' ? $name : $fallback;
}
