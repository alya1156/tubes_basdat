<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = ['id_kamar' => '', 'no_kamar' => '', 'id_tipe' => '', 'status' => 'tersedia', 'catatan' => '', 'fasilitas_ids' => []];

// Get tipe_kamar list
$stmt = $pdo->query("SELECT * FROM tipe_kamar ORDER BY nama_tipe ASC");
$tipe_list = $stmt->fetchAll();

// Get fasilitas list
$stmt = $pdo->query("SELECT * FROM fasilitas ORDER BY nama_fasilitas ASC");
$fasilitas_list = $stmt->fetchAll();

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM kamar WHERE id_kamar = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch() ?: $data;
    
    // Get selected fasilitas
    $stmt = $pdo->prepare("SELECT id_fasilitas FROM kamar_fasilitas WHERE id_kamar = ?");
    $stmt->execute([$edit_id]);
    $data['fasilitas_ids'] = array_column($stmt->fetchAll(), 'id_fasilitas');
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $no_kamar = sanitizeInput($_POST['no_kamar']);
    $id_tipe = (int)$_POST['id_tipe'];
    $status = sanitizeInput($_POST['status']);
    $catatan = sanitizeInput($_POST['catatan']);
    $fasilitas_ids = isset($_POST['fasilitas']) ? array_map('intval', $_POST['fasilitas']) : [];
    
    if (empty($no_kamar) || $id_tipe <= 0) {
        $error = 'Semua field harus diisi';
    } else {
        try {
            if ($edit_id) {
                $stmt = $pdo->prepare("UPDATE kamar SET no_kamar=?, id_tipe=?, status=?, catatan=? WHERE id_kamar=?");
                $stmt->execute([$no_kamar, $id_tipe, $status, $catatan, $edit_id]);
                
                // Update fasilitas
                $pdo->prepare("DELETE FROM kamar_fasilitas WHERE id_kamar=?")->execute([$edit_id]);
                foreach ($fasilitas_ids as $fid) {
                    $pdo->prepare("INSERT INTO kamar_fasilitas (id_kamar, id_fasilitas) VALUES (?, ?)")->execute([$edit_id, $fid]);
                }
                redirectWithMessage('/tubes_basdat/modules/kamar/list.php', 'Kamar berhasil diupdate', 'success');
            } else {
                $stmt = $pdo->prepare("INSERT INTO kamar (no_kamar, id_tipe, status, catatan) VALUES (?, ?, ?, ?)");
                $stmt->execute([$no_kamar, $id_tipe, $status, $catatan]);
                $kamar_id = $pdo->lastInsertId();
                
                // Insert fasilitas
                foreach ($fasilitas_ids as $fid) {
                    $pdo->prepare("INSERT INTO kamar_fasilitas (id_kamar, id_fasilitas) VALUES (?, ?)")->execute([$kamar_id, $fid]);
                }
                redirectWithMessage('/tubes_basdat/modules/kamar/list.php', 'Kamar berhasil ditambahkan', 'success');
            }
        } catch (Exception $e) {
            $error = 'Error: ' . $e->getMessage();
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
    <title><?php echo $edit_id ? 'Edit' : 'Tambah'; ?> Kamar</title>
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
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link active"><i class="bi bi-houses"></i> Kamar</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;"><?php echo $edit_id ? 'Edit Kamar' : 'Tambah Kamar'; ?></h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width: 600px;">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">No. Kamar *</label>
                    <input type="text" name="no_kamar" class="form-control" value="<?php echo htmlspecialchars($data['no_kamar']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">Tipe Kamar *</label>
                    <select name="id_tipe" class="form-control" required>
                        <option value="">-- Pilih --</option>
                        <?php foreach ($tipe_list as $t): ?>
                            <option value="<?php echo $t['id_tipe']; ?>" <?php echo $data['id_tipe'] == $t['id_tipe'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($t['nama_tipe']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Status</label>
                    <select name="status" class="form-control">
                        <option value="tersedia" <?php echo $data['status'] === 'tersedia' ? 'selected' : ''; ?>>Tersedia</option>
                        <option value="terpesan" <?php echo $data['status'] === 'terpesan' ? 'selected' : ''; ?>>Terpesan</option>
                        <option value="ditempati" <?php echo $data['status'] === 'ditempati' ? 'selected' : ''; ?>>Ditempati</option>
                        <option value="maintenance" <?php echo $data['status'] === 'maintenance' ? 'selected' : ''; ?>>Maintenance</option>
                    </select>
                </div>
                <div class="mb-3">
                    <label class="form-label">Fasilitas</label>
                    <div style="max-height: 200px; overflow-y: auto; border: 1px solid #ddd; padding: 10px; border-radius: 4px;">
                        <?php foreach ($fasilitas_list as $f): ?>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="fasilitas[]" value="<?php echo $f['id_fasilitas']; ?>" <?php echo in_array($f['id_fasilitas'], $data['fasilitas_ids']) ? 'checked' : ''; ?> id="fas_<?php echo $f['id_fasilitas']; ?>">
                                <label class="form-check-label" for="fas_<?php echo $f['id_fasilitas']; ?>">
                                    <?php echo htmlspecialchars($f['nama_fasilitas']); ?>
                                </label>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="3"><?php echo htmlspecialchars($data['catatan']); ?></textarea>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary"><i class="bi bi-check-circle"></i> Simpan</button>
                    <a href="/tubes_basdat/modules/kamar/list.php" class="btn btn-secondary"><i class="bi bi-arrow-left"></i> Batal</a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
