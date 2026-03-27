<?php

require_once __DIR__ . '/../includes/bootstrap.php';

logout_user();
set_flash('success', 'Deconnexion effectuee.');
redirect('../../pages/login.php');
