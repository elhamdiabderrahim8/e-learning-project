<?php
require_once __DIR__ . '/session_prof.php';
require_once __DIR__ . '/../shared/auth.php';

shared_logout();
header("Location: login.html");
exit();
