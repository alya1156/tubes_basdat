<?php
// Database Configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'hotel_db');
define('DB_PORT', 3306);

// Hotel Info
define('HOTEL_NAME', 'Grand Hotel Ashri');
define('HOTEL_PHONE', '+62 812-3456-7890');
define('HOTEL_EMAIL', 'info@grandhotelashri.com');
define('HOTEL_ADDRESS', 'Jl. Pantai Indah No. 456, Denpasar, Bali');
define('HOTEL_DESC', 'Nikmati pengalaman menginap mewah di tepi pantai dengan fasilitas world-class dan layanan prima');

// Bank Info
define('BANK_NAME', 'BCA');
define('BANK_ACCOUNT', '1234567890');
define('BANK_ACCOUNT_NAME', 'PT. Hotel Xyz');

// Admin Credentials (for first login setup)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD', '1234');
define('ADMIN_PASSWORD_HASH', '$2y$10$qzLG0X5gVm5EfYU7W9ZGP.WVnVEGfNGJB2v3qCT5yv5QWJiWPUg1u'); // hashed password for '1234'

// Booking Code Format
define('BOOKING_CODE_PREFIX', 'HTL');

// Upload Directories
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('UPLOAD_URL', '/tubes_basdat/uploads/');

// Session & Cookie
ini_set('session.gc_maxlifetime', 3600);
session_start();

// PDO Connection
try {
    $pdo = new PDO(
        'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4',
        DB_USER,
        DB_PASS,
        [
            PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES => false,
        ]
    );
} catch (PDOException $e) {
    die('Database Connection Error: ' . $e->getMessage());
}

// Timezone
date_default_timezone_set('Asia/Jakarta');
