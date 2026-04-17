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

$conn = new mysqli('localhost', 'root', '', 'elearning');
if ($conn->connect_error) {
    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Connexion base de donnees impossible.',
    ]);
    exit();
}

$conn->set_charset('utf8mb4');

try {
    $check = $conn->prepare('SELECT id FROM cours WHERE id = ? AND id_professeur = ? LIMIT 1');
    if (!$check) {
        throw new RuntimeException($conn->error);
    }

    $check->bind_param('ii', $courseId, $profId);
    $check->execute();
    $course = $check->get_result()->fetch_assoc();
    $check->close();

    if (!$course) {
        http_response_code(404);
        echo json_encode([
            'success' => false,
            'message' => 'Cours introuvable ou non autorise.',
        ]);
        $conn->close();
        exit();
    }

    $conn->begin_transaction();

    $deleteCertificate = $conn->prepare('DELETE FROM certificaton WHERE id_cours = ?');
    if ($deleteCertificate) {
        $deleteCertificate->bind_param('i', $courseId);
        if (!$deleteCertificate->execute()) {
            throw new RuntimeException($deleteCertificate->error);
        }
        $deleteCertificate->close();
    }

    $deleteProgress = $conn->prepare('DELETE FROM suivi_lecons WHERE id_cours = ?');
    if ($deleteProgress) {
        $deleteProgress->bind_param('i', $courseId);
        if (!$deleteProgress->execute()) {
            throw new RuntimeException($deleteProgress->error);
        }
        $deleteProgress->close();
    }

    $deleteEnrollments = $conn->prepare('DELETE FROM inscription WHERE id_cours = ?');
    if ($deleteEnrollments) {
        $deleteEnrollments->bind_param('i', $courseId);
        if (!$deleteEnrollments->execute()) {
            throw new RuntimeException($deleteEnrollments->error);
        }
        $deleteEnrollments->close();
    }

    $deleteLessons = $conn->prepare('DELETE FROM lecon WHERE id_cours = ?');
    if ($deleteLessons) {
        $deleteLessons->bind_param('i', $courseId);
        if (!$deleteLessons->execute()) {
            throw new RuntimeException($deleteLessons->error);
        }
        $deleteLessons->close();
    }

    $deleteCourse = $conn->prepare('DELETE FROM cours WHERE id = ? AND id_professeur = ?');
    if (!$deleteCourse) {
        throw new RuntimeException($conn->error);
    }

    $deleteCourse->bind_param('ii', $courseId, $profId);
    if (!$deleteCourse->execute()) {
        throw new RuntimeException($deleteCourse->error);
    }

    if ($deleteCourse->affected_rows < 1) {
        throw new RuntimeException('Aucun cours supprime.');
    }

    $deleteCourse->close();
    $conn->commit();

    echo json_encode([
        'success' => true,
        'message' => 'Cours supprime.',
    ]);
} catch (Throwable $e) {
    if ($conn->ping()) {
        $conn->rollback();
    }

    http_response_code(500);
    echo json_encode([
        'success' => false,
        'message' => 'Suppression impossible: ' . $e->getMessage(),
    ]);
}

$conn->close();
