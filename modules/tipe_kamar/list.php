<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$query = "SELECT t.*, GROUP_CONCAT(f.nama_fasilitas SEPARATOR ', ') as fasilitas FROM tipe_kamar t LEFT JOIN kamar k ON t.id_tipe = k.id_tipe LEFT JOIN kamar_fasilitas kf ON k.id_kamar = kf.id_kamar LEFT JOIN fasilitas f ON kf.id_fasilitas = f.id_fasilitas GROUP BY t.id_tipe ORDER BY t.created_at DESC";
$stmt = $pdo->query($query);
$tipe_kamar_list = $stmt->fetchAll();

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tipe Kamar - <?php echo HOTEL_NAME; ?></title>
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
        .btn-action { font-size: 12px; padding: 5px 10px; }
        .img-thumb { max-width: 50px; height: 50px; object-fit: cover; border-radius: 4px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/tipe_kamar/list.php" class="nav-link active"><i class="bi bi-door-closed"></i> Tipe Kamar</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
            <h3>Data Tipe Kamar</h3>
            <a href="/tubes_basdat/modules/tipe_kamar/tambah.php" class="btn btn-primary">
                <i class="bi bi-plus-circle"></i> Tambah
            </a>
        </div>

        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                <?php echo $msg['message']; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <div class="table-wrapper">
            <?php if (count($tipe_kamar_list) > 0): ?>
                <table class="table table-hover">
                    <thead class="table-light">
                        <tr>
                            <th>Foto</th>
                            <th>Nama Tipe</th>
                            <th>Kapasitas</th>
                            <th>Harga/Malam</th>
                            <th>Aksi</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($tipe_kamar_list as $tipe): ?>
                            <tr>
                                <td>
                                    <?php if ($tipe['foto_cover']): ?>
                                        <img src="<?php echo UPLOAD_URL; ?>tipe_kamar/<?php echo htmlspecialchars($tipe['foto_cover']); ?>" class="img-thumb">
                                    <?php else: ?>
                                        <i class="bi bi-image" style="font-size: 24px; color: #ccc;"></i>
                                    <?php endif; ?>
                                </td>
                                <td><?php echo htmlspecialchars($tipe['nama_tipe']); ?></td>
                                <td><?php echo $tipe['kapasitas']; ?> orang</td>
                                <td><?php echo formatCurrency($tipe['harga_malam']); ?></td>
                                <td>
                                    <a href="/tubes_basdat/modules/tipe_kamar/tambah.php?id=<?php echo $tipe['id_tipe']; ?>" class="btn btn-sm btn-warning btn-action">Edit</a>
                                    <a href="/tubes_basdat/modules/tipe_kamar/delete.php?id=<?php echo $tipe['id_tipe']; ?>" class="btn btn-sm btn-danger btn-action" onclick="return confirm('Hapus?')">Hapus</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            <?php else: ?>
                <div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada data. <a href="/tubes_basdat/modules/tipe_kamar/tambah.php">Tambah sekarang</a></div>
            <?php endif; ?>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
