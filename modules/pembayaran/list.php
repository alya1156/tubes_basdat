<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

// Search & Sort
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'created_at';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'DESC';

// Validate sort column
$allowed_sorts = ['kode_booking', 'nama', 'jumlah', 'status', 'created_at'];
if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';
if (!in_array($order, ['ASC', 'DESC'])) $order = 'DESC';

$query = "SELECT p.*, r.kode_booking, t.nama, r.total_harga 
          FROM pembayaran p 
          JOIN reservasi r ON p.id_reservasi = r.id_reservasi 
          JOIN tamu t ON r.id_tamu = t.id_tamu 
          WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (r.kode_booking LIKE ? OR t.nama LIKE ?)";
    $params = ["%$search%", "%$search%"];
}

$query .= " ORDER BY p.$sort $order";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$pembayaran_list = $stmt->fetchAll();

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #0f0f1e; color: #e0e0e0; font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .sidebar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 250px; left: 0; top: 0; border-right: 2px solid #d4af37; }
        .sidebar .brand { color: white; padding: 20px; text-align: center; border-bottom: 2px solid #d4af37; margin-bottom: 20px; cursor: pointer; }
        .sidebar .brand h5 { margin: 0; font-weight: 700; color: #d4af37; }
        .sidebar .brand small { color: rgba(255,255,255,0.6); }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-left: 3px solid transparent; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: #d4af37; background: rgba(212, 175, 55, 0.1); border-left-color: #d4af37; }
        .sidebar .nav-section { color: #967EC4; font-size: 12px; padding: 15px 20px 5px; text-transform: uppercase; font-weight: 600; margin-top: 20px; }
        .main-content { margin-left: 250px; padding: 20px; }
        .table-wrapper { background: #1a1a2e; border-radius: 8px; padding: 20px; box-shadow: 0 2px 4px rgba(212, 175, 55, 0.1); border: 1px solid #d4af37; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand" style="cursor: pointer;">
            <h5><?php echo HOTEL_NAME; ?></h5>
            <small>klik untuk logout</small>
        </div>
        <nav class="nav flex-column">
            <div class="nav-section">Menu Utama</div>
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <div class="nav-section">Operasional</div>
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link">
                <i class="bi bi-door-closed"></i> Cek Kamar
            </a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link">
                <i class="bi bi-calendar-check"></i> Reservasi
            </a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link active">
                <i class="bi bi-credit-card"></i> Pembayaran
            </a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link">
                <i class="bi bi-people"></i> Tamu
            </a>
            <div class="nav-section">Akun</div>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;">Verifikasi Pembayaran</h3>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                <?php echo $msg['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Search & Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-8">
                        <input type="text" name="search" class="form-control" placeholder="Cari kode booking atau nama tamu..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="/tubes_basdat/modules/pembayaran/list.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <?php if (count($pembayaran_list) > 0): ?>
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=kode_booking&order=<?php echo ($sort === 'kode_booking' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Kode Booking
                                    <?php if ($sort === 'kode_booking'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=nama&order=<?php echo ($sort === 'nama' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Tamu
                                    <?php if ($sort === 'nama'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=jumlah&order=<?php echo ($sort === 'jumlah' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Jumlah
                                    <?php if ($sort === 'jumlah'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>Metode</th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=status&order=<?php echo ($sort === 'status' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Status
                                    <?php if ($sort === 'status'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>Tgl. Bayar</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($pembayaran_list as $p): ?>
                            <tr>
                                <td><code><?php echo htmlspecialchars($p['kode_booking']); ?></code></td>
                                <td><?php echo htmlspecialchars($p['nama']); ?></td>
                                <td><?php echo formatCurrency($p['jumlah']); ?></td>
                                <td><span class="badge bg-info"><?php echo ucfirst(str_replace('_', ' ', $p['metode'])); ?></span></td>
                                <td>
                                    <?php 
                                        $status_color = $p['status'] === 'lunas' ? 'success' : ($p['status'] === 'verifikasi' ? 'info' : ($p['status'] === 'rejected' ? 'danger' : 'warning'));
                                    ?>
                                    <span class="badge bg-<?php echo $status_color; ?>"><?php echo htmlspecialchars(ucfirst($p['status'])); ?></span>
                                </td>
                                <td><?php echo $p['tgl_bayar'] ? formatDate($p['tgl_bayar']) : '-'; ?></td>
                                <td>
                                    <a href="/tubes_basdat/modules/pembayaran/verifikasi.php?id=<?php echo $p['id_pembayaran']; ?>" class="btn btn-sm btn-primary">
                                        <i class="bi bi-check-circle"></i> Verifikasi
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada data pembayaran
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
