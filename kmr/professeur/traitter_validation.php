<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once __DIR__ . '/backend/config/database.php';
$pdo = db();

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $cin_e = $_POST['id_etudiant'] ?? 'VIDE';
    $id_c  = $_POST['id_cours'] ?? 'VIDE';
    $cin_p = $_SESSION['CIN'] ?? 'SESSION_VIDE';

    if ($cin_e === 'VIDE' || $id_c === 'VIDE' || $cin_p === 'SESSION_VIDE') {
        die("<b style='color:red;'>Erreur : Données manquantes.</b>");
    }

    // 1. Génération du code unique
    $code = "ENJAH-" . strtoupper(substr(md5(uniqid()), 0, 8));

    // 2. Préparation du nom du fichier PDF
    $nom_fichier = "certificat_" . $code . ".pdf"; 
    // Chemin vers ton dossier de stockage (assure-toi que ce dossier existe)
    $dossier_stockage = "certificat/uploads/"; 

    try {
        $pdo->beginTransaction();

        // 3. Insertion dans la table 'certificaton' (AVEC chemin_pdf)
        $sqlCert = "INSERT INTO certificaton (code_verification, id_etudiant, id_professeur, id_cours, date_obtention, chemin_pdf) 
                    VALUES (:code, :cin_e, :cin_p, :cours, NOW(), :chemin)";
        
        $stmtCert = $pdo->prepare($sqlCert);
        $stmtCert->execute([
            ':code'   => $code,
            ':cin_e'  => $cin_e,
            ':cin_p'  => $cin_p,
            ':cours'  => $id_c,
            ':chemin' => $nom_fichier // <-- On remplit ta colonne ici !
        ]);

        // 4. Mise à jour du statut dans la table 'inscription'
        $sqlUpdate = "UPDATE inscription 
                      SET statut_certificat = 'validé' 
                      WHERE id_etudiant = :cin_e AND id_cours = :id_c";
        $stmtUpdate = $pdo->prepare($sqlUpdate);
        $stmtUpdate->execute([
            ':cin_e' => $cin_e,
            ':id_c'  => $id_c
        ]);

        // --- ÉTAPE CRUCIALE : GÉNÉRATION RÉELLE DU PDF ---
        /* Ici, tu devrais appeler une fonction qui utilise Dompdf pour 
           sauvegarder le HTML dans le dossier $dossier_stockage . $nom_fichier
           Si tu ne veux pas de fichier physique, ignore cette partie et utilise 
           la génération "à la volée" comme on a vu avant.
        */

        $pdo->commit();

        header("Location: valider_certificats.php?success=1");
        exit();

    } catch (PDOException $e) {
        $pdo->rollBack();
        die("<b style='color:red;'>ERREUR SQL :</b> " . $e->getMessage());
    }
} else {
    header("Location: valider_certificats.php");
    exit();
}