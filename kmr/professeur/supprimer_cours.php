<?php
session_start();
$conn = new mysqli('localhost', 'root', '', 'elearning');

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);
    
    // 1. Suppression dans la base de données
    $stmt = $conn->prepare("DELETE FROM cours WHERE id = ?");
    $stmt->bind_param("i", $id);
    
    if ($stmt->execute()) {
        echo "success";
    } else {
        echo "error";
    }
    $stmt->close();
}
$conn->close();
?>