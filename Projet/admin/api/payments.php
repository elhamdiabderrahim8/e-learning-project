<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../../student/database/database.php';
require_once __DIR__ . '/../../student/database/migrate_inscription.php';

try {
    $pdo = db();
    ensure_inscription_columns($pdo);
    $sql = "SELECT i.id_inscription, i.id_etudiant, i.id_cours, i.date_achat, i.methode_paiement, i.paiement_valide,
                   e.nom AS nom_etudiant, e.prenom AS prenom_etudiant,
                   c.nom_cours, p.nom AS nom_prof, p.prenom AS prenom_prof
            FROM inscription i
            LEFT JOIN etudiant e ON e.CIN=i.id_etudiant
            LEFT JOIN cours c ON c.id=i.id_cours
            LEFT JOIN professeur p ON p.CIN=c.id_professeur
            ORDER BY i.date_achat DESC";
    $result = $pdo->query($sql);
    $rows = $result->fetchAll(PDO::FETCH_ASSOC);
    echo json_encode($rows);
} catch (Throwable $e) {
    error_log('Payments API error: ' . $e->getMessage());
    http_response_code(500);
    echo json_encode(['error' => 'Erreur lors du chargement des paiements.']);
}
