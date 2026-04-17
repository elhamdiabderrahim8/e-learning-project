<?php
session_start();
// Connexion à la base de données
$conn = new mysqli('localhost', 'root', '', 'elearning');

if ($conn->connect_error) {
    die("La connexion a échoué: " . $conn->connect_error);
}

if (!isset($_GET['id'])) {
    die("ID de leçon manquant.");
}

$id_lecon = intval($_GET['id']);

// RÉCUPÉRATION DE LA LEÇON
$res = $conn->query("SELECT * FROM lecon WHERE id_lecon = $id_lecon");
$lecon = $res->fetch_assoc();

if (!$lecon) {
    die("Leçon introuvable.");
}

// --- CRUCIAL : On récupère l'ID du cours pour la redirection et le suivi ---
$id_cours = intval($lecon['id_cours']); 

$chemin = "../../professeur/uploads/" . $lecon['nom_fichier'];
$type = $lecon['type_fichier'];

// Détermination de la classe dynamique
$mode_class = "mode-empty";
if (strpos($type, 'video') !== false) {
    $mode_class = "mode-video";
} elseif (strpos($type, 'pdf') !== false) {
    $mode_class = "mode-pdf";
} elseif (strpos($type, 'audio') !== false || strpos($type, 'mpeg') !== false) {
    $mode_class = "mode-audio";
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title><?php echo htmlspecialchars($lecon['titre']); ?> - Enjah</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="../../professeur/visualisation.css">
    <style>
        :root {
            --primary-blue: #4d68e1;
            --slate-dark: #1e293b;
            --white: #ffffff;
            --shadow-lg: 0 20px 50px rgba(0, 0, 0, 0.15);
        }

        body { background-color: #f4f7f6; font-family: 'Segoe UI', sans-serif; }

        .viewer-container {
            max-width: 1100px;
            margin: 20px auto;
            background: var(--white);
            border-radius: 24px;
            overflow: hidden;
            box-shadow: 0 10px 30px rgba(0,0,0,0.08);
        }

        .back-nav { padding: 20px; border-bottom: 1px solid #eee; }
        .back-link { text-decoration: none; color: var(--slate-dark); font-weight: 600; display: flex; align-items: center; gap: 8px; }

        /* ZONE MÉDIA */
        .media-box {
            display: flex; justify-content: center; align-items: center;
            min-height: 500px; width: 100%; transition: all 0.3s ease;
        }

        .mode-video { background: #0f172a; }
        .mode-video video { width: 90%; max-width: 900px; max-height: 80vh; border-radius: 12px; }

        .mode-pdf { background: #f1f5f9; padding: 40px; }
        .pdf-viewer { width: 95%; height: 80vh; border-radius: 12px; border: 8px solid white; box-shadow: var(--shadow-lg); }

        .mode-audio { background: linear-gradient(135deg, #1e293b, #4d68e1); flex-direction: column; padding: 60px; }
        .audio-icon { font-size: 80px; color: white; margin-bottom: 20px; animation: pulse 2s infinite ease-in-out; }
        .mode-audio audio { width: 100%; max-width: 500px; filter: invert(1) brightness(1.5); }

        .media-info { padding: 30px 40px; }
        .btn-primary {
            background: var(--primary-blue); color: white; border: none;
            padding: 12px 25px; border-radius: 12px; font-weight: bold;
            cursor: pointer; transition: 0.3s; display: inline-flex; align-items: center; gap: 10px;
        }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(77, 104, 225, 0.4); }

        @keyframes pulse {
            0%, 100% { transform: scale(1); opacity: 0.8; }
            50% { transform: scale(1.1); opacity: 1; }
        }
    </style>
</head>
<body>

<div class="viewer-container">
    <div class="back-nav">
        <a href="lesson(a).php?id=<?php echo $id_cours; ?>" class="back-link">
            <i class="fas fa-arrow-left"></i> Retour aux leçons
        </a>
    </div>

    <div class="media-box <?php echo $mode_class; ?>">
        <?php if ($mode_class === "mode-video"): ?>
            <video id="mainVideo" controls autoplay>
                <source src="<?php echo $chemin; ?>" type="<?php echo $type; ?>">
                Votre navigateur ne supporte pas la vidéo.
            </video>

        <?php elseif ($mode_class === "mode-pdf"): ?>
            <embed src="<?php echo $chemin; ?>#toolbar=0" type="application/pdf" class="pdf-viewer" />

        <?php elseif ($mode_class === "mode-audio"): ?>
            <div class="audio-player-wrapper">
                <div class="audio-icon">♫</div>
                <audio controls>
                    <source src="<?php echo $chemin; ?>" type="<?php echo $type; ?>">
                </audio>
            </div>

        <?php else: ?>
            <div class="empty-state" style="text-align:center; padding:50px;">
                <i class="fas fa-exclamation-triangle" style="font-size:40px; color:#e74c3c;"></i>
                <p>Format non supporté pour l'aperçu.</p>
                <a href="<?php echo $chemin; ?>" download class="btn-primary">Télécharger le fichier</a>
            </div>
        <?php endif; ?>
    </div>

    <div class="media-info">
        <h2><?php echo htmlspecialchars($lecon['titre']); ?></h2>
        <p style="color: #64748b; margin-bottom: 20px;"><?php echo htmlspecialchars($lecon['description']); ?></p>
        
        <hr style="border:0; border-top:1px solid #eee; margin: 20px 0;">

       
        <div style="text-align: center; margin-top: 20px;">
    <a href="../backend/actions/valider_lesson.php?id_lecon=<?php echo $id_lecon; ?>&id_cours=<?php echo $id_cours; ?>" 
       class="btn-primary" 
       style="text-decoration: none;">
        <i class="fas fa-check-circle"></i> Marquer cette leçon comme terminée
    </a>
</div>
    </div>
</div>

<script>
/**
 * FONCTION UNIQUE POUR VALIDER LA LEÇON
 * Utilisée par le bouton ET par la fin de la vidéo
 */
function confirmerLeconTerminee() {
    const idLecon = <?php echo $id_lecon; ?>;
    const idCours = <?php echo $id_cours; ?>;

    console.log("Validation en cours pour la leçon : " + idLecon);

    // Envoi des données vers valider_lecon.php
    fetch('../backend/actions/valider_lesson.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'id_lecon=' + idLecon + '&id_cours=' + idCours
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            alert("Félicitations ! Votre progression a été enregistrée.");
            // Redirection pour voir la mise à jour (barre de progression / badge vert)
            window.location.href = 'lesson(a).php?id=' + idCours;
        } else {
            console.warn("Réponse serveur : ", data.message);
            // Si c'est déjà validé, on redirige quand même pour l'expérience utilisateur
            window.location.href = 'lesson(a).php?id=' + idCours;
        }
    })
    .catch(error => {
        console.error('Erreur Fetch :', error);
        alert("Erreur lors de la sauvegarde. Vérifiez votre connexion.");
    });
}

// GESTION AUTOMATIQUE POUR LES VIDÉOS
const video = document.getElementById('mainVideo');
if (video) {
    video.onended = function() {
        console.log("Vidéo finie. Validation automatique...");
        confirmerLeconTerminee();
    };
}
</script>

</body>
</html>