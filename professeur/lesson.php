<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

// On récupère l'ID depuis la session
$id_cours = $_SESSION['id_cours_actuel'];

// On récupère le nom du cours pour l'affichage
$res = $conn->query("SELECT nom_cours FROM cours WHERE id = $id_cours");
$cours = $res->fetch_assoc();
?>

<form action="enrg_lesson.php" method="POST" enctype="multipart/form-data">
    <input type="hidden" name="id_cours" value="<?php echo $id_cours; ?>">
    </form>
<!DOCTYPE html>
<html>
<head>
     <link rel="stylesheet" href="nouvel.css">
     <link rel="stylesheet" href="form_cours.css">
   <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
   <link rel="stylesheet" href="lesson.css">
   <style></style>
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar" id="sidebar">
            <div class="head">
                <img  id="logo"src="enjah.png" alt="logo">
                <p class="logo brand-name">ENJAH</p>
            </div>
            
<nav>
    <ul>
        <li><a href="offres.php">Mes Cours</a></li>
        
        <li><a href="offres.php">Mes Tâches</a></li>
        
        <li class="active"><a href="offres.php">Vos Cours</a></li>
        
        <li><a href="offres.php">Calendrier</a></li>
        
        <li><a href="certificats.html">Certificats</a></li>
        
        <li><a href="reclamation.html">Réclamation</a></li>
        <li><a href="infos.php">Mes Infos</a></li>
    </ul>
</nav>
        </aside>
        <main class="main-content">
            <header class="header">
                <h1>Ajouter des leçons pour votre cour</h1>
            </header>
            <div class="leçons-container">
    <h3>Contenu du cours : <?php echo htmlspecialchars($cours['nom_cours'] ?? 'Cours inconnu'); ?></h3>
    <?php
    // Requête pour récupérer les leçons de ce cours spécifique
    $sql_lecons = "SELECT * FROM lecon WHERE id_cours = $id_cours ORDER BY id_lecon ASC";
    $res_lecons = $conn->query($sql_lecons);

    if ($res_lecons && $res_lecons->num_rows > 0) {
        while ($row = $res_lecons->fetch_assoc()) {
            // Logique pour choisir l'icône et le texte du bouton selon le type de fichier
            $type = $row['type_fichier'];
            $icon = "fas fa-file"; // Icône par défaut
            $btnText = "Télécharger";

            if (strpos($type, 'video') !== false) {
                $icon = "fas fa-play-circle";
                $btnText = "Regarder";
            } elseif (strpos($type, 'pdf') !== false) {
                $icon = "fas fa-file-pdf";
                $btnText = "Ouvrir";
            } elseif (strpos($type, 'audio') !== false) {
                $icon = "fas fa-volume-up";
                $btnText = "Écouter";
            }
    ?>
    
   <div class="lecon-item">
    <a href="supprimer_lecon.php?id=<?php echo $row['id_lecon']; ?>" 
       class="delete-cross" 
       style="display: none; position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; width: 25px; height: 25px; text-align: center; line-height: 25px; text-decoration: none; font-weight: bold; z-index: 10;"
       onclick="return confirm('Voulez-vous vraiment supprimer cette leçon ?');">
       &times;
    </a>
            <div class="lecon-icon"><i class="<?php echo $icon; ?>"></i></div>
            <div class="lecon-info">
                <h4><?php echo htmlspecialchars($row['titre']); ?></h4>
                <span><?php echo htmlspecialchars($row['description']); ?></span>
            </div>
            <a  href="visualiser_leçon.php?id=<?php echo $row['id_lecon']; ?>"  class="btn-view" style="text-decoration: none; text-align: center;margin-left:6px;">
                <?php echo $btnText; ?>
            </a>
        </div>
    <?php 
        }
    } else {
        echo "<p style='padding: 20px; color: var(--text-muted);'>Aucune leçon n'a encore été ajoutée pour ce cours.</p>";
    }
?>

</div>
<button class="btn-add-lecon" id="ajout">
    <i class="fas fa-plus"></i> Nouvelle Leçon
</button>
<button class="btn-manage" onclick="toggleDeleteMode()" style="background: #ff4b2b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">
    <i class="fas fa-trash"></i> Gérer les leçons
</button>

<div id="form-ajout" class="modal">
    <div class="modal-content">
        <span class="close-btn">&times;</span>
        <h2>Ajouter une nouvelle leçon</h2>
        <form action="enrg_lesson.php" method="POST" enctype="multipart/form-data">
            <input type="hidden" name="id_cours" value="<?php echo $id_cours; ?>">

            <div class="form-group">
                <label>Titre de la leçon :</label>
                <input type="text" name="titre" placeholder="Ex: 03. Optique géométrique" required>
            </div>

            <div class="form-group">
                <label>Description courte :</label>
                <input type="text" name="description" placeholder="Ex: Vidéo MP4 - 12:00">
            </div>

            <div class="form-group">
                <label>Fichier (PDF, Vidéo, MP3...) :</label>
                <input type="file" name="fichier_lecon" required>
            </div>

            <button type="submit" class="btn-save">Publier la leçon</button>
        </form>
    </div>
</div>
</main>
</body>
<script src="lesson.js"></script>
<script src="supprimer_leçon.js"></script>
</html>

