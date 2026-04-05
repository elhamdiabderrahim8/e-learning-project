<?php

declare(strict_types=1);

require_once __DIR__ . '/../includes/bootstrap.php';
require_auth();

$pdo = db();
$cin_etudiant = (int) ($_SESSION['CIN'] ?? 0);
$id_cours = isset($_POST['course_id']) ? (int) $_POST['course_id'] : 0;
$message = trim((string) ($_POST['message'] ?? ''));

if ($cin_etudiant <= 0 || $id_cours <= 0) {
    redirect('../../pages/offres.php');
}

try {
    // Allow only Free courses in this action.
    $check = $pdo->prepare("SELECT categorie, id_professeur, nom_cours FROM cours WHERE id = ? LIMIT 1");
    $check->execute([$id_cours]);
    $courseRow = $check->fetch(PDO::FETCH_ASSOC);
    $categorie = (string) ($courseRow['categorie'] ?? '');
    $idProf = (int) ($courseRow['id_professeur'] ?? 0);
    $courseName = (string) ($courseRow['nom_cours'] ?? '');
    if ($categorie !== 'Free' || $idProf <= 0) {
        redirect('../../pages/offres.php');
    }

    // Create requests table if missing (for professor visibility).
    $pdo->exec(
        "CREATE TABLE IF NOT EXISTS inscription_demandes (
            id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
            id_professeur INT NOT NULL,
            id_etudiant INT NOT NULL,
            id_cours INT NOT NULL,
            student_nom VARCHAR(255) NOT NULL,
            student_prenom VARCHAR(255) NOT NULL,
            student_email VARCHAR(255) NOT NULL,
            course_name VARCHAR(255) NOT NULL,
            message TEXT NULL,
            created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            UNIQUE KEY uniq_student_course (id_etudiant, id_cours),
            KEY idx_prof (id_professeur),
            KEY idx_course (id_cours)
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4"
    );

    $stmtStudent = $pdo->prepare("SELECT nom, prenom, email FROM etudiant WHERE CIN = ? LIMIT 1");
    $stmtStudent->execute([$cin_etudiant]);
    $student = $stmtStudent->fetch(PDO::FETCH_ASSOC) ?: ['nom' => '', 'prenom' => '', 'email' => ''];

    $insertReq = $pdo->prepare(
        "INSERT INTO inscription_demandes
            (id_professeur, id_etudiant, id_cours, student_nom, student_prenom, student_email, course_name, message)
         VALUES (?, ?, ?, ?, ?, ?, ?, ?)
         ON DUPLICATE KEY UPDATE message = VALUES(message), updated_at = CURRENT_TIMESTAMP"
    );
    $insertReq->execute([
        $idProf,
        $cin_etudiant,
        $id_cours,
        (string) ($student['nom'] ?? ''),
        (string) ($student['prenom'] ?? ''),
        (string) ($student['email'] ?? ''),
        $courseName,
        $message !== '' ? $message : null,
    ]);

    // Use positional placeholders (PDO MySQL doesn't reliably allow repeating named placeholders).
    $sql = "INSERT INTO inscription (id_etudiant, id_cours, methode_paiement, date_achat)
            SELECT ?, ?, 'Gratuit', NOW()
            WHERE NOT EXISTS (
                SELECT 1 FROM inscription WHERE id_etudiant = ? AND id_cours = ?
            )";
    $stmt = $pdo->prepare($sql);
    $stmt->execute([$cin_etudiant, $id_cours, $cin_etudiant, $id_cours]);
} catch (Throwable $e) {
    // If anything fails, return to offers.
    redirect('../../pages/offres.php');
}

redirect('../../pages/cours.php');
