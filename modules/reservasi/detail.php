<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT r.*, t.nama, t.email, t.no_telp, k.no_kamar, tp.nama_tipe, tp.harga_malam, p.status as payment_status, p.metode, p.tgl_bayar 
          FROM reservasi r 
          JOIN tamu t ON r.id_tamu = t.id_tamu 
          JOIN kamar k ON r.id_kamar = k.id_kamar 
          JOIN tipe_kamar tp ON k.id_tipe = tp.id_tipe 
          LEFT JOIN pembayaran p ON r.id_reservasi = p.id_reservasi 
          WHERE r.id_reservasi = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$reservasi = $stmt->fetch();

if (!$reservasi) {
    header('Location: /tubes_basdat/modules/reservasi/list.php');
    exit;
}

$days = calculateDays($reservasi['tgl_masuk'], $reservasi['tgl_keluar']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $new_status = sanitizeInput($_POST['status']);
    $pdo->prepare("UPDATE reservasi SET status = ? WHERE id_reservasi = ?")->execute([$new_status, $id]);
    
    if ($new_status === 'checked_in') {
        $pdo->prepare("UPDATE kamar SET status = 'ditempati' WHERE id_kamar = ?")->execute([$reservasi['id_kamar']]);
    } elseif ($new_status === 'checked_out') {
        $pdo->prepare("UPDATE kamar SET status = 'tersedia' WHERE id_kamar = ?")->execute([$reservasi['id_kamar']]);
    }
    
    redirectWithMessage('/tubes_basdat/modules/reservasi/list.php', 'Status reservasi berhasil diupdate', 'success');
}

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Reservasi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #0f0f1e; color: #e0e0e0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 250px; left: 0; top: 0; border-right: 2px solid #d4af37; }
        .sidebar .brand { color: white; padding: 20px; text-align: center; border-bottom: 2px solid #d4af37; margin-bottom: 20px; cursor: pointer; }
        .sidebar .brand h5 { margin: 0; font-weight: 700; color: #d4af37; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-left: 3px solid transparent; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #d4af37; background: rgba(212, 175, 55, 0.1); border-left-color: #d4af37; }
        .main-content { margin-left: 250px; padding: 20px; }
        .card { background: #1a1a2e; border: 1px solid #d4af37; box-shadow: 0 2px 4px rgba(212, 175, 55, 0.1); border-radius: 8px; margin-bottom: 20px; color: #e0e0e0; }
        .info-box { background: rgba(212, 175, 55, 0.1); padding: 15px; border-radius: 8px; margin-bottom: 15px; border-left: 3px solid #d4af37; }
        .label-info { font-weight: 600; color: #d4af37; font-size: 13px; text-transform: uppercase; }
        .value-info { font-size: 16px; color: #e0e0e0; font-weight: 500; }
        .btn-secondary { background: #16213e; border-color: #d4af37; color: #d4af37; }
        .btn-secondary:hover { background: #1a1a2e; border-color: #ffd700; color: #ffd700; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link"><i class="bi bi-door-closed"></i> Cek Kamar</a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link active"><i class="bi bi-calendar-check"></i> Reservasi</a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link"><i class="bi bi-credit-card"></i> Pembayaran</a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link"><i class="bi bi-people"></i> Tamu</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <a href="/tubes_basdat/modules/reservasi/list.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

        <div class="card p-4">
            <h3 style="margin-bottom: 20px;">Detail Reservasi</h3>

            <div class="info-box">
                <div class="label-info">Kode Booking</div>
                <div class="value-info"><code><?php echo htmlspecialchars($reservasi['kode_booking']); ?></code></div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Nama Tamu</div>
                        <div class="value-info"><?php echo htmlspecialchars($reservasi['nama']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Email</div>
                        <div class="value-info"><?php echo htmlspecialchars($reservasi['email']); ?></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Kamar</div>
                        <div class="value-info"><?php echo htmlspecialchars($reservasi['no_kamar']) . ' - ' . htmlspecialchars($reservasi['nama_tipe']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Jumlah Tamu</div>
                        <div class="value-info"><?php echo $reservasi['jumlah_tamu']; ?> orang</div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Check-in</div>
                        <div class="value-info"><?php echo formatDate($reservasi['tgl_masuk']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Check-out</div>
                        <div class="value-info"><?php echo formatDate($reservasi['tgl_keluar']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Durasi</div>
                        <div class="value-info"><?php echo $days; ?> malam</div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Harga per Malam</div>
                        <div class="value-info"><?php echo formatCurrency($reservasi['harga_malam']); ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Total Harga</div>
                        <div class="value-info"><?php echo formatCurrency($reservasi['total_harga']); ?></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Status Reservasi</div>
                        <div class="value-info"><span class="badge bg-primary"><?php echo ucfirst($reservasi['status']); ?></span></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Status Pembayaran</div>
                        <div class="value-info">
                            <?php if ($reservasi['payment_status']): ?>
                                <span class="badge bg-<?php echo $reservasi['payment_status'] === 'lunas' ? 'success' : 'warning'; ?>">
                                    <?php echo ucfirst($reservasi['payment_status']); ?>
                                </span>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum ada</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>

            <form method="POST" class="mt-4">
                <div class="mb-3">
                    <label class="form-label">Update Status Reservasi</label>
                    <select name="status" class="form-control" required>
                        <option value="pending" <?php echo $reservasi['status'] === 'pending' ? 'selected' : ''; ?>>Pending</option>
                        <option value="konfirmasi" <?php echo $reservasi['status'] === 'konfirmasi' ? 'selected' : ''; ?>>Konfirmasi</option>
                        <option value="checked_in" <?php echo $reservasi['status'] === 'checked_in' ? 'selected' : ''; ?>>Checked In</option>
                        <option value="checked_out" <?php echo $reservasi['status'] === 'checked_out' ? 'selected' : ''; ?>>Checked Out</option>
                        <option value="batal" <?php echo $reservasi['status'] === 'batal' ? 'selected' : ''; ?>>Batal</option>
                    </select>
                </div>
                <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Update Status</button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
