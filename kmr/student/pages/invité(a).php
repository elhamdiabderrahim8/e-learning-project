<?php
// On inclut le fichier de connexion que vous venez de créer
require_once  __DIR__ .'/../../professeur/config/connexion.php'; 

// Requête pour récupérer uniquement les cours gratuits
// On suppose que votre table s'appelle 'cours'
$sql = "SELECT c.*, p.nom ,p.prenom
        FROM cours c 
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        WHERE c.categorie = 'free'";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Cours Gratuits - Enjah</title>
    <link rel="stylesheet" href="../../professeur/form_cours.css">
    <link rel="stylesheet" href="../../professeur/nouvel.css">
    <style>
        img{
            width:100px;
            height:90px;
        }
    </style>
</head>
<body>
<div class="dashboard">
                <img  id="logo"src="../../professeur/enjah.png" alt="logo">
                <h3 class="logo brand-name">ENJAH</h3>
<div class="container">
    
   <div class="courses-grid" id="courses-grid">
                <?php
        if ($result->num_rows > 0) {
            while($row = $result->fetch_assoc()) {
                
                // --- ÉTAPE : STOCKAGE DANS LES VARIABLES ---
                $id_c       = $row['id'];
                $titre      = $row['nom_cours'];
                $prix       = number_format($row['prix'], 2);
                $cat        = $row['categorie'];
                $prof       = $row['nom']." ".$row['prenom'];
                $image_src  = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                
                // Choix de la classe CSS selon la catégorie pour garder votre style
                $badge_class = ($cat == 'Premium') ? 'or' : 'silver';
                ?>
                <div class="course-card" id="cours-<?php echo $row['id']; ?>">
                    <div class="course-image dev-bg" 
                         style="background-image: url('<?php echo $image_src; ?>'); background-size: cover; background-position: center;">
                        <span class="<?php echo $badge_class;?>"><?php echo $cat; ?></span>
                    </div>
                    
                    <div class="course-body">
                        <h3><?php echo $titre; ?></h3>
                        <p>Par <strong><?php echo $prof; ?></strong></p>
                        <div class="price-tag"><?php echo $prix; ?> DT</div>
                        <a href="lesson(a).php?id=<?php echo $row['id']; ?>" class="btn-primary" style="display:block; text-align:center; text-decoration:none; margin-top:10px;">
                            Consulter leçons
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

</body>
</html>