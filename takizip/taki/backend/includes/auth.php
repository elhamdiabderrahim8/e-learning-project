<?php

declare(strict_types=1);

require_once __DIR__ . '/helpers.php';

function user_id(): ?int
{
    return isset($_SESSION['user_id']) ? (int) $_SESSION['user_id'] : null;
}

function is_authenticated(): bool
{
    return user_id() !== null;
}

function require_auth(): void
{
    if (!is_authenticated()) {
        set_flash('error', 'Veuillez vous connecter pour continuer.');
        redirect('/login.php');
    }
}

function login_user(int $id, string $fullName): void
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = $id;
    $_SESSION['full_name'] = $fullName;
}

function logout_user(): void
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }

    session_destroy();
}
