<?php
require_once '../../includes/config.php';
require_once '../../includes/functions.php';
require_once '../../includes/auth.php';

requireAdminLogin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['foto'])) {
    $files = $_FILES['foto'];
    $success_count = 0;
    $error_count = 0;
    
    for ($i = 0; $i < count($files['name']); $i++) {
        if ($files['size'][$i] > 0) {
            $file = [
                'name' => $files['name'][$i],
                'type' => $files['type'][$i],
                'tmp_name' => $files['tmp_name'][$i],
                'size' => $files['size'][$i]
            ];
            
            $upload = uploadImage($file, UPLOAD_PATH . 'gallery/');
            if ($upload['success']) {
                $deskripsi = isset($_POST['deskripsi'][$i]) ? sanitizeInput($_POST['deskripsi'][$i]) : '';
                $pdo->prepare("INSERT INTO galeri_hotel (foto_path, deskripsi, urutan) VALUES (?, ?, ?)")
                    ->execute([$upload['filename'], $deskripsi, 0]);
                $success_count++;
            } else {
                $error_count++;
            }
        }
    }
    
    if ($success_count > 0) {
        redirectWithMessage('/tubes_basdat/modules/gallery/list.php', "{$success_count} foto berhasil diupload", 'success');
    } elseif ($error_count > 0) {
        redirectWithMessage('/tubes_basdat/modules/gallery/list.php', 'Gagal mengupload foto', 'danger');
    }
}

$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Upload Foto Galeri</title>
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
        .drop-zone { border: 2px dashed #667eea; border-radius: 8px; padding: 40px; text-align: center; cursor: pointer; transition: all 0.3s; }
        .drop-zone.dragover { background: #f0f0ff; border-color: #764ba2; }
        .drop-zone-icon { font-size: 48px; color: #667eea; margin-bottom: 10px; }
    </style>
</head>
<body>
    <div class="sidebar">
        <div class="brand"><h5><?php echo HOTEL_NAME; ?></h5></div>
        <nav class="nav flex-column">
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link"><i class="bi bi-speedometer2"></i> Dashboard</a>
            <a href="/tubes_basdat/modules/gallery/list.php" class="nav-link active"><i class="bi bi-image"></i> Galeri</a>
            <a href="/tubes_basdat/admin/logout.php" class="nav-link" onclick="return confirm('Logout?')"><i class="bi bi-box-arrow-left"></i> Logout</a>
        </nav>
    </div>

    <div class="main-content">
        <a href="/tubes_basdat/modules/gallery/list.php" class="btn btn-secondary mb-3"><i class="bi bi-arrow-left"></i> Kembali</a>

        <div class="card p-4" style="max-width: 600px;">
            <h3 style="margin-bottom: 20px;">Upload Foto Galeri</h3>

            <?php if ($msg): ?>
                <div class="alert alert-<?php echo $msg['type']; ?> alert-dismissible fade show">
                    <?php echo $msg['message']; ?>
                    <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                </div>
            <?php endif; ?>

            <form method="POST" enctype="multipart/form-data">
                <div class="drop-zone" id="dropZone">
                    <div class="drop-zone-icon"><i class="bi bi-cloud-upload"></i></div>
                    <h5>Drag & drop foto di sini</h5>
                    <p class="text-muted">atau klik untuk memilih file</p>
                    <input type="file" id="fileInput" name="foto[]" multiple accept="image/*" style="display: none;">
                </div>

                <button type="submit" class="btn btn-primary mt-4 w-100" id="submitBtn" style="display: none;">
                    <i class="bi bi-upload"></i> Upload Foto
                </button>
            </form>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const dropZone = document.getElementById('dropZone');
        const fileInput = document.getElementById('fileInput');
        const submitBtn = document.getElementById('submitBtn');
        
        dropZone.addEventListener('click', () => fileInput.click());
        
        dropZone.addEventListener('dragover', (e) => {
            e.preventDefault();
            dropZone.classList.add('dragover');
        });
        
        dropZone.addEventListener('dragleave', () => {
            dropZone.classList.remove('dragover');
        });
        
        dropZone.addEventListener('drop', (e) => {
            e.preventDefault();
            dropZone.classList.remove('dragover');
            fileInput.files = e.dataTransfer.files;
            submitBtn.style.display = 'block';
        });
        
        fileInput.addEventListener('change', () => {
            if (fileInput.files.length > 0) {
                submitBtn.style.display = 'block';
            }
        });
    </script>
</body>
</html>
