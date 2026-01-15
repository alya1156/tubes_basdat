<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';

$step = isset($_GET['step']) ? (int)$_GET['step'] : 1;
$checkIn = isset($_GET['checkin']) ? $_GET['checkin'] : date('Y-m-d', strtotime('+1 day'));
$checkOut = isset($_GET['checkout']) ? $_GET['checkout'] : date('Y-m-d', strtotime('+3 days'));
$id_tipe = isset($_GET['id_tipe']) ? (int)$_GET['id_tipe'] : 0;
$guests = isset($_GET['guests']) ? (int)$_GET['guests'] : 1;

$tipe_kamar = null;
$available_rooms = [];
$days = calculateDays($checkIn, $checkOut);

// Step 2: Get available room numbers for selected room type
if ($step == 2 && $id_tipe > 0) {
    $stmt = $pdo->prepare("SELECT * FROM tipe_kamar WHERE id_tipe = ?");
    $stmt->execute([$id_tipe]);
    $tipe_kamar = $stmt->fetch();
    
    if ($tipe_kamar) {
        // Get available kamar for this type
        $sql = "SELECT k.* FROM kamar k 
                WHERE k.id_tipe = ? AND k.status = 'tersedia'
                AND k.no_kamar NOT IN (
                    SELECT DISTINCT id_kamar FROM reservasi 
                    WHERE (tgl_masuk < ? AND tgl_keluar > ?)
                    AND status IN ('pending', 'confirmed')
                )
                ORDER BY k.no_kamar ASC";
        $stmt = $pdo->prepare($sql);
        $stmt->execute([$id_tipe, $checkOut, $checkIn]);
        $available_rooms = $stmt->fetchAll();
    }
} else {
    // Step 1: Get all room types
    $stmt = $pdo->query("SELECT * FROM tipe_kamar ORDER BY harga_malam ASC");
    $available_rooms = $stmt->fetchAll();
}
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
        :root { --primary: #667eea; --secondary: #764ba2; }
        body { font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif; background: #f8f9fa; }
        .navbar { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); box-shadow: 0 2px 4px rgba(0,0,0,0.1); }
        .navbar-brand { font-weight: 700; font-size: 24px; }
        .page-header { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); color: white; padding: 40px 0; }
        .section { padding: 40px 0; }
        .step-indicator { display: flex; justify-content: space-between; margin-bottom: 40px; }
        .step-item { flex: 1; text-align: center; padding: 15px; }
        .step-item.active .step-number { background: var(--primary); color: white; }
        .step-item .step-number { width: 50px; height: 50px; border-radius: 50%; background: #ddd; color: #999; font-weight: 700; font-size: 24px; line-height: 50px; margin: 0 auto 10px; }
        .step-item .step-label { font-weight: 600; color: #333; }
        .step-divider { flex-grow: 1; border-top: 3px solid #ddd; margin: 20px 0; }
        .room-card { border: none; border-radius: 12px; overflow: hidden; box-shadow: 0 4px 15px rgba(0,0,0,0.1); transition: all 0.3s; margin-bottom: 20px; cursor: pointer; }
        .room-card:hover { transform: translateY(-5px); box-shadow: 0 10px 30px rgba(0,0,0,0.2); }
        .room-card.selected { border: 3px solid var(--primary); }
        .room-image-placeholder { background: linear-gradient(135deg, #e8f4f8 0%, #f0f0f0 100%); height: 200px; display: flex; align-items: center; justify-content: center; font-size: 48px; color: #ccc; }
        .room-info { padding: 20px; }
        .room-title { font-size: 18px; font-weight: 700; margin-bottom: 8px; color: #333; }
        .room-capacity { font-size: 13px; color: #999; margin-bottom: 10px; }
        .room-desc { font-size: 13px; color: #666; margin-bottom: 15px; line-height: 1.5; }
        .room-price { font-size: 22px; color: var(--primary); font-weight: 700; }
        .room-total { font-size: 12px; color: #999; margin-top: 5px; }
        .btn-primary { background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%); border: none; }
        .btn-primary:hover { color: white; }
        .form-section { background: white; border-radius: 12px; padding: 30px; box-shadow: 0 2px 10px rgba(0,0,0,0.05); margin-bottom: 30px; }
        .footer { background: #2c3e50; color: white; padding: 40px 0; text-align: center; margin-top: 80px; }
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
                </ul>
            </div>
        </div>
    </nav>

    <!-- Page Header -->
    <section class="page-header">
        <div class="container">
            <h1>Pesan Kamar Anda</h1>
            <p>Pilih kamar impian dengan harga terbaik di <?php echo HOTEL_NAME; ?></p>
        </div>
    </section>

    <!-- Main Content -->
    <section class="section">
        <div class="container">
            <!-- Step Indicator -->
            <div class="step-indicator">
                <div class="step-item <?php echo $step == 1 ? 'active' : ''; ?>">
                    <div class="step-number">1</div>
                    <div class="step-label">Pilih Tanggal & Tipe Kamar</div>
                </div>
                <div class="step-item <?php echo $step == 2 ? 'active' : ''; ?>">
                    <div class="step-number">2</div>
                    <div class="step-label">Pilih Nomor Kamar</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-label">Isi Data & Bayar</div>
                </div>
            </div>

            <!-- Date & Guest Selection -->
            <div class="form-section">
                <h5 class="mb-3"><i class="bi bi-calendar-event"></i> Informasi Menginap</h5>
                <div class="row">
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
                            <i class="bi bi-search"></i> Perbarui
                        </button>
                    </div>
                </div>
                <div class="alert alert-info mt-3 mb-0">
                    <strong><i class="bi bi-info-circle"></i> Durasi Menginap:</strong> <?php echo $days; ?> malam (<?php echo formatDate($checkIn); ?> - <?php echo formatDate($checkOut); ?>)
                </div>
            </div>

            <!-- Step 1: Select Room Type -->
            <?php if ($step == 1): ?>
                <div class="form-section">
                    <h5 class="mb-3"><i class="bi bi-door-closed"></i> Pilih Tipe Kamar</h5>
                    <?php if (count($available_rooms) > 0): ?>
                        <div class="row">
                            <?php foreach ($available_rooms as $room): ?>
                                <div class="col-md-6 col-lg-4">
                                    <div class="room-card" onclick="selectRoomType(<?php echo $room['id_tipe']; ?>)">
                                        <div class="room-image-placeholder">
                                            <i class="bi bi-door-closed"></i>
                                        </div>
                                        <div class="room-info">
                                            <h6 class="room-title"><?php echo htmlspecialchars($room['nama_tipe']); ?></h6>
                                            <p class="room-capacity"><i class="bi bi-people"></i> Kapasitas: <?php echo $room['kapasitas']; ?> orang</p>
                                            <p class="room-desc"><?php echo htmlspecialchars($room['deskripsi']); ?></p>
                                            <div class="room-price"><?php echo formatCurrency($room['harga_malam']); ?>/malam</div>
                                            <div class="room-total">Total <?php echo $days; ?> malam: <strong><?php echo formatCurrency($room['harga_malam'] * $days); ?></strong></div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Maaf, tidak ada tipe kamar yang tersedia.
                        </div>
                    <?php endif; ?>
                </div>

            <!-- Step 2: Select Room Number -->
            <?php elseif ($step == 2 && $tipe_kamar): ?>
                <div class="form-section">
                    <h5 class="mb-3">
                        <i class="bi bi-house-door"></i> Pilih Nomor Kamar - <?php echo htmlspecialchars($tipe_kamar['nama_tipe']); ?>
                        <button class="btn btn-sm btn-secondary float-end" onclick="backToStep1()">
                            <i class="bi bi-arrow-left"></i> Kembali
                        </button>
                    </h5>
                    <?php if (count($available_rooms) > 0): ?>
                        <div class="row">
                            <?php foreach ($available_rooms as $room): ?>
                                <div class="col-md-6 col-lg-3">
                                    <div class="room-card" onclick="selectRoom(<?php echo $room['id_kamar']; ?>)">
                                        <div class="room-image-placeholder">
                                            <i class="bi bi-calendar-check"></i>
                                        </div>
                                        <div class="room-info">
                                            <h6 class="room-title">Kamar <?php echo htmlspecialchars($room['no_kamar']); ?></h6>
                                            <p class="text-muted" style="margin-bottom: 15px; font-size: 13px;">
                                                <i class="bi bi-check-circle text-success"></i> Tersedia
                                            </p>
                                            <a href="/tubes_basdat/guest/proses_booking.php?id_kamar=<?php echo $room['id_kamar']; ?>&checkin=<?php echo urlencode($checkIn); ?>&checkout=<?php echo urlencode($checkOut); ?>&guests=<?php echo $guests; ?>" 
                                               class="btn btn-primary w-100 btn-sm">
                                                <i class="bi bi-check"></i> Pilih Kamar Ini
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        </div>
                    <?php else: ?>
                        <div class="alert alert-warning">
                            <i class="bi bi-exclamation-triangle"></i> Maaf, tidak ada kamar tipe ini yang tersedia untuk tanggal yang dipilih.
                        </div>
                    <?php endif; ?>
                </div>
            <?php endif; ?>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer">
        <p>&copy; 2026 <?php echo HOTEL_NAME; ?>. All rights reserved.</p>
        <p style="margin-top: 10px; font-size: 12px;"><a href="/tubes_basdat/admin/login.php" style="color: #aaa; text-decoration: none;"><i class="bi bi-lock-fill"></i> Admin Login</a></p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function updateSearch() {
            const checkin = document.getElementById('checkIn').value;
            const checkout = document.getElementById('checkOut').value;
            const guests = document.getElementById('guests').value;
            window.location = `/tubes_basdat/guest/booking.php?step=1&checkin=${checkin}&checkout=${checkout}&guests=${guests}`;
        }

        function selectRoomType(id_tipe) {
            const checkin = document.getElementById('checkIn').value;
            const checkout = document.getElementById('checkOut').value;
            const guests = document.getElementById('guests').value;
            window.location = `/tubes_basdat/guest/booking.php?step=2&id_tipe=${id_tipe}&checkin=${checkin}&checkout=${checkout}&guests=${guests}`;
        }

        function backToStep1() {
            const checkin = document.getElementById('checkIn').value;
            const checkout = document.getElementById('checkOut').value;
            const guests = document.getElementById('guests').value;
            window.location = `/tubes_basdat/guest/booking.php?step=1&checkin=${checkin}&checkout=${checkout}&guests=${guests}`;
        }
    </script>
</body>
</html>