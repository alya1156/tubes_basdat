<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$query = "SELECT k.*, t.nama_tipe FROM kamar k JOIN tipe_kamar t ON k.id_tipe = t.id_tipe ORDER BY k.no_kamar ASC";
$stmt = $pdo->query($query);
$kamar_list = $stmt->fetchAll();

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kamar - <?php echo HOTEL_NAME; ?></title>
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
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link active"><i class="bi bi-houses"></i> Kamar</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Data Kamar</h3>
            <a href="/tubes_basdat/modules/kamar/tambah.php" class="btn btn-primary"><i class="bi bi-plus-circle"></i> Tambah</a>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                <?php echo $msg['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <?php if (count($kamar_list) > 0): ?>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr><th>No. Kamar</th><th>Tipe</th><th>Status</th><th>Aksi</th></tr>
                    </thead>
                    <tbody>
                        <?php foreach ($kamar_list as $k): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($k['no_kamar']); ?></td>
                                <td><?php echo htmlspecialchars($k['nama_tipe']); ?></td>
                                <td>
                                    <span class="badge bg-<?php echo $k['status'] === 'tersedia' ? 'success' : ($k['status'] === 'terpesan' ? 'warning' : 'danger'); ?>">
                                        <?php echo ucfirst($k['status']); ?>
                                    </span>
                                </td>
                                <td>
                                    <a href="/tubes_basdat/modules/kamar/tambah.php?id=<?php echo $k['id_kamar']; ?>" class="btn btn-sm btn-warning">Edit</a>
                                    <a href="/tubes_basdat/modules/kamar/delete.php?id=<?php echo $k['id_kamar']; ?>" class="btn btn-sm btn-danger" onclick="return confirm('Hapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada data. <a href="/tubes_basdat/modules/kamar/tambah.php">Tambah sekarang</a></div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
