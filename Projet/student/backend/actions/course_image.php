<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
if ($courseId <= 0) {
    http_response_code(404);
    exit;
}

try {
    $pdo = db();
    $stmt = $pdo->prepare('SELECT image_type, image_data FROM cours WHERE id = :id LIMIT 1');
    $stmt->execute(['id' => $courseId]);
    $course = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$course || empty($course['image_data'])) {
        http_response_code(404);
        exit;
    }

    $contentType = (string) ($course['image_type'] ?? 'image/jpeg');
    if ($contentType === '') {
        $contentType = 'image/jpeg';
    }

    header('Content-Type: ' . $contentType);
    header('Content-Length: ' . strlen((string) $course['image_data']));
    header('Cache-Control: public, max-age=86400, stale-while-revalidate=604800');
    header('X-Content-Type-Options: nosniff');

    echo $course['image_data'];
} catch (Throwable $e) {
    http_response_code(500);
}
