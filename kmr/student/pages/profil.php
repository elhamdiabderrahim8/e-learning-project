<?php
require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

$pdo = db();
$session_cin = $_SESSION['CIN'] ?? $_SESSION['user_id']; 

// --- RÉCUPÉRATION DES DONNÉES ---
// On s'assure de récupérer 'data' et 'type'
$stmt = $pdo->prepare('SELECT CIN, nom, prenom, email, date_inscription, preferred_language, data, type FROM etudiant WHERE CIN = :cin');
$stmt->execute(['cin' => $session_cin]);
$user = $stmt->fetch();

if (!$user) {
    redirect('cours.php'); 
}

// --- LOGIQUE D'AFFICHAGE DE LA PHOTO (CORRECTIONS APPLIQUÉES ICI) ---
if (!empty($user['data'])) {
    // Correction : Utilisation des colonnes 'data' et 'type' de ta table etudiant
    $base64 = base64_encode($user['data']);
    $src = "data:" . $user['type'] . ";base64," . $base64;
} else {
    $src = "../../professeur/profil.avif"; // Image par défaut
}

// Variables d'affichage
$memberSince = date('d/m/Y', strtotime((string)$user['date_inscription']));
$fullName = trim((string) $user['prenom'] . ' ' . (string) $user['nom']);
$cinDisplay = (string) ($user['CIN'] ?? '');
if ($cinDisplay !== '' && ctype_digit($cinDisplay) && strlen($cinDisplay) < 8) {
    $cinDisplay = str_pad($cinDisplay, 8, '0', STR_PAD_LEFT);
}

$error = get_flash('error');
$success = get_flash('success');
?>
<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Mon Profil - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
        .photo-container-wrapper {
            position: relative;
            width: 150px;
            height: 150px;
            margin: 0 auto 20px;
        }
        .profil-avatar-img {
            width: 100%;
            height: 100%;
            border-radius: 50%;
            border: 4px solid #1e293b;
            object-fit: cover;
            box-shadow: 0 4px 15px rgba(0,0,0,0.1);
        }
        .btn-delete-photo {
            position: absolute;
            top: 5px;
            right: 5px;
            width: 36px;
            height: 36px;
            background: #ffffff;
            border: 2px solid #ef4444;
            border-radius: 50%;
            color: #ef4444;
            cursor: pointer;
            display: flex;
            justify-content: center;
            align-items: center;
            transition: all 0.2s ease;
            box-shadow: 0 2px 10px rgba(0,0,0,0.2);
        }
        .btn-delete-photo:hover {
            background: #ef4444; color: #ffffff; transform: scale(1.1);
        }
        .input-file-enjah {
            display: block; width: 100%; padding: 10px;
            background: #f8fafc; border: 2px dashed #e2e8f0; border-radius: 12px;
            cursor: pointer; margin-top: 5px;
        }
        .input-file-enjah::file-selector-button {
            border: none; background: #4d68e1; padding: 8px 15px;
            border-radius: 8px; color: white; font-weight: 600;
            margin-right: 15px; cursor: pointer;
        }
    </style>
</head>
<body>
    <div class="dashboard-container">
        <?php $active = 'profil'; require __DIR__ . '/partials/sidebar.php'; ?>

        <main class="main-content">
            <header class="header">
                <h1>Mon Profil</h1>
                <p>Bonjour, <strong><?php echo htmlspecialchars((string)$user['prenom']); ?></strong> !</p>
            </header>

            <?php if ($error): ?><div class="profile-alert profile-alert-error"><?php echo htmlspecialchars($error); ?></div><?php endif; ?>
            <?php if ($success): ?><div class="profile-alert profile-alert-success"><?php echo htmlspecialchars($success); ?></div><?php endif; ?>

            <section class="profile-shell">
                <article class="profile-hero profile-card">
                    <div class="photo-container-wrapper">
                        <img src="<?php echo $src; ?>" alt="Avatar" class="profil-avatar-img">
                        
                        <?php if(!empty($user['data'])): ?>
                        <button type="button" class="btn-delete-photo" title="Supprimer la photo" onclick="confirmerSuppression()">
                            <i class="fas fa-trash-alt"></i>
                        </button>
                        <?php endif; ?>
                    </div>

                    <div class="profile-hero-text">
                        <h2><?php echo htmlspecialchars($fullName); ?></h2>
                        <p><?php echo htmlspecialchars((string) $user['email']); ?></p>
                        <p><small>CIN : <?php echo htmlspecialchars($cinDisplay); ?></small></p>
                    </div>
                </article>

                <article class="profile-details profile-card" id="settings">
                    <h3>Mettre à jour mes informations</h3>
                    <form class="profile-settings-form" action="../backend/actions/update_profile_settings.php" method="post" enctype="multipart/form-data">
                        <div class="input-row">
                            <div class="input-group">
                                <label for="first_name">Prénom</label>
                                <input type="text" id="first_name" name="first_name" value="<?php echo htmlspecialchars((string) $user['prenom']); ?>" required>
                            </div>
                            <div class="input-group">
                                <label for="last_name">Nom</label>
                                <input type="text" id="last_name" name="last_name" value="<?php echo htmlspecialchars((string) $user['nom']); ?>" required>
                            </div>
                        </div>

                        <div class="input-group">
                            <label>Changer la photo de profil</label>
                            <input type="file" name="profile_image" class="input-file-enjah" accept="image/*">
                        </div>

                        <div class="input-group">
                            <label for="preferred_language">Langue du site</label>
                            <select id="preferred_language" name="preferred_language">
                                <option value="en" <?php echo ($user['preferred_language'] == 'en' ? 'selected' : ''); ?>>English</option>
                                <option value="fr" <?php echo ($user['preferred_language'] == 'fr' ? 'selected' : ''); ?>>Français</option>
                            </select>
                        </div>

                        <input type="submit" class="btn-primary profile-settings-btn" value="Enregistrer les modifications">
                    </form>
                    <div class="profile-actions">
                        <a href="../backend/actions/logout.php" class="btn-logout">Se déconnecter</a>
                    </div>
                </article>
            </section>
        </main>
    </div>

    <script>
    function confirmerSuppression() {
        if(confirm("Voulez-vous vraiment supprimer votre photo de profil ?")) {
            window.location.href = "../backend/actions/delete_photo.php";
        }
    }
    </script>
</body>
</html>
