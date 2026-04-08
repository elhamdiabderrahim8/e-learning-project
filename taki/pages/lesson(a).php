<?php
session_start();
require_once  __DIR__ .'/../../professeur/config/connexion.php';

// On récupère le CIN de l'étudiant en session
$cin_etudiant = $_SESSION['CIN'] ?? 0;

if (isset($_GET['id'])) {
    $id_cours = intval($_GET['id']);
    $_SESSION['id_cours_actuel'] = $id_cours;
} elseif (isset($_SESSION['id_cours_actuel'])) {
    $id_cours = $_SESSION['id_cours_actuel'];
} else {
    die("Erreur : Aucun cours sélectionné.");
}

$res = $conn->query("SELECT nom_cours FROM cours WHERE id = $id_cours");
$cours = $res->fetch_assoc();
?>

<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="../../professeur/nouvel.css">
     <link rel="stylesheet" href="../../professeur/form_cours.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="../../professeur/lesson.css">
   <style>
       /* Style pour le badge de statut */
       .status-indicator {
           font-size: 11px;
           padding: 3px 8px;
           border-radius: 12px;
           margin-top: 5px;
           display: inline-block;
       }
       .status-done { background: #d1fae5; color: #065f46; border: 1px solid #34d399; }
       .status-todo { background: #f1f5f9; color: #64748b; border: 1px solid #e2e8f0; }
       
       /* On ajuste un peu le lecon-item pour accueillir le badge */
       .lecon-info { display: flex; flex-direction: column; }
   </style>
</head>
<body>
    <main class="main-content">
        <header class="header">
            <h1>Visualiser les leçons de votre cours</h1>
        </header>
        <div class="leçons-container">
            <h3 style="text-align:center; color: #4d68e1; margin-bottom: 25px;">
                Contenu du cours : <?php echo htmlspecialchars($cours['nom_cours'] ?? 'Cours inconnu'); ?>
            </h3>

            <?php
            $sql_lecons = "SELECT * FROM lecon WHERE id_cours = $id_cours ORDER BY id_lecon ASC";
            $res_lecons = $conn->query($sql_lecons);

            if ($res_lecons && $res_lecons->num_rows > 0) {
                while ($row = $res_lecons->fetch_assoc()) {
                    $id_l = $row['id_lecon'];
                    
                    // --- LOGIQUE DE PROGRESSION : Vérifier si terminée ---
                    $check = $conn->query("SELECT id_suivi FROM suivi_lecons WHERE id_etudiant = '$cin_etudiant' AND id_lecon = $id_l");
                    $est_terminee = ($check->num_rows > 0);

                    // Icônes selon le type
                    $type = $row['type_fichier'];
                    $icon = "fas fa-file"; 
                    $btnText = "Ouvrir";

                    if (strpos($type, 'video') !== false) {
                        $icon = "fas fa-play-circle";
                        $btnText = "Regarder";
                    } elseif (strpos($type, 'pdf') !== false) {
                        $icon = "fas fa-file-pdf";
                        $btnText = "Lire PDF";
                    } elseif (strpos($type, 'audio') !== false) {
                        $icon = "fas fa-volume-up";
                        $btnText = "Écouter";
                    }
            ?>
            
            <div class="lecon-item" style="opacity: <?php echo $est_terminee ? '0.8' : '1'; ?>;">
                <div class="lecon-icon" style="color: <?php echo $est_terminee ? '#20c997' : '#4d68e1'; ?>;">
                    <i class="<?php echo $icon; ?>"></i>
                </div>
                <div class="lecon-info">
                    <h4><?php echo htmlspecialchars($row['titre']); ?></h4>
                    <span style="font-size: 0.85em; color: #666;"><?php echo htmlspecialchars($row['description']); ?></span>
                    
                    <?php if($est_terminee): ?>
                        <span class="status-indicator status-done"><i class="fas fa-check"></i> Terminée</span>
                    <?php else: ?>
                        <span class="status-indicator status-todo">À faire</span>
                    <?php endif; ?>
                </div>
                
                <a href="visualiser_leçon.php?id=<?php echo $id_l; ?>" class="btn-view" style="text-decoration: none; background: <?php echo $est_terminee ? '#20c997' : ''; ?>;">
                    <?php echo $est_terminee ? "Revoir" : $btnText; ?>
                </a>
            </div>
            <?php 
                }
            } else {
                echo "<p style='text-align: center; color: #94a3b8;'>Aucune leçon disponible.</p>";
            }
            ?>
        </div>
    </main>
</body>
</html>