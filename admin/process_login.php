<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        header('Location: /tubes_basdat/admin/login.php?error=empty');
        exit;
    }
    
    if (loginAdmin($username, $password)) {
        header('Location: /tubes_basdat/admin/dashboard.php');
        exit;
    } else {
        header('Location: /tubes_basdat/admin/login.php?error=invalid');
        exit;
    }
}

header('Location: /tubes_basdat/admin/login.php');
