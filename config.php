<?php
// config.php - Konfigurasi database yang diperbaiki
$host = "localhost";
$username = "root";
$password = "";
$database = "tahfeedz";
$port = 3306;

// Koneksi database dengan error handling yang lebih baik
try {
    $koneksi = mysqli_connect($host, $username, $password, $database, $port);
    
    if (!$koneksi) {
        throw new Exception("Koneksi Gagal: " . mysqli_connect_error());
    }
    
    // Set charset untuk menghindari masalah encoding
    mysqli_set_charset($koneksi, "utf8mb4");
    
} catch (Exception $e) {
    // Log error dan tampilkan pesan yang user-friendly
    error_log("Database Connection Error: " . $e->getMessage());
    die("Maaf, terjadi kesalahan koneksi database. Silakan coba lagi nanti.");
}

// Set timezone sesuai dengan Indonesia
date_default_timezone_set('Asia/Jakarta');

// Konfigurasi session yang lebih aman
if (session_status() == PHP_SESSION_NONE) {
    ini_set('session.cookie_httponly', 1);
    ini_set('session.cookie_secure', 0); // Set ke 1 jika menggunakan HTTPS
    ini_set('session.use_strict_mode', 1);
    session_start();
}
?>