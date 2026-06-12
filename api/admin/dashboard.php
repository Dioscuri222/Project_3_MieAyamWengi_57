<?php
// Admin Dashboard - Mie Ayam Wengi 57
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_check.php';

// Handle order status updates
$status_success = false;
$status_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_status') {
    $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
    $new_status = filter_input(INPUT_POST, 'status', FILTER_DEFAULT);

    $valid_statuses = ['Pending', 'Cooking', 'Completed', 'Cancelled'];

    if ($order_id && in_array($new_status, $valid_statuses)) {
        try {
            $stmt = $pdo->prepare("UPDATE orders SET status = ? WHERE id = ?");
            $stmt->execute([$new_status, $order_id]);
            $status_success = true;
        } catch (PDOException $e) {
            $status_error = 'Gagal memperbarui status: ' . $e->getMessage();
        }
    }
}

// Fetch stats
try {
    // 1. Total Orders
    $total_orders = $pdo->query("SELECT COUNT(*) FROM orders")->fetchColumn();

    // 2. Total Earnings (from Completed orders)
    $total_earnings = $pdo->query("SELECT SUM(total_amount) FROM orders WHERE status = 'Completed'")->fetchColumn() ?: 0;

    // 3. Active Menu Count
    $total_active_menus = $pdo->query("SELECT COUNT(*) FROM menus WHERE is_available = 1")->fetchColumn();

    // 4. Pending / In-progress Orders
    $active_orders_count = $pdo->query("SELECT COUNT(*) FROM orders WHERE status IN ('Pending', 'Cooking')")->fetchColumn();

    // Fetch active/pending orders for tracker
    $stmt = $pdo->query("SELECT * FROM orders WHERE status IN ('Pending', 'Cooking') ORDER BY created_at DESC");
    $active_orders = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Gagal memuat data dashboard: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard Admin - Mie Ayam Wengi 57</title>
    
    <!-- Bootstrap 5 CSS CDN -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.2/font/bootstrap-icons.min.css">
    
    <!-- Custom Style -->
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>

    <!-- Admin Top Navbar -->
    <nav class="navbar navbar-expand-lg navbar-dark navbar-wengi sticky-top py-3">
        <div class="container">
            <a class="navbar-brand text-gradient" href="dashboard.php">
                <i class="bi bi-fire text-warning"></i> WENGI 57 ADMIN
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0 ms-lg-4">
                    <li class="nav-item">
                        <a class="nav-link active" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="orders.php"><i class="bi bi-receipt me-1"></i> Riwayat Pesanan</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="menu-manage.php"><i class="bi bi-menu-button-wide me-1"></i> CRUD Menu</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center gap-3">
                    <span class="text-muted fs-7 d-none d-lg-inline"><i class="bi bi-person-circle text-warning me-1"></i> <?php echo htmlspecialchars($_SESSION['admin_name']); ?></span>
                    <a href="../logout.php" class="btn btn-danger btn-sm px-3 py-2"><i class="bi bi-box-arrow-right"></i> Keluar</a>
                </div>
            </div>
        </div>
    </nav>

    <!-- Main Content Area -->
    <main class="container py-5">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-4 gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">Ringkasan Operasional</h1>
                <p class="text-muted mb-0">Selamat datang kembali, kelola pesanan malam ini secara efisien.</p>
            </div>
            <div>
                <a href="../index.php" target="_blank" class="btn btn-wengi-outline">
                    <i class="bi bi-eye me-1"></i> Lihat Landing Page
                </a>
            </div>
        </div>

        <?php if ($status_success): ?>
            <div class="alert alert-success card-wengi text-white py-3 border-success mb-4" role="alert">
                <i class="bi bi-check-circle-fill text-success me-2"></i> Status pesanan berhasil diperbarui.
            </div>
        <?php endif; ?>
        <?php if (!empty($status_error)): ?>
            <div class="alert alert-danger card-wengi text-white py-3 border-danger mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i> <?php echo $status_error; ?>
            </div>
        <?php endif; ?>

        <!-- Stats Cards Row -->
        <div class="row g-4 mb-5">
            <!-- Stat 1: Pending Orders -->
            <div class="col-sm-6 col-lg-3">
                <div class="admin-card-stat">
                    <div class="icon"><i class="bi bi-clock-history"></i></div>
                    <div class="number text-warning"><?php echo $active_orders_count; ?></div>
                    <div class="label">Sedang Diproses</div>
                </div>
            </div>
            
            <!-- Stat 2: Total Earnings -->
            <div class="col-sm-6 col-lg-3">
                <div class="admin-card-stat">
                    <div class="icon"><i class="bi bi-wallet2 text-success"></i></div>
                    <div class="number text-success">Rp <?php echo number_format($total_earnings, 0, ',', '.'); ?></div>
                    <div class="label">Total Pendapatan (Selesai)</div>
                </div>
            </div>

            <!-- Stat 3: Total Active Menus -->
            <div class="col-sm-6 col-lg-3">
                <div class="admin-card-stat">
                    <div class="icon"><i class="bi bi-grid-3x3-gap"></i></div>
                    <div class="number"><?php echo $total_active_menus; ?></div>
                    <div class="label">Menu Mie Aktif</div>
                </div>
            </div>

            <!-- Stat 4: Total Orders All Time -->
            <div class="col-sm-6 col-lg-3">
                <div class="admin-card-stat">
                    <div class="icon"><i class="bi bi-basket3"></i></div>
                    <div class="number text-info"><?php echo $total_orders; ?></div>
                    <div class="label">Total Pesanan Masuk</div>
                </div>
            </div>
        </div>

        <!-- Orders Tracking Table -->
        <div class="card-wengi p-4 mb-5">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2 class="h4 fw-bold text-white mb-0">
                    <i class="bi bi-activity text-warning me-2"></i> Pelacak Antrean Aktif
                </h2>
                <span class="badge bg-warning text-dark px-3 py-2 fw-semibold rounded-pill">
                    <?php echo count($active_orders); ?> Pesanan Baru/Memasak
                </span>
            </div>

            <div class="table-responsive">
                <table class="table table-wengi table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>PEMESAN</th>
                            <th>MEJA</th>
                            <th>WAKTU MASUK</th>
                            <th>TOTAL BAYAR</th>
                            <th>STATUS</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($active_orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-inbox fs-2 mb-2 d-block text-secondary"></i>
                                    Belum ada pesanan aktif saat ini. Semua antrean telah selesai disajikan!
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($active_orders as $order): ?>
                                <tr>
                                    <td class="fw-bold text-warning">#<?php echo $order['id']; ?></td>
                                    <td>
                                        <div class="fw-bold text-white"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="text-muted fs-8"><i class="bi bi-whatsapp"></i> <?php echo htmlspecialchars($order['phone_number']); ?></div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary py-1.5 px-3 border border-color">
                                            <?php echo htmlspecialchars($order['table_number']); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted fs-7">
                                        <?php echo date('d M, H:i', strtotime($order['created_at'])); ?> WIB
                                    </td>
                                    <td class="fw-bold text-gradient">
                                        Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <form action="dashboard.php" method="POST" class="d-inline">
                                            <input type="hidden" name="action" value="update_status">
                                            <input type="hidden" name="order_id" value="<?php echo $order['id']; ?>">
                                            <select name="status" onchange="this.form.submit()" class="form-select form-select-sm bg-primary border-color text-light fw-semibold" style="width: 140px;">
                                                <option value="Pending" <?php echo $order['status'] === 'Pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                                                <option value="Cooking" <?php echo $order['status'] === 'Cooking' ? 'selected' : ''; ?>>🔥 Cooking</option>
                                                <option value="Completed" <?php echo $order['status'] === 'Completed' ? 'selected' : ''; ?>>✅ Completed</option>
                                                <option value="Cancelled" <?php echo $order['status'] === 'Cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                                            </select>
                                        </form>
                                    </td>
                                    <td class="text-end">
                                        <button type="button" class="btn btn-wengi-outline btn-sm py-1.5 px-3 view-details-btn" data-id="<?php echo $order['id']; ?>">
                                            <i class="bi bi-file-earmark-text"></i> Rincian
                                        </button>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>

    </main>

    <!-- Order Details Modal -->
    <div class="modal fade" id="orderDetailsModal" tabindex="-1" aria-labelledby="orderDetailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content card-wengi" id="modal-details-payload">
                <!-- Loaded dynamically via AJAX / helper -->
                <div class="modal-body text-center py-5">
                    <span class="spinner-border text-warning" role="status"></span>
                    <p class="text-muted mt-3">Memuat detail pesanan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Bootstrap 5 Bundle JS CDN -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>

    <!-- Ajax Details Handler -->
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const detailsModal = new bootstrap.Modal(document.getElementById('orderDetailsModal'));
            const modalPayload = document.getElementById('modal-details-payload');

            document.querySelectorAll('.view-details-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    modalPayload.innerHTML = `
                        <div class="modal-body text-center py-5">
                            <span class="spinner-border text-warning" role="status"></span>
                            <p class="text-muted mt-3">Mengambil data dari server Wengi...</p>
                        </div>
                    `;
                    detailsModal.show();

                    fetch(`get-order-details.php?id=${id}`)
                        .then(response => response.text())
                        .then(html => {
                            modalPayload.innerHTML = html;
                        })
                        .catch(err => {
                            modalPayload.innerHTML = `
                                <div class="modal-body p-4 text-center">
                                    <i class="bi bi-x-circle text-danger fs-1 mb-2"></i>
                                    <h5 class="text-white">Gagal Memuat</h5>
                                    <p class="text-muted">Terjadi kesalahan saat memuat data pesanan.</p>
                                    <button class="btn btn-wengi-primary btn-sm" data-bs-dismiss="modal">Tutup</button>
                                </div>
                            `;
                        });
                });
            });
        });
    </script>
</body>
</html>
