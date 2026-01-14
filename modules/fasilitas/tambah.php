<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = ['id_fasilitas' => '', 'nama_fasilitas' => '', 'icon' => 'star'];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM fasilitas WHERE id_fasilitas = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch() ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama_fasilitas']);
    $icon = sanitizeInput($_POST['icon']);
    
    if (empty($nama)) {
        $error = 'Nama fasilitas harus diisi';
    } else {
        if ($edit_id) {
            $pdo->prepare("UPDATE fasilitas SET nama_fasilitas=?, icon=? WHERE id_fasilitas=?")->execute([$nama, $icon, $edit_id]);
            redirectWithMessage('/tubes_basdat/modules/fasilitas/list.php', 'Fasilitas berhasil diupdate', 'success');
        } else {
            $pdo->prepare("INSERT INTO fasilitas (nama_fasilitas, icon) VALUES (?, ?)")->execute([$nama, $icon]);
            redirectWithMessage('/tubes_basdat/modules/fasilitas/list.php', 'Fasilitas berhasil ditambahkan', 'success');
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
    <title><?php echo $edit_id ? 'Edit' : 'Tambah'; ?> Fasilitas</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { background-color: #f8f9fa; }
        .sidebar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); min-height: 100vh; padding: 20px 0; position: fixed; width: 250px; left: 0; top: 0; }
        .sidebar .brand { color: white; padding: 20px; text-align: center; border-bottom: 1px solid rgba(255,255,255,0.1); margin-bottom: 20px; }
        .sidebar .nav-link { color: rgba(255,255,255,0.7); padding: 12px 20px; border-left: 3px solid transparent; transition: all 0.3s; font-size: 14px; }
        .sidebar .nav-link:hover, .sidebar .nav-link.active { color: white; background: rgba(255,255,255,0.1); border-left-color: white; }
        .main-content { margin-left: 250px; padding: 20px; }
        .card { border: none; box-shadow: 0 2px 4px rgba(0,0,0,0.05); border-radius: 8px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/fasilitas/list.php" class="nav-link active"><i class="bi bi-star"></i> Fasilitas</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;"><?php echo $edit_id ? 'Edit Fasilitas' : 'Tambah Fasilitas'; ?></h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width: 400px;">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama Fasilitas *</label>
                    <input type="text" name="nama_fasilitas" class="form-control" value="<?php echo htmlspecialchars($data['nama_fasilitas']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Bootstrap Icon Name</label>
                    <small class="text-muted d-block mb-2">Lihat di https://icons.getbootstrap.com/</small>
                    <input type="text" name="icon" class="form-control" value="<?php echo htmlspecialchars($data['icon']); ?>" placeholder="Contoh: wifi, fire, door-open">
                    <small class="d-block mt-2"><i class="bi bi-<?php echo htmlspecialchars($data['icon']); ?>"></i> Preview</small>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
                    <a href="/tubes_basdat/modules/fasilitas/list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
