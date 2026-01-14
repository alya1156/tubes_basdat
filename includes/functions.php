<?php
// Helper Functions

/**
 * Generate Booking Code
 * Format: HTL-YYYY-[AUTO_ID]-[RANDOM]
 * Example: HTL-2026-001-ABC123
 */
function generateBookingCode() {
    $prefix = BOOKING_CODE_PREFIX;
    $year = date('Y');
    $random = strtoupper(substr(bin2hex(random_bytes(3)), 0, 6));
    
    return "{$prefix}-{$year}-" . str_pad(random_int(1, 999), 3, '0', STR_PAD_LEFT) . "-{$random}";
}

/**
 * Format Currency (IDR)
 */
function formatCurrency($amount) {
    return 'Rp ' . number_format($amount, 0, ',', '.');
}

/**
 * Format Date to Indonesian Format
 */
function formatDate($date, $format = 'd M Y') {
    $months = [
        'Jan' => 'Jan',
        'Feb' => 'Feb',
        'Mar' => 'Mar',
        'Apr' => 'Apr',
        'May' => 'Mei',
        'Jun' => 'Jun',
        'Jul' => 'Jul',
        'Aug' => 'Ags',
        'Sep' => 'Sep',
        'Oct' => 'Okt',
        'Nov' => 'Nov',
        'Dec' => 'Des'
    ];
    
    $dateObj = new DateTime($date);
    $formatted = $dateObj->format($format);
    
    foreach ($months as $en => $id) {
        $formatted = str_replace($en, $id, $formatted);
    }
    
    return $formatted;
}

/**
 * Calculate days between two dates
 */
function calculateDays($checkIn, $checkOut) {
    $start = new DateTime($checkIn);
    $end = new DateTime($checkOut);
    $interval = $start->diff($end);
    return $interval->days;
}

/**
 * Get available rooms for date range
 */
function getAvailableRooms($checkIn, $checkOut, $pdo) {
    $query = "
        SELECT DISTINCT k.*, t.nama_tipe, t.kapasitas, t.harga_malam, t.foto_cover
        FROM kamar k
        JOIN tipe_kamar t ON k.id_tipe = t.id_tipe
        WHERE k.id_kamar NOT IN (
            SELECT k2.id_kamar FROM kamar k2
            JOIN reservasi r ON k2.id_kamar = r.id_kamar
            WHERE r.status IN ('pending', 'konfirmasi', 'checked_in')
            AND (
                (r.tgl_masuk <= ? AND r.tgl_keluar > ?)
                OR (r.tgl_masuk < ? AND r.tgl_keluar >= ?)
                OR (r.tgl_masuk >= ? AND r.tgl_keluar <= ?)
            )
        )
        AND k.status = 'tersedia'
        ORDER BY t.nama_tipe
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$checkOut, $checkIn, $checkOut, $checkIn, $checkIn, $checkOut]);
    
    return $stmt->fetchAll();
}

/**
 * Get room by ID with facilities
 */
function getRoomWithFacilities($roomId, $pdo) {
    $query = "
        SELECT k.*, t.*, f.id_fasilitas, f.nama_fasilitas, f.icon
        FROM kamar k
        JOIN tipe_kamar t ON k.id_tipe = t.id_tipe
        LEFT JOIN kamar_fasilitas kf ON k.id_kamar = kf.id_kamar
        LEFT JOIN fasilitas f ON kf.id_fasilitas = f.id_fasilitas
        WHERE k.id_kamar = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$roomId]);
    
    return $stmt->fetchAll();
}

/**
 * Get reservation by booking code
 */
function getReservationByCode($bookingCode, $pdo) {
    $query = "
        SELECT r.*, t.*, k.no_kamar, tp.nama_tipe, tp.harga_malam, p.status as payment_status, p.metode
        FROM reservasi r
        JOIN tamu t ON r.id_tamu = t.id_tamu
        JOIN kamar k ON r.id_kamar = k.id_kamar
        JOIN tipe_kamar tp ON k.id_tipe = tp.id_tipe
        LEFT JOIN pembayaran p ON r.id_reservasi = p.id_reservasi
        WHERE r.kode_booking = ?
    ";
    
    $stmt = $pdo->prepare($query);
    $stmt->execute([$bookingCode]);
    
    return $stmt->fetch();
}

