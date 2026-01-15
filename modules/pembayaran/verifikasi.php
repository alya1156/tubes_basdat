<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$query = "SELECT p.*, r.kode_booking, t.nama, r.total_harga 
          FROM pembayaran p 
          JOIN reservasi r ON p.id_reservasi = r.id_reservasi 
          JOIN tamu t ON r.id_tamu = t.id_tamu 
          WHERE p.id_pembayaran = ?";
$stmt = $pdo->prepare($query);
$stmt->execute([$id]);
$pembayaran = $stmt->fetch();

if (!$pembayaran) {
    header('Location: /tubes_basdat/modules/pembayaran/list.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $action = sanitizeInput($_POST['action']);
    
    if ($action === 'verify') {
        $pdo->prepare("UPDATE pembayaran SET status = 'lunas', tgl_verifikasi = NOW() WHERE id_pembayaran = ?")->execute([$id]);
        $pdo->prepare("UPDATE reservasi SET status = 'konfirmasi' WHERE id_reservasi = ?")->execute([$pembayaran['id_reservasi']]);
        redirectWithMessage('/tubes_basdat/modules/pembayaran/list.php', 'Pembayaran berhasil diverifikasi', 'success');
    } elseif ($action === 'reject') {
        $catatan = sanitizeInput($_POST['catatan']);
        if (empty($catatan)) {
            $error = 'Alasan penolakan harus diisi';
        } else {
            $pdo->prepare("UPDATE pembayaran SET status = 'rejected', catatan = ? WHERE id_pembayaran = ?")->execute([$catatan, $id]);
            redirectWithMessage('/tubes_basdat/modules/pembayaran/list.php', 'Pembayaran ditolak dengan alasan: ' . $catatan, 'success');
        }
    }
}

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verifikasi Pembayaran</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 250px; left: 0; top: 0; }
        .sidebar .brand { color: white; padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-left: 3px solid transparent; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left-color: white; }
        .main-content { margin-left: 250px; padding: 20px; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: 8px; margin-bottom: 20px; }
        .info-box { background: #f0f2f5; padding: 15px; border-radius: 8px; margin-bottom: 15px; }
        .label-info { font-weight: 600; color: #666; font-size: 13px; text-transform: uppercase; }
        .value-info { font-size: 16px; color: #333; font-weight: 500; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link active"><i class="bi bi-credit-card"></i> Verifikasi Pembayaran</a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link"><i class="bi bi-door-closed"></i> Cek Kamar & Reservasi</a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link"><i class="bi bi-people"></i> Manajemen Tamu</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <a href="/tubes_basdat/modules/pembayaran/list.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger alert-dismissible fade show">
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="card p-4">
            <h3 style="margin-bottom: 20px;">Verifikasi Pembayaran</h3>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Kode Booking</div>
                        <div class="value-info"><code><?php echo htmlspecialchars($pembayaran['kode_booking']); ?></code></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Nama Tamu</div>
                        <div class="value-info"><?php echo htmlspecialchars($pembayaran['nama']); ?></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Total Harga</div>
                        <div class="value-info"><?php echo formatCurrency($pembayaran['total_harga']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Jumlah Pembayaran</div>
                        <div class="value-info"><?php echo formatCurrency($pembayaran['jumlah']); ?></div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="info-box">
                        <div class="label-info">Metode</div>
                        <div class="value-info"><?php echo ucfirst(str_replace('_', ' ', $pembayaran['metode'])); ?></div>
                    </div>
                </div>
            </div>

            <div class="row mb-3">
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">No. Bukti</div>
                        <div class="value-info"><?php echo $pembayaran['no_bukti'] ? htmlspecialchars($pembayaran['no_bukti']) : '-'; ?></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="info-box">
                        <div class="label-info">Status</div>
                        <div class="value-info">
                            <?php 
                                $status_color = $pembayaran['status'] === 'lunas' ? 'success' : ($pembayaran['status'] === 'rejected' ? 'danger' : ($pembayaran['status'] === 'verifikasi' ? 'info' : 'warning'));
                            ?>
                            <span class="badge bg-<?php echo $status_color; ?>"><?php echo htmlspecialchars(ucfirst($pembayaran['status'])); ?></span>
                        </div>
                    </div>
                </div>
            </div>

            <?php if ($pembayaran['catatan']): ?>
                <div class="info-box">
                    <div class="label-info">Catatan / Alasan Penolakan</div>
                    <div class="value-info"><?php echo htmlspecialchars($pembayaran['catatan']); ?></div>
                </div>
            <?php endif; ?>

            <?php if ($pembayaran['status'] === 'pending' || $pembayaran['status'] === 'verifikasi'): ?>
                <form method="POST" class="mt-4">
                    <div class="alert alert-info">
                        <strong>Verifikasi Pembayaran</strong>
                        <p>Pastikan pembayaran telah diterima sebelum mengklik tombol verifikasi.</p>
                    </div>
                    <div class="d-flex gap-2">
                        <button type="submit" name="action" value="verify" class="btn btn-success" onclick="return confirm('Verifikasi pembayaran ini?')">
                            <i class="bi bi-check-circle"></i> Verifikasi Pembayaran
                        </button>
                        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
                            <i class="bi bi-x-circle"></i> Tolak Pembayaran
                        </button>
                    </div>
                </form>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Pembayaran sudah diproses
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Reject Modal -->
    <div class="modal fade" id="rejectModal" tabindex="-1">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Tolak Pembayaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <form method="POST">
                    <div class="modal-body">
                        <p class="text-muted">Masukkan alasan penolakan pembayaran. Alasan ini akan terlihat oleh pemesan.</p>
                        <div class="mb-3">
                            <label class="form-label">Alasan Penolakan *</label>
                            <textarea name="catatan" class="form-control" rows="4" placeholder="Contoh: Jumlah pembayaran tidak sesuai, bukti tidak valid, dll." required></textarea>
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                        <button type="submit" name="action" value="reject" class="btn btn-danger">
                            <i class="bi bi-x-circle"></i> Tolak Pembayaran
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
