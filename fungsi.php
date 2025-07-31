<?php
// fungsi.php - File fungsi yang diperbaiki dan dilengkapi
include_once 'config.php';

// Fungsi untuk mengambil data dari database
function ambildata($query)
{
    global $koneksi;
    
    if (!$koneksi) {
        return [];
    }
    
    $result = mysqli_query($koneksi, $query);
    
    if (!$result) {
        error_log("Query Error: " . mysqli_error($koneksi));
        return [];
    }
    
    $rows = [];
    while($row = mysqli_fetch_assoc($result)) {
        $rows[] = $row;
    }
    
    return $rows;
}

// Fungsi login user yang diperbaiki
function loginUser($email, $password) {
    global $koneksi;
    
    // Cek koneksi database
    if (!$koneksi) {
        return [
            'status' => 'error',
            'message' => 'Koneksi database gagal'
        ];
    }
    
    // Validasi input
    if (empty($email) || empty($password)) {
        return [
            'status' => 'error',
            'message' => 'Email dan password harus diisi'
        ];
    }
    
    // Escape input untuk mencegah SQL injection
    $email = mysqli_real_escape_string($koneksi, trim($email));
    
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
        
        // Verifikasi password (support untuk password lama yang tidak di-hash)
        $password_valid = false;
        
        if (password_verify($password, $user['password'])) {
            $password_valid = true;
        } elseif ($password === $user['password']) {
            // Support untuk password lama yang tidak di-hash
            $password_valid = true;
            
            // Update password ke format hash yang aman
            $hashed_password = password_hash($password, PASSWORD_DEFAULT);
            $update_query = "UPDATE users SET password = '$hashed_password' WHERE id = " . intval($user['id']);
            mysqli_query($koneksi, $update_query);
        }
        
        if ($password_valid) {
            // Login berhasil - Set session
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            $_SESSION['nama'] = $user['nama'];
            $_SESSION['logged_in'] = true;
            $_SESSION['login_time'] = time();
            
            return [
                'status' => 'success',
                'message' => 'Login berhasil',
                'user' => $user
            ];
        } else {
            return [
                'status' => 'error',
                'message' => 'Password salah'
            ];
        }
    } else {
        return [
            'status' => 'error',
            'message' => 'Email tidak ditemukan'
        ];
    }
}

// Fungsi register user yang diperbaiki
function registerUser($email, $password, $nama = '') {
    global $koneksi;
    
    // Cek koneksi database
    if (!$koneksi) {
        return [
            'status' => 'error',
            'message' => 'Koneksi database gagal'
        ];
    }
    
    // Validasi input
    if (empty($email) || empty($password)) {
        return [
            'status' => 'error',
            'message' => 'Email dan password harus diisi'
        ];
    }
    
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        return [
            'status' => 'error',
            'message' => 'Format email tidak valid'
        ];
    }
    
    if (strlen($password) < 6) {
        return [
            'status' => 'error',
            'message' => 'Password minimal 6 karakter'
        ];
    }
    
    // Escape input
    $email = mysqli_real_escape_string($koneksi, trim($email));
    $nama = mysqli_real_escape_string($koneksi, trim($nama));
    
    // Cek apakah email sudah ada
    $checkQuery = "SELECT id FROM users WHERE email = '$email'";
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
        $user_id = mysqli_insert_id($koneksi);
        
        // Buat user settings default
        $settings_query = "INSERT INTO user_settings (user_id) VALUES ($user_id)";
        mysqli_query($koneksi, $settings_query);
        
        return [
            'status' => 'success',
            'message' => 'Registrasi berhasil',
            'user_id' => $user_id
        ];
    } else {
        return [
            'status' => 'error',
            'message' => 'Gagal melakukan registrasi: ' . mysqli_error($koneksi)
        ];
    }
}

// Fungsi untuk mengecek status login
function isLoggedIn() {
    if (!isset($_SESSION['user_id']) || empty($_SESSION['user_id']) || 
        !isset($_SESSION['logged_in']) || $_SESSION['logged_in'] !== true) {
        return false;
    }
    
    // Optional: Cek timeout session (24 jam)
    if (isset($_SESSION['login_time']) && (time() - $_SESSION['login_time']) > 86400) {
        logout();
        return false;
    }
    
    return true;
}

// Fungsi logout
function logout() {
    if (session_status() == PHP_SESSION_NONE) {
        session_start();
    }
    
    // Hapus semua data session
    $_SESSION = array();
    
    // Hapus session cookie
    if (ini_get("session.use_cookies")) {
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
    }
    
    // Destroy session
    session_destroy();
    return true;
}

// Fungsi untuk memastikan user sudah login
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']));
        exit;
    }
}

