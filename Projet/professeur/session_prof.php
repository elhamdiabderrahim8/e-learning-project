<?php
/**
 * Shared session bootstrap for all professeur pages.
 * Forces session cookie path='/' and secure settings via ini_set
 * so it works on ANY server (Railway, Apache, Nginx, PHP-FPM).
 */
if (session_status() === PHP_SESSION_NONE) {
    // Force settings via ini_set - works regardless of php.ini location
    ini_set('session.cookie_path', '/');
    ini_set('session.cookie_httponly', '1');
    ini_set('session.cookie_samesite', 'Lax');
    ini_set('session.gc_maxlifetime', '7200');
    ini_set('session.use_strict_mode', '1');

    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
