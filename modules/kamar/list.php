<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

// Search & Sort
$search = isset($_GET['search']) ? sanitizeInput($_GET['search']) : '';
$sort = isset($_GET['sort']) ? sanitizeInput($_GET['sort']) : 'no_kamar';
$order = isset($_GET['order']) ? sanitizeInput($_GET['order']) : 'ASC';
$status_filter = isset($_GET['status']) ? sanitizeInput($_GET['status']) : '';
$tipe_filter = isset($_GET['tipe']) ? sanitizeInput($_GET['tipe']) : '';

$allowed_sorts = ['no_kamar', 'nama_tipe', 'status', 'harga_malam'];
if (!in_array($sort, $allowed_sorts)) $sort = 'no_kamar';
if (!in_array($order, ['ASC', 'DESC'])) $order = 'ASC';

$query = "SELECT k.*, t.nama_tipe, t.harga_malam FROM kamar k
          JOIN tipe_kamar t ON k.id_tipe = t.id_tipe
          WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND k.no_kamar LIKE ?";
    $params[] = "%$search%";
}

if ($status_filter) {
    $query .= " AND k.status = ?";
    $params[] = $status_filter;
}

if ($tipe_filter) {
    $query .= " AND k.id_tipe = ?";
    $params[] = $tipe_filter;
}

$query .= " ORDER BY k.$sort $order";

$stmt = $pdo->prepare($query);
$stmt->execute($params);
$kamar_list = $stmt->fetchAll();

// Get all room types for filter
$stmt = $pdo->prepare("SELECT * FROM tipe_kamar ORDER BY nama_tipe");
$stmt->execute();
$tipe_list = $stmt->fetchAll();

// Count statistics
$stat_tersedia = count(array_filter($kamar_list, fn($k) => $k['status'] === 'tersedia'));
$stat_terpesan = count(array_filter($kamar_list, fn($k) => $k['status'] === 'terpesan'));
$stat_ditempati = count(array_filter($kamar_list, fn($k) => $k['status'] === 'ditempati'));
$stat_maintenance = count(array_filter($kamar_list, fn($k) => $k['status'] === 'maintenance'));

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Kamar - <?php echo HOTEL_NAME; ?></title>
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
        .badge-tersedia { background-color: #28a745; }
        .badge-terpesan { background-color: #ffc107; color: #333; }
        .badge-ditempati { background-color: #17a2b8; }
        .badge-maintenance { background-color: #dc3545; }
        .stat-card { border-left: 4px solid #667eea; background: white; padding: 15px; border-radius: 5px; box-shadow: 0 2px 4px rgba(0,0,0,0.05); }
        .stat-card h6 { color: #666; font-weight: 600; font-size: 12px; text-transform: uppercase; margin-bottom: 10px; }
        .stat-card .number { font-size: 28px; font-weight: bold; color: #333; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link active"><i class="bi bi-door-closed"></i> Cek Kamar</a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link"><i class="bi bi-calendar"></i> Reservasi</a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link"><i class="bi bi-credit-card"></i> Pembayaran</a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link"><i class="bi bi-people"></i> Tamu</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;"><i class="bi bi-door-closed"></i> Cek Ketersediaan Kamar</h3>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                <?php echo $msg['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Row -->
        <div class="row mb-4">
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #28a745;">
                    <h6><i class="bi bi-check-circle"></i> Tersedia</h6>
                    <div class="number" style="color: #28a745;"><?php echo $stat_tersedia; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #ffc107;">
                    <h6><i class="bi bi-hourglass-split"></i> Terpesan</h6>
                    <div class="number" style="color: #ffc107;"><?php echo $stat_terpesan; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #17a2b8;">
                    <h6><i class="bi bi-person-check"></i> Ditempati</h6>
                    <div class="number" style="color: #17a2b8;"><?php echo $stat_ditempati; ?></div>
                </div>
            </div>
            <div class="col-md-3">
                <div class="stat-card" style="border-left-color: #dc3545;">
                    <h6><i class="bi bi-exclamation-circle"></i> Maintenance</h6>
                    <div class="number" style="color: #dc3545;"><?php echo $stat_maintenance; ?></div>
                </div>
            </div>
        </div>

        <!-- Search & Filter -->
        <div class="card mb-3">
            <div class="card-body">
                <form method="GET" class="row g-2">
                    <div class="col-md-3">
                        <input type="text" name="search" class="form-control" placeholder="No. Kamar..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <select name="status" class="form-select">
                            <option value="">Semua Status</option>
                            <option value="tersedia" <?php echo $status_filter === 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                            <option value="terpesan" <?php echo $status_filter === 'terpesan' ? 'selected' : ''; ?>>Terpesan</option>
                            <option value="ditempati" <?php echo $status_filter === 'ditempati' ? 'selected' : ''; ?>>Ditempati</option>
                            <option value="maintenance" <?php echo $status_filter === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <select name="tipe" class="form-select">
                            <option value="">Semua Tipe</option>
                            <?php foreach ($tipe_list as $t): ?>
                                <option value="<?php echo $t['id_tipe']; ?>" <?php echo $tipe_filter == $t['id_tipe'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($t['nama_tipe']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100"><i class="bi bi-search"></i> Filter</button>
                    </div>
                    <div class="col-md-2">
                        <a href="/tubes_basdat/modules/kamar/list.php" class="btn btn-outline-secondary w-100"><i class="bi bi-arrow-clockwise"></i> Reset</a>
                    </div>
                </form>
            </div>
        </div>

        <!-- Room Table -->
        <div class="table-wrapper">
            <?php if (count($kamar_list) > 0): ?>
                <table class="table table-hover table-sm">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&tipe=<?php echo urlencode($tipe_filter); ?>&sort=no_kamar&order=<?php echo ($sort === 'no_kamar' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit; cursor: pointer;">
                                    No. Kamar <?php if ($sort === 'no_kamar'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&tipe=<?php echo urlencode($tipe_filter); ?>&sort=nama_tipe&order=<?php echo ($sort === 'nama_tipe' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit; cursor: pointer;">
                                    Tipe Kamar <?php if ($sort === 'nama_tipe'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&tipe=<?php echo urlencode($tipe_filter); ?>&sort=harga_malam&order=<?php echo ($sort === 'harga_malam' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit; cursor: pointer;">
                                    Harga/Malam <?php if ($sort === 'harga_malam'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&status=<?php echo urlencode($status_filter); ?>&tipe=<?php echo urlencode($tipe_filter); ?>&sort=status&order=<?php echo ($sort === 'status' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit; cursor: pointer;">
                                    Status <?php if ($sort === 'status'): ?><i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i><?php endif; ?>
                                </a>
                            </th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kamar_list as $k): ?>
                            <tr>
                                <td><strong><?php echo htmlspecialchars($k['no_kamar']); ?></strong></td>
                                <td><?php echo htmlspecialchars($k['nama_tipe']); ?></td>
                                <td><?php echo formatCurrency($k['harga_malam']); ?></td>
                                <td>
                                    <span class="badge badge-<?php echo $k['status']; ?>">
                                        <?php echo ucfirst($k['status']); ?>
                                    </span>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info"><i class="bi bi-info-circle"></i> Tidak ada kamar dengan filter tersebut</div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
