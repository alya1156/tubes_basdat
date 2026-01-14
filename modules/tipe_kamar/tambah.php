<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = ['id_tipe' => '', 'nama_tipe' => '', 'kapasitas' => '', 'harga_malam' => '', 'deskripsi' => '', 'foto_cover' => ''];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM tipe_kamar WHERE id_tipe = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch() ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_tipe = sanitizeInput($_POST['nama_tipe']);
    $kapasitas = (int)$_POST['kapasitas'];
    $harga_malam = (float)$_POST['harga_malam'];
    $deskripsi = sanitizeInput($_POST['deskripsi']);
    
    $foto_cover = $data['foto_cover'];
    
    // Handle file upload
    if (isset($_FILES['foto_cover']) && $_FILES['foto_cover']['size'] > 0) {
        $upload = uploadImage($_FILES['foto_cover'], UPLOAD_PATH . 'tipe_kamar/');
        if ($upload['success']) {
            if ($data['foto_cover'] && file_exists(UPLOAD_PATH . 'tipe_kamar/' . $data['foto_cover'])) {
                unlink(UPLOAD_PATH . 'tipe_kamar/' . $data['foto_cover']);
            }
            $foto_cover = $upload['filename'];
        }
    }
    
    if (empty($nama_tipe) || $kapasitas <= 0 || $harga_malam <= 0) {
        $error = 'Semua field harus diisi dengan benar';
    } else {
        if ($edit_id) {
            $stmt = $pdo->prepare("UPDATE tipe_kamar SET nama_tipe=?, kapasitas=?, harga_malam=?, deskripsi=?, foto_cover=? WHERE id_tipe=?");
            $stmt->execute([$nama_tipe, $kapasitas, $harga_malam, $deskripsi, $foto_cover, $edit_id]);
            redirectWithMessage('/tubes_basdat/modules/tipe_kamar/list.php', 'Tipe kamar berhasil diupdate', 'success');
        } else {
            $stmt = $pdo->prepare("INSERT INTO tipe_kamar (nama_tipe, kapasitas, harga_malam, deskripsi, foto_cover) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$nama_tipe, $kapasitas, $harga_malam, $deskripsi, $foto_cover]);
            redirectWithMessage('/tubes_basdat/modules/tipe_kamar/list.php', 'Tipe kamar berhasil ditambahkan', 'success');
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
    <title><?php echo $edit_id ? 'Edit' : 'Tambah'; ?> Tipe Kamar</title>
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
        .img-preview { max-width: 200px; margin-top: 10px; border-radius: 8px; }
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
        <h3 style="margin-bottom: 20px;"><?php echo $edit_id ? 'Edit Tipe Kamar' : 'Tambah Tipe Kamar'; ?></h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width: 600px;">
            <form method="POST" enctype="multipart/form-data">
                <div class="mb-3">
                    <label class="form-label">Nama Tipe *</label>
                    <input type="text" name="nama_tipe" class="form-control" value="<?php echo htmlspecialchars($data['nama_tipe']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Kapasitas (orang) *</label>
                    <input type="number" name="kapasitas" class="form-control" value="<?php echo $data['kapasitas']; ?>" min="1" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Harga per Malam (Rp) *</label>
                    <input type="number" name="harga_malam" class="form-control" value="<?php echo $data['harga_malam']; ?>" min="0" step="0.01" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Deskripsi</label>
                    <textarea name="deskripsi" class="form-control" rows="4"><?php echo htmlspecialchars($data['deskripsi']); ?></textarea>
                </div>
                <div class="mb-3">
                    <label class="form-label">Foto Cover</label>
                    <input type="file" name="foto_cover" class="form-control" accept="image/*">
                    <?php if ($data['foto_cover']): ?>
                        <img src="<?php echo UPLOAD_URL; ?>tipe_kamar/<?php echo htmlspecialchars($data['foto_cover']); ?>" class="img-preview">
                    <?php endif; ?>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
                    <a href="/tubes_basdat/modules/tipe_kamar/list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
