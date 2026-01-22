<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$roomTypeId = isset($_GET['id']) ? (int)$_GET['id'] : 0;

$stmt = $pdo->prepare("SELECT * FROM tipe_kamar WHERE id_tipe = ?");
$stmt->execute([$roomTypeId]);
$roomType = $stmt->fetch();

if (!$roomType) {
    header('Location: /tubes_basdat/rooms.php');
    exit;
}

// Get facilities
$stmt = $pdo->prepare("SELECT DISTINCT f.* FROM fasilitas f JOIN kamar_fasilitas kf ON f.id_fasilitas = kf.id_fasilitas JOIN kamar k ON kf.id_kamar = k.id_kamar WHERE k.id_tipe = ?");
$stmt->execute([$roomTypeId]);
$fasilitas = $stmt->fetchAll();

// Get room photos
$stmt = $pdo->prepare("SELECT * FROM kamar_foto WHERE id_kamar IN (SELECT id_kamar FROM kamar WHERE id_tipe = ?) ORDER BY urutan ASC");
$stmt->execute([$roomTypeId]);
$photos = $stmt->fetchAll();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($roomType['nama_tipe']); ?> - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #0f0f1e; color: #e0e0e0; }
        .navbar { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); border-bottom: 2px solid #d4af37; }
        .navbar-brand { font-weight: 700; font-size: 24px; color: #d4af37; }
        .page-header { background: linear-gradient(135deg, #1a1a2e 0%, #16213e 100%); color: #d4af37; padding: 40px 0; border-bottom: 2px solid #d4af37; }
        .page-header h1 { color: #d4af37; }
        .section { padding: 40px 0; }
        .carousel-inner { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(212, 175, 55, 0.2); border: 2px solid #d4af37; }
        .carousel-item img { height: 500px; object-fit: cover; }
        .detail-section { background: #1a1a2e; padding: 30px; border-radius: 12px; box-shadow: 0 2px 4px rgba(212, 175, 55, 0.1); margin-bottom: 30px; border: 1px solid #d4af37; }
        .detail-section h5 { color: #d4af37; }
        .facility-item { display: inline-block; margin-right: 20px; margin-bottom: 15px; }
        .facility-icon { font-size: 24px; color: #d4af37; margin-right: 8px; }
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
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/gallery.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1><?php echo htmlspecialchars($roomType['nama_tipe']); ?></h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="row">
                <div class="col-lg-7">
                    <!-- Photo Carousel -->
                    <?php if (count($photos) > 0): ?>
                        <div id="photoCarousel" class="carousel slide" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                <?php foreach ($photos as $index => $photo): ?>
                                    <div class="carousel-item<?php echo $index === 0 ? ' active' : ''; ?>">
                                        <img src="<?php echo UPLOAD_URL; ?>kamar/<?php echo htmlspecialchars($photo['foto_path']); ?>" class="d-block w-100" alt="Room Photo">
                                    </div>
                                <?php endforeach; ?>
                            </div>
                            <?php if (count($photos) > 1): ?>
                                <button class="carousel-control-prev" type="button" data-bs-target="#photoCarousel" data-bs-slide="prev">
                                    <span class="carousel-control-prev-icon"></span>
                                </button>
                                <button class="carousel-control-next" type="button" data-bs-target="#photoCarousel" data-bs-slide="next">
                                    <span class="carousel-control-next-icon"></span>
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php else: ?>
                        <?php 
                            $imagePath = 'uploads/gallery/' . htmlspecialchars($roomType['nama_tipe']) . '.jpg';
                            $imageExists = file_exists($imagePath);
                        ?>
                        <?php if ($imageExists): ?>
                            <img src="<?php echo $imagePath; ?>" class="img-fluid" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <?php else: ?>
                            <img src="https://via.placeholder.com/600x500?text=<?php echo urlencode($roomType['nama_tipe']); ?>" class="img-fluid" style="border-radius: 12px; box-shadow: 0 4px 15px rgba(0,0,0,0.1);">
                        <?php endif; ?>
                    <?php endif; ?>

                    <!-- Description -->
                    <div class="detail-section mt-4">
                        <h4 style="margin-bottom: 15px;">Deskripsi</h4>
                        <p><?php echo htmlspecialchars($roomType['deskripsi']); ?></p>
                    </div>

                    <!-- Facilities -->
                    <?php if (count($fasilitas) > 0): ?>
                        <div class="detail-section">
                            <h4 style="margin-bottom: 20px;">Fasilitas</h4>
                            <div>
                                <?php foreach ($fasilitas as $f): ?>
                                    <div class="facility-item">
                                        <span class="facility-icon"><i class="bi bi-<?php echo htmlspecialchars($f['icon']); ?>"></i></span>
                                        <span><?php echo htmlspecialchars($f['nama_fasilitas']); ?></span>
                                    </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    <?php endif; ?>
                </div>

                <div class="col-lg-5">
                    <!-- Booking Info -->
                    <div class="detail-section" style="position: sticky; top: 20px;">
                        <h4 style="margin-bottom: 20px;">Info Pemesanan</h4>

                        <div style="margin-bottom: 20px;">
                            <small class="text-muted">Kapasitas</small>
                            <div style="font-size: 18px; font-weight: 700; color: #333;">
                                <i class="bi bi-people"></i> <?php echo $roomType['kapasitas']; ?> orang
                            </div>
                        </div>

                        <div style="margin-bottom: 20px;">
                            <small class="text-muted">Harga per Malam</small>
                            <div style="font-size: 28px; font-weight: 700; color: #667eea;">
                                <?php echo formatCurrency($roomType['harga_malam']); ?>
                            </div>
                        </div>

                        <hr>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label"><strong>Check-in</strong></label>
                            <input type="date" id="checkIn" class="form-control" value="<?php echo date('Y-m-d', strtotime('+1 day')); ?>">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label"><strong>Check-out</strong></label>
                            <input type="date" id="checkOut" class="form-control" value="<?php echo date('Y-m-d', strtotime('+3 days')); ?>">
                        </div>

                        <div style="margin-bottom: 20px;">
                            <label class="form-label"><strong>Jumlah Tamu</strong></label>
                            <input type="number" id="guests" class="form-control" min="1" value="1">
                        </div>

                        <button class="btn btn-primary btn-lg w-100" style="color: white;" onclick="bookNow()">
                            <i class="bi bi-calendar-plus"></i> Pesan Sekarang
                        </button>

                        <a href="/tubes_basdat/rooms.php" class="btn btn-secondary btn-lg w-100 mt-2">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function bookNow() {
            const checkIn = document.getElementById('checkIn').value;
            const checkOut = document.getElementById('checkOut').value;
            const guests = document.getElementById('guests').value;
            window.location = `/tubes_basdat/guest/booking.php?checkin=${checkIn}&checkout=${checkOut}&guests=${guests}`;
        }
    </script>
</body>
</html>
