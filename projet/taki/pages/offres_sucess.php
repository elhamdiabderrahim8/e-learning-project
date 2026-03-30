<?php
declare(strict_types=1);

// 1. Chargement de l'environnement élève
require_once __DIR__ . '/../backend/includes/bootstrap.php';
require_auth();

// 2. SAUVEGARDE de la session élève (très important pour éviter le vol de session par le prof)
$current_student_cin = $_SESSION['CIN'] ?? null;

// 3. Inclusion de la connexion externe
require_once __DIR__ .'/../../professeur/config/connexion.php'; 

// 4. RESTAURATION : On s'assure que le CIN reste celui de l'élève
if ($current_student_cin) {
    $_SESSION['CIN'] = $current_student_cin;
}

// 5. Requête SQL avec les bons Alias
$sql = "SELECT c.id AS id_cours, c.nom_cours, c.prix, c.categorie, 
               c.image_type, c.image_data, 
               p.nom AS nom_prof, p.prenom AS prenom_prof
        FROM cours c 
        INNER JOIN professeur p ON c.id_professeur = p.CIN
        WHERE c.categorie = 'Premium'";

$result = $conn->query($sql);

if (!$result) {
    die("Erreur SQL : " . $conn->error);
}
?>

<!DOCTYPE html>
<html lang="fr">
<head>
    <meta charset="UTF-8">
    <title>Choisir une offre - Enjah</title>
    <link rel="stylesheet" href="../style.css">
    <link rel="stylesheet" href="../../professeur/nouvel.css">
</head>
<body>
    <div class="dashboard-container">
        <aside class="sidebar">
            <div class="logo"><img src="../media/logo.jpg" alt="Logo"><span>Enjah</span></div>
            <nav>
                <ul>
                    <li><a href="cours.php"><span>Mes Cours</span></a></li>
                    <li><a href="tache_a_fair.php"><span>Mes Tâches</span></a></li>
                    <li class="active"><a href="offres.php"><span>Choisir une offre</span></a></li>
                    <li><a href="profil.php"><span>Mon Profil</span></a></li>
                </ul>
            </nav>
        </aside>

        <main class="main-content">
            <header class="header">
                <h1>Offres Spéciales</h1>
                <p>Étudiant connecté (CIN) : <strong><?php echo $_SESSION['CIN']; ?></strong></p>
            </header>

            <div class="courses-grid" style="display: grid; grid-template-columns: repeat(auto-fill, minmax(280px, 1fr)); gap: 20px; padding: 20px;">
                <?php
                if ($result->num_rows > 0) {
                    while($row = $result->fetch_assoc()) {
                        $image_src = 'data:' . $row['image_type'] . ';base64,' . base64_encode($row['image_data']);
                ?>
                        <div class="course-card">
                             <div style="background-image: url('<?php echo $image_src; ?>'); height:160px; background-size:cover; background-position:center; position: relative;">
                                <span class="badge or" style="position: absolute; top: 10px; right: 10px;">Premium</span>
                            </div>
                            <div style="padding: 15px;">
                                <h3 style="margin:0;"><?php echo htmlspecialchars($row['nom_cours']); ?></h3>
                                <p style="color:#666;">Par <?php echo htmlspecialchars($row['nom_prof'] . " " . $row['prenom_prof']); ?></p>
                                
                                <div style="font-size: 1.3em; font-weight: bold; color: #20c997; margin: 10px 0;">
                                    <?php echo number_format((float)$row['prix'], 2); ?> DT
                                </div>
                                
                                <button type="button" onclick="ouvrirModalPaiement(<?php echo $row['id_cours']; ?>)" 
                                        style="width:100%; padding: 12px; background: #007bff; color: white; border:none; border-radius: 5px; cursor:pointer; font-weight:bold;">
                                    Payer
                                </button>
                            </div>
                        </div>
                <?php
                    }
                } else {
                    echo "<p>Aucun cours premium disponible.</p>";
                }
                $conn->close();
                ?>
            </div>
        </main>
    </div>

    <div id="modal-paiement-global" class="modal-overlay">
        <div class="modal-content">
            <h2 style="margin-top:0;">Paiement sécurisé</h2>
            <form action="../backend/actions/traitter_payement(a).php" method="POST">
                <input type="hidden" id="input_id_cours_final" name="course_id">
                
                <label>Nom sur la carte</label>
                <input type="text" placeholder="M. JEAN DUPONT">
                <label>Numéro de carte</label>
                <input type="text" placeholder="4532 •••• •••• ••••">
                <div class="input-row">
                    <input type="text" placeholder="MM/AA">
                    <input type="text" placeholder="CVC">
                </div>
                
                <button type="submit" style="width:100%; background:#20c997; color:white; border:none; padding:12px; margin-top:20px; cursor:pointer; border-radius:5px; font-weight:bold;">
                    Confirmer l'achat
                </button>
                <button type="button" onclick="fermerModal()" style="width:100%; background:none; border:none; color:#666; margin-top:10px; cursor:pointer;">Annuler</button>
            </form>
        </div>
     <div id="step-success" class="modal-overlay" style="display: flex;"> 
    <div class="modal-content" style="text-align:center;">
        <div style="font-size: 4rem; color: #20c997;">&#10003;</div>
        <h2>Bienvenue sur nos offres !</h2>
        <p style="margin: 15px 0; color: #666;">Consultez nos cours Premium disponibles dès maintenant.</p>
        
        <button type="button" onclick="fermerSucces()" 
                style="width:100%; background:#dc3545; color:white; border:none; padding:12px; border-radius:5px; cursor:pointer; font-weight:bold;">
            Fermer l'annonce
        </button>
    </div>
</div>

   <script>
    // Cette fonction s'exécute dès que la page est chargée
    window.addEventListener('load', function() {
        // 1. On vérifie si l'URL contient #step-success
        // 2. OU si le paramètre paiement=success est présent
        const urlParams = new URLSearchParams(window.location.search);
        
        if (window.location.hash === '#step-success' || urlParams.get('paiement') === 'success') {
            const modalSuccess = document.getElementById('step-success');
            if (modalSuccess) {
                modalSuccess.style.display = 'flex'; // On affiche le modal
            }
        }
    });

    function ouvrirModalPaiement(id) {
        document.getElementById('input_id_cours_final').value = id;
        document.getElementById('modal-paiement-global').style.display = 'flex';
    }

    function fermerModal() {
        document.getElementById('modal-paiement-global').style.display = 'none';
        // On cache aussi le modal de succès si on clique sur fermer
        document.getElementById('step-success').style.display = 'none';
    }
</script>


<script>
    // Fonction pour fermer spécifiquement le modal de succès
    function fermerSucces() {
        const modal = document.getElementById('step-success');
        if (modal) {
            modal.style.display = 'none';
        }
    }

    // Fonction pour ouvrir le modal de paiement
    function ouvrirModalPaiement(id) {
        document.getElementById('input_id_cours_final').value = id;
        document.getElementById('modal-paiement-global').style.display = 'flex';
    }

    // Fonction pour fermer le modal de paiement
    function fermerModal() {
        document.getElementById('modal-paiement-global').style.display = 'none';
    }
</script>
</body>
</html>