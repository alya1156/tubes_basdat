<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

requireAdminLogin();

$stats = getDashboardStats($pdo);
$msg = getSessionMessage();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        body {
            background-color: #f8f9fa;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }
        .sidebar {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            padding: 20px 0;
            position: fixed;
            width: 250px;
            left: 0;
            top: 0;
        }
        .sidebar .brand {
            color: white;
            padding: 20px;
            text-align: center;
            border-bottom: 1px solid rgba(255,255,255,0.1);
            margin-bottom: 20px;
        }
        .sidebar .brand h5 {
            margin: 0;
            font-weight: 700;
        }
        .sidebar .nav-link {
            color: rgba(255,255,255,0.7);
            padding: 12px 20px;
            border-left: 3px solid transparent;
            transition: all 0.3s;
            font-size: 14px;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            color: white;
            background: rgba(255,255,255,0.1);
            border-left-color: white;
        }
        .sidebar .nav-section {
            color: rgba(255,255,255,0.5);
            font-size: 12px;        
            padding: 15px 20px 5px;
            text-transform: uppercase;
            font-weight: 600;
            margin-top: 20px;
        }
        .main-content {
            margin-left: 250px;
            padding: 20px;
        }
        .topbar {
            background: white;
            padding: 15px 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        .stat-card {
            background: white;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0,0,0,0.05);
            margin-bottom: 20px;
            border-left: 4px solid #667eea;
        }
        .stat-card.revenue {
            border-left-color: #4CAF50;
        }
        .stat-card.occupied {
            border-left-color: #2196F3;
        }
        .stat-card.pending {
            border-left-color: #FF9800;
        }
        .stat-card.unpaid {
            border-left-color: #F44336;
        }
        .stat-card .stat-value {
            font-size: 28px;
            font-weight: 700;
            color: #333;
            margin: 10px 0;
        }
        .stat-card .stat-label {
            color: #666;
            font-size: 13px;
        }
        .stat-card .stat-icon {
            font-size: 24px;
            opacity: 0.3;
            margin-bottom: 10px;
        }
        @media (max-width: 768px) {
            .sidebar {
                width: 200px;
            }
            .main-content {
                margin-left: 200px;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="brand" style="cursor: pointer;" onclick="logoutConfirm()">
            <h5><?php echo HOTEL_NAME; ?></h5>
            <small>klik untuk logout</small>
        </div>

        <nav class="nav flex-column">
            <div class="nav-section">Menu Utama</div>
            <a href="/tubes_basdat/admin/dashboard.php" class="nav-link active">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>

            <div class="nav-section">Operasional</div>
            <a href="/tubes_basdat/modules/kamar/list.php" class="nav-link">
                <i class="bi bi-door-closed"></i> Cek Kamar
            </a>
            <a href="/tubes_basdat/modules/reservasi/list.php" class="nav-link">
                <i class="bi bi-calendar-check"></i> Reservasi
            </a>
            <a href="/tubes_basdat/modules/tamu/list.php" class="nav-link">
                <i class="bi bi-people"></i> Manajemen Tamu
            </a>
            <a href="/tubes_basdat/modules/pembayaran/list.php" class="nav-link">
                <i class="bi bi-credit-card"></i> Verifikasi Pembayaran
            </a>

            <div class="nav-section">Sistem</div>
            <a href="#" class="nav-link" onclick="logoutConfirm(); return false;">
                <i class="bi bi-box-arrow-left"></i> Logout
            </a>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        <!-- Topbar -->
        <div class="topbar">
            <div>
                <h4 style="margin: 0;">Dashboard</h4>
                <small class="text-muted">Sistem Manajemen Hotel <?php echo HOTEL_NAME; ?></small>
            </div>
            <div>
                <small class="text-muted">
                    <i class="bi bi-calendar-event"></i> <?php echo date('l, d M Y'); ?>
                </small>
            </div>
        </div>

        <!-- Messages -->
        <?php if ($msg): ?>
            <div class="alert alert-<?php echo $msg['type'] === 'success' ? 'success' : 'danger'; ?> alert-dismissible fade show" role="alert">
                <i class="bi bi-<?php echo $msg['type'] === 'success' ? 'check-circle' : 'exclamation-circle'; ?>"></i>
                <?php echo htmlspecialchars($msg['message']); ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        <?php endif; ?>

        <!-- Statistics Cards -->
        <div class="row">
            <div class="col-md-6 col-lg-3">
                <div class="stat-card revenue">
                    <div class="stat-icon"><i class="bi bi-cash-coin"></i></div>
                    <div class="stat-label">Pendapatan Hari Ini</div>
                    <div class="stat-value"><?php echo formatCurrency($stats['revenue_today']); ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card occupied">
                    <div class="stat-icon"><i class="bi bi-door-closed"></i></div>
                    <div class="stat-label">Kamar Terisi</div>
                    <div class="stat-value"><?php echo $stats['occupied_rooms']; ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card pending">
                    <div class="stat-icon"><i class="bi bi-clock-history"></i></div>
                    <div class="stat-label">Reservasi Pending</div>
                    <div class="stat-value"><?php echo $stats['pending_reservations']; ?></div>
                </div>
            </div>

            <div class="col-md-6 col-lg-3">
                <div class="stat-card unpaid">
                    <div class="stat-icon"><i class="bi bi-exclamation-circle"></i></div>
                    <div class="stat-label">Pembayaran Belum Lunas</div>
                    <div class="stat-value"><?php echo $stats['unpaid_payments']; ?></div>
                </div>
            </div>
        </div>

        <!-- Quick Actions -->
        <div style="margin-top: 30px;">
            <h5 style="margin-bottom: 15px;">Menu Utama</h5>
            <div class="row">
                <div class="col-md-4 mb-3">
                    <a href="/tubes_basdat/modules/kamar/list.php" class="btn btn-primary w-100 btn-lg">
                        <i class="bi bi-door-closed"></i> Cek Kamar
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="/tubes_basdat/modules/reservasi/list.php" class="btn btn-info w-100 btn-lg">
                        <i class="bi bi-calendar-check"></i> Reservasi
                    </a>
                </div>
                <div class="col-md-4 mb-3">
                    <a href="/tubes_basdat/modules/pembayaran/list.php" class="btn btn-danger w-100 btn-lg">
                        <i class="bi bi-credit-card"></i> Pembayaran
                    </a>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12 mb-3">
                    <a href="/tubes_basdat/modules/tamu/list.php" class="btn btn-success w-100 btn-lg">
                        <i class="bi bi-people"></i> Manajemen Tamu
                    </a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        function logoutConfirm() {
            if (confirm('Anda akan logout. Lanjutkan?')) {
                window.location.href = '/tubes_basdat/admin/logout.php';
            }
        }
    </script>
</body>
</html>
