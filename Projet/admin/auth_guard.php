<?php
session_start();
if (empty($_SESSION['admin_logged_in'])) {
    // For AJAX/API requests, return 401 JSON instead of redirect
    $isApi = (
        str_contains(($_SERVER['HTTP_ACCEPT'] ?? ''), 'application/json') ||
        str_contains(($_SERVER['SCRIPT_NAME'] ?? ''), '/api/')
    );
    if ($isApi) {
        http_response_code(401);
        header('Content-Type: application/json');
        echo json_encode(['error' => 'Unauthorized']);
        exit();
    }
    header('Location: login.php');
    exit();
}
