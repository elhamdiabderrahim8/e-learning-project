<?php

require_once __DIR__ . '/helpers.php';

function user_id()
{
    if (!isset($_SESSION['user_id'])) {
        return null;
    }

    return (int) $_SESSION['user_id'];
}

function is_authenticated()
{
    return user_id() !== null;
}

function require_auth()
{
    if (!is_authenticated()) {
        set_flash('error', 'Veuillez vous connecter pour continuer.');
        redirect('/pages/login.php');
    }
}

function login_user($id, $fullName)
{
    session_regenerate_id(true);
    $_SESSION['user_id'] = (int) $id;
    $_SESSION['full_name'] = (string) $fullName;
}

function logout_user()
{
    $_SESSION = [];

    if (ini_get('session.use_cookies')) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'] ?? '', $params['secure'] ?? false, $params['httponly'] ?? true);
    }

    session_destroy();
}
