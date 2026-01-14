<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$query = "SELECT r.*, t.nama as nama_tamu, k.no_kamar, tp.nama_tipe FROM reservasi r 
          JOIN tamu t ON r.id_tamu = t.id_tamu 
          JOIN kamar k ON r.id_kamar = k.id_kamar 
          JOIN tipe_kamar tp ON k.id_tipe = tp.id_tipe 
          ORDER BY r.tgl_pesan DESC";
$stmt = $pdo->query($query);
$reservasi_list = $stmt->fetchAll();

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reservasi - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 250px; left: 0; top: 0; }
        .sidebar .brand { color: white; padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-left: 3px solid transparent; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left-color: white; }
        .main-content { margin-left: 250px; padding: 20px; }
        .table-wrapper { background: white; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .badge { padding: 6px 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link active"><i class="bi bi-calendar-check"></i> Reservasi</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;">Data Reservasi</h3>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                <?php echo $msg['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <?php if (count($reservasi_list) > 0): ?>
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>Kode Booking</th>
                            <th>Tamu</th>
                            <th>Kamar</th>
                            <th>Check-in</th>
                            <th>Check-out</th>
                            <th>Total</th>
                            <th>Status</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($reservasi_list as $r): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($r['kode_booking']); ?></code></td>
                                <td><?php echo htmlspecialchars($r['nama_tamu']); ?></td>
                                <td><?php echo htmlspecialchars($r['no_kamar']) . ' (' . htmlspecialchars($r['nama_tipe']) . ')'; ?></td>
                                <td><?php echo formatDate($r['tgl_masuk']); ?></td>
                                <td><?php echo formatDate($r['tgl_keluar']); ?></td>
                                <td><?php echo formatCurrency($r['total_harga']); ?></td>
                                <td><span class="badge bg-<?php echo $r['status'] === 'konfirmasi' ? 'success' : ($r['status'] === 'pending' ? 'warning' : 'secondary'); ?>"><?php echo ucfirst($r['status']); ?></span></td>
                                <td>
                                    <a href="/tubes_basdat/modules/reservasi/detail.php?id=<?php echo $r['id_reservasi']; ?>" class="btn btn-sm btn-info">Detail</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">Belum ada data reservasi</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
