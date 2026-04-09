<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

$id_cours = $_SESSION['id_cours_actuel'];

$res = $conn->query("SELECT nom_cours FROM cours WHERE id = $id_cours");
$cours = $res->fetch_assoc();

$cin = $_SESSION['CIN'] ?? null;
$user = null;
$src = 'profil.avif';
if ($cin !== null) {
    $resProf = $conn->query("SELECT nom, prenom, data, type FROM professeur WHERE CIN = '$cin'");
    $user = $resProf ? $resProf->fetch_assoc() : null;
    if (!empty($user['data']) && !empty($user['type'])) {
        $base64 = base64_encode($user['data']);
        $src = 'data:' . $user['type'] . ';base64,' . $base64;
    }
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecons du cours</title>
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="form_cours.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link rel="stylesheet" href="lesson.css">
    <style></style>
</head>
<body class="lesson-page">
    <div class="dashboard-container">
        <header class="topbar">
            <div class="container topbar-inner">
                <a class="brand" href="index.php" aria-label="Accueil professeur">
                    <img src="enjah.png" alt="Logo Enjah">
                    <span>Professeur</span>
                </a>

                <nav class="nav-links" aria-label="Navigation professeur">
                    <a href="offres.php" class="is-active">Vos Cours</a>
                    <a href="valider_certificats.php">Certificats</a>
                    <a href="reclamation.php">Reclamation</a>
                    <a href="infos.php">Mes Infos</a>
                </nav>

                <div class="top-actions">
                    <details class="profile-menu">
                        <summary class="profile-trigger" aria-label="Ouvrir le menu profil" title="Mon Profil">
                            <img src="<?php echo $src; ?>" class="nav-avatar" alt="Profil">
                        </summary>
                        <div class="profile-dropdown" role="menu" aria-label="Menu profil">
                            <div class="profile-dropdown-header">
                                <div class="profile-dropdown-name"><?php echo htmlspecialchars(trim(($_SESSION['nom'] ?? '') . ' ' . ($_SESSION['prenom'] ?? '')), ENT_QUOTES, 'UTF-8'); ?></div>
                                <div class="profile-dropdown-sub">Professeur</div>
                            </div>
                            <a href="infos.php" role="menuitem">Voir profil</a>
                            <a href="infos.php" role="menuitem">Mes infos</a>
                            <a href="logout.php" class="danger" role="menuitem">Se deconnecter</a>
                        </div>
                    </details>
                </div>
            </div>
        </header>

        <main class="main-content">
            <header class="header">
                <h1>Ajouter des lecons pour votre cours</h1>
            </header>
            <div class="lecons-container">
                <h3>Contenu du cours : <?php echo htmlspecialchars($cours['nom_cours'] ?? 'Cours inconnu'); ?></h3>
                <?php
                $sql_lecons = "SELECT * FROM lecon WHERE id_cours = $id_cours ORDER BY id_lecon ASC";
                $res_lecons = $conn->query($sql_lecons);

                if ($res_lecons && $res_lecons->num_rows > 0) {
                    while ($row = $res_lecons->fetch_assoc()) {
                        $type = $row['type_fichier'];
                        $icon = 'fas fa-file';
                        $btnText = 'Telecharger';

                        if (strpos($type, 'video') !== false) {
                            $icon = 'fas fa-play-circle';
                            $btnText = 'Regarder';
                        } elseif (strpos($type, 'pdf') !== false) {
                            $icon = 'fas fa-file-pdf';
                            $btnText = 'Ouvrir';
                        } elseif (strpos($type, 'audio') !== false) {
                            $icon = 'fas fa-volume-up';
                            $btnText = 'Ecouter';
                        }
                ?>

                <div class="lecon-item">
                    <a href="supprimer_lecon.php?id=<?php echo $row['id_lecon']; ?>"
                       class="delete-cross"
                       style="display: none; position: absolute; top: -10px; right: -10px; background: red; color: white; border-radius: 50%; width: 25px; height: 25px; text-align: center; line-height: 25px; text-decoration: none; font-weight: bold; z-index: 10;"
                       onclick="return confirm('Voulez-vous vraiment supprimer cette lecon ?');">
                       &times;
                    </a>
                    <div class="lecon-icon"><i class="<?php echo $icon; ?>"></i></div>
                    <div class="lecon-info">
                        <h4><?php echo htmlspecialchars($row['titre']); ?></h4>
                        <span><?php echo htmlspecialchars($row['description']); ?></span>
                    </div>
                    <a href="visualiser_lecon.php?id=<?php echo $row['id_lecon']; ?>" class="btn-view" style="text-decoration: none; text-align: center; margin-left: 6px;">
                        <?php echo $btnText; ?>
                    </a>
                </div>
                <?php
                    }
                } else {
                    echo "<p style='padding: 20px; color: var(--text-muted);'>Aucune lecon n'a encore ete ajoutee pour ce cours.</p>";
                }
                ?>
            </div>

            <button class="btn-add-lecon" id="ajout">
                <i class="fas fa-plus"></i> Nouvelle Lecon
            </button>
            <button class="btn-manage" onclick="toggleDeleteMode()" style="background: #ff4b2b; color: white; border: none; padding: 10px; border-radius: 5px; cursor: pointer;">
                <i class="fas fa-trash"></i> Gerer les lecons
            </button>

            <div id="form-ajout" class="modal">
                <div class="modal-content">
                    <span class="close-btn">&times;</span>
                    <h2>Ajouter une nouvelle lecon</h2>
                    <form action="enrg_lesson.php" method="POST" enctype="multipart/form-data">
                        <input type="hidden" name="id_cours" value="<?php echo $id_cours; ?>">

                        <div class="form-group">
                            <label>Titre de la lecon :</label>
                            <input type="text" name="titre" placeholder="Ex: 03. Optique geometrique" required>
                        </div>

                        <div class="form-group">
                            <label>Description courte :</label>
                            <input type="text" name="description" placeholder="Ex: Video MP4 - 12:00">
                        </div>

                        <div class="form-group">
                            <label>Fichier (PDF, Video, MP3...) :</label>
                            <input type="file" name="fichier_lecon" required>
                        </div>

                        <button type="submit" class="btn-save">Publier la lecon</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="lesson.js"></script>
    <script src="supprimer_lecon.js"></script>
</body>
</html>
