<?php
require_once 'includes/config.php';
require_once 'includes/functions.php';

$stmt = $pdo->query("SELECT * FROM tipe_kamar ORDER BY harga_malam ASC LIMIT 5");
$featured_rooms = $stmt->fetchAll();
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
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #ff6b6b;
        }
        * { margin: 0; padding: 0; box-sizing: border-box; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; color: #333; }
        
        /* Navbar */
        .navbar { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); box-shadow: 0 2px 10px rgba(0,0,0,0.15); padding: 15px 0; }
        .navbar-brand { font-weight: 800; font-size: 26px; letter-spacing: -1px; }
        .navbar-nav .nav-link { font-weight: 500; margin: 0 10px; transition: all 0.3s; }
        .navbar-nav .nav-link:hover { color: #ffd700 !important; }
        
        /* Hero Section */
        .hero-section { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; padding: 80px 0; position: relative; overflow: hidden; }
        .hero-section::before { content: ''; position: absolute; top: -50%; right: -10%; width: 500px; height: 500px; background: rgba(255,255,255,0.1); border-radius: 50%; }
        .hero-section::after { content: ''; position: absolute; bottom: -30%; left: 0; width: 300px; height: 300px; background: rgba(255,255,255,0.05); border-radius: 50%; }
        .hero-content { position: relative; z-index: 1; }
        .hero-content h1 { font-size: 56px; font-weight: 800; margin-bottom: 20px; line-height: 1.2; }
        .hero-content .tagline { font-size: 22px; opacity: 0.95; margin-bottom: 10px; font-weight: 500; }
        .hero-content .description { font-size: 16px; opacity: 0.9; margin-bottom: 30px; line-height: 1.8; max-width: 500px; }
        .hero-image { text-align: center; }
        .hero-image-placeholder { background: rgba(255,255,255,0.15); border-radius: 15px; padding: 80px 40px; backdrop-filter: blur(10px); border: 2px solid rgba(255,255,255,0.2); }
        .hero-image-placeholder i { font-size: 80px; opacity: 0.7; }
        
        /* Features Section */
        .features { background: #f8f9fa; padding: 60px 0; }
        .feature-card { text-align: center; padding: 40px 30px; border-radius: 12px; background: white; margin-bottom: 30px; box-shadow: 0 2px 8px rgba(0,0,0,0.05); transition: all 0.3s; }
        .feature-card:hover { transform: translateY(-5px); box-shadow: 0 8px 20px rgba(0,0,0,0.1); }
        .feature-card i { font-size: 48px; color: var(--primary); margin-bottom: 20px; }
        .feature-card h5 { font-weight: 700; margin-bottom: 15px; color: #333; }
        .feature-card p { color: #666; font-size: 14px; line-height: 1.6; }
        
        /* Rooms Section */
        .rooms-section { padding: 80px 0; }
        .section-title { text-align: center; font-size: 42px; font-weight: 800; margin-bottom: 15px; color: #333; }
        .section-subtitle { text-align: center; color: #666; font-size: 18px; margin-bottom: 50px; }
        .room-card { border: none; border-radius: 15px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; height: 100%; }
        .room-card:hover { transform: translateY(-15px); box-shadow: 0 15px 40px rgba(0,0,0,0.2); }
        .room-image-placeholder { background: linear-gradient(135deg, #e8f4f8 0%, #f0f0f0 100%); height: 220px; display: flex; align-items: center; justify-content: center; font-size: 60px; }
        .room-body { padding: 25px; }
        .room-title { font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #333; }
        .room-capacity { color: #999; font-size: 13px; margin-bottom: 12px; }
        .room-desc { color: #666; font-size: 13px; line-height: 1.6; margin-bottom: 20px; }
        .room-price { font-size: 28px; font-weight: 800; color: var(--primary); margin-bottom: 15px; }
        .btn-book { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border: none; border-radius: 8px; padding: 12px 25px; color: white; font-weight: 600; transition: all 0.3s; }
        .btn-book:hover { transform: translateY(-2px); box-shadow: 0 5px 15px rgba(102, 126, 234, 0.4); color: white; }
        
        /* Gallery Section */
        .gallery-section { background: #f8f9fa; padding: 80px 0; }
        .gallery-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: 20px; }
        .gallery-item { border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); aspect-ratio: 1; }
        .gallery-item-inner { width: 100%; height: 100%; background: linear-gradient(135deg, #e8f4f8 0%, #f0f0f0 100%); display: flex; align-items: center; justify-content: center; font-size: 50px; transition: all 0.3s; }
        .gallery-item:hover .gallery-item-inner { transform: scale(1.05); }
        
        /* Footer */
        .footer { background: #2c3e50; color: white; padding: 50px 0 20px; }
        .footer-col h6 { font-weight: 700; margin-bottom: 20px; color: var(--primary); }
        .footer-col p { font-size: 14px; line-height: 1.8; color: #bbb; margin: 8px 0; }
        .footer-bottom { border-top: 1px solid rgba(255,255,255,0.1); margin-top: 30px; padding-top: 20px; text-align: center; color: #999; font-size: 13px; }
        
        /* Buttons */
        .btn-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border: none; border-radius: 8px; padding: 12px 28px; font-weight: 600; transition: all 0.3s; }
        .btn-primary:hover { transform: translateY(-2px); box-shadow: 0 8px 20px rgba(102, 126, 234, 0.4); color: white; }
    </style>
</head>
<body>
    <!-- Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#"><i class="bi bi-building"></i> <?php echo HOTEL_NAME; ?></a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="#home">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/rooms.php">Kamar</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/gallery.php">Galeri</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/booking.php">Booking</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section" id="home">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <div class="hero-content">
                        <h1><?php echo HOTEL_NAME; ?></h1>
                        <p class="tagline">Temukan Keindahan & Kenyamanan di Tepi Pantai</p>
                        <p class="description"><?php echo HOTEL_DESC; ?> Nikmati sunset yang memukau, pantai pribadi, dan layanan bintang lima yang tak terlupakan.</p>
                        <a href="/tubes_basdat/guest/booking.php" class="btn btn-primary btn-lg"><i class="bi bi-calendar-check"></i> Booking Sekarang</a>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="hero-image">
                        <div class="hero-image-placeholder">
                            <i class="bi bi-image"></i>
                            <p style="color: rgba(255,255,255,0.7); margin-top: 20px;">Pemandangan Pantai Grand Hotel Ashri</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section class="features">
        <div class="container">
            <div class="row">
                <div class="col-md-3">
                    <div class="feature-card">
                        <i class="bi bi-geo-alt"></i>
                        <h5>Lokasi Pantai Premium</h5>
                        <p>Langsung menghadap pantai dengan akses private beach eksklusif untuk tamu</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <i class="bi bi-star"></i>
                        <h5>Fasilitas Bintang Lima</h5>
                        <p>Kolam renang infinity, spa luxury, restaurant fine dining & gym internasional</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <i class="bi bi-heart"></i>
                        <h5>Layanan Prima 24/7</h5>
                        <p>Tim profesional siap memenuhi semua kebutuhan Anda dengan dedikasi tinggi</p>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="feature-card">
                        <i class="bi bi-shield-check"></i>
                        <h5>Keamanan Terjamin</h5>
                        <p>Sistem keamanan canggih dan staf keamanan profesional sepanjang waktu</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Rooms Section -->
    <section class="rooms-section">
        <div class="container">
            <h2 class="section-title">Pilihan Kamar Kami</h2>
            <p class="section-subtitle">Nikmati berbagai pilihan kamar dengan harga terjangkau</p>
            
            <div class="row g-4">
                <?php foreach ($featured_rooms as $room): ?>
                    <div class="col-md-6 col-lg-4">
                        <div class="room-card">
                            <div class="room-image-placeholder">
                                <i class="bi bi-door-closed"></i>
                            </div>
                            <div class="room-body">
                                <h5 class="room-title"><?php echo htmlspecialchars($room['nama_tipe']); ?></h5>
                                <p class="room-capacity"><i class="bi bi-people"></i> Kapasitas: <?php echo $room['kapasitas']; ?> orang</p>
                                <p class="room-desc"><?php echo htmlspecialchars($room['deskripsi']); ?></p>
                                <p class="room-price"><?php echo formatCurrency($room['harga_malam']); ?></p>
                                <a href="/tubes_basdat/guest/booking.php" class="btn btn-book w-100">
                                    <i class="bi bi-check-circle"></i> Pesan Sekarang
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="/tubes_basdat/rooms.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-arrow-right"></i> Lihat Semua Kamar
                </a>
            </div>
        </div>
    </section>

    <!-- Gallery Section -->
    <section class="gallery-section">
        <div class="container">
            <h2 class="section-title">Galeri Hotel</h2>
            <p class="section-subtitle">Jelajahi keindahan fasilitas Grand Hotel Ashri</p>
            
            <div class="gallery-grid">
                <?php for ($i = 0; $i < 6; $i++): ?>
                    <div class="gallery-item">
                        <div class="gallery-item-inner">
                            <i class="bi bi-image"></i>
                        </div>
                    </div>
                <?php endfor; ?>
            </div>
            
            <div class="text-center mt-5">
                <a href="/tubes_basdat/gallery.php" class="btn btn-primary btn-lg">
                    <i class="bi bi-images"></i> Lihat Semua Foto
                </a>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <div class="container">
            <div class="row">
                <div class="col-md-4 footer-col">
                    <h6><?php echo HOTEL_NAME; ?></h6>
                    <p><?php echo HOTEL_DESC; ?></p>
                </div>
                <div class="col-md-4 footer-col">
                    <h6>Kontak</h6>
                    <p><i class="bi bi-geo-alt"></i> <?php echo HOTEL_ADDRESS; ?></p>
                    <p><i class="bi bi-telephone"></i> <?php echo HOTEL_PHONE; ?></p>
                    <p><i class="bi bi-envelope"></i> <?php echo HOTEL_EMAIL; ?></p>
                </div>
                <div class="col-md-4 footer-col">
                    <h6>Jam Operasional</h6>
                    <p>Checkin: 14:00 - 23:00</p>
                    <p>Checkout: 11:00</p>
                    <p>24/7 Customer Support</p>
                </div>
            </div>
            <div class="footer-bottom">
                <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All Rights Reserved. | <a href="#" style="color: var(--primary); text-decoration: none;">Privacy Policy</a></p>
                <p style="margin-top: 10px;"><a href="/tubes_basdat/admin/login.php" style="color: var(--primary); text-decoration: none; font-size: 12px;"><i class="bi bi-lock-fill"></i> Admin Login</a></p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>