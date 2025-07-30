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

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Dashboard - Tahfeedz</title>
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
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

    .header {
      display: flex;
      justify-content: flex-start;
      align-items: center;
      margin-bottom: 3rem;
      position: relative;
      z-index: 2;
    }

    .header h1 {
      font-size: 2.5rem;
      background: linear-gradient(135deg, #2d3748 0%, #4a5568 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      font-weight: 700;
      text-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    .btn-create {
      background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 50px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      box-shadow: 0 4px 15px rgba(72, 187, 120, 0.3);
      position: relative;
      overflow: hidden;
      display: none;
    }

    .btn-create::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255,255,255,0.2);
      transition: all 0.3s ease;
      border-radius: 50%;
      transform: translate(-50%, -50%);
    }

    .btn-create:hover::before {
      width: 300px;
      height: 300px;
    }

    .btn-create:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(72, 187, 120, 0.4);
    }

    /* Cards Grid */
    .cards-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
      gap: 2rem;
      margin-bottom: 3rem;
      position: relative;
      z-index: 2;
    }

    .card {
      background: white;
      padding: 2rem;
      border-radius: 20px;
      text-decoration: none;
      color: #2d3748;
      transition: all 0.3s ease;
      box-shadow: 0 4px 20px rgba(0,0,0,0.08);
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
      transform: scaleX(0);
      transition: transform 0.3s ease;
    }

    .card:hover::before {
      transform: scaleX(1);
    }

    .card:hover {
      transform: translateY(-10px);
      box-shadow: 0 12px 40px rgba(0,0,0,0.15);
    }

    .card h3 {
      font-size: 1.3rem;
      font-weight: 600;
      margin-bottom: 1rem;
      display: flex;
      align-items: center;
    }

    .card h3 i {
      margin-right: 1rem;
      font-size: 1.5rem;
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
    }

    .card p {
      color: #718096;
      line-height: 1.6;
    }

    /* Rekap Section */
    .rekap-box {
      background: white;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      position: relative;
      z-index: 2;
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .rekap-box h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .rekap-box h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .rekap-item {
      background: linear-gradient(135deg, #f7fafc 0%, #edf2f7 100%);
      padding: 1.5rem;
      margin-bottom: 1rem;
      border-radius: 12px;
      border-left: 4px solid #4facfe;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .rekap-item::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      width: 0;
      height: 100%;
      background: linear-gradient(90deg, rgba(79, 172, 254, 0.1), transparent);
      transition: width 0.3s ease;
    }

    .rekap-item:hover::before {
      width: 100%;
    }

    .rekap-item:hover {
      transform: translateX(5px);
      box-shadow: 0 4px 15px rgba(0,0,0,0.1);
    }

    .rekap-item strong {
      color: #2d3748;
      font-size: 1.1rem;
      display: block;
      margin-bottom: 0.5rem;
    }

    .rekap-item .meta {
      color: #718096;
      font-size: 0.9rem;
      display: flex;
      flex-wrap: wrap;
      gap: 1rem;
    }

    .rekap-item .meta span {
      display: flex;
      align-items: center;
    }

    .rekap-item .meta i {
      margin-right: 0.5rem;
      color: #4facfe;
    }

    /* Loading and Empty States */
    .loading, .empty-state {
      text-align: center;
      padding: 3rem;
      color: #718096;
    }

    .loading i, .empty-state i {
      font-size: 3rem;
      margin-bottom: 1rem;
      color: #4facfe;
      animation: pulse 2s ease-in-out infinite;
    }

    @keyframes pulse {
      0%, 100% { opacity: 1; }
      50% { opacity: 0.5; }
    }

    /* Stats Cards */
    .stats-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      margin-bottom: 2rem;
      position: relative;
      z-index: 2;
    }

    .stat-card {
      background: white;
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      text-align: center;
      position: relative;
      overflow: hidden;
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .stat-card::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 3px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }

    .stat-card i {
      font-size: 2rem;
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      -webkit-background-clip: text;
      -webkit-text-fill-color: transparent;
      background-clip: text;
      margin-bottom: 0.5rem;
    }

    .stat-card h3 {
      font-size: 1.8rem;
      color: #2d3748;
      margin-bottom: 0.3rem;
    }

    .stat-card p {
      color: #718096;
      font-size: 0.9rem;
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .container {
        flex-direction: column;
      }
      
      .sidebar {
        width: 100%;
        order: 2;
      }
      
      .main-content {
        margin: 0;
        border-radius: 0;
        order: 1;
      }
      
      .header {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
        justify-content: center;
      }
      
      .cards-grid {
        grid-template-columns: 1fr;
      }
    }
  </style>
</head>
<body>
  <div class="container">
    <!-- Sidebar -->
    <aside class="sidebar">
      <div class="logo">
        <i class="fas fa-quran"></i>
        <span>TAHFEEDZ</span>
      </div>
      <nav>
        <ul>
          <li class="active">
            <span><i class="fas fa-home"></i> Dashboard</span>
          </li>
          <li>
            <a href="rekaphafalan.html"><i class="fas fa-chart-line"></i> Rekap Hafalan</a>
          </li>
          <li>
            <a href="index.html"><i class="fas fa-eye"></i> Lihat Rekapan</a>
          </li>
          <li>
            <a href="#"><i class="fas fa-users"></i> Pengguna</a>
          </li>
          <li>
            <a href="#"><i class="fas fa-cog"></i> Pengaturan</a>
          </li>
          <li>
            <a href="#"><i class="fas fa-sign-out-alt"></i> Keluar</a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="header">
        <h1>Dashboard</h1>
      </header>
      
      <!-- Stats Grid -->
      <section class="stats-grid" id="statsGrid">
        <div class="stat-card">
          <i class="fas fa-book-open"></i>
          <h3 id="totalHafalan">0</h3>
          <p>Total Hafalan</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-calendar-day"></i>
          <h3 id="hafalanBulanIni">0</h3>
          <p>Hafalan Bulan Ini</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-fire"></i>
          <h3 id="streakHari">0</h3>
          <p>Streak Hari</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-star"></i>
          <h3 id="poinTotal">0</h3>
          <p>Total Poin</p>
        </div>
      </section>

      <!-- Course Cards -->
      <section class="cards-grid">
        <a href="tajwid.html" class="card" onclick="trackClick('tajwid')">
          <h3><i class="fas fa-graduation-cap"></i>Hukum-Hukum Tajwid Al-Quran</h3>
          <p>Pelajari kaidah-kaidah tajwid untuk membaca Al-Quran dengan benar dan indah</p>
        </a>
        <a href="adab.html" class="card" onclick="trackClick('adab')">
          <h3><i class="fas fa-heart"></i>Adab-Adab Membaca Al-Quran</h3>
          <p>Memahami etika dan tata cara yang baik dalam membaca kitab suci Al-Quran</p>
        </a>
        <a href="pengingat.html" class="card" onclick="trackClick('pengingat')">
          <h3><i class="fas fa-bell"></i>Pengingat Hafalan dan Murojaah</h3>
          <p>Sistem pengingat otomatis untuk menjaga hafalan dan rutinitas murojaah harian</p>
        </a>
      </section>

      <!-- Rekap Hafalan -->
      <section class="rekap-box">
        <h2><i class="fas fa-clipboard-list"></i>Rekapan Hafalan Terbaru</h2>
        <div id="rekapContainer">
          <div class="loading">
            <i class="fas fa-spinner fa-spin"></i>
            <p>Memuat data hafalan...</p>
          </div>
        </div>
      </section>
    </main>
  </div>

  <script>
    // Data management using JavaScript variables instead of localStorage
    let hafalanData = [];
    let userStats = {
      totalHafalan: 0,
      hafalanBulanIni: 0,
      streakHari: 0,
      poinTotal: 0
    };

    // Initialize dashboard
    document.addEventListener("DOMContentLoaded", () => {
      loadHafalanData();
      updateStats();
      displayRekapHafalan();
    });

    // Load hafalan data (in a real app, this would come from a database)
    function loadHafalanData() {
      // Sample data for demonstration
      hafalanData = [
        {
          nama: "Ahmad Fauzi",
          jenis: "Hafalan Baru",
          surat: "Al-Fatihah",
          ayat: "1-7",
          tanggal: "2025-07-30",
          poin: 50
        },
        {
          nama: "Siti Aisyah",
          jenis: "Murojaah",
          surat: "Al-Baqarah",
          ayat: "1-5",
          tanggal: "2025-07-29",
          poin: 30
        },
        {
          nama: "Muhammad Ridho",
          jenis: "Hafalan Baru",
          surat: "Al-Ikhlas",
          ayat: "1-4",
          tanggal: "2025-07-28",
          poin: 40
        }
      ];
    }

    // Update statistics
    function updateStats() {
      userStats.totalHafalan = hafalanData.length;
      
      // Calculate hafalan this month
      const currentMonth = new Date().getMonth();
      userStats.hafalanBulanIni = hafalanData.filter(item => {
        const itemDate = new Date(item.tanggal);
        return itemDate.getMonth() === currentMonth;
      }).length;

      // Calculate streak (simplified)
      userStats.streakHari = Math.min(hafalanData.length, 7);
      
      // Calculate total points
      userStats.poinTotal = hafalanData.reduce((total, item) => total + (item.poin || 0), 0);

      // Update DOM
      document.getElementById('totalHafalan').textContent = userStats.totalHafalan;
      document.getElementById('hafalanBulanIni').textContent = userStats.hafalanBulanIni;
      document.getElementById('streakHari').textContent = userStats.streakHari;
      document.getElementById('poinTotal').textContent = userStats.poinTotal;
    }

    // Display rekap hafalan
    function displayRekapHafalan() {
      const container = document.getElementById("rekapContainer");
      
      setTimeout(() => {
        if (hafalanData.length === 0) {
          container.innerHTML = `
            <div class="empty-state">
              <i class="fas fa-book-open"></i>
              <p>Belum ada data hafalan yang tersimpan.</p>
              <p>Mulai hafalan pertama Anda hari ini!</p>
            </div>
          `;
          return;
        }

        container.innerHTML = "";
        
        // Display latest 5 entries
        const recentHafalan = hafalanData.slice().reverse().slice(0, 5);
        
        recentHafalan.forEach((item, index) => {
          const div = document.createElement("div");
          div.className = "rekap-item";
          div.style.animationDelay = `${index * 0.1}s`;
          
          div.innerHTML = `
            <strong>${item.nama}</strong>
            <div class="meta">
              <span><i class="fas fa-tag"></i>${item.jenis}</span>
              <span><i class="fas fa-book"></i>${item.surat}</span>
              <span><i class="fas fa-list-ol"></i>Ayat: ${item.ayat}</span>
              <span><i class="fas fa-calendar"></i>${formatDate(item.tanggal)}</span>
              <span><i class="fas fa-star"></i>${item.poin || 0} poin</span>
            </div>
          `;
          container.appendChild(div);
        });
      }, 1000);
    }

    // Format date to Indonesian format
    function formatDate(dateString) {
      const date = new Date(dateString);
      const options = { 
        weekday: 'long', 
        year: 'numeric', 
        month: 'long', 
        day: 'numeric' 
      };
      return date.toLocaleDateString('id-ID', options);
    }

    // Create new course function
    function createNewCourse() {
      // Function removed - button no longer exists
    }

    // Track click analytics
    function trackClick(courseType) {
      console.log(`User clicked on: ${courseType}`);
      // In a real app, this would send analytics data
    }

    // Add some interactive features
    document.addEventListener('mousemove', (e) => {
      const cursor = document.querySelector('.cursor');
      if (!cursor) {
        const newCursor = document.createElement('div');
        newCursor.className = 'cursor';
        newCursor.style.cssText = `
          position: fixed;
          width: 20px;
          height: 20px;
          background: radial-gradient(circle, rgba(79, 172, 254, 0.3), transparent);
          border-radius: 50%;
          pointer-events: none;
          z-index: 9999;
          transition: transform 0.1s ease;
        `;
        document.body.appendChild(newCursor);
      }
      
      const cursorElement = document.querySelector('.cursor');
      cursorElement.style.left = e.clientX - 10 + 'px';
      cursorElement.style.top = e.clientY - 10 + 'px';
    });
  </script>
</body>
</html>