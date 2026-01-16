<?php
require_once '../includes/config.php';
require_once '../includes/auth.php';

logoutAdmin();
header('Location: /tubes_basdat/');
exit;