// Fungsi untuk mendapatkan data user yang sedang login
function getCurrentUser() {
    global $koneksi;
    
    if (!isLoggedIn()) {
        return null;
    }
    
    $user_id = intval($_SESSION['user_id']);
    $query = "SELECT * FROM users WHERE id = $user_id LIMIT 1";
    $result = mysqli_query($koneksi, $query);
    
    if ($result && mysqli_num_rows($result) === 1) {
        return mysqli_fetch_assoc($result);
    }
    
    return null;
}

// Fungsi untuk mendapatkan statistik hafalan user
function getUserHafalanStats($user_id) {
    global $koneksi;
    
    $user_id = intval($user_id);
    
    $query = "SELECT 
        COUNT(*) as total_hafalan,
        COUNT(CASE WHEN jenis = 'Hafalan Baru' THEN 1 END) as hafalan_baru,
        COUNT(CASE WHEN jenis = 'Murojaah' THEN 1 END) as murojaah,
        COUNT(CASE WHEN jenis = 'Tasmi\\'' THEN 1 END) as tasmi,
        COUNT(CASE WHEN MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN 1 END) as hafalan_bulan_ini,
        COUNT(CASE WHEN tanggal >= CURDATE() - INTERVAL 7 DAY THEN 1 END) as hafalan_7_hari,
        COALESCE(SUM(poin), 0) as total_poin
        FROM hafalan WHERE user_id = $user_id";
    
    $result = mysqli_query($koneksi, $query);
    
    if ($result) {
        return mysqli_fetch_assoc($result);
    }
    
    return [
        'total_hafalan' => 0,
        'hafalan_baru' => 0,
        'murojaah' => 0,
        'tasmi' => 0,
        'hafalan_bulan_ini' => 0,
        'hafalan_7_hari' => 0,
        'total_poin' => 0
    ];
}

// Fungsi untuk menghitung streak user (tanpa stored procedure)
function getUserStreak($user_id) {
    global $koneksi;
    
    $user_id = intval($user_id);
    
    // Hitung streak secara manual tanpa stored procedure
    $current_streak = 0;
    $check_date = date('Y-m-d');
    
    while (true) {
        $query = "SELECT COUNT(*) as count FROM hafalan 
                  WHERE user_id = $user_id AND tanggal = '$check_date'";
        $result = mysqli_query($koneksi, $query);
        
        if (!$result) {
            break;
        }
        
        $row = mysqli_fetch_assoc($result);
        
        if ($row['count'] > 0) {
            $current_streak++;
            $check_date = date('Y-m-d', strtotime($check_date . ' -1 day'));
        } else {
            // Jika hari ini belum ada hafalan, cek dari kemarin
            if ($check_date === date('Y-m-d')) {
                $check_date = date('Y-m-d', strtotime($check_date . ' -1 day'));
                continue;
            }
            break;
        }
        
        // Batasi loop maksimal 365 hari untuk mencegah infinite loop
        if ($current_streak >= 365) {
            break;
        }
    }
    
    return $current_streak;
}

// Fungsi untuk mendapatkan hafalan terbaru
function getRecentHafalan($user_id, $limit = 5) {
    global $koneksi;
    
    $user_id = intval($user_id);
    $limit = intval($limit);
    
    $query = "SELECT h.*, u.nama as user_nama 
              FROM hafalan h 
              LEFT JOIN users u ON h.user_id = u.id 
              WHERE h.user_id = $user_id 
              ORDER BY h.created_at DESC 
              LIMIT $limit";
    
    return ambildata($query);
}

// Fungsi untuk mendapatkan reminder yang akan datang
function getUpcomingReminders($user_id, $limit = 3) {
    global $koneksi;
    
    $user_id = intval($user_id);
    $limit = intval($limit);
    
    $query = "SELECT * FROM reminders 
              WHERE user_id = $user_id 
              AND is_active = 1 
              AND reminder_datetime > NOW() 
              ORDER BY reminder_datetime ASC 
              LIMIT $limit";
    
    return ambildata($query);
}

// Fungsi untuk format tanggal Indonesia
function formatTanggalIndonesia($tanggal) {
    $bulan = array(
        1 => 'Januari',
        2 => 'Februari',
        3 => 'Maret',
        4 => 'April',
        5 => 'Mei',
        6 => 'Juni',
        7 => 'Juli',
        8 => 'Agustus',
        9 => 'September',
        10 => 'Oktober',
        11 => 'November',
        12 => 'Desember'
    );
    
    $timestamp = strtotime($tanggal);
    $hari = date('d', $timestamp);
    $bulan_nama = $bulan[date('n', $timestamp)];
    $tahun = date('Y', $timestamp);
    
    return "$hari $bulan_nama $tahun";
}

// Fungsi untuk sanitasi output HTML
function sanitizeOutput($string) {
    return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
}

// Fungsi untuk debug (hanya untuk development)
function debugLog($message, $data = null) {
    if (defined('DEBUG') && DEBUG === true) {
        error_log("DEBUG: $message" . ($data ? " - Data: " . print_r($data, true) : ""));
    }
}
?>