<?php
// PDO Database Connection Configuration for Mie Ayam Wengi 57
// Compatible with local XAMPP MySQL server and TiDB Cloud / Remote MySQL

$host = getenv('DB_HOST') ?: 'localhost';
$port = getenv('DB_PORT') ?: '3306';
$dbname = getenv('DB_NAME') ?: 'mie_ayam_wengi_57';
$username = getenv('DB_USER') ?: 'root';
$password = getenv('DB_PASSWORD') ?: '';

try {
    // Default PDO options
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    // TiDB Cloud / Remote MySQL usually require SSL.
    // If not localhost, enable SSL options to ensure secure connection and compatibility.
    if ($host !== 'localhost' && $host !== '127.0.0.1') {
        // Find system CA bundle path to enable SSL/TLS
        $caPaths = [
            '/etc/pki/tls/certs/ca-bundle.crt',     // Amazon Linux (used by Vercel) / CentOS / RHEL
            '/etc/ssl/certs/ca-certificates.crt',   // Debian / Ubuntu
            '/etc/ssl/cert.pem',                    // macOS / Alpine
            '/etc/ssl/ca-bundle.pem',               // SUSE
        ];
        $caFile = null;
        foreach ($caPaths as $path) {
            if (file_exists($path)) {
                $caFile = $path;
                break;
            }
        }

        if ($caFile) {
            $options[PDO::MYSQL_ATTR_SSL_CA] = $caFile;
        } else {
            // Fallback: Enable SSL verification disablement if no CA file is found
            $options[PDO::MYSQL_ATTR_SSL_VERIFY_SERVER_CERT] = false;
        }
    }

    // Create PDO connection with port specified
    $pdo = new PDO("mysql:host=$host;port=$port;dbname=$dbname;charset=utf8mb4", $username, $password, $options);
} catch (PDOException $e) {
    // Elegant warning on connection failure
    die("Koneksi database gagal: " . $e->getMessage());
}
?>
