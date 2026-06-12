<?php
// Session Authentication Checker - Mie Ayam Wengi 57
// Included at the top of all administrative views to prevent unauthorized actions

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    // Save attempted URL or redirect to login.php
    header("Location: ../login.php");
    exit;
}
?>
