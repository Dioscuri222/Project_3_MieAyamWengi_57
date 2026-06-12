<?php
// Order Details Modal Renderer - Mie Ayam Wengi 57
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/auth_check.php';

$order_id = filter_input(INPUT_GET, 'id', FILTER_VALIDATE_INT);

if (!$order_id) {
    echo '<div class="modal-body p-4 text-center text-muted">ID Pesanan tidak valid.</div>';
    exit;
}

try {
    // 1. Fetch order details
    $order_stmt = $pdo->prepare("SELECT * FROM orders WHERE id = ?");
    $order_stmt->execute([$order_id]);
    $order = $order_stmt->fetch();

    if (!$order) {
        echo '<div class="modal-body p-4 text-center text-muted">Pesanan tidak ditemukan.</div>';
        exit;
    }

    // 2. Fetch order items
    $items_stmt = $pdo->prepare("
        SELECT oi.*, m.name as menu_name, m.image_path
        FROM order_items oi
        JOIN menus m ON oi.menu_id = m.id
        WHERE oi.order_id = ?
    ");
    $items_stmt->execute([$order_id]);
    $items = $items_stmt->fetchAll();

} catch (PDOException $e) {
    echo '<div class="modal-body p-4 text-center text-danger">Database error: ' . htmlspecialchars($e->getMessage()) . '</div>';
    exit;
}

// Generate the modal HTML
?>
<div class="modal-header border-color">
    <h5 class="modal-title fw-bold text-white" id="orderDetailsModalLabel">
        <i class="bi bi-receipt text-warning me-2"></i> Rincian Pesanan #${order_id}
    </h5>
    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
</div>
<div class="modal-body p-4">
    <!-- Customer Meta -->
    <div class="p-3 bg-secondary rounded-3 border border-color mb-4" style="font-size: 0.9rem;">
        <div class="row g-2">
            <div class="col-6">
                <span class="text-muted d-block">Pemesan</span>
                <span class="fw-bold text-white"><?php echo htmlspecialchars($order['customer_name']); ?></span>
            </div>
            <div class="col-6 text-end">
                <span class="text-muted d-block">Metode / Meja</span>
                <span class="badge bg-warning text-dark fw-bold"><?php echo htmlspecialchars($order['table_number']); ?></span>
            </div>
            <div class="col-6 mt-2">
                <span class="text-muted d-block">WhatsApp</span>
                <span class="text-white"><i class="bi bi-whatsapp text-success me-1"></i> <?php echo htmlspecialchars($order['phone_number']); ?></span>
            </div>
            <div class="col-6 text-end mt-2">
                <span class="text-muted d-block">Pembayaran</span>
                <span class="badge bg-info text-white fw-bold" style="background-color: var(--accent-orange) !important;"><i class="bi bi-credit-card me-1"></i> <?php echo htmlspecialchars($order['payment_method'] ?? 'Tunai'); ?></span>
            </div>
            <div class="col-12 mt-2 border-top border-color pt-2">
                <span class="text-muted d-block">Waktu Order</span>
                <span class="text-white"><?php echo date('d F Y, H:i:s WIB', strtotime($order['created_at'])); ?></span>
            </div>
        </div>
    </div>

    <!-- Items Breakdown Title -->
    <h6 class="text-muted fw-bold mb-3 fs-8 text-uppercase tracking-wider">Item Noodle & Pendamping</h6>
    
    <!-- Items Table -->
    <div class="d-flex flex-column gap-3 mb-4">
        <?php foreach ($items as $item): ?>
            <div class="d-flex align-items-center justify-content-between border-bottom border-color pb-2" style="font-size: 0.95rem;">
                <div class="d-flex align-items-center gap-3">
                    <img src="<?php echo htmlspecialchars($item['image_path']); ?>" alt="<?php echo htmlspecialchars($item['menu_name']); ?>" class="rounded" style="width: 40px; height: 40px; object-fit: cover;" onerror="this.src='https://images.unsplash.com/photo-1546069901-ba9599a7e63c?w=100&auto=format&fit=crop&q=80'">
                    <div>
                        <div class="fw-bold text-white"><?php echo htmlspecialchars($item['menu_name']); ?></div>
                        <div class="text-muted fs-8">Rp <?php echo number_format($item['price'], 0, ',', '.'); ?> x <?php echo $item['quantity']; ?></div>
                    </div>
                </div>
                <div class="fw-bold text-white">
                    Rp <?php echo number_format($item['price'] * $item['quantity'], 0, ',', '.'); ?>
                </div>
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Grand Total -->
    <div class="d-flex justify-content-between align-items-center mb-4 p-3 bg-secondary rounded-3 border border-color">
        <span class="fw-bold text-muted">Total Pembayaran:</span>
        <span class="fs-4 fw-extrabold text-gradient">Rp <?php echo number_format($order['total_amount'], 0, ',', '.'); ?></span>
    </div>

    <!-- Customer Notes -->
    <h6 class="text-muted fw-bold mb-2 fs-8 text-uppercase tracking-wider">Catatan Tambahan</h6>
    <div class="p-3 bg-secondary rounded-3 border border-color" style="font-size: 0.9rem; min-height: 50px;">
        <?php if (!empty($order['notes'])): ?>
            <span class="text-white"><i class="bi bi-chat-left-text text-warning me-1"></i> "<?php echo htmlspecialchars($order['notes']); ?>"</span>
        <?php else: ?>
            <span class="text-muted italic">Tidak ada catatan khusus dari pelanggan.</span>
        <?php endif; ?>
    </div>

</div>
<div class="modal-footer border-color">
    <button type="button" class="btn btn-wengi-primary btn-sm" data-bs-dismiss="modal">Selesai Membaca</button>
</div>
