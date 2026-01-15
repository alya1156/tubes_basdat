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
$allowed_sorts = ['nama', 'no_identitas', 'email', 'no_telp', 'created_at'];
if (!in_array($sort, $allowed_sorts)) $sort = 'created_at';
if (!in_array($order, ['ASC', 'DESC'])) $order = 'DESC';

// Build query
$query = "SELECT * FROM tamu WHERE 1=1";
$params = [];

if ($search) {
    $query .= " AND (nama LIKE ? OR email LIKE ? OR no_telp LIKE ?)";
    $params = ["%$search%", "%$search%", "%$search%"];
}

$query .= " ORDER BY $sort $order";
$stmt = $pdo->prepare($query);
$stmt->execute($params);
$tamu_list = $stmt->fetchAll();

// Toggle sort order for current column
$next_order = ($sort === isset($_GET['sort']) && $order === 'ASC') ? 'DESC' : 'ASC';

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Tamu - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
        }
        .sidebar .brand {
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .card {
            border: none;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            border-radius: 8px;
        }
        .table-wrapper {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
        }
        .btn-action {
            font-size: 12px;
            padding: 5px 10px;
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand">
            <h5><?php echo HOTEL_NAME; ?></h5>
        </div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link active">
                <i class="bi bi-people"></i> Manajemen Tamu
            </a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link">
                <i class="bi bi-door-closed"></i> Cek Kamar & Reservasi
            </a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link">
                <i class="bi bi-credit-card"></i> Verifikasi Pembayaran
            </a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Manajemen Tamu</h3>
            <a href="/tubes_basdat/modules/tamu/tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah Tamu
            </a>
        </div>

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
                        <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau nomor telepon..." value="<?php echo htmlspecialchars($search); ?>">
                    </div>
                    <div class="col-md-2">
                        <button type="submit" class="btn btn-outline-primary w-100">
                            <i class="bi bi-search"></i> Cari
                        </button>
                    </div>
                    <div class="col-md-2">
                        <a href="/tubes_basdat/modules/tamu/list.php" class="btn btn-outline-secondary w-100">
                            <i class="bi bi-arrow-clockwise"></i> Reset
                        </a>
                    </div>
                </form>
            </div>
        </div>

        <div class="table-wrapper">
            <?php if (count($tamu_list) > 0): ?>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=nama&order=<?php echo ($sort === 'nama' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Nama 
                                    <?php if ($sort === 'nama'): ?>
                                        <i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>No. Identitas</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>
                                <a href="?search=<?php echo urlencode($search); ?>&sort=created_at&order=<?php echo ($sort === 'created_at' && $order === 'ASC') ? 'DESC' : 'ASC'; ?>" style="text-decoration: none; color: inherit;">
                                    Terdaftar
                                    <?php if ($sort === 'created_at'): ?>
                                        <i class="bi bi-sort-<?php echo strtolower($order) === 'asc' ? 'down' : 'up'; ?>"></i>
                                    <?php endif; ?>
                                </a>
                            </th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tamu_list as $tamu): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($tamu['nama']); ?></td>
                                <td><?php echo htmlspecialchars($tamu['no_identitas']); ?></td>
                                <td><?php echo htmlspecialchars($tamu['email']); ?></td>
                                <td><?php echo htmlspecialchars($tamu['no_telp']); ?></td>
                                <td><?php echo formatDate($tamu['created_at']); ?></td>
                                <td>
                                    <a href="/tubes_basdat/modules/tamu/edit.php?id=<?php echo $tamu['id_tamu']; ?>" class="btn btn-sm btn-warning btn-action">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="/tubes_basdat/modules/tamu/delete.php?id=<?php echo $tamu['id_tamu']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Hapus data tamu ini?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Tidak ada data tamu. <a href="/tubes_basdat/modules/tamu/tambah.php">Tambah sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
