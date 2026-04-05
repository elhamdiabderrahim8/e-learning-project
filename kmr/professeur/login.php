<?php
session_start();
$CIN      = $_POST["CIN"]      ?? '';
$PASSWORD = $_POST["PASSWORD"] ?? '';

try {
    $conn = new mysqli('localhost', 'root', '', 'elearning');
    if ($conn->connect_error) {
        throw new Exception("Impossible de se connecter au serveur.");
    }
    $a = $conn->prepare("SELECT * FROM professeur WHERE CIN = ?");
    $a->bind_param("i", $CIN);
    $a->execute();
    $result = $a->get_result(); 
    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        if (password_verify($PASSWORD, $row["password"])) {
            session_regenerate_id(true);
            $_SESSION["CIN"]    = $row["CIN"];
            $_SESSION["prenom"] = $row["prenom"];
            $_SESSION["nom"]    = $row["nom"];
            header("Location: offres.php");
            exit();
        } else {
            $message = urlencode("Mot de passe incorrect. Veuillez réessayer.");
            header("Location: erreur_login.php?msg=$message");
            exit();
        }
    } else {
        $message = urlencode("Aucun compte trouvé avec ce CIN.");
        header("Location: erreur_login.php?msg=$message");
        exit();
    }

    $a->close();
    $conn->close();

} catch (Exception $e) {
    $message = urlencode($e->getMessage());
    header("Location: erreur_login.php?msg=$message");
    exit();
}
?>