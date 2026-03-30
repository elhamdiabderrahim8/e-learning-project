<?php
session_start();
require_once('../taki/backend/config/database.php');

$pdo = db(); 

if (!isset($_SESSION['CIN'])) {
    die("Erreur : Vous n'êtes pas connecté.");
}

$id_prof_connecte = $_SESSION['CIN']; 

// On remplace e.id par e.CIN et c.id_professeur doit correspondre au CIN du prof
$sql = "SELECT 
            e.nom AS nom_etudiant, 
            e.prenom, 
            c.nom_cours AS nom_cours, 
            c.id AS id_cours, 
            e.CIN AS cin_etudiant
        FROM inscription i
        JOIN etudiant e ON i.id_etudiant = e.CIN
        JOIN cours c ON i.id_cours = c.id
        LEFT JOIN certificaton cert ON (cert.id_etudiant = e.CIN AND cert.id_cours = c.id)
        WHERE c.id_professeur = :id_prof
        AND i.progression = 100 
        AND cert.id_certificat IS NULL";

try {
    $query = $pdo->prepare($sql);
    $query->execute(['id_prof' => $id_prof_connecte]);
    $alertes = $query->fetchAll();
} catch (PDOException $e) {
    // Ce die affichera l'erreur précise si une colonne manque encore
    die("Erreur SQL : " . $e->getMessage());
}
?>

<h2>Tableau de bord des validations</h2>

<?php if (count($alertes) > 0): ?>
    <div class="alert-container">
        <?php foreach ($alertes as $alerte): ?>
            <div class="alert-card" style="border: 1px solid #b8860b; padding: 15px; margin-bottom: 10px; border-left: 5px solid #b8860b; background: #fffaf0; border-radius: 8px;">
                <p style="font-family: 'Montserrat', sans-serif;">
                    <strong>🔔 Nouvelle demande :</strong> 
                    L'étudiant <strong><?php echo htmlspecialchars($alerte['nom_etudiant'] . " " . $alerte['prenom']); ?></strong> 
                    (CIN: <?php echo htmlspecialchars($alerte['cin_etudiant']); ?>)
                    a terminé votre cours <em>"<?php echo htmlspecialchars($alerte['nom_cours']); ?>"</em>.
                </p>
                
                <form action="traitter_validation.php" method="POST">
                    <input type="hidden" name="id_etudiant" value="<?php echo $alerte['cin_etudiant']; ?>">
                    <input type="hidden" name="id_cours" value="<?php echo $alerte['id_cours']; ?>">
                    <button type="submit" style="background: #b8860b; color: white; border: none; padding: 10px 20px; cursor: pointer; border-radius: 4px; font-weight: bold;">
                        ✍️ Valider & Signer le Certificat
                    </button>
                </form>
            </div>
        <?php endforeach; ?>
    </div>
<?php else: ?>
    <div style="padding: 20px; color: #666; font-style: italic; border: 1px dashed #ccc;">
        Aucun certificat en attente de validation pour le moment.
    </div>
<?php endif; ?>