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
  <link rel="stylesheet" href="css/dashboard-style.css" />
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
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
          </li>

        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="header">
        <h1>Selamat Datang, <?php echo htmlspecialchars($nama); ?>!</h1>
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