/**
 * Upload image file
 */
function uploadImage($file, $uploadDir) {
    if (!isset($file['tmp_name']) || !is_uploaded_file($file['tmp_name'])) {
        return ['success' => false, 'message' => 'File upload failed'];
    }
    
    // Validate file type
    $allowedTypes = ['image/jpeg', 'image/png', 'image/gif', 'image/webp'];
    if (!in_array($file['type'], $allowedTypes)) {
        return ['success' => false, 'message' => 'Invalid file type. Only JPEG, PNG, GIF, WebP allowed'];
    }
    
    // Validate file size (max 5MB)
    if ($file['size'] > 5 * 1024 * 1024) {
        return ['success' => false, 'message' => 'File too large. Max 5MB'];
    }
    
    // Create directory if doesn't exist
    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }
    
    // Generate unique filename
    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = uniqid('img_') . '.' . $ext;
    $filepath = $uploadDir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename, 'path' => $filepath];
    }
    
    return ['success' => false, 'message' => 'Failed to move uploaded file'];
}

/**
 * Redirect with message
 */
function redirectWithMessage($url, $message, $type = 'success') {
    $_SESSION['message'] = $message;
    $_SESSION['message_type'] = $type;
    header('Location: ' . $url);
    exit;
}

/**
 * Get and clear session message
 */
function getSessionMessage() {
    if (isset($_SESSION['message'])) {
        $message = $_SESSION['message'];
        $type = $_SESSION['message_type'] ?? 'info';
        unset($_SESSION['message']);
        unset($_SESSION['message_type']);
        return ['message' => $message, 'type' => $type];
    }
    return null;
}

/**
 * Check admin login
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

/**
 * Get dashboard statistics
 */
function getDashboardStats($pdo) {
    $today = date('Y-m-d');
    
    // Revenue today
    $revenueQuery = "
        SELECT SUM(jumlah) as total
        FROM pembayaran
        WHERE DATE(tgl_bayar) = ? AND status = 'lunas'
    ";
    $revenueStmt = $pdo->prepare($revenueQuery);
    $revenueStmt->execute([$today]);
    $revenue = $revenueStmt->fetch()['total'] ?? 0;
    
    // Occupied rooms
    $occupiedQuery = "
        SELECT COUNT(DISTINCT k.id_kamar) as total
        FROM kamar k
        JOIN reservasi r ON k.id_kamar = r.id_kamar
        WHERE r.status IN ('checked_in')
    ";
    $occupiedStmt = $pdo->query($occupiedQuery);
    $occupied = $occupiedStmt->fetch()['total'] ?? 0;
    
    // Pending reservations
    $pendingQuery = "SELECT COUNT(*) as total FROM reservasi WHERE status = 'pending'";
    $pendingStmt = $pdo->query($pendingQuery);
    $pending = $pendingStmt->fetch()['total'] ?? 0;
    
    // Unpaid payments
    $unpaidQuery = "
        SELECT COUNT(*) as total
        FROM pembayaran
        WHERE status IN ('pending', 'verifikasi')
    ";
    $unpaidStmt = $pdo->query($unpaidQuery);
    $unpaid = $unpaidStmt->fetch()['total'] ?? 0;
    
    return [
        'revenue_today' => $revenue,
        'occupied_rooms' => $occupied,
        'pending_reservations' => $pending,
        'unpaid_payments' => $unpaid
    ];
}

/**
 * Sanitize input
 */
function sanitizeInput($input) {
    return htmlspecialchars(trim($input), ENT_QUOTES, 'UTF-8');
}

/**
 * Validate email
 */
function validateEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL);
}

/**
 * Validate phone number
 */
function validatePhone($phone) {
    return preg_match('/^(\+62|0)[0-9]{9,12}$/', str_replace('-', '', $phone));
}
