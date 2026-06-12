<?php
// config/db.php

$host     = 'project-3-mie-ayam-wengi-57.app'; // JANGAN gunakan localhost
$port     = '3306';                         // Sesuaikan dengan port dari penyedia cloud
$dbname   = 'nama_database_anda';
$username = 'user_database_anda';
$password = 'password_database_anda';

try {
    // Tambahkan parameter charset agar koneksi lebih aman
    $dsn = "mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4";
    $pdo = new PDO($dsn, $username, $password);
    
    // Atur mode error ke pengecualian
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}
?>