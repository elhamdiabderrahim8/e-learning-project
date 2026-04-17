<?php

if (session_status() === PHP_SESSION_NONE) {
    session_set_cookie_params([
        'lifetime' => 86400 * 30,
        'path' => '/',
        'httponly' => true,
        'samesite' => 'Lax',
    ]);
    session_start();
    
    // Refresh session expiration on each request
    if (ini_get('session.gc_probability') == '0') {
        // If garbage collection is disabled, manually handle expiration
        if (isset($_SESSION['__lastactivity'])) {
            $inactivity = time() - $_SESSION['__lastactivity'];
            if ($inactivity > 86400 * 30) { // 30 days of inactivity
                session_regenerate_id(true);
                $_SESSION = [];
                session_destroy();
            }
        }
    }
    $_SESSION['__lastactivity'] = time();
}
