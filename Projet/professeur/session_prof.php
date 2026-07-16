<?php
/**
 * Shared session bootstrap for all professeur pages.
 * Forces session cookie path='/' and secure settings via ini_set
 * so it works on ANY server (Railway, Apache, Nginx, PHP-FPM).
 */
if (session_status() === PHP_SESSION_NONE) {
    $isHttps = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');

    // Force settings via ini_set - works regardless of php.ini location
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_secure', $isHttps ? '1' : '0');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', '7200');
    ini_set('session.use_strict_mode', '1');

    // KILL THE OLD COOKIE PATH to prevent browser cookie conflicts!
    if (isset($_COOKIE['PHPSESSID'])) {
        setcookie('PHPSESSID', '', time() - 3600, '/Projet/professeur/');
        setcookie('PHPSESSID', '', time() - 3600, '/Projet/professeur');
    }

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'secure'   => $isHttps,
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
