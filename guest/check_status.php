<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$bookingCode = '';
$reservasi = null;
$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $bookingCode = sanitizeInput($_POST['kode_booking']);
    if (!empty($bookingCode)) {
        $reservasi = getReservationByCode($bookingCode, $pdo);
        if (!$reservasi) {
            $error = 'Kode booking tidak ditemukan. Silakan periksa kembali.';
        }
    } else {
        $error = 'Masukkan kode booking terlebih dahulu.';
    }
}

if ($reservasi) {
    $days = calculateDays($reservasi['tgl_masuk'], $reservasi['tgl_keluar']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 0; }
        .section { padding: 40px 0; }
        .search-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 500px; margin: 0 auto 40px; }
        .result-box { background: white; padding: 30px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 700px; margin: 0 auto; }
        .status-badge { display: inline-block; padding: 8px 16px; border-radius: 20px; font-weight: 600; margin: 5px 0; }
        .status-pending { background: #fff3cd; color: #856404; }
        .status-konfirmasi { background: #d4edda; color: #155724; }
        .status-checked-in { background: #cfe2ff; color: #084298; }
        .status-checked-out { background: #d1e7dd; color: #0f5132; }
        .footer { background: #333; color: white; padding: 40px 0; text-align: center; margin-top: 80px; }
        .info-line { padding: 12px 0; border-bottom: 1px solid #eee; }
        .info-label { font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .info-value { font-size: 16px; color: #333; margin-top: 5px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/tubes_basdat/"><i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/booking.php">Booking</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Cek Status Reservasi</h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <!-- Search Form -->
            <div class="search-box">
                <form method="POST">
                    <label class="form-label"><strong>Masukkan Kode Booking</strong></label>
                    <div class="input-group">
                        <input type="text" name="kode_booking" class="form-control form-control-lg" placeholder="Contoh: HTL-2026-001-ABC123" value="<?php echo htmlspecialchars($bookingCode); ?>" required>
                        <button class="btn btn-primary" type="submit">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <small class="text-muted d-block mt-2">Kode booking Anda ada di struk pemesanan</small>
                </form>
            </div>

            <!-- Error Message -->
            <?php if ($error): ?>
                <div class="alert alert-danger alert-dismissible fade show" role="alert">
                    <i class="bi bi-exclamation-circle"></i> <?php echo $error; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <!-- Result -->
            <?php if ($reservasi): ?>
                <div class="result-box">
                    <h4 style="margin-bottom: 20px;">Hasil Pencarian</h4>

                    <div class="info-line">
                        <div class="info-label">Kode Booking</div>
                        <div class="info-value" style="font-family: monospace; font-size: 18px; color: #667eea; font-weight: 700;">
                            <?php echo htmlspecialchars($reservasi['kode_booking']); ?>
                        </div>
                    </div>

                    <div style="margin: 20px 0;">
                        <div class="info-label">Status Reservasi</div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge status-<?php echo $reservasi['status']; ?>">
                                <i class="bi bi-circle-fill"></i> <?php echo ucfirst($reservasi['status']); ?>
                            </span>
                        </div>
                    </div>

                    <div style="margin: 20px 0;">
                        <div class="info-label">Status Pembayaran</div>
                        <div style="margin-top: 10px;">
                            <span class="status-badge status-<?php echo $reservasi['payment_status'] ?? 'pending'; ?>">
                                <i class="bi bi-circle-fill"></i> <?php echo ucfirst($reservasi['payment_status'] ?? 'Pending'); ?>
                            </span>
                        </div>
                    </div>

                    <hr style="margin: 30px 0;">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-line">
                                <div class="info-label">Nama Tamu</div>
                                <div class="info-value"><?php echo htmlspecialchars($reservasi['nama']); ?></div>
                            </div>
                            <div class="info-line">
                                <div class="info-label">Email</div>
                                <div class="info-value"><?php echo htmlspecialchars($reservasi['email']); ?></div>
                            </div>
                            <div class="info-line">
                                <div class="info-label">No. Telepon</div>
                                <div class="info-value"><?php echo htmlspecialchars($reservasi['no_telp']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-line">
                                <div class="info-label">Tipe Kamar</div>
                                <div class="info-value"><?php echo htmlspecialchars($reservasi['nama_tipe']); ?></div>
                            </div>
                            <div class="info-line">
                                <div class="info-label">No. Kamar</div>
                                <div class="info-value"><?php echo htmlspecialchars($reservasi['no_kamar']); ?></div>
                            </div>
                            <div class="info-line">
                                <div class="info-label">Jumlah Tamu</div>
                                <div class="info-value"><?php echo $reservasi['jumlah_tamu']; ?> orang</div>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 30px 0;">

                    <div class="row">
                        <div class="col-md-4">
                            <div class="info-line">
                                <div class="info-label">Check-in</div>
                                <div class="info-value"><?php echo formatDate($reservasi['tgl_masuk']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-line">
                                <div class="info-label">Check-out</div>
                                <div class="info-value"><?php echo formatDate($reservasi['tgl_keluar']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="info-line">
                                <div class="info-label">Durasi</div>
                                <div class="info-value"><?php echo $days; ?> malam</div>
                            </div>
                        </div>
                    </div>

                    <hr style="margin: 30px 0;">

                    <div class="row">
                        <div class="col-md-6">
                            <div class="info-line">
                                <div class="info-label">Harga per Malam</div>
                                <div class="info-value"><?php echo formatCurrency($reservasi['harga_malam']); ?></div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="info-line">
                                <div class="info-label">Total Harga</div>
                                <div class="info-value" style="font-size: 20px; color: #667eea; font-weight: 700;">
                                    <?php echo formatCurrency($reservasi['total_harga']); ?>
                                </div>
                            </div>
                        </div>
                    </div>

                    <div style="margin-top: 30px; text-align: center;">
                        <button class="btn btn-primary btn-lg" onclick="window.print()">
                            <i class="bi bi-printer"></i> Cetak Struk
                        </button>
                    </div>
                </div>
            <?php elseif ($_SERVER['REQUEST_METHOD'] === 'POST' && !$error): ?>
                <!-- Initial state - show empty -->
            <?php endif; ?>
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
