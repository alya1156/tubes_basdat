<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$stmt = $pdo->query("SELECT * FROM tipe_kamar ORDER BY created_at DESC");
$rooms = $stmt->fetchAll();
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
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f0f1e; color: #e0e0e0; }
        .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); box-shadow: 0 2px 4px rgba(212, 175, 55, 0.1); border-bottom: 2px solid #d4af37; }
        .navbar-brand { font-weight: 700; font-size: 24px; color: #d4af37; }
        .page-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #d4af37; padding: 40px 0; border-bottom: 2px solid #d4af37; }
        .page-header h1 { color: #d4af37; }
        .section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 50px; color: #d4af37; text-shadow: 0 0 20px rgba(212, 175, 55, 0.3); }
        .room-card { border: 1px solid #d4af37; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.1); transition: transform 0.3s, box-shadow 0.3s; margin-bottom: 30px; background: #1a1a2e; }
        .room-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(212, 175, 55, 0.3); border-color: #ffd700; }
        .room-card img { width: 100%; height: 300px; object-fit: cover; }
        .room-info { padding: 25px; }
        .room-title { font-size: 22px; font-weight: 700; margin-bottom: 10px; color: #d4af37; }
        .room-price { font-size: 26px; color: #ffd700; font-weight: 700; margin-bottom: 15px; }
        .room-capacity { color: #888; font-size: 14px; margin-bottom: 15px; }
        .room-description { color: #b0b0b0; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        .btn-primary { background: linear-gradient(135deg, #d4af37 0%, #ffd700 100%); border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; color: #1a1a2e; }
        .btn-primary:hover { background: linear-gradient(135deg, #ffd700 0%, #d4af37 100%); color: #1a1a2e; }
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
                    <li class="nav-item"><a class="nav-link active" href="/tubes_basdat/rooms.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/gallery.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Kamar Kami</h1>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="section">
        <div class="container">
            <div class="row">
                <?php foreach ($rooms as $room): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card room-card">
                            <?php 
                                $imagePath = 'uploads/gallery/' . htmlspecialchars($room['nama_tipe']) . '.jpg';
                                $imageExists = file_exists($imagePath);
                            ?>
                            <?php if ($imageExists): ?>
                                <img src="<?php echo $imagePath; ?>" alt="<?php echo htmlspecialchars($room['nama_tipe']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=<?php echo urlencode($room['nama_tipe']); ?>" alt="<?php echo htmlspecialchars($room['nama_tipe']); ?>">
                            <?php endif; ?>
                            <div class="room-info">
                                <div class="room-title"><?php echo htmlspecialchars($room['nama_tipe']); ?></div>
                                <div class="room-capacity">
                                    <i class="bi bi-people"></i> Kapasitas: <?php echo $room['kapasitas']; ?> orang
                                </div>
                                <div class="room-description">
                                    <?php echo htmlspecialchars(substr($room['deskripsi'] ?? '', 0, 100)); ?>...
                                </div>
                                <div class="room-price"><?php echo formatCurrency($room['harga_malam']); ?>/malam</div>
                                <a href="/tubes_basdat/detail_kamar.php?id=<?php echo $room['id_tipe']; ?>" class="btn btn-primary w-100" style="color: white;">
                                    <i class="bi bi-eye"></i> Lihat Detail
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
