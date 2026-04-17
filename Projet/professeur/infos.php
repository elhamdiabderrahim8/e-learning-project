<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');
$cin = $_SESSION['CIN'];
$sql = "SELECT * FROM professeur WHERE CIN = '$cin'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
if (!empty($user['data'])) {
    $base64 = base64_encode($user['data']);
    $src = "data:" . $user['type'] . ";base64," . $base64;
} else {
    $src = "profil.avif";
}
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="infos.css">
    <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="form_cours.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
    .form-group input[type="file"] {
        display: block;
        width: 100%;
        padding: 10px;
        color: #64748b;
        font-size: 0.9rem;
        background: #f8fafc;
        border: 2px dashed #e2e8f0;
        border-radius: 12px;
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .form-group input[type="file"]::file-selector-button {
        border: none;
        background: #4d68e1;
        padding: 10px 20px;
        border-radius: 8px;
        color: white;
        font-weight: 600;
        margin-right: 15px;
        cursor: pointer;
        transition: background 0.3s ease, transform 0.2s ease;
        box-shadow: 0 4px 10px rgba(77, 104, 225, 0.2);
    }

    .form-group input[type="file"]:hover {
        border-color: #4d68e1;
        background: #f1f5f9;
    }

    .form-group input[type="file"]::file-selector-button:hover {
        background: #3b52c1;
        transform: scale(1.02);
    }

    .photo-wrapper {
        position: relative;
        width: 180px;
        height: 180px;
        margin: 0 auto 30px;
    }

    .profil-avatar {
        width: 100%;
        height: 100%;
        border-radius: 50%;
        border: 4px solid #1e293b;
        object-fit: cover;
        box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .btn-delete-photo {
        position: absolute !important;
        top: 5px !important;
        right: 5px !important;
        width: 36px !important;
        height: 36px !important;
        background: #ffffff !important;
        border: 2px solid #ef4444 !important;
        border-radius: 50%;
        color: #ef4444 !important;
        cursor: pointer !important;
        box-shadow: 0 2px 10px rgba(0,0,0,0.2) !important;
        display: flex !important;
        justify-content: center !important;
        align-items: center !important;
        font-size: 1rem !important;
        transition: all 0.2s ease !important;
    }

    .btn-delete-photo:hover {
        background: #ef4444 !important;
        color: #ffffff !important;
        transform: scale(1.1);
        box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
    }

    .input-group {
        width: min(760px, 100%);
        margin: 0 auto;
    }
    </style>
</head>
<body class="profile-page">
    <div class="dashboard-container">
        <header class="topbar topbar-full">
            <div class="container topbar-inner">
                <a class="brand" href="index.php" aria-label="Accueil professeur">
                    <img src="enjah.png" alt="Logo Enjah">
                    <span>Professeur</span>
                </a>

                <nav class="nav-links" aria-label="Navigation professeur">
                    <a href="offres.php">Vos Cours</a>
                    <a href="valider_certificats.php">Certificats</a>
                    <a href="reclamation.php">Reclamation</a>
                    <a href="infos.php" class="is-active">Mes Infos</a>
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
            <div class="input-group">
                <h3>bonjour <?php echo $user['nom']; ?></h3>
                <div class="profile-header photo-wrapper">
                    <img src="<?php echo $src; ?>" alt="Photo de profil" id="preview-img" class="profil-avatar">
                    <button class="btn-delete-photo" title="Supprimer la photo" onclick="confirmerSuppression()">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </div>
                <h2>Profil de <?php echo $user['nom']; ?></h2>

                <form action="update_profil.php" method="POST" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Nom complet :</label>
                        <input type="text" name="nom" value="<?php echo $user['nom']; ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Prenom :</label>
                        <input type="text" name="prenom" value="<?php echo $user['prenom']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>CIN :</label>
                        <input type="text" name="CIN" value="<?php echo $user['CIN']; ?>" required>
                    </div>

                    <div class="form-group">
                        <label>Changer la photo :</label>
                        <input type="file" name="nouvelle_image" accept="image/*">
                    </div>

                    <hr>
                    <p style="color: red;">* Pour enregistrer, confirmez votre identite :</p>

                    <div class="form-group">
                        <label>Ancien mot de passe :</label>
                        <input type="password" name="old_password" placeholder="Mot de passe actuel" required>
                    </div>

                    <button type="submit" class="btn-save">Enregistrer les modifications</button>
                </form>
            </div>
        </main>
    </div>
    <script src="supprimer_photo.js"></script>
</body>
</html>
