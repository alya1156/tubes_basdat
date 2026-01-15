<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

$edit_id = isset($_GET['id']) ? (int)$_GET['id'] : null;
$data = ['id_tamu' => '', 'nama' => '', 'no_identitas' => '', 'email' => '', 'no_telp' => ''];

if ($edit_id) {
    $stmt = $pdo->prepare("SELECT * FROM tamu WHERE id_tamu = ?");
    $stmt->execute([$edit_id]);
    $data = $stmt->fetch() ?: $data;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama = sanitizeInput($_POST['nama']);
    $no_identitas = sanitizeInput($_POST['no_identitas']);
    $email = sanitizeInput($_POST['email']);
    $no_telp = sanitizeInput($_POST['no_telp']);
    
    // Validation
    $errors = [];
    if (empty($nama)) $errors[] = 'Nama tidak boleh kosong';
    if (empty($email)) $errors[] = 'Email tidak boleh kosong';
    if (!validateEmail($email)) $errors[] = 'Email tidak valid';
    if (empty($no_telp)) $errors[] = 'No. Telepon tidak boleh kosong';
    if (!validatePhone($no_telp)) $errors[] = 'No. Telepon tidak valid';
    
    if (count($errors) > 0) {
        $error = implode(', ', $errors);
    } else {
        if ($edit_id) {
            $stmt = $pdo->prepare("UPDATE tamu SET nama=?, no_identitas=?, email=?, no_telp=? WHERE id_tamu=?");
            $stmt->execute([$nama, $no_identitas, $email, $no_telp, $edit_id]);
            redirectWithMessage('/tubes_basdat/modules/tamu/list.php', 'Data tamu berhasil diupdate', 'success');
        } else {
            $stmt = $pdo->prepare("INSERT INTO tamu (nama, no_identitas, email, no_telp) VALUES (?, ?, ?, ?)");
            $stmt->execute([$nama, $no_identitas, $email, $no_telp]);
            redirectWithMessage('/tubes_basdat/modules/tamu/list.php', 'Data tamu berhasil ditambahkan', 'success');
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
    <title><?php echo $edit_id ? 'Edit' : 'Tambah'; ?> Tamu - <?php echo HOTEL_NAME; ?></title>
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
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link active"><i class="bi bi-people"></i> Manajemen Tamu</a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link"><i class="bi bi-door-closed"></i> Cek Kamar & Reservasi</a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link"><i class="bi bi-credit-card"></i> Verifikasi Pembayaran</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <h3 style="margin-bottom: 20px;"><?php echo $edit_id ? 'Edit Tamu' : 'Tambah Tamu'; ?></h3>

        <?php if (isset($error)): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <div class="card p-4" style="max-width: 600px;">
            <form method="POST">
                <div class="mb-3">
                    <label class="form-label">Nama *</label>
                    <input type="text" name="nama" class="form-control" value="<?php echo htmlspecialchars($data['nama']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Identitas</label>
                    <input type="text" name="no_identitas" class="form-control" value="<?php echo htmlspecialchars($data['no_identitas']); ?>">
                </div>
                <div class="mb-3">
                    <label class="form-label">Email *</label>
                    <input type="email" name="email" class="form-control" value="<?php echo htmlspecialchars($data['email']); ?>" required>
                </div>
                <div class="mb-3">
                    <label class="form-label">No. Telepon * (format: 08xxx-xxxx-xxx)</label>
                    <input type="tel" name="no_telp" class="form-control" placeholder="08123456789" value="<?php echo htmlspecialchars($data['no_telp']); ?>" required>
                </div>
                <div class="d-flex gap-2">
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-check-circle"></i> Simpan
                    </button>
                    <a href="/tubes_basdat/modules/tamu/list.php" class="btn btn-secondary">
                        <i class="bi bi-arrow-left"></i> Batal
                    </a>
                </div>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
