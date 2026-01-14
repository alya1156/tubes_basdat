<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$stmt = $pdo->query("SELECT * FROM tipe_kamar ORDER BY created_at DESC LIMIT 6");
$featured_rooms = $stmt->fetchAll();

$stmt = $pdo->query("SELECT * FROM galeri_hotel ORDER BY urutan ASC LIMIT 8");
$gallery_photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Home - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .hero { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 100px 0; text-align: center; }
        .hero h1 { font-size: 48px; font-weight: 700; margin-bottom: 20px; }
        .hero p { font-size: 18px; opacity: 0.9; }
        .search-box { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 10px 40px rgba(0,0,0,0.2); margin-top: 40px; max-width: 600px; margin-left: auto; margin-right: auto; }
        .search-box input, .search-box button { border-radius: 8px; border: 1px solid #ddd; padding: 12px; margin-bottom: 10px; }
        .section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 36px; font-weight: 700; margin-bottom: 50px; color: #333; }
        .room-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s, box-shadow 0.3s; }
        .room-card:hover { transform: translateY(-10px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .room-card img { width: 100%; height: 250px; object-fit: cover; }
        .room-info { padding: 20px; }
        .room-title { font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #333; }
        .room-price { font-size: 24px; color: #667eea; font-weight: 700; margin-bottom: 10px; }
        .room-capacity { color: #666; font-size: 14px; margin-bottom: 15px; }
        .room-desc { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 15px; }
        .btn-primary { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border: none; border-radius: 8px; padding: 10px 20px; font-weight: 600; }
        .btn-primary:hover { background: linear-gradient(135deg, #764ba2 0%, #667eea 100%); }
        .gallery-item { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); aspect-ratio: 1; }
        .gallery-item img { width: 100%; height: 100%; object-fit: cover; transition: transform 0.3s; }
        .gallery-item:hover img { transform: scale(1.05); }
        .footer { background: #333; color: white; padding: 40px 0; text-align: center; }
        .footer p { margin: 10px 0; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand" href="/tubes_basdat/">
                <i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?>
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="/tubes_basdat/">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/rooms.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/gallery.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/admin/login.php">Admin</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero">
        <div class="container">
            <h1>Selamat Datang di <?php echo HOTEL_NAME; ?></h1>
            <p>Nikmati pengalaman menginap yang tak terlupakan dengan layanan terbaik kami</p>
            
            <div class="search-box">
                <form method="GET" action="/tubes_basdat/guest/booking.php">
                    <label class="form-label">Check-in</label>
                    <input type="date" name="checkin" class="form-control" required>
                    
                    <label class="form-label mt-3">Check-out</label>
                    <input type="date" name="checkout" class="form-control" required>
                    
                    <label class="form-label mt-3">Jumlah Tamu</label>
                    <input type="number" name="guests" class="form-control" min="1" value="1" required>
                    
                    <button type="submit" class="btn btn-primary w-100 mt-3" style="color: white;">
                        <i class="bi bi-search"></i> Cari Kamar
                    </button>
                </form>
            </div>
        </div>
    </section>

    <!-- Featured Rooms -->
    <section class="section bg-light">
        <div class="container">
            <h2 class="section-title">Kamar Pilihan Kami</h2>
            <p style="text-align: center; color: #666; margin-bottom: 40px; font-size: 16px;">Koleksi kamar eksklusif dengan desain modern dan fasilitas lengkap untuk kenyamanan Anda</p>
            <div class="row g-4">
                <?php foreach ($featured_rooms as $room): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="card room-card">
                            <?php if ($room['foto_cover']): ?>
                                <img src="<?php echo UPLOAD_URL; ?>tipe_kamar/<?php echo htmlspecialchars($room['foto_cover']); ?>" alt="<?php echo htmlspecialchars($room['nama_tipe']); ?>">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x300?text=<?php echo urlencode($room['nama_tipe']); ?>" alt="<?php echo htmlspecialchars($room['nama_tipe']); ?>">
                            <?php endif; ?>
                            <div class="room-info">
                                <div class="room-title"><?php echo htmlspecialchars($room['nama_tipe']); ?></div>
                                <div class="room-capacity">
                                    <i class="bi bi-people"></i> Kapasitas <?php echo $room['kapasitas']; ?> orang
                                </div>
                                <div class="room-desc"><?php echo htmlspecialchars(substr($room['deskripsi'], 0, 80)) . (strlen($room['deskripsi']) > 80 ? '...' : ''); ?></div>
                                <div class="room-price"><?php echo formatCurrency($room['harga_malam']); ?> per malam</div>
                                <a href="/tubes_basdat/detail_kamar.php?id=<?php echo $room['id_tipe']; ?>" class="btn btn-primary w-100" style="color: white;">
                                    <i class="bi bi-eye"></i> Lihat Detail Lengkap
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            <div class="text-center mt-5">
                <a href="/tubes_basdat/rooms.php" class="btn btn-primary btn-lg" style="color: white;">Jelajahi Semua Kamar</a>
            </div>
        </div>
    </section>

    <!-- Gallery -->
    <?php if (count($gallery_photos) > 0): ?>
        <section class="section">
            <div class="container">
                <h2 class="section-title">Galeri Hotel</h2>
                <div class="row g-3">
                    <?php foreach ($gallery_photos as $photo): ?>
                        <div class="col-md-6 col-lg-3">
                            <div class="gallery-item">
                                <img src="<?php echo UPLOAD_URL; ?>gallery/<?php echo htmlspecialchars($photo['foto_path']); ?>" alt="Gallery">
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
                <div class="text-center mt-4">
                    <a href="/tubes_basdat/gallery.php" class="btn btn-primary btn-lg" style="color: white;">Lihat Selengkapnya</a>
                </div>
            </div>
        </section>
    <?php endif; ?>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <p><strong><?php echo HOTEL_NAME; ?></strong></p>
            <p><?php echo HOTEL_ADDRESS; ?></p>
            <p>
                <i class="bi bi-telephone"></i> <?php echo HOTEL_PHONE; ?> | 
                <i class="bi bi-envelope"></i> <?php echo HOTEL_EMAIL; ?>
            </p>
            <p style="margin-top: 20px; opacity: 0.7;">&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
            <p style="margin-top: 30px; text-align: center;"><a href="/tubes_basdat/admin/login.php" style="font-size: 11px; color: rgba(255,255,255,0.3); text-decoration: none;">Admin</a></p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>