<?php
// 1. Initialisation sécurisée de la session
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Correction du chemin vers bootstrap
require_once __DIR__ . '/../includes/bootstrap.php';
require_once __DIR__ . '/../../database/migrate_inscription.php';
require_auth(); 

$pdo = db();

$id_cours = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$cin_etudiant = isset($_SESSION['CIN']) ? (int)$_SESSION['CIN'] : 0; 
$cardHolder = trim((string) ($_POST['card_holder'] ?? ''));
$cardNumber = preg_replace('/\D+/', '', (string) ($_POST['card_number'] ?? ''));
$cardExpiry = trim((string) ($_POST['card_expiry'] ?? ''));
$cardCvc = preg_replace('/\D+/', '', (string) ($_POST['card_cvc'] ?? ''));

if ($id_cours === 0 || $cin_etudiant === 0) {
    header('Location: ../../pages/offres.php?payment=invalid');
    exit();
}

if ($cardHolder === '' || strlen($cardNumber) < 12 || strlen($cardExpiry) < 4 || strlen($cardCvc) < 3) {
    header('Location: ../../pages/offres.php?payment=invalid');
    exit();
}

$cardLast4 = substr($cardNumber, -4);

try {
    ensure_inscription_columns($pdo);

    $checkStmt = $pdo->prepare("SELECT id_inscription FROM inscription WHERE id_etudiant = :cin AND id_cours = :id_c LIMIT 1");
    $checkStmt->execute([
        'cin'  => $cin_etudiant,
        'id_c' => $id_cours,
    ]);
    $existingId = (int) ($checkStmt->fetchColumn() ?: 0);

    if ($existingId > 0) {
        $updateStmt = $pdo->prepare(
            "UPDATE inscription
             SET methode_paiement = 'Carte Bancaire',
                 date_achat = NOW(),
                 paiement_valide = 0,
                 card_holder = :holder,
                 card_last4 = :last4,
                 payment_status_note = NULL
             WHERE id_inscription = :id"
        );
        $updateStmt->execute([
            'holder' => $cardHolder,
            'last4'  => $cardLast4,
            'id'     => $existingId,
        ]);
    } else {
        $insertStmt = $pdo->prepare(
            "INSERT INTO inscription
                (id_etudiant, id_cours, methode_paiement, date_achat, paiement_valide, card_holder, card_last4, payment_status_note)
             VALUES
                (:cin, :id_c, 'Carte Bancaire', NOW(), 0, :holder, :last4, NULL)"
        );
        $insertStmt->execute([
            'cin'    => $cin_etudiant,
            'id_c'   => $id_cours,
            'holder' => $cardHolder,
            'last4'  => $cardLast4,
        ]);
    }

    ob_clean();
    header('Location: ../../pages/offres.php?payment=pending');
    exit();

} catch (PDOException $e) {
    error_log('Payment processing error: ' . $e->getMessage());
    header('Location: ../../pages/offres.php?payment=error');
    exit();
}