<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/config/database.php';

$result = [
    'status' => 'error',
    'message' => '',
    'details' => []
];

try {
    $pdo = db();
    $stmt = $pdo->query('SELECT NOW() as current_time, version() as db_version');
    $data = $stmt->fetch();
    
    $result['status'] = 'success';
    $result['message'] = 'Connected to Supabase PostgreSQL successfully!';
    $result['details'] = [
        'current_time' => $data['current_time'],
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
