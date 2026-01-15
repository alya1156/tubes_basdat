<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$id_kamar = isset($_GET['id_kamar']) ? (int)$_GET['id_kamar'] : 0;
$checkIn = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkOut = isset($_GET['checkout']) ? $_GET['checkout'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

// Get room details
$stmt = $pdo->prepare("SELECT k.*, t.harga_malam, t.nama_tipe FROM kamar k JOIN tipe_kamar t ON k.id_tipe = t.id_tipe WHERE k.id_kamar = ?");
$stmt->execute([$id_kamar]);
$room = $stmt->fetch();

if (!$room || empty($checkIn) || empty($checkOut)) {
    header('Location: /tubes_basdat/guest/booking.php');
    exit;
}

$days = calculateDays($checkIn, $checkOut);
$totalPrice = $room['harga_malam'] * $days;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $no_identitas = sanitizeInput($_POST['no_identitas']);
    $email = sanitizeInput($_POST['email']);
    $no_telp = sanitizeInput($_POST['no_telp']);
    
    if (empty($nama) || empty($email) || empty($no_telp)) {
        $error = 'Semua field wajib diisi';
    } else {
        try {
            // Insert guest (without alamat)
            $stmt = $pdo->prepare("INSERT INTO tamu (nama, no_identitas, email, no_telp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $no_identitas, $email, $no_telp]);
            $guestId = $pdo->lastInsertId();
            
            // Generate booking code
            $bookingCode = generateBookingCode();
            
            // Insert reservation
            $stmt = $pdo->prepare("INSERT INTO reservasi (kode_booking, id_tamu, id_kamar, tgl_masuk, tgl_keluar, jumlah_tamu, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$bookingCode, $guestId, $id_kamar, $checkIn, $checkOut, $guests, $totalPrice]);
            $reservasiId = $pdo->lastInsertId();
            
            // Insert payment record
            $stmt = $pdo->prepare("INSERT INTO pembayaran (id_reservasi, jumlah, metode, status) VALUES (?, ?, 'transfer_bank', 'pending')");
            $stmt->execute([$reservasiId, $totalPrice]);
            
            // Update room status
            $pdo->prepare("UPDATE kamar SET status = 'terpesan' WHERE id_kamar = ?")->execute([$id_kamar]);
            
            // Redirect to payment page
            header("Location: /tubes_basdat/guest/struk.php?kode={$bookingCode}");
            exit;
        } catch (Exception $e) {
            $error = 'Terjadi kesalahan: ' . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Proses Booking - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root { --primary: #1a1a2e; --secondary: #16213e; --accent: #d4af37; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f0f1e; color: #e0e0e0; }
        .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-bottom: 2px solid #d4af37; }
        .navbar-brand { font-weight: 700; font-size: 24px; color: #d4af37; }
        .page-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #d4af37; padding: 40px 0; border-bottom: 2px solid #d4af37; }
        .page-header h1 { color: #d4af37; }
        .section { padding: 40px 0; }
        .card { border: 1px solid #d4af37; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1); border-radius: 12px; background: #1a1a2e; color: #e0e0e0; }
        .form-section { background: #1a1a2e; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1); margin-bottom: 30px; border: 1px solid #d4af37; }
        .summary-box { background: #1a1a2e; padding: 25px; border-radius: 12px; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1); border-left: 4px solid #d4af37; }
        .summary-row { display: flex; justify-content: space-between; margin-bottom: 12px; padding-bottom: 12px; border-bottom: 1px solid #333; }
        .summary-row:last-child { border-bottom: none; }
        .summary-label { color: #888; font-size: 13px; }
        .summary-value { font-weight: 600; color: #d4af37; }
        .summary-total { font-size: 20px; color: #ffd700; font-weight: 700; margin-top: 15px; padding-top: 15px; border-top: 2px solid #d4af37; }
        .btn-primary { background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%); border: none; color: #1a1a2e; }
        .footer { background: #0f0f1e; color: #d4af37; padding: 40px 0; text-align: center; margin-top: 80px; border-top: 2px solid #d4af37; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/tubes_basdat/"><i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?></a>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Konfirmasi Booking</h1>
            <p>Lengkapi data diri untuk menyelesaikan pemesanan</p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-section">
                        <h3 style="margin-bottom: 25px;"><i class="bi bi-person-check"></i> Data Diri Tamu</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger" role="alert">
                                <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                            </div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label"><strong>Nama Lengkap</strong> <span class="text-danger">*</span></label>
                                <input type="text" name="nama" class="form-control" required placeholder="Contoh: Budi Santoso">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>No. Identitas</strong></label>
                                <input type="text" name="no_identitas" class="form-control" placeholder="KTP, Paspor, atau SIM">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>Email</strong> <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" required placeholder="contoh@email.com">
                            </div>
                            <div class="mb-3">
                                <label class="form-label"><strong>No. Telepon</strong> <span class="text-danger">*</span></label>
                                <input type="tel" name="no_telp" class="form-control" required placeholder="+62 812 3456 7890">
                            </div>
                            
                            <div class="d-grid gap-2">
                                <button type="submit" class="btn btn-primary btn-lg">
                                    <i class="bi bi-check-circle"></i> Lanjutkan ke Pembayaran
                                </button>
                            </div>
                            <div class="mt-2">
                                <a href="/tubes_basdat/guest/booking.php" class="btn btn-outline-secondary w-100">
                                    <i class="bi bi-arrow-left"></i> Kembali Pilih Kamar
                                </a>
                            </div>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-box">
                        <h5 style="margin-bottom: 20px;"><i class="bi bi-receipt"></i> Ringkasan Pemesanan</h5>
                        
                        <div style="background: #f8f9fa; padding: 15px; border-radius: 8px; margin-bottom: 20px;">
                            <div style="font-size: 14px; color: #999; margin-bottom: 5px;">Jenis Kamar</div>
                            <div style="font-size: 18px; font-weight: 700; color: #333;"><?php echo htmlspecialchars($room['nama_tipe']); ?></div>
                            <div style="font-size: 12px; color: #999; margin-top: 5px;">Kamar No. <?php echo htmlspecialchars($room['no_kamar']); ?></div>
                        </div>

                        <div class="summary-row">
                            <span class="summary-label">Check-in</span>
                            <span class="summary-value"><?php echo formatDate($checkIn); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Check-out</span>
                            <span class="summary-value"><?php echo formatDate($checkOut); ?></span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Durasi Menginap</span>
                            <span class="summary-value"><?php echo $days; ?> malam</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Jumlah Tamu</span>
                            <span class="summary-value"><?php echo $guests; ?> orang</span>
                        </div>
                        <div class="summary-row">
                            <span class="summary-label">Harga per Malam</span>
                            <span class="summary-value"><?php echo formatCurrency($room['harga_malam']); ?></span>
                        </div>
                        
                        <div class="summary-total">
                            <div style="font-size: 12px; color: #999; margin-bottom: 8px;">Total Harga</div>
                            <?php echo formatCurrency($totalPrice); ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 12px;"><a href="/tubes_basdat/admin/login.php" style="color: #aaa; text-decoration: none;"><i class="bi bi-lock-fill"></i> Admin Login</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
