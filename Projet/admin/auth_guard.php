<?php
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off')
        || (($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https');
    session_set_cookie_params([
        'lifetime' => 0, 'path' => '/', 'secure' => $isSecure,
        'httponly' => true, 'samesite' => 'Strict',
    ]);
    ini_set('session.use_strict_mode', '1');
    session_start();
}
if (empty($_SESSION['admin_logged_in'])) {
    header('Location: login.php');
    exit();
}
if (isset($_SESSION['last_activity']) && time() - (int) $_SESSION['last_activity'] > 1800) {
    $_SESSION = [];
    session_destroy();
    header('Location: login.php?expired=1');
    exit();
}
$_SESSION['last_activity'] = time();
