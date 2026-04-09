<?php
session_start();
$conn = new mysqli('localhost', 'root','', 'elearning');

// Vérifier la connexion
if ($conn->connect_error) {
    die("La connexion a échoué : " . $conn->connect_error);
}

// Vérifier si le formulaire a été soumis
if(isset($_POST['submit'])) {
    $id_prof = $_SESSION['CIN'];
    $nom = $_POST['nom_cours'];
    $cat = $_POST['categorie'];
    if($cat=="Free"){
        $prix=0;
    }
    else{
       $prix = $_POST['prix'];
    }
    
    // 2. Gestion de l'image
    // On récupère le contenu binaire du fichier temporaire
$file = $_FILES["file"];
$type = $file["type"];
$name= $file["name"];
$data = file_get_contents($file["tmp_name"]);

    // 3. Préparation de la requête
    // Note : On utilise "b" pour le type BLOB (données binaires)
    $stmt = $conn->prepare("INSERT INTO cours (nom_cours, prix, categorie, image_data, image_type,image_name,id_professeur) VALUES (?, ?, ?, ?, ?,?,?)");

    /* Explication du "sssbs" :
       s = string (chaîne)
       b = blob (données binaires)
    */
    $stmt->bind_param("ssssssi", $nom, $prix, $cat, $data, $type,$name,$id_prof);

    // 4. Exécution
    if ($stmt->execute()) {
        header('Location: offres.php'); // Redirection en cas de succès
    } else {
        echo "Erreur lors de l'insertion : " . $stmt->error;
    }

    $stmt->close();
}

$conn->close();
?>