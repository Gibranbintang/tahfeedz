<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Ambil data user dari database untuk memastikan data terbaru
global $koneksi;
$user_query = "SELECT * FROM users WHERE id = " . intval($user_id);
$user_result = mysqli_query($koneksi, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

if (!$user_data) {
    // Jika user tidak ditemukan di database, logout dan redirect
    logout();
    header("Location: login.php");
    exit;
}

// Update session dengan data terbaru
$_SESSION['nama'] = $user_data['nama'];
$nama = $user_data['nama'] ?: 'User';
?>

<style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      overflow-x: hidden;
    }

    .container {
      display: flex;
      min-height: 100vh;
    }

    /* Sidebar Styles */
    .sidebar {
      width: 280px;
      background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
      padding: 2rem 0;
      box-shadow: 4px 0 20px rgba(0,0,0,0.1);
      position: relative;
      transition: all 0.3s ease;
    }

    .sidebar::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }

    .logo {
      display: flex;
      align-items: center;
      justify-content: center;
      padding: 0 2rem;
      margin-bottom: 3rem;
      color: white;
    }

    .logo i {
      font-size: 2.5rem;
      margin-right: 1rem;
      background: linear-gradient(45deg, #4facfe, #00f2fe);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .logo span {
      font-size: 1.8rem;
      font-weight: bold;
      letter-spacing: 2px;
    }

    .sidebar nav ul {
      list-style: none;
    }

    .sidebar nav li {
      margin: 0.5rem 1rem;
      border-radius: 12px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .sidebar nav li::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.1), transparent);
      transition: left 0.5s;
    }

    .sidebar nav li:hover::before {
      left: 100%;
    }

    .sidebar nav li.active {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }

    .sidebar nav li:hover:not(.active) {
      background: rgba(255,255,255,0.1);
      transform: translateX(3px);
    }

    .sidebar nav li a, .sidebar nav li span {
      display: flex;
      align-items: center;
      padding: 1rem 1.5rem;
      color: white;
      text-decoration: none;
      font-weight: 500;
      transition: all 0.3s ease;
    }

    .sidebar nav li i {
      margin-right: 1rem;
      width: 20px;
      text-align: center;
    }

    /* Main Content */
    .main-content {
      flex: 1;
      padding: 2rem;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      margin: 1rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      position: relative;
      overflow: hidden;
    }

    .main-content::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(79, 172, 254, 0.1) 0%, transparent 70%);
      pointer-events: none;
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }