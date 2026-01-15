<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitizeInput($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    $remember = isset($_POST['remember']) ? true : false;
    
    if (empty($username) || empty($password)) {
        header('Location: /tubes_basdat/admin/login.php?error=empty');
        exit;
    }
    
    if (loginAdmin($username, $password)) {
        // Set remember me cookie if checked (30 days)
        if ($remember) {
            setcookie('admin_remember', $username, time() + (30 * 24 * 60 * 60), '/tubes_basdat/', '', true, true);
        } else {
            setcookie('admin_remember', '', time() - 3600, '/tubes_basdat/', '', true, true);
        }
        
        // Redirect to dashboard
        header('Location: /tubes_basdat/admin/dashboard.php');
        exit;
    } else {
        header('Location: /tubes_basdat/admin/login.php?error=invalid');
        exit;
    }
}

header('Location: /tubes_basdat/admin/login.php');
?>
