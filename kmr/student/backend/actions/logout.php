<?php

require_once __DIR__ . '/../includes/bootstrap.php';

if (($_SERVER['REQUEST_METHOD'] ?? 'GET') === 'POST') {
	logout_user();
	http_response_code(204);
	exit;
}

logout_user();
set_flash('success', 'Deconnexion effectuee.');
redirect('../../pages/login.php');
