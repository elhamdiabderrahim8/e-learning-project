<?php
$msg = (string) ($_GET['msg'] ?? 'Erreur de connexion.');
$query = http_build_query([
    'msg' => $msg,
    'success' => '0',
]);

header('Location: login.php?' . $query);
exit();
