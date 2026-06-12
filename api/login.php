<?php
// Admin Login Portal - Mie Ayam Wengi 57
require_once 'config/db.php';

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Redirect if already logged in
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: admin/dashboard.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim(filter_input(INPUT_POST, 'username', FILTER_SANITIZE_SPECIAL_CHARS));
    $password = trim($_POST['password']);

    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $pdo->prepare("SELECT * FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $admin = $stmt->fetch();

            if ($admin && password_verify($password, $admin['password'])) {
                // Password matches, write session variables
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $admin['id'];
                $_SESSION['admin_username'] = $admin['username'];
                $_SESSION['admin_name'] = $admin['full_name'];

                header("Location: admin/dashboard.php");
                exit;
            } else {
                $error = 'Username atau Password salah!';
            }
        } catch (PDOException $e) {
            $error = 'Terjadi masalah database: ' . $e->getMessage();
        }
    } else {
        $error = 'Harap isi seluruh kolom login.';
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login Admin - Mie Ayam Wengi 57</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="assets/css/style.css">
    
    <style>
        body {
            background: radial-gradient(circle at center, #ffffff 0%, #faf9f6 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .login-card {
            max-width: 420px;
            width: 100%;
            padding: 40px;
            border-radius: var(--radius-md);
            animation: fadeIn 0.5s ease;
        }
    </style>
</head>
<body>

    <div class="container d-flex justify-content-center px-3">
        <div class="card-wengi login-card">
            
            <div class="text-center mb-4">
                <a href="index.php" class="text-decoration-none fs-2 fw-extrabold text-gradient mb-2 d-inline-block">
                    <i class="bi bi-fire text-warning"></i>
                </a>
                <h2 class="fw-bold text-white mb-1">Portal Admin</h2>
                <p class="text-muted fs-7">Masuk untuk mengelola pesanan & menu Wengi 57</p>
            </div>

            <?php if (!empty($error)): ?>
                <div class="alert alert-danger py-2.5 px-3 border-danger bg-danger bg-opacity-10 text-danger text-center fs-7 rounded-3 mb-3 d-flex align-items-center justify-content-center gap-2" role="alert">
                    <i class="bi bi-exclamation-octagon-fill"></i>
                    <span><?php echo $error; ?></span>
                </div>
            <?php endif; ?>

            <form action="login.php" method="POST">
                <div class="mb-3">
                    <label for="username" class="form-label text-muted fs-7 fw-semibold">USERNAME</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-color text-muted"><i class="bi bi-person"></i></span>
                        <input type="text" class="form-control form-wengi bg-primary" id="username" name="username" placeholder="Masukkan username..." required>
                    </div>
                </div>

                <div class="mb-4">
                    <label for="password" class="form-label text-muted fs-7 fw-semibold">PASSWORD</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-color text-muted"><i class="bi bi-lock"></i></span>
                        <input type="password" class="form-control form-wengi bg-primary" id="password" name="password" placeholder="Masukkan password..." required>
                    </div>
                </div>

                <button type="submit" class="btn btn-wengi-primary w-100 py-2.5 mb-3">
                    <i class="bi bi-box-arrow-in-right me-1"></i> Masuk Sekarang
                </button>

                <div class="text-center">
                    <a href="index.php" class="text-muted fs-7 text-decoration-none hover-text-white">
                        <i class="bi bi-arrow-left me-1"></i> Kembali ke Menu Utama
                    </a>
                </div>
            </form>

        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
