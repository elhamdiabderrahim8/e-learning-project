<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');
$cin = $_SESSION['CIN']; 
$sql = "SELECT * FROM professeur WHERE CIN = '$cin'";
$result = $conn->query($sql);
$user = $result->fetch_assoc();
if (!empty($user['data'])) {
    // On transforme le binaire en texte lisible par le navigateur
    $base64 = base64_encode($user['data']);
    $src = "data:" . $user['type'] . ";base64," . $base64;
} else {
    // Image par défaut si la base est vide
    $src = "profil.avif"; 
}
?>
<!DOCTYPE html>
<head>
    <link rel="stylesheet" href="infos.css">
     <link rel="stylesheet" href="nouvel.css">
    <link rel="stylesheet" href="form_cours.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <style>
 /* --- Style Enjah pour Input File sans modifier le HTML --- */

.form-group input[type="file"] {
    display: block;
    width: 100%;
    padding: 10px;
    color: #64748b; /* Couleur du texte "Aucun fichier choisi" */
    font-size: 0.9rem;
    background: #f8fafc; /* Fond très léger pour la zone */
    border: 2px dashed #e2e8f0; /* Bordure en pointillés élégante */
    border-radius: 12px; /* Arrondi Enjah */
    cursor: pointer;
    transition: all 0.3s ease;
}

/* Stylisation du bouton interne (Le vrai secret) */
.form-group input[type="file"]::file-selector-button {
    border: none;
    background: #4d68e1; /* Bleu Royal Enjah */
    padding: 10px 20px;
    border-radius: 8px; /* Arrondi du bouton interne */
    color: white;
    font-weight: 600;
    margin-right: 15px;
    cursor: pointer;
    transition: background 0.3s ease, transform 0.2s ease;
    box-shadow: 0 4px 10px rgba(77, 104, 225, 0.2); /* Relief Enjah */
}

/* Effets au survol de la zone entière */
.form-group input[type="file"]:hover {
    border-color: #4d68e1; /* La bordure devient bleue au survol */
    background: #f1f5f9;
}

/* Effet au survol du bouton interne uniquement */
.form-group input[type="file"]::file-selector-button:hover {
    background: #3b52c1; /* Bleu plus foncé */
    transform: scale(1.02);
}







.photo-wrapper {
    position: relative;
    width: 180px;
    height: 180px;
    margin: 0 auto 30px; /* Centré horizontalement */
}

.profil-avatar {
    width: 100%;
    height: 100%;
    object-fit: cover;
    border-radius: 50%; /* Image parfaitement ronde */
    /* Bordure Bleue Ardoise épaisse */
    border: 6px solid var(--slate-dark);
    box-shadow: 0 10px 30px rgba(0,0,0,0.15);
}

/* BOUTON SUPPRESSION (Corbeille) Positionné en haut à droite */
.btn-delete-photo {
    position: absolute;
    top: 5px; /* Positionné en haut */
    right: 5px; /* Positionné à droite */
    width: 40px;
    height: 40px;
    background: var(--white);
    border: 2px solid var(--danger); /* Bordure rouge pour l'alerte */
    border-radius: 50%;
    color: var(--danger);
    cursor: pointer;
    transition: all 0.3s ease;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    display: flex;
    justify-content: center;
    align-items: center;
    font-size: 1.1rem;
}

/* Effets de survol Enjah pour le bouton Corbeille */
.btn-delete-photo:hover {
    background: var(--danger);
    color: var(--white);
    transform: scale(1.1) rotate(-10deg); /* Légère rotation au survol */
    box-shadow: 0 8px 20px rgba(239, 68, 68, 0.4);
}

/* --- Titre de Profil --- */
.profile-title {
    margin-top: 30px;
    margin-bottom: 20px;
}

.profile-title h2 {
    color: var(--primary-blue); /* Le bleu dynamique d'Enjah */
    font-size: 1.6rem;
    font-weight: 800;
    margin: 0;
}

.profile-title h2 span {
    text-transform: uppercase; /* Nom de l'utilisateur */
}


/* --- Style Premium pour l'icône de suppression Enjah --- */

/* 1. On crée le conteneur relatif pour empiler les éléments */
.photo-container-wrapper {
    position: relative !important;
    display: inline-block !important; /* Pour que le wrapper prenne la taille de l'image */
    width: 150px; /* Taille de votre avatar */
    height: 150px;
}

/* 2. Style de l'avatar (Cercle parfait) */
.profil-avatar {
    width: 100%;
    height: 100%;
    border-radius: 50%;
    border: 4px solid #1e293b; /* Bordure sombre Enjah */
    object-fit: cover;
    box-shadow: 0 4px 15px rgba(0,0,0,0.1);
}

/* 3. LE BOUTON (Positionné sur le point blanc) */
.btn-delete-photo {
    position: absolute !important;
    top: 5px !important;       /* Positionné en haut */
    right: 5px !important;     /* Positionné à droite */
    width: 36px !important;    /* Taille du petit point */
    height: 36px !important;
    background: #ffffff !important; /* Fond blanc du point */
    border: 2px solid #ef4444 !important; /* Bordure rouge Alerte */
    border-radius: 50%;
    color: #ef4444 !important;    /* L'icône Corbeille est rouge */
    cursor: pointer !important;
    box-shadow: 0 2px 10px rgba(0,0,0,0.2) !important; /* Relief Enjah */
    
    /* Centrage parfait de l'icône FontAwesome */
    display: flex !important;
    justify-content: center !important;
    align-items: center !important;
    font-size: 1rem !important;
    transition: all 0.2s ease !important;
}

/* 4. Effet d'interaction Enjah au survol */
.btn-delete-photo:hover {
    background: #ef4444 !important; /* Le bouton devient rouge */
    color: #ffffff !important;      /* L'icône devient blanche */
    transform: scale(1.1);
    box-shadow: 0 4px 15px rgba(239, 68, 68, 0.4);
}







</style>
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
        <li><a href="cours.html">Mes Cours</a></li>
        
        <li><a href="tache_a_fair.html">Mes Tâches</a></li>
        
        <li ><a href="offres.php">Vos Cours</a></li>
        
        <li><a href="calendrier.html">Calendrier</a></li>
        
        <li><a href="certificats.html">Certificats</a></li>
        
        <li><a href="reclamation.html">Réclamation</a></li>
        <li class="active"><a href="infos.php">Mes Infos</a></li>
    </ul>
</nav>
        </aside>

<div class="input-group" style="margin-left:100px;">
    <h3>bonjour <?php echo $user['nom'];?></h3>
    <div class="profile-header photo-wrapper">
        <img src="<?php echo $src; ?>" alt="Photo de profil" id="preview-img">
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
            <label> prenom :</label>
            <input type="text" name="prenom" value="<?php echo $user['prenom']; ?>" required>
        </div>

        <div class="form-group">
            <label>CIN :</label>
            <input type="CIN" name="CIN" value="<?php echo $user['CIN']; ?>" required>
        </div>

        <div class="form-group">
            <label>Changer la photo :</label>
            <input type="file" name="nouvelle_image" accept="image/*">
        </div>

        <hr>
        <p style="color: red;">* Pour enregistrer, confirmez votre identité :</p>
        
        <div class="form-group">
            <label>Ancien mot de passe :</label>
            <input type="password" name="old_password" placeholder="Mot de passe actuel" required>
        </div>

        <button type="submit" class="btn-save">Enregistrer les modifications</button>
    </form>
</div>
<script src="supprimer_photo.js"></script>
</body>