<?php
// Admin Order History - Mie Ayam Wengi 57
require_once __DIR__ . '/auth_check.php';
require_once __DIR__ . '/../config/db.php';

// Handle order status updates and deletions
$status_success = false;
$status_error = '';
$delete_success = false;
$delete_error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    $action = $_POST['action'];

    if ($action === 'update_status') {
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

    elseif ($action === 'delete_order') {
        $order_id = filter_input(INPUT_POST, 'order_id', FILTER_VALIDATE_INT);
        if ($order_id) {
            try {
                $stmt = $pdo->prepare("DELETE FROM orders WHERE id = ?");
                $stmt->execute([$order_id]);
                $delete_success = true;
            } catch (PDOException $e) {
                $delete_error = 'Gagal menghapus pesanan: ' . $e->getMessage();
            }
        }
    }
}

// Handle filters
$filter_status = filter_input(INPUT_GET, 'status', FILTER_DEFAULT) ?: 'All';
$search_query = trim(filter_input(INPUT_GET, 'search', FILTER_DEFAULT)) ?: '';

try {
    // Construct base query
    $query_str = "SELECT * FROM orders WHERE 1=1";
    $params = [];

    // Filter by status if selected
    if ($filter_status !== 'All') {
        $query_str .= " AND status = :status";
        $params[':status'] = $filter_status;
    }

    // Filter by search query (customer name or table number)
    if ($search_query !== '') {
        $query_str .= " AND (customer_name LIKE :search OR table_number LIKE :search OR id LIKE :search)";
        $params[':search'] = '%' . $search_query . '%';
    }

    // Sort by newest
    $query_str .= " ORDER BY created_at DESC";

    $stmt = $pdo->prepare($query_str);
    $stmt->execute($params);
    $orders = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Gagal memuat data riwayat pesanan: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan - Mie Ayam Wengi 57</title>
    
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
                        <a class="nav-link" href="dashboard.php"><i class="bi bi-speedometer2 me-1"></i> Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="orders.php"><i class="bi bi-receipt me-1"></i> Riwayat Pesanan</a>
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

    <!-- Main Content -->
    <main class="container py-5">
        
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-md-center mb-5 gap-3">
            <div>
                <h1 class="text-white fw-bold mb-1">Riwayat Pesanan Lengkap</h1>
                <p class="text-muted mb-0">Kelola dan telusuri seluruh transaksi Mie Ayam Wengi 57.</p>
            </div>
        </div>

        <?php if ($status_success): ?>
            <div class="alert alert-success card-wengi text-white py-3 border-success mb-4" role="alert">
                <i class="bi bi-check-circle-fill text-success me-2"></i> Status pesanan berhasil diperbarui.
            </div>
        <?php endif; ?>
        <?php if ($delete_success): ?>
            <div class="alert alert-success card-wengi text-white py-3 border-success mb-4" role="alert">
                <i class="bi bi-trash-fill text-success me-2"></i> Riwayat pesanan berhasil dihapus secara permanen.
            </div>
        <?php endif; ?>
        <?php if (!empty($delete_error)): ?>
            <div class="alert alert-danger card-wengi text-white py-3 border-danger mb-4" role="alert">
                <i class="bi bi-exclamation-octagon-fill text-danger me-2"></i> <?php echo $delete_error; ?>
            </div>
        <?php endif; ?>

        <!-- Search & Filter Controls -->
        <div class="card-wengi p-4 mb-4">
            <form action="orders.php" method="GET" class="row g-3">
                <div class="col-md-5">
                    <label class="form-label text-muted fs-7 fw-semibold">PENCARIAN CEPAT</label>
                    <div class="input-group">
                        <span class="input-group-text bg-secondary border-color text-muted"><i class="bi bi-search"></i></span>
                        <input type="text" name="search" class="form-control form-wengi" placeholder="Cari nama, meja, atau ID..." value="<?php echo htmlspecialchars($search_query); ?>">
                    </div>
                </div>

                <div class="col-md-4">
                    <label class="form-label text-muted fs-7 fw-semibold">FILTER STATUS PESANAN</label>
                    <select name="status" class="form-select form-wengi">
                        <option value="All" <?php echo $filter_status === 'All' ? 'selected' : ''; ?>>🍲 Semua Status</option>
                        <option value="Pending" <?php echo $filter_status === 'Pending' ? 'selected' : ''; ?>>⏳ Pending</option>
                        <option value="Cooking" <?php echo $filter_status === 'Cooking' ? 'selected' : ''; ?>>🔥 Cooking</option>
                        <option value="Completed" <?php echo $filter_status === 'Completed' ? 'selected' : ''; ?>>✅ Completed</option>
                        <option value="Cancelled" <?php echo $filter_status === 'Cancelled' ? 'selected' : ''; ?>>❌ Cancelled</option>
                    </select>
                </div>

                <div class="col-md-3 d-flex align-items-end">
                    <button type="submit" class="btn btn-wengi-primary w-100 py-2.5">
                        <i class="bi bi-funnel me-1"></i> Terapkan Filter
                    </button>
                </div>
            </form>
        </div>

        <!-- Filter Tab Summary -->
        <div class="d-flex justify-content-between align-items-center mb-4">
            <h2 class="h5 fw-bold text-white mb-0">Hasil Penelusuran</h2>
            <span class="text-muted fs-7">Menampilkan **<?php echo count($orders); ?>** pesanan</span>
        </div>

        <!-- Orders Table -->
        <div class="card-wengi p-4">
            <div class="table-responsive">
                <table class="table table-wengi table-hover mb-0">
                    <thead>
                        <tr>
                            <th>ORDER ID</th>
                            <th>PEMESAN</th>
                            <th>MEJA</th>
                            <th>TANGGAL & WAKTU</th>
                            <th>TOTAL BAYAR</th>
                            <th>STATUS</th>
                            <th class="text-end">AKSI</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($orders)): ?>
                            <tr>
                                <td colspan="7" class="text-center text-muted py-5">
                                    <i class="bi bi-receipt-cutoff fs-2 mb-2 d-block text-secondary"></i>
                                    Tidak ada data pesanan yang cocok dengan pencarian/filter Anda.
                                </td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($orders as $order): ?>
                                <tr>
                                    <td class="fw-bold text-warning">#<?php echo $order['id']; ?></td>
                                    <td>
                                        <div class="fw-bold text-white"><?php echo htmlspecialchars($order['customer_name']); ?></div>
                                        <div class="d-flex align-items-center gap-2 mt-1">
                                            <span class="text-muted fs-8"><i class="bi bi-whatsapp"></i> <?php echo htmlspecialchars($order['phone_number']); ?></span>
                                            <span class="badge bg-secondary text-muted border border-color fs-9 py-0.5 px-2" style="font-size: 0.75rem; font-weight: 600;">
                                                <i class="bi bi-credit-card me-1"></i> <?php echo htmlspecialchars($order['payment_method'] ?? 'Tunai'); ?>
                                            </span>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="badge bg-secondary py-1.5 px-3 border border-color">
                                            <?php echo htmlspecialchars($order['table_number']); ?>
                                        </span>
                                    </td>
                                    <td class="text-muted fs-7">
                                        <?php echo date('d F Y, H:i', strtotime($order['created_at'])); ?> WIB
                                    </td>
                                    <td class="fw-bold text-gradient">
                                        Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?>
                                    </td>
                                    <td>
                                        <form action="orders.php?status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_query); ?>" method="POST" class="d-inline">
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
                                        <div class="d-flex justify-content-end gap-2">
                                            <button type="button" class="btn btn-wengi-outline btn-sm py-1.5 px-3 view-details-btn" data-id="<?php echo $order['id']; ?>">
                                                <i class="bi bi-file-earmark-text"></i> Rincian
                                            </button>
                                            <button type="button" class="btn btn-danger btn-sm py-1.5 px-2.5 delete-order-btn" data-id="<?php echo $order['id']; ?>">
                                                <i class="bi bi-trash"></i>
                                            </button>
                                        </div>
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
                <div class="modal-body text-center py-5">
                    <span class="spinner-border text-warning" role="status"></span>
                    <p class="text-muted mt-3">Memuat detail pesanan...</p>
                </div>
            </div>
        </div>
    </div>

    <!-- DELETE CONFIRMATION MODAL -->
    <div class="modal fade" id="deleteOrderModal" tabindex="-1" aria-labelledby="deleteOrderModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered modal-sm">
            <div class="modal-content card-wengi" style="border-color: #ef4444;">
                <div class="modal-body text-center p-4">
                    <div class="text-danger mb-3" style="font-size: 3rem;">
                        <i class="bi bi-exclamation-triangle-fill"></i>
                    </div>
                    <h5 class="fw-bold mb-2 text-white">Hapus Pesanan?</h5>
                    <p class="text-muted fs-7" id="delete-order-alert-text">Apakah Anda yakin ingin menghapus pesanan ini secara permanen?</p>
                    
                    <form action="orders.php?status=<?php echo urlencode($filter_status); ?>&search=<?php echo urlencode($search_query); ?>" method="POST">
                        <input type="hidden" name="action" value="delete_order">
                        <input type="hidden" name="order_id" id="delete-order-id">
                        
                        <div class="d-flex gap-2 justify-content-center mt-4">
                            <button type="button" class="btn btn-wengi-outline btn-sm px-3" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-danger btn-sm px-3">Hapus</button>
                        </div>
                    </form>
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

            // Delete Order Confirm Dialog
            const deleteOrderModal = new bootstrap.Modal(document.getElementById('deleteOrderModal'));
            document.querySelectorAll('.delete-order-btn').forEach(button => {
                button.addEventListener('click', () => {
                    const id = button.dataset.id;
                    document.getElementById('delete-order-id').value = id;
                    document.getElementById('delete-order-alert-text').innerHTML = `Apakah Anda yakin ingin menghapus pesanan <strong>#${id}</strong> secara permanen dari database?`;
                    deleteOrderModal.show();
                });
            });
        });
    </script>
</body>
</html>
