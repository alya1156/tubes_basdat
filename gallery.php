<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Galeri - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f0f1e; color: #e0e0e0; }
        .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-bottom: 2px solid #d4af37; }
        .navbar-brand { font-weight: 700; font-size: 24px; color: #d4af37; }
        .page-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #d4af37; padding: 40px 0; border-bottom: 2px solid #d4af37; }
        .page-header h1 { color: #d4af37; }
        .section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 50px; color: #d4af37; text-shadow: 0 0 20px rgba(212, 175, 55, 0.3); }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fill, minmax(250px, 1fr)); gap: 20px; }
        .gallery-item { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2); aspect-ratio: 1; cursor: pointer; transition: transform 0.3s; border: 2px solid #d4af37; }
        .gallery-item:hover { transform: scale(1.05); box-shadow: 0 8px 25px rgba(212, 175, 55, 0.4); }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; }
        .footer { background: #0f0f1e; color: #d4af37; padding: 40px 0; text-align: center; margin-top: 80px; border-top: 2px solid #d4af37; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/tubes_basdat/"><i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/rooms.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link active" href="/tubes_basdat/gallery.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Galeri Hotel</h1>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="section">
        <div class="container">
            <?php 
                $galleryImages = [];
                // Scan gallery folder untuk gambar galeri
                for ($i = 1; $i <= 20; $i++) {
                    $imagePath = 'uploads/gallery/galeri hotel' . $i . '.jpg';
                    if (file_exists($imagePath)) {
                        $galleryImages[] = [
                            'path' => $imagePath,
                            'name' => 'Galeri ' . $i
                        ];
                    }
                }
            ?>
            <?php if (count($galleryImages) > 0): ?>
                <div class="gallery-grid">
                    <?php foreach ($galleryImages as $index => $photo): ?>
                        <div class="gallery-item" data-bs-toggle="modal" data-bs-target="#photoModal<?php echo $index; ?>" style="cursor: pointer;">
                            <img src="<?php echo $photo['path']; ?>" alt="<?php echo $photo['name']; ?>" style="width: 100%; height: 100%; object-fit: cover;">
                        </div>
                        <div class="modal fade" id="photoModal<?php echo $index; ?>" tabindex="-1">
                            <div class="modal-dialog modal-xl modal-dialog-centered">
                                <div class="modal-content" style="background: transparent; border: none;">
                                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                                    <img src="<?php echo $photo['path']; ?>" style="width: 100%; border-radius: 12px;">
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-info" style="text-align: center;">
                    <i class="bi bi-image"></i> Galeri masih kosong
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
