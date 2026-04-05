<?php
session_start();
if (!isset($_SESSION['CIN'])) {
    header("Location: login.html");
    exit();
}
?>
<!DOCTYPE html>
<html>
    <head>
        <link rel="stylesheet" href="form_cours.css">
    </head>
    <body>
        <button  class="btn-ouvrir-form "onclick="document.getElementById('form-ajout').style.display='block'">Ajouter un cours</button>
        <h1>Bienvenue, Professeur n° <?php echo $_SESSION['CIN']; ?></h1>

<div id="form-ajout" style="display:none;">
    <form action="envoyer_cours.php" method="POST" enctype="multipart/form-data">
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
<script src="form.js"></script>
    </body>
</html>
