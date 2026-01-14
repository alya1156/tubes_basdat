<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$checkIn = isset($_GET['checkin']) ? $_GET['checkin'] : date('Y-m-d', strtotime('+1 day'));
$checkOut = isset($_GET['checkout']) ? $_GET['checkout'] : date('Y-m-d', strtotime('+3 days'));
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

$available_rooms = [];
if (!empty($checkIn) && !empty($checkOut)) {
    $available_rooms = getAvailableRooms($checkIn, $checkOut, $pdo);
}

$days = !empty($checkIn) && !empty($checkOut) ? calculateDays($checkIn, $checkOut) : 1;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Booking - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; }
        .navbar { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .page-header { background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; padding: 40px 0; }
        .section { padding: 40px 0; }
        .room-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: transform 0.3s; margin-bottom: 20px; }
        .room-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .room-card img { width: 100%; height: 250px; object-fit: cover; }
        .room-info { padding: 20px; }
        .room-title { font-size: 20px; font-weight: 700; margin-bottom: 10px; color: #333; }
        .room-price { font-size: 24px; color: #667eea; font-weight: 700; margin-bottom: 10px; }
        .footer { background: #333; color: white; padding: 40px 0; text-align: center; margin-top: 80px; }
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
                    <li class="nav-item"><a class="nav-link active" href="/tubes_basdat/guest/booking.php">Booking</a></li>
                    <li class="nav-item"><a class="nav-link" href="/tubes_basdat/guest/check_status.php">Cek Status</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Pesan Kamar</h1>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <div class="row mb-4">
                <div class="col-md-3">
                    <label class="form-label"><strong>Check-in</strong></label>
                    <input type="date" class="form-control" id="checkIn" value="<?php echo htmlspecialchars($checkIn); ?>" onchange="updateSearch()">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Check-out</strong></label>
                    <input type="date" class="form-control" id="checkOut" value="<?php echo htmlspecialchars($checkOut); ?>" onchange="updateSearch()">
                </div>
                <div class="col-md-3">
                    <label class="form-label"><strong>Jumlah Tamu</strong></label>
                    <input type="number" class="form-control" id="guests" value="<?php echo $guests; ?>" min="1" onchange="updateSearch()">
                </div>
                <div class="col-md-3">
                    <label class="form-label">&nbsp;</label>
                    <button class="btn btn-primary w-100" onclick="updateSearch()">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </div>

            <div class="alert alert-info">
                <strong>Durasi Menginap:</strong> <?php echo $days; ?> malam (<?php echo formatDate($checkIn); ?> - <?php echo formatDate($checkOut); ?>)
            </div>

            <?php if (count($available_rooms) > 0): ?>
                <div class="row">
                    <?php foreach ($available_rooms as $room): ?>
                        <div class="col-md-6">
                            <div class="room-card">
                                <?php if ($room['foto_cover']): ?>
                                    <img src="<?php echo UPLOAD_URL; ?>tipe_kamar/<?php echo htmlspecialchars($room['foto_cover']); ?>" alt="<?php echo htmlspecialchars($room['nama_tipe']); ?>">
                                <?php else: ?>
                                    <img src="https://via.placeholder.com/400x300?text=<?php echo urlencode($room['nama_tipe']); ?>">
                                <?php endif; ?>
                                <div class="room-info">
                                    <div class="room-title"><?php echo htmlspecialchars($room['nama_tipe']); ?></div>
                                    <p class="text-muted"><?php echo htmlspecialchars(substr($room['deskripsi'] ?? '', 0, 100)); ?>...</p>
                                    <div class="room-price"><?php echo formatCurrency($room['harga_malam']); ?>/malam</div>
                                    <div style="margin-bottom: 15px; color: #666;">
                                        <strong>Total untuk <?php echo $days; ?> malam:</strong><br>
                                        <span style="font-size: 18px; color: #667eea; font-weight: 700;">
                                            <?php echo formatCurrency($room['harga_malam'] * $days); ?>
                                        </span>
                                    </div>
                                    <a href="/tubes_basdat/guest/proses_booking.php?room=<?php echo $room['id_kamar']; ?>&checkin=<?php echo urlencode($checkIn); ?>&checkout=<?php echo urlencode($checkOut); ?>&guests=<?php echo $guests; ?>" 
                                       class="btn btn-primary w-100" style="color: white;">
                                        <i class="bi bi-calendar-plus"></i> Pesan Sekarang
                                    </a>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">
                    <i class="bi bi-exclamation-triangle"></i> Maaf, tidak ada kamar yang tersedia untuk tanggal yang Anda pilih.
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateSearch() {
            const checkin = document.getElementById('checkIn').value;
            const checkout = document.getElementById('checkOut').value;
            const guests = document.getElementById('guests').value;
            window.location = `/tubes_basdat/guest/booking.php?checkin=${checkin}&checkout=${checkout}&guests=${guests}`;
        }
    </script>
</body>
</html>
