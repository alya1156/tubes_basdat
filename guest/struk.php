<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$bookingCode = isset($_GET['kode']) ? sanitizeInput($_GET['kode']) : '';
$reservasi = getReservationByCode($bookingCode, $pdo);

if (!$reservasi) {
    die('Booking code tidak ditemukan');
}

$days = calculateDays($reservasi['tgl_masuk'], $reservasi['tgl_keluar']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Struk Pemesanan - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        @media print {
            .no-print { display: none; }
        }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .section { padding: 40px 0; }
        .struk-container { background: white; padding: 40px; border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1); max-width: 600px; margin: 0 auto; }
        .struk-header { text-align: center; border-bottom: 2px solid #667eea; padding-bottom: 20px; margin-bottom: 30px; }
        .struk-header h2 { color: #667eea; font-weight: 700; }
        .struk-section { margin-bottom: 30px; }
        .struk-label { font-weight: 600; color: #666; font-size: 12px; text-transform: uppercase; }
        .struk-value { font-size: 16px; color: #333; margin-bottom: 15px; }
        .struk-line { display: flex; justify-content: space-between; padding: 10px 0; border-bottom: 1px solid #ddd; }
        .struk-line.total { border-bottom: 2px solid #667eea; font-weight: 700; font-size: 18px; color: #667eea; }
        .alert-success { background: #d4edda; border: 1px solid #c3e6cb; color: #155724; }
        .footer { background: #333; color: white; padding: 40px 0; text-align: center; margin-top: 80px; }
        .btn-group-vertical { gap: 10px; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark no-print">
        <div class="container">
            <a class="navbar-brand" href="/tubes_basdat/"><i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?></a>
        </div>
    </nav>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="struk-container">
                <div class="struk-header">
                    <h2><?php echo HOTEL_NAME; ?></h2>
                    <p class="text-muted">Struk Pemesanan Kamar</p>
                </div>

                <div class="alert alert-success" role="alert">
                    <i class="bi bi-check-circle"></i> <strong>Pemesanan Berhasil!</strong><br>
                    Simpan kode booking ini untuk mengecek status reservasi Anda.
                </div>

                <div class="struk-section">
                    <div class="struk-label">Kode Booking</div>
                    <div class="struk-value" style="font-size: 20px; color: #667eea; font-weight: 700; font-family: monospace;">
                        <?php echo htmlspecialchars($reservasi['kode_booking']); ?>
                    </div>
                </div>

                <hr>

                <div class="struk-section">
                    <h5 style="margin-bottom: 15px;">Informasi Tamu</h5>
                    <div class="struk-line">
                        <span>Nama:</span>
                        <strong><?php echo htmlspecialchars($reservasi['nama']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>Email:</span>
                        <strong><?php echo htmlspecialchars($reservasi['email']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>No. Telepon:</span>
                        <strong><?php echo htmlspecialchars($reservasi['no_telp']); ?></strong>
                    </div>
                </div>

                <hr>

                <div class="struk-section">
                    <h5 style="margin-bottom: 15px;">Detail Kamar</h5>
                    <div class="struk-line">
                        <span>Tipe Kamar:</span>
                        <strong><?php echo htmlspecialchars($reservasi['nama_tipe']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>No. Kamar:</span>
                        <strong><?php echo htmlspecialchars($reservasi['no_kamar']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>Jumlah Tamu:</span>
                        <strong><?php echo $reservasi['jumlah_tamu']; ?> orang</strong>
                    </div>
                </div>

                <hr>

                <div class="struk-section">
                    <h5 style="margin-bottom: 15px;">Tanggal</h5>
                    <div class="struk-line">
                        <span>Check-in:</span>
                        <strong><?php echo formatDate($reservasi['tgl_masuk']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>Check-out:</span>
                        <strong><?php echo formatDate($reservasi['tgl_keluar']); ?></strong>
                    </div>
                    <div class="struk-line">
                        <span>Durasi:</span>
                        <strong><?php echo $days; ?> malam</strong>
                    </div>
                </div>

                <hr>

                <div class="struk-section">
                    <h5 style="margin-bottom: 15px;">Perhitungan Harga</h5>
                    <div class="struk-line">
                        <span><?php echo formatCurrency($reservasi['harga_malam']); ?> × <?php echo $days; ?> malam</span>
                        <strong><?php echo formatCurrency($reservasi['harga_malam'] * $days); ?></strong>
                    </div>
                    <div class="struk-line total">
                        <span>TOTAL HARGA</span>
                        <strong><?php echo formatCurrency($reservasi['total_harga']); ?></strong>
                    </div>
                </div>

                <hr>

                <div class="struk-section">
                    <h5 style="margin-bottom: 15px;">Status Pembayaran</h5>
                    <div class="alert alert-warning">
                        <i class="bi bi-clock-history"></i> Status: <strong>MENUNGGU PEMBAYARAN</strong><br>
                        <small>Silakan melakukan pembayaran sesuai dengan metode yang tersedia.</small>
                    </div>
                    <h6 style="margin-top: 20px; margin-bottom: 10px;">Transfer Bank:</h6>
                    <p style="margin-bottom: 5px;">
                        Bank: <strong><?php echo BANK_NAME; ?></strong><br>
                        Nomor Rekening: <strong><?php echo BANK_ACCOUNT; ?></strong><br>
                        Atas Nama: <strong><?php echo BANK_ACCOUNT_NAME; ?></strong><br>
                        Jumlah: <strong><?php echo formatCurrency($reservasi['total_harga']); ?></strong>
                    </p>
                </div>

                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Setelah melakukan pembayaran, silakan upload bukti pembayaran atau hubungi hotel untuk verifikasi.
                </div>

                <div class="d-grid gap-2 no-print">
                    <button class="btn btn-primary btn-lg" onclick="window.print()">
                        <i class="bi bi-printer"></i> Cetak Struk
                    </button>
                    <a href="/tubes_basdat/guest/check_status.php" class="btn btn-secondary btn-lg">
                        <i class="bi bi-arrow-left"></i> Kembali
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer no-print">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
