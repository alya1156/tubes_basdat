<?php
// includes/config.php
// Konfigurasi koneksi database dan konstanta aplikasi

// Mulai session jika belum
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ---- Konfigurasi database (sesuaikan jika perlu) ----
define('DB_HOST', 'localhost');
define('DB_NAME', 'hotel_db'); // sesuai schema.sql
define('DB_USER', 'root');
define('DB_PASS', ''); // isi password jika ada
define('DB_CHARSET', 'utf8mb4');

// ---- Konfigurasi aplikasi ----
define('BASE_URL', '/tubes_basdat/'); // path relatif aplikasi di webroot
define('UPLOAD_URL', BASE_URL . 'uploads/'); // folder upload (sesuaikan struktur folder)
define('HOTEL_NAME', 'Hotel Galasa');
define('HOTEL_DESC', 'Resort eksklusif dengan layanan bintang lima.');
define('HOTEL_ADDRESS', 'Jl. Galasa No.1, Indonesia');
define('HOTEL_PHONE', '+62 81 2345 6789');
define('HOTEL_EMAIL', 'info@hotelgalasa.com');

// ---- Error reporting (development) ----
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// ---- Buat koneksi PDO ----
$dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;

try {
    $pdoOptions = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    $pdo = new PDO($dsn, DB_USER, DB_PASS, $pdoOptions);
} catch (PDOException $e) {
    // Jika di production, jangan tampilkan detail error ke user; log saja.
    die("Koneksi Database Gagal: " . $e->getMessage());
}