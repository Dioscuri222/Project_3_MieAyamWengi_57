<?php
// Database Migration Script - Mie Ayam Wengi 57
// Run this script to update the orders table schema.

require_once __DIR__ . '/../config/db.php';

try {
    echo "Starting migration...\n";
    
    // Check if payment_method column already exists
    $stmt = $pdo->prepare("SHOW COLUMNS FROM `orders` LIKE 'payment_method'");
    $stmt->execute();
    $column_exists = $stmt->fetch();
    
    if (!$column_exists) {
        // Add payment_method column
        $pdo->exec("ALTER TABLE `orders` ADD COLUMN `payment_method` VARCHAR(50) NOT NULL DEFAULT 'Tunai' AFTER `status`");
        echo "Successfully added 'payment_method' column to 'orders' table.\n";
    } else {
        echo "'payment_method' column already exists in 'orders' table. No changes made.\n";
    }
    
    echo "Migration completed successfully!\n";
} catch (PDOException $e) {
    echo "Migration failed: " . $e->getMessage() . "\n";
    exit(1);
}
?>
