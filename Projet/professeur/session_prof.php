<?php
/**
 * Shared session bootstrap for all professeur pages.
 * Must be included BEFORE any session_start() call.
 * Sets a consistent cookie path '/' so the session
 * survives navigation across all pages.
 */
if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 0,
        'path'     => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
}
