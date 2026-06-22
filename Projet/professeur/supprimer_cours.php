<?php
session_start();
header('Content-Type: application/json; charset=UTF-8');

if (!isset($_SESSION['CIN'])) {
    http_response_code(401);
    echo json_encode([
        'success' => false,
        'message' => 'Session invalide.',
    ]);
    exit();
}

$courseId = isset($_GET['id']) ? (int) $_GET['id'] : 0;
$profId = (int) $_SESSION['CIN'];

if ($courseId <= 0) {
    http_response_code(400);
    echo json_encode([
        'success' => false,
        'message' => 'Cours invalide.',
    ]);
    exit();
}

require_once __DIR__ . '/../student/database/database.php';
try {
    $pdo = db();
} catch (Exception $e) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Connexion base de donnees impossible.',
    ]);
    exit();
}

try {
    $check = $pdo->prepare('SELECT id FROM cours WHERE id = :courseId AND id_professeur = :profId LIMIT 1');
    $check->execute(['courseId' => $courseId, 'profId' => $profId]);
    $course = $check->fetch(PDO::FETCH_ASSOC);

    if (!$course) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Cours introuvable ou non autorise.',
        ]);
        exit();
    }

    $pdo->beginTransaction();

    $deleteCertificate = $pdo->prepare('DELETE FROM certificaton WHERE id_cours = :courseId');
    $deleteCertificate->execute(['courseId' => $courseId]);

    $deleteProgress = $pdo->prepare('DELETE FROM suivi_lecons WHERE id_cours = :courseId');
    $deleteProgress->execute(['courseId' => $courseId]);

    $deleteEnrollments = $pdo->prepare('DELETE FROM inscription WHERE id_cours = :courseId');
    $deleteEnrollments->execute(['courseId' => $courseId]);

    $deleteLessons = $pdo->prepare('DELETE FROM lecon WHERE id_cours = :courseId');
    $deleteLessons->execute(['courseId' => $courseId]);

    $deleteCourse = $pdo->prepare('DELETE FROM cours WHERE id = :courseId AND id_professeur = :profId');
    $deleteCourse->execute(['courseId' => $courseId, 'profId' => $profId]);

    if ($deleteCourse->rowCount() < 1) {
        throw new RuntimeException('Aucun cours supprime.');
    }

    $pdo->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Cours supprime.',
    ]);
} catch (Throwable $e) {
    if ($pdo->inTransaction()) {
        $pdo->rollBack();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Suppression impossible: ' . $e->getMessage(),
    ]);
}
