<?php

declare(strict_types=1);

require_once __DIR__ . '/../database/database.php';

$result = [
    'status' => 'error',
    'message' => '',
    'details' => []
];

try {
    $pdo = db();
    $stmt = $pdo->query('SELECT NOW() AS db_now, VERSION() AS db_version');
    $data = $stmt->fetch();
    
    $result['status'] = 'success';
    $result['message'] = 'Database connection successful.';
    $result['details'] = [
        'driver' => $pdo->getAttribute(PDO::ATTR_DRIVER_NAME),
        'current_time' => $data['db_now'],
        'db_version' => substr($data['db_version'], 0, 50),
    ];
} catch (Exception $e) {
    $result['message'] = $e->getMessage();
    $result['details'] = [
        'file' => $e->getFile(),
        'line' => $e->getLine(),
    ];
}

header('Content-Type: application/json');
echo json_encode($result, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);


