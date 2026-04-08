<?php

declare(strict_types=1);

require_once __DIR__ . '/backend/includes/bootstrap.php';

if (is_authenticated()) {
    redirect('pages/cours.php');
}

require __DIR__ . '/home.html';

$chat_user_id   = $_SESSION['CIN'];
$chat_user_type = 'professeur';
$chat_user_name = $_SESSION['nom'] . ' ' . $_SESSION['prenom'];
require_once __DIR__ . '/../admin/chat_widget.php';
?>