<?php
// PDO Database Connection Configuration for Mie Ayam Wengi 57
// Compatible with local XAMPP MySQL server

$host = 'localhost';
$dbname = 'mie_ayam_wengi_57';
$username = 'root';
$password = '';

try {
    // Create PDO connection with strict error mode and default fetch mode to object/associative array
    $pdo = new PDO("mysql:host=$host;dbname=$dbname;charset=utf8mb4", $username, $password, [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ]);
} catch (PDOException $e) {
    // Elegant warning on connection failure
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
