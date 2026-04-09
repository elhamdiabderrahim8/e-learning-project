<?php
session_start();
if (!isset($_SESSION['CIN'])) {
    header("Location: login.html");
    exit();
}

$conn = new mysqli('localhost', 'root', '', 'elearning');
$cin = $_SESSION['CIN'];

// --- RECUPERATION PROFIL ---
$sql_prof = "SELECT * FROM professeur WHERE CIN = '$cin'";
$res_prof = $conn->query($sql_prof);
$user = $res_prof->fetch_assoc();

if (!empty($user['data'])) {
    $base64 = base64_encode($user['data']);
    $src = "data:" . $user['type'] . ";base64," . $base64;
} else {
    $src = "profil.avif";
}

// --- RECUPERATION COURS ---
$sql_cours = "SELECT c.*, p.nom, p.prenom
              FROM cours c
              JOIN professeur p ON c.id_professeur = p.CIN
              WHERE c.id_professeur = '$cin'";
$result_cours = $conn->query($sql_cours);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Offres Speciales - ENJAH</title>
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="form_cours.css">
</head>
<body>
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
                                <div class="profile-dropdown-name"><?php echo htmlspecialchars(trim($_SESSION['nom'] . ' ' . $_SESSION['prenom']), ENT_QUOTES, 'UTF-8'); ?></div>
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
            <div class="content-header">
                <h1>Offres Speciales</h1>
                <p>Investissez dans votre avenir avec nos cours.</p>
            </div>

            <div class="courses-grid" id="courses-grid">
                <?php
                if ($result_cours && $result_cours->num_rows > 0) {
                    while ($row = $result_cours->fetch_assoc()) {
                        $id_c = $row['id'];
                        $titre = $row['nom_cours'];
                        $prix = number_format($row['prix'], 2);
                        $cat = $row['categorie'];
                        $prof = $row['nom'] . " " . $row['prenom'];
                        $image_src = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                        $badge_class = ($cat == 'Premium') ? 'or' : 'silver';
                ?>
                        <div class="course-card" id="cours-<?php echo $id_c; ?>">
                            <div class="course-image" style="background-image: url('<?php echo $image_src; ?>'); background-size: cover; background-position: center;">
                                <span class="<?php echo $badge_class; ?>"><?php echo $cat; ?></span>
                                <button type="button" class="delete-btn-cross btn-delete-x hidden" onclick="confirmerSuppression(<?php echo $id_c; ?>)">
                                    &times;
                                </button>
                            </div>

                            <div class="course-body">
                                <h3><?php echo $titre; ?></h3>
                                <p>Par <strong><?php echo $prof; ?></strong></p>
                                <div class="price-tag"><?php echo $prix; ?> DT</div>
                                <a href="ouvrir_session_cours.php?id=<?php echo $id_c; ?>" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                                    Ajouter une lecon
                                </a>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p style='grid-column: 1/-1; text-align: center;'>Aucun cours disponible.</p>";
                }
                $conn->close();
                ?>
            </div>

            <div class="buttons">
                <button class="btn alter_prof" id="ajout" onclick="document.getElementById('form-ajout').style.display='block'">Ajouter</button>
                <button class="btn alter_prof" onclick="activerModeSuppression()">Supprimer</button>
            </div>

            <div id="form-ajout" style="display:none;">
                <form action="envoyer_cours.php" method="POST" enctype="multipart/form-data">
                    <button type="button" class="close-btn" onclick="this.parentElement.parentElement.style.display='none'">&times;</button>
                    <input type="text" name="nom_cours" placeholder="Nom du cours" required>
                    <select name="categorie" id="categorie">
                        <option value="Premium">Premium</option>
                        <option value="Free">Gratuit (Free)</option>
                    </select>
                    <input type="number" id="prix" step="0.01" name="prix" placeholder="Prix (ex: 49.99)" required>
                    <label>Image du cours :</label>
                    <input type="file" name="file" accept="image/*" required>
                    <button type="submit" name="submit">Publier le cours</button>
                </form>
            </div>
        </main>
    </div>

    <script src="lesson.js"></script>
    <script src="logout.js"></script>
    <script src="form.js"></script>
    <script src="button_ajouter.js"></script>
    <?php
    $chat_user_id = $_SESSION['CIN'];
    $chat_user_type = 'professeur';
    $chat_user_name = $_SESSION['nom'] . ' ' . $_SESSION['prenom'];
    require_once __DIR__ . '/../admin/chat_widget.php';
    ?>
</body>
</html>
