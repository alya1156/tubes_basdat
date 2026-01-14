<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$roomId = isset($_GET['room']) ? (int)$_GET['room'] : 0;
$checkIn = isset($_GET['checkin']) ? $_GET['checkin'] : '';
$checkOut = isset($_GET['checkout']) ? $_GET['checkout'] : '';
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

// Get room details
$stmt = $pdo->prepare("SELECT k.*, t.harga_malam, t.nama_tipe FROM kamar k JOIN tipe_kamar t ON k.id_tipe = t.id_tipe WHERE k.id_kamar = ?");
$stmt->execute([$roomId]);
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
    $alamat = sanitizeInput($_POST['alamat']);
    
    if (empty($nama) || empty($email) || empty($no_telp)) {
        $error = 'Semua field wajib diisi';
    } else {
        try {
            // Insert guest
            $stmt = $pdo->prepare("INSERT INTO tamu (nama, no_identitas, email, no_telp, alamat) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama, $no_identitas, $email, $no_telp, $alamat]);
            $guestId = $pdo->lastInsertId();
            
            // Generate booking code
            $bookingCode = generateBookingCode();
            
            // Insert reservation
            $stmt = $pdo->prepare("INSERT INTO reservasi (kode_booking, id_tamu, id_kamar, tgl_masuk, tgl_keluar, jumlah_tamu, total_harga, status) VALUES (?, ?, ?, ?, ?, ?, ?, 'pending')");
            $stmt->execute([$bookingCode, $guestId, $roomId, $checkIn, $checkOut, $guests, $totalPrice]);
            $reservasiId = $pdo->lastInsertId();
            
            // Insert payment record
            $stmt = $pdo->prepare("INSERT INTO pembayaran (id_reservasi, jumlah, metode, status) VALUES (?, ?, 'transfer_bank', 'pending')");
            $stmt->execute([$reservasiId, $totalPrice]);
            
            // Update room status
            $pdo->prepare("UPDATE kamar SET status = 'terpesan' WHERE id_kamar = ?")->execute([$roomId]);
            
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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 0; }
        .section { padding: 40px 0; }
        .card { border: none; box-shadow: 0 4px 15px rgba(0,0,0,0.1); border-radius: 12px; }
        .form-section { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); margin-bottom: 30px; }
        .summary-box { background: #f8f9fa; padding: 20px; border-radius: 12px; border-left: 4px solid #667eea; }
        .footer { background: #333; color: white; padding: 40px 0; text-align: center; margin-top: 80px; }
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
            <h1>Proses Booking</h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-md-8">
                    <div class="form-section">
                        <h3 style="margin-bottom: 20px;">Data Diri Tamu</h3>
                        
                        <?php if (isset($error)): ?>
                            <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>

                        <form method="POST">
                            <div class="mb-3">
                                <label class="form-label">Nama Lengkap *</label>
                                <input type="text" name="nama" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Identitas (KTP/Paspor)</label>
                                <input type="text" name="no_identitas" class="form-control">
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Email *</label>
                                <input type="email" name="email" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">No. Telepon *</label>
                                <input type="tel" name="no_telp" class="form-control" required>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Alamat</label>
                                <textarea name="alamat" class="form-control" rows="3"></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary btn-lg" style="color: white;">
                                <i class="bi bi-check-circle"></i> Lanjutkan ke Pembayaran
                            </button>
                            <a href="/tubes_basdat/guest/booking.php" class="btn btn-secondary btn-lg">
                                <i class="bi bi-arrow-left"></i> Kembali
                            </a>
                        </form>
                    </div>
                </div>

                <div class="col-md-4">
                    <div class="summary-box">
                        <h5 style="margin-bottom: 20px;">Ringkasan Pemesanan</h5>
                        <div style="margin-bottom: 15px;">
                            <strong><?php echo htmlspecialchars($room['nama_tipe']); ?></strong><br>
                            <span class="text-muted">Kamar #<?php echo htmlspecialchars($room['no_kamar']); ?></span>
                        </div>
                        <hr>
                        <div style="margin-bottom: 15px;">
                            <small class="text-muted">Check-in</small><br>
                            <strong><?php echo formatDate($checkIn); ?></strong>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <small class="text-muted">Check-out</small><br>
                            <strong><?php echo formatDate($checkOut); ?></strong>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <small class="text-muted">Durasi</small><br>
                            <strong><?php echo $days; ?> malam</strong>
                        </div>
                        <div style="margin-bottom: 15px;">
                            <small class="text-muted">Jumlah Tamu</small><br>
                            <strong><?php echo $guests; ?> orang</strong>
                        </div>
                        <hr>
                        <div style="margin-bottom: 15px;">
                            <small class="text-muted">Harga per Malam</small><br>
                            <strong><?php echo formatCurrency($room['harga_malam']); ?></strong>
                        </div>
                        <div style="font-size: 18px; color: #667eea; font-weight: 700;">
                            <small class="text-muted">Total Harga</small><br>
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
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
