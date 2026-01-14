<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

// Get all guests
$query = "SELECT * FROM tamu ORDER BY created_at DESC";
$stmt = $pdo->query($query);
$tamu_list = $stmt->fetchAll();

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
                <i class="bi bi-people"></i> Tamu
            </a>
            <a href="/tubes_basdat/modules/tipe_kamar/list.php" class="nav-link">
                <i class="bi bi-door-closed"></i> Tipe Kamar
            </a>
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link">
                <i class="bi bi-houses"></i> Kamar
            </a>
            <a href="/tubes_basdat/modules/fasilitas/list.php" class="nav-link">
                <i class="bi bi-star"></i> Fasilitas
            </a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link">
                <i class="bi bi-calendar-check"></i> Reservasi
            </a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link">
                <i class="bi bi-credit-card"></i> Pembayaran
            </a>
            <a href="/tubes_basdat/modules/gallery/list.php" class="nav-link">
                <i class="bi bi-image"></i> Galeri
            </a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Data Tamu</h3>
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

        <div class="table-wrapper">
            <?php if (count($tamu_list) > 0): ?>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Nama</th>
                            <th>No. Identitas</th>
                            <th>Email</th>
                            <th>No. Telepon</th>
                            <th>Alamat</th>
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
                                <td><?php echo substr(htmlspecialchars($tamu['alamat']), 0, 30); ?>...</td>
                                <td>
                                    <a href="/tubes_basdat/modules/tamu/edit.php?id=<?php echo $tamu['id_tamu']; ?>" class="btn btn-sm btn-warning btn-action">
                                        <i class="bi bi-pencil"></i> Edit
                                    </a>
                                    <a href="/tubes_basdat/modules/tamu/delete.php?id=<?php echo $tamu['id_tamu']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Hapus?')">
                                        <i class="bi bi-trash"></i> Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info">
                    <i class="bi bi-info-circle"></i> Belum ada data tamu. <a href="/tubes_basdat/modules/tamu/tambah.php">Tambah sekarang</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
