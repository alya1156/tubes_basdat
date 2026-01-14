<?php
// Authentication Helper

require_once __DIR__ . '/config.php';

/**
 * Login admin with username and password
 */
function loginAdmin($username, $password) {
    global $pdo;
    
    $username = sanitizeInput($username);
    
    // Check credentials
    if ($username === ADMIN_USERNAME && $password === ADMIN_PASSWORD) {
        $_SESSION['admin_logged_in'] = true;
        $_SESSION['admin_username'] = $username;
        $_SESSION['login_time'] = time();
        return true;
    }
    
    return false;
}

/**
 * Logout admin
 */
function logoutAdmin() {
    session_destroy();
    return true;
}

/**
 * Check if admin is logged in and session is valid
 */
function requireAdminLogin() {
    if (!isAdminLoggedIn()) {
        header('Location: /tubes_basdat/admin/login.php');
        exit;
    }
    
    // Check session timeout (1 hour)
    if (time() - $_SESSION['login_time'] > 3600) {
        logoutAdmin();
        header('Location: /tubes_basdat/admin/login.php?timeout=1');
        exit;
    }
    
    // Update login time
    $_SESSION['login_time'] = time();
}

/**
 * Verify password hash
 */
function verifyPasswordHash($password, $hash) {
    return password_verify($password, $hash);
}

/**
 * Generate password hash
 */
function hashPassword($password) {
    return password_hash($password, PASSWORD_BCRYPT, ['cost' => 10]);
}
