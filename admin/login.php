<?php
require_once '../includes/config.php';
require_once '../includes/functions.php';
require_once '../includes/auth.php';

// If already logged in, redirect to dashboard
if (isAdminLoggedIn()) {
    header('Location: /tubes_basdat/admin/dashboard.php');
    exit;
}

$error = '';
$errorType = '';
$success = false;

if (isset($_GET['timeout'])) {
    $error = 'Session Anda telah berakhir. Silakan login kembali.';
    $errorType = 'timeout';
} elseif (isset($_GET['logout'])) {
    $success = true;
    $error = 'Logout berhasil! Anda akan kembali login.';
    $errorType = 'success';
} elseif (isset($_GET['error'])) {
    if ($_GET['error'] === 'invalid') {
        $error = 'Username atau password salah. Coba lagi!';
        $errorType = 'invalid';
    } elseif ($_GET['error'] === 'empty') {
        $error = 'Username dan password harus diisi.';
        $errorType = 'empty';
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - <?php echo HOTEL_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <style>
        :root {
            --primary: #667eea;
            --secondary: #764ba2;
            --accent: #ff6b6b;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            position: relative;
            overflow: hidden;
        }
        
        /* Decorative background elements */
        body::before {
            content: '';
            position: absolute;
            top: -50%;
            right: -10%;
            width: 500px;
            height: 500px;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 50%;
            z-index: 0;
        }
        
        body::after {
            content: '';
            position: absolute;
            bottom: -30%;
            left: 0;
            width: 300px;
            height: 300px;
            background: rgba(255, 255, 255, 0.05);
            border-radius: 50%;
            z-index: 0;
        }
        
        .login-wrapper {
            position: relative;
            z-index: 10;
            width: 100%;
            max-width: 420px;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 16px;
            box-shadow: 0 20px 60px rgba(0, 0, 0, 0.3);
            padding: 45px;
            backdrop-filter: blur(10px);
        }
        
        .login-header {
            text-align: center;
            margin-bottom: 40px;
        }
        
        .hotel-icon {
            width: 60px;
            height: 60px;
            margin: 0 auto 20px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 32px;
            color: white;
        }
        
        .login-header h1 {
            font-size: 28px;
            font-weight: 800;
            color: #333;
            margin-bottom: 8px;
            letter-spacing: -0.5px;
        }
        
        .login-header p {
            color: #999;
            font-size: 14px;
            font-weight: 500;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-label {
            display: block;
            margin-bottom: 8px;
            color: #333;
            font-weight: 600;
            font-size: 14px;
        }
        
        .form-control {
            width: 100%;
            padding: 12px 16px;
            border: 1.5px solid #e0e0e0;
            border-radius: 10px;
            font-size: 14px;
            font-family: inherit;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }
        
        .form-control:focus {
            outline: none;
            border-color: var(--primary);
            background: white;
            box-shadow: 0 0 0 4px rgba(102, 126, 234, 0.1);
        }
        
        .form-control::placeholder {
            color: #aaa;
        }
        
        .input-group-icon {
            position: relative;
        }
        
        .input-group-icon i {
            position: absolute;
            right: 14px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
            font-size: 16px;
            pointer-events: none;
        }
        
        .btn-login {
            width: 100%;
            padding: 14px;
            border-radius: 10px;
            font-weight: 700;
            font-size: 16px;
            background: linear-gradient(135deg, var(--primary) 0%, var(--secondary) 100%);
            border: none;
            color: white;
            cursor: pointer;
            transition: all 0.3s ease;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-top: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
        }
        
        .btn-login:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102, 126, 234, 0.4);
        }
        
        .btn-login:active {
            transform: translateY(0);
        }
        
        .btn-back {
            width: 100%;
            padding: 12px;
            border-radius: 10px;
            font-weight: 600;
            border: 2px solid #e0e0e0;
            background: white;
            color: #666;
            cursor: pointer;
            margin-top: 12px;
            transition: all 0.3s ease;
            text-decoration: none;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 6px;
            font-size: 14px;
        }
        
        .btn-back:hover {
            border-color: var(--primary);
            color: var(--primary);
            background: #f8f9fa;
        }
        
        .alert-box {
            border-radius: 10px;
            border: none;
            margin-bottom: 25px;
            padding: 14px 16px;
            display: flex;
            align-items: center;
            gap: 12px;
            font-size: 14px;
            animation: slideDown 0.3s ease;
        }
        
        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-10px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        
        .alert-danger {
            background: #fee;
            border-left: 4px solid var(--accent);
            color: #c33;
        }
        
        .alert-warning {
            background: #fff3cd;
            border-left: 4px solid #ffc107;
            color: #664d03;
        }
        
        .divider {
            text-align: center;
            margin: 25px 0;
            color: #ddd;
            font-size: 13px;
        }
        
        .demo-credentials {
            background: linear-gradient(135deg, #e7f3ff 0%, #e0f7ff 100%);
            border: 1px solid #b3e5fc;
            border-radius: 10px;
            padding: 16px;
            margin-top: 20px;
            font-size: 13px;
            color: #0d47a1;
        }
        
        .demo-credentials strong {
            display: block;
            margin-bottom: 10px;
            font-weight: 700;
            color: #0d47a1;
        }
        
        .demo-credentials code {
            background: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-family: 'Courier New', monospace;
            display: inline-block;
            margin-bottom: 6px;
            border: 1px solid #b3e5fc;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 8px;
            margin-bottom: 20px;
            font-size: 14px;
        }
        
        .remember-me input[type="checkbox"] {
            width: 18px;
            height: 18px;
            cursor: pointer;
            accent-color: var(--primary);
        }
        
        .remember-me label {
            cursor: pointer;
            margin: 0;
            color: #666;
        }
    </style>
</head>
<body>
    <div class="login-wrapper">
        <div class="login-card">
            <!-- Header -->
            <div class="login-header">
                <div class="hotel-icon">
                    <i class="bi bi-building"></i>
                </div>
                <h1><?php echo HOTEL_NAME; ?></h1>
                <p>Admin Panel Login</p>
            </div>
            
            <!-- Error/Success Alert -->
            <?php if ($error): ?>
                <div class="alert-box <?php echo ($errorType === 'success' || $errorType === 'timeout') ? 'alert-warning' : 'alert-danger'; ?>">
                    <i class="bi bi-<?php echo ($errorType === 'success' ? 'check-circle-fill' : 'exclamation-circle-fill'); ?>"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>
            
            <!-- Login Form -->
            <form method="POST" action="process_login.php" autocomplete="off">
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-person-fill"></i> Username
                    </label>
                    <div class="input-group-icon">
                        <input 
                            type="text"
                            class="form-control"
                            name="username"
                            placeholder="Masukkan username Anda"
                            required
                            autofocus
                            autocomplete="off"
                        >
                        <i class="bi bi-person"></i>
                    </div>
                </div>
                
                <div class="form-group">
                    <label class="form-label">
                        <i class="bi bi-lock-fill"></i> Password
                    </label>
                    <div class="input-group-icon">
                        <input 
                            type="password"
                            class="form-control"
                            name="password"
                            placeholder="Masukkan password Anda"
                            required
                            id="passwordInput"
                        >
                        <i class="bi bi-lock"></i>
                    </div>
                </div>
                
                <!-- Remember Me Checkbox -->
                <div class="remember-me">
                    <input type="checkbox" id="rememberMe" name="remember">
                    <label for="rememberMe">Ingat saya di perangkat ini</label>
                </div>
                
                <!-- Login Button -->
                <button type="submit" class="btn-login">
                    <i class="bi bi-box-arrow-in-right"></i>
                    Masuk ke Admin Panel
                </button>
                
                <!-- Back Button -->
                <a href="/tubes_basdat/" class="btn-back">
                    <i class="bi bi-arrow-left"></i>
                    Kembali ke Halaman Utama
                </a>
            </form>
            
            <!-- Demo Credentials -->
            <div class="divider">Demo Akun</div>
            <div class="demo-credentials">
                <strong><i class="bi bi-info-circle"></i> Akun Demonstrasi:</strong>
                <div>Username: <code>admin</code></div>
                <div>Password: <code>1234</code></div>
            </div>
        </div>
    </div>
    
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Simple form validation
        document.querySelector('form').addEventListener('submit', function(e) {
            const username = document.querySelector('input[name="username"]').value.trim();
            const password = document.querySelector('input[name="password"]').value;
            
            if (!username || !password) {
                e.preventDefault();
                alert('Username dan password tidak boleh kosong!');
            }
        });
        
        // Remember me functionality
        const rememberCheckbox = document.getElementById('rememberMe');
        const usernameInput = document.querySelector('input[name="username"]');
        
        // Load saved username if exists
        if (localStorage.getItem('savedUsername')) {
            usernameInput.value = localStorage.getItem('savedUsername');
            rememberCheckbox.checked = true;
        }
        
        // Save username on form submit
        document.querySelector('form').addEventListener('submit', function() {
            if (rememberCheckbox.checked) {
                localStorage.setItem('savedUsername', usernameInput.value);
            } else {
                localStorage.removeItem('savedUsername');
            }
        });
    </script>
</body>
</html>
