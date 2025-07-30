<?php
// fungsi.php - File fungsi yang diperbaiki
include_once 'config.php';

function ambildata($query)
{
    global $koneksi;
    $result = mysqli_query($koneksi,$query);
    $rows = [];
    while($row = mysqli_fetch_assoc($result))
    {
        $rows[] = $row;
    }
    return $rows;
}

function loginUser($email, $password) {
    global $koneksi;
    
    // Cek koneksi database
    if (!$koneksi) {
        return [
            'status' => 'error',
            'message' => 'Koneksi database gagal'
        ];
    }
    
    // Escape input untuk mencegah SQL injection
    $email = mysqli_real_escape_string($koneksi, $email);
    
    // Query untuk mencari user berdasarkan email
    $query = "SELECT * FROM users WHERE email = '$email' LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        return [
            'status' => 'error',
            'message' => 'Query error: ' . mysqli_error($koneksi)
        ];
    }
    
    if (mysqli_num_rows($result) === 1) {
        $user = mysqli_fetch_assoc($result);
        
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Login berhasil - Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['logged_in'] = true;
            
            return [
                'status' => 'success',
                'message' => 'Login berhasil',
                'user' => $user
            ];
        } else {
            // Password salah
            return [
                'status' => 'error',
                'message' => 'Password salah'
            ];
        }
    } else {
        // Email tidak ditemukan
        return [
            'status' => 'error',
            'message' => 'Email tidak ditemukan'
        ];
    }
}

function registerUser($email, $password, $nama = '') {
    global $koneksi;
    
    // Cek koneksi database
    if (!$koneksi) {
        return [
            'status' => 'error',
            'message' => 'Koneksi database gagal'
        ];
    }
    
    // Escape input
    $email = mysqli_real_escape_string($koneksi, $email);
    $nama = mysqli_real_escape_string($koneksi, $nama);
    
    // Cek apakah email sudah ada
    $checkQuery = "SELECT * FROM users WHERE email = '$email'";
    $checkResult = mysqli_query($koneksi, $checkQuery);
    
    if (!$checkResult) {
        return [
            'status' => 'error',
            'message' => 'Query error: ' . mysqli_error($koneksi)
        ];
    }
    
    if (mysqli_num_rows($checkResult) > 0) {
        return [
            'status' => 'error',
            'message' => 'Email sudah terdaftar'
        ];
    }
    
    // Hash password
    $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
    
    // Insert user baru
    $query = "INSERT INTO users (email, password, nama, created_at) VALUES ('$email', '$hashedPassword', '$nama', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        return [
            'status' => 'success',
            'message' => 'Registrasi berhasil'
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Gagal melakukan registrasi: ' . mysqli_error($koneksi)
        ];
    }
}

function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']) && isset($_SESSION['logged_in']) && $_SESSION['logged_in'] === true;
}

function logout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    session_unset();
    session_destroy();
    return true;
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}
?>