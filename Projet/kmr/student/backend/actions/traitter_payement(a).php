<?php
// 1. Initialisation sécurisée de la session
if (session_status() === PHP_SESSION_NONE) { 
    session_start(); 
}

// Correction du chemin vers bootstrap
require_once __DIR__ . '/../includes/bootstrap.php';
require_auth(); 

$pdo = db();

$id_cours = isset($_POST['course_id']) ? (int)$_POST['course_id'] : 0;
$cin_etudiant = isset($_SESSION['CIN']) ? (int)$_SESSION['CIN'] : 0; 

if ($id_cours === 0 || $cin_etudiant === 0) {
    die("Erreur : Données invalides.");
}

try {
    $sql = "INSERT INTO inscription (id_etudiant, id_cours, methode_paiement, date_achat) 
            VALUES (:cin, :id_c, 'Carte Bancaire', NOW())";
    
    $stmt = $pdo->prepare($sql);
    $success = $stmt->execute([
        'cin'  => $cin_etudiant,
        'id_c' => $id_cours
    ]);

    if ($success) {
        /**
         * CORRECTION DU CHEMIN : 
         * Si tu es dans backend/actions/
         * ../ remonte vers backend/
         * ../../ remonte à la racine
         * donc : ../../pages/offres.php est correct SI 'pages' est à la racine.
         * On utilise ob_clean() pour s'assurer qu'aucun espace ne bloque le header.
         */
        ob_clean();
        header('Location: ../../pages/offres.php#step-success');
        exit();
    }

} catch (PDOException $e) {
    // Gestion de l'erreur 1452 (contrainte d'intégrité)
    ?>
    <div style="font-family:sans-serif; padding:20px; border:2px solid #e74c3c; background:#fdf2f2; border-radius:10px; max-width:600px; margin:50px auto; text-align:center;">
        <h2 style="color:#c0392b;">Action Interdite</h2>
        <p>Votre compte (CIN: <strong><?php echo $cin_etudiant; ?></strong>) n'est pas enregistré comme <strong>Étudiant</strong>.</p>
        <p style="font-size:0.9em; color:#666;">Note : Les professeurs ne peuvent pas acheter de cours.</p>
        <hr style="border:0; border-top:1px solid #eee; margin:20px 0;">
        <a href="../../pages/login.php" style="display:inline-block; background:#3498db; color:white; padding:10px 20px; text-decoration:none; border-radius:5px;">Changer de compte</a>
    </div>
    <?php
    die();
}