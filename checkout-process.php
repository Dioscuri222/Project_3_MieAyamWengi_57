<?php
// Checkout Order Processor Backend - Mie Ayam Wengi 57
header('Content-Type: application/json');
require_once 'config/db.php';

// Enable error reporting for safety
error_reporting(E_ALL);
ini_set('display_errors', 0);

$response = [
    'success' => false,
    'message' => 'Metode request tidak diizinkan.'
];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Validate required fields
    $customer_name = filter_input(INPUT_POST, 'customer_name', FILTER_SANITIZE_SPECIAL_CHARS);
    $table_number = filter_input(INPUT_POST, 'table_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $phone_number = filter_input(INPUT_POST, 'phone_number', FILTER_SANITIZE_SPECIAL_CHARS);
    $notes = filter_input(INPUT_POST, 'notes', FILTER_SANITIZE_SPECIAL_CHARS);
    $payment_method = filter_input(INPUT_POST, 'payment_method', FILTER_SANITIZE_SPECIAL_CHARS) ?: 'Tunai';
    
    $cart_json = isset($_POST['cart_items']) ? $_POST['cart_items'] : '';
    $total_amount = filter_input(INPUT_POST, 'total_amount', FILTER_VALIDATE_FLOAT);

    if (empty($customer_name) || empty($table_number) || empty($phone_number) || empty($cart_json) || $total_amount === false) {
        $response['message'] = 'Semua field wajib diisi dengan benar.';
        echo json_encode($response);
        exit;
    }

    $cart_items = json_decode($cart_json, true);
    if (!is_array($cart_items) || empty($cart_items)) {
        $response['message'] = 'Keranjang Anda kosong atau format belanja tidak valid.';
        echo json_encode($response);
        exit;
    }

    try {
        // Begin strict atomic transaction to guarantee database consistency
        $pdo->beginTransaction();

        // 1. Insert into orders table
        $order_stmt = $pdo->prepare("
            INSERT INTO orders (customer_name, table_number, phone_number, notes, total_amount, status, payment_method) 
            VALUES (:customer_name, :table_number, :phone_number, :notes, :total_amount, 'Pending', :payment_method)
        ");
        $order_stmt->execute([
            ':customer_name' => $customer_name,
            ':table_number' => $table_number,
            ':phone_number' => $phone_number,
            ':notes' => $notes ?: null,
            ':total_amount' => $total_amount,
            ':payment_method' => $payment_method
        ]);

        $order_id = $pdo->lastInsertId();

        // 2. Insert order items
        $item_stmt = $pdo->prepare("
            INSERT INTO order_items (order_id, menu_id, quantity, price) 
            VALUES (:order_id, :menu_id, :quantity, :price)
        ");

        foreach ($cart_items as $item) {
            $menu_id = isset($item['id']) ? intval($item['id']) : 0;
            $quantity = isset($item['quantity']) ? intval($item['quantity']) : 0;
            $price = isset($item['price']) ? floatval($item['price']) : 0.0;

            if ($menu_id <= 0 || $quantity <= 0 || $price < 0) {
                throw new Exception("Data menu di keranjang belanja tidak valid.");
            }

            // Verify item exists and is active in database to prevent manipulation
            $check_stmt = $pdo->prepare("SELECT price, is_available FROM menus WHERE id = ?");
            $check_stmt->execute([$menu_id]);
            $db_menu = $check_stmt->fetch();

            if (!$db_menu) {
                throw new Exception("Menu dengan ID {$menu_id} tidak ditemukan.");
            }
            if ($db_menu['is_available'] == 0) {
                throw new Exception("Menu '" . htmlspecialchars($item['name']) . "' sedang tidak tersedia.");
            }

            $item_stmt->execute([
                ':order_id' => $order_id,
                ':menu_id' => $menu_id,
                ':quantity' => $quantity,
                ':price' => $price
            ]);
        }

        // Commit transaction if all inserts succeed
        $pdo->commit();

        $response['success'] = true;
        $response['message'] = 'Pesanan berhasil dikirim.';
        $response['order_id'] = $order_id;
        $response['customer_name'] = $customer_name;
        $response['table_number'] = $table_number;
        $response['total_amount'] = $total_amount;
        $response['payment_method'] = $payment_method;

    } catch (Exception $e) {
        // Rollback transaction to protect database state on error
        if ($pdo->inTransaction()) {
            $pdo->rollBack();
        }
        $response['message'] = 'Terjadi kesalahan sistem: ' . $e->getMessage();
    }
}

echo json_encode($response);
?>
