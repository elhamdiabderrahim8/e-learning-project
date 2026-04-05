 <?php
session_start();
if (!isset($_SESSION['CIN'])) {
    header("Location: login.php");
    exit();
}
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
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir une offre - Smart Learning</title>
    <link rel="stylesheet" href="nouvel.css">
     <link rel="stylesheet" href="form_cours.css">
</head>
<body >
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

        <main class="main-content" >
            <header class="header">
                <h1>Offres Spéciales</h1>
                <p>Investissez dans votre avenir avec nos cours.</p>
            </header>

            <div class="courses-grid" id="courses-grid">
                <?php
        // 1. Connexion à la base
        $conn = new mysqli("localhost", "root", "", "elearning");

        // 2. Requête pour récupérer tous les cours avec le nom du prof
        $sql = "SELECT c.*, p.nom AS nom_prof 
                FROM cours c 
                JOIN professeur p ON c.id_professeur = p.CIN";
        $result = $conn->query($sql);

        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                // --- ÉTAPE : STOCKAGE DANS LES VARIABLES ---
                $id_c       = $row['id'];
                $titre      = $row['nom_cours'];
                $prix       = number_format($row['prix'], 2);
                $cat        = $row['categorie'];
                $prof       = $row['nom_prof'];
                $image_src  = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                
                // Choix de la classe CSS selon la catégorie pour garder votre style
                $badge_class = ($cat == 'Premium') ? 'or' : 'silver';
                ?>
                <div class="course-card" id="cours-<?php echo $row['id']; ?>">
                    <div class="course-image dev-bg" 
                         style="background-image: url('<?php echo $image_src; ?>'); background-size: cover; background-position: center;">
                        <span class="<?php echo $badge_class;?>"><?php echo $cat; ?></span>
                        <button type="button" class="delete-btn-cross btn-delete-x hidden"  id="btn"
            onclick="confirmerSuppression(<?php echo $row['id']; ?>)" 
           >
        &times;
    </button>
                    </div>
                    
                    <div class="course-body">
                        <h3><?php echo $titre; ?></h3>
                        <p>Par <strong><?php echo $prof; ?></strong></p>
                        <div class="price-tag"><?php echo $prix; ?> DT</div>
                        
                        <a href="ouvrir_session_cours.php?id=<?php echo $row['id']; ?>" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                            Ajouter un leçon
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
    <button  class="btn alter_prof"  id="ajout" onclick="document.getElementById('form-ajout').style.display='block'" >Ajouter</button>
    <button  class="btn  alter_prof"  onclick="activerModeSuppression()">Supprimer</button>
    </div>
    <div id="form-ajout" style="display:none;">
    <form action="envoyer_cours.php" method="POST" enctype="multipart/form-data">
        <button type="button" class="close-btn" onclick="this.parentElement.parentElement.style.display='none'">&times;</button>
        <input type="text" name="nom_cours" placeholder="Nom du cours" required>
         <select name="categorie" id="categorie">
            <option value="Premium">Premium</option>
            <option value="Free">Gratuit (Free)</option>
        </select>
        <input type="number"  id="prix"step="0.01" name="prix" placeholder="Prix (ex: 49.99)" required>
        <label>Image du cours :</label>
        <input type="file" name="file" accept="image/*" required>
        
        <button type="submit" name="submit">Publier le cours</button>
    </form>
     </div>
</main>
 <div id="container_log_out">
        <img src="<?php echo $src;?>" class="logout" id="profil" onclick="vers_profile()">
        <img  src="logout.png" id="logout" class="logout">
    </div>
     <script src="lesson.js"></script>
     <script src="logout.js"></script>
     <script src="form.js"></script>
    </body>
     <script src="button_ajouter.js"></script>
    </html>