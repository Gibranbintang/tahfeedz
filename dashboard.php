<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Update last login
global $koneksi;
$update_login = "UPDATE users SET last_login = NOW(), login_count = login_count + 1 WHERE id = $user_id";
mysqli_query($koneksi, $update_login);

// Ambil data user dari database untuk memastikan data terbaru
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

// Ambil statistik hafalan dari database
$stats_query = "SELECT 
    COUNT(*) as total_hafalan,
    COUNT(CASE WHEN jenis = 'Hafalan Baru' THEN 1 END) as hafalan_baru,
    COUNT(CASE WHEN jenis = 'Murojaah' THEN 1 END) as murojaah,
    COUNT(CASE WHEN MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN 1 END) as hafalan_bulan_ini,
    COUNT(CASE WHEN tanggal >= CURDATE() - INTERVAL 7 DAY THEN 1 END) as hafalan_7_hari,
    COALESCE(SUM(poin), 0) as total_poin
    FROM hafalan WHERE user_id = $user_id";
$stats_result = mysqli_query($koneksi, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);

// Hitung current streak
$streak_query = "CALL CalculateUserStreak($user_id)";
$streak_result = mysqli_query($koneksi, $streak_query);
$streak_data = mysqli_fetch_assoc($streak_result);
$current_streak = $streak_data['current_streak'] ?? 0;

// Ambil hafalan terbaru (5 terakhir)
$recent_hafalan_query = "SELECT h.*, u.nama as user_nama 
                        FROM hafalan h 
                        LEFT JOIN users u ON h.user_id = u.id 
                        WHERE h.user_id = $user_id 
                        ORDER BY h.created_at DESC 
                        LIMIT 5";
$recent_hafalan_result = mysqli_query($koneksi, $recent_hafalan_query);
$recent_hafalan = [];
while ($row = mysqli_fetch_assoc($recent_hafalan_result)) {
    $recent_hafalan[] = $row;
}

// Ambil reminder yang akan datang
$upcoming_reminders_query = "SELECT * FROM reminders 
                            WHERE user_id = $user_id 
                            AND is_active = 1 
                            AND reminder_datetime > NOW() 
                            ORDER BY reminder_datetime ASC 
                            LIMIT 3";
$upcoming_reminders_result = mysqli_query($koneksi, $upcoming_reminders_query);
$upcoming_reminders = [];
while ($row = mysqli_fetch_assoc($upcoming_reminders_result)) {
    $upcoming_reminders[] = $row;
}
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
            <a href="rekaphafalan.php"><i class="fas fa-chart-line"></i> Rekap Hafalan</a>
          </li>
          <li>
            <a href="lihatrekapan.php"><i class="fas fa-eye"></i> Lihat Rekapan</a>
          </li>
          <li>
            <a href="profile.php"><i class="fas fa-user"></i> Profil</a>
          </li>
          <li>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
          </li>
          <li>
            <a href="pengingat.php"><i class="fas fa-bell"></i> Pengingat</a>
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
          <h3><?php echo $stats['total_hafalan']; ?></h3>
          <p>Total Hafalan</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-calendar-day"></i>
          <h3><?php echo $stats['hafalan_bulan_ini']; ?></h3>
          <p>Hafalan Bulan Ini</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-fire"></i>
          <h3><?php echo $current_streak; ?></h3>
          <p>Streak Hari</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-star"></i>
          <h3><?php echo $stats['total_poin']; ?></h3>
          <p>Total Poin</p>
        </div>
      </section>

      <!-- Quick Actions -->
      <section class="quick-actions-section">
        <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
        <div class="cards-grid">
          <a href="rekaphafalan.php" class="card quick-action" onclick="trackClick('rekap_hafalan')">
            <h3><i class="fas fa-plus-circle"></i>Tambah Hafalan</h3>
            <p>Input hafalan baru atau murojaah yang sudah diselesaikan hari ini</p>
          </a>
          <a href="pengingat.php" class="card quick-action" onclick="trackClick('pengingat')">
            <h3><i class="fas fa-bell"></i>Atur Pengingat</h3>
            <p>Buat pengingat untuk jadwal hafalan, murojaah, atau kegiatan lainnya</p>
          </a>
          <a href="lihatrekapan.php" class="card quick-action" onclick="trackClick('lihat_rekap')">
            <h3><i class="fas fa-chart-bar"></i>Lihat Statistik</h3>
            <p>Analisa progress dan perkembangan hafalan Anda dengan detail</p>
          </a>
        </div>
      </section>

      <!-- Upcoming Reminders -->
      <?php if (!empty($upcoming_reminders)): ?>
      <section class="upcoming-reminders">
        <h2><i class="fas fa-clock"></i> Pengingat Mendatang</h2>
        <div class="reminders-list">
          <?php foreach ($upcoming_reminders as $reminder): ?>
            <div class="reminder-item">
              <div class="reminder-icon">
                <i class="fas <?php 
                  echo $reminder['reminder_type'] === 'hafalan' ? 'fa-book-open' : 
                      ($reminder['reminder_type'] === 'murojaah' ? 'fa-repeat' : 
                      ($reminder['reminder_type'] === 'tasmi' ? 'fa-microphone' : 'fa-bell'));
                ?>"></i>
              </div>
              <div class="reminder-content">
                <h4><?php echo htmlspecialchars($reminder['title']); ?></h4>
                <p class="reminder-time">
                  <i class="fas fa-calendar"></i>
                  <?php echo date('d F Y, H:i', strtotime($reminder['reminder_datetime'])); ?>
                </p>
              </div>
              <div class="reminder-actions">
                <button onclick="snoozeReminder(<?php echo $reminder['id']; ?>)" class="btn-snooze" title="Tunda">
                  <i class="fas fa-clock"></i>
                </button>
              </div>
            </div>
          <?php endforeach; ?>
        </div>
      </section>
      <?php endif; ?>

      <!-- Learning Resources -->
      <section class="learning-resources">
        <h2><i class="fas fa-graduation-cap"></i> Sumber Belajar</h2>
        <div class="cards-grid">
          <a href="tajwid.html" class="card resource" onclick="trackClick('tajwid')">
            <h3><i class="fas fa-graduation-cap"></i>Hukum-Hukum Tajwid</h3>
            <p>Pelajari kaidah-kaidah tajwid untuk membaca Al-Quran dengan benar dan indah</p>
          </a>
          <a href="adab.html" class="card resource" onclick="trackClick('adab')">
            <h3><i class="fas fa-heart"></i>Adab Membaca Al-Quran</h3>
            <p>Memahami etika dan tata cara yang baik dalam membaca kitab suci Al-Quran</p>
          </a>
          <a href="#" class="card resource" onclick="trackClick('tips')">
            <h3><i class="fas fa-lightbulb"></i>Tips & Trik Menghafal</h3>
            <p>Kumpulan tips dan metode efektif untuk mempercepat dan memperkuat hafalan</p>
          </a>
        </div>
      </section>

      <!-- Rekap Hafalan Terbaru -->
      <section class="rekap-box">
        <h2><i class="fas fa-clipboard-list"></i>Hafalan Terbaru</h2>
        <div id="rekapContainer">
          <?php if (empty($recent_hafalan)): ?>
            <div class="empty-state">
              <i class="fas fa-book-open"></i>
              <p>Belum ada data hafalan yang tersimpan.</p>
              <p>Mulai hafalan pertama Anda hari ini!</p>
              <a href="rekaphafalan.php" class="btn-start-hafalan">
                <i class="fas fa-plus"></i> Mulai Hafalan
              </a>
            </div>
          <?php else: ?>
            <?php foreach ($recent_hafalan as $item): ?>
              <div class="rekap-item">
                <strong><?php echo htmlspecialchars($item['nama_santri']); ?></strong>
                <div class="meta">
                  <span><i class="fas fa-tag"></i><?php echo htmlspecialchars($item['jenis']); ?></span>
                  <span><i class="fas fa-book"></i><?php echo htmlspecialchars($item['surat']); ?></span>
                  <span><i class="fas fa-list-ol"></i>Ayat: <?php echo htmlspecialchars($item['ayat']); ?></span>
                  <span><i class="fas fa-calendar"></i><?php echo date('d F Y', strtotime($item['tanggal'])); ?></span>
                  <span><i class="fas fa-star"></i><?php echo $item['poin']; ?> poin</span>
                </div>
              </div>
            <?php endforeach; ?>
            
            <div class="view-all-link">
              <a href="lihatrekapan.php">
                <i class="fas fa-arrow-right"></i> Lihat Semua Hafalan
              </a>
            </div>
          <?php endif; ?>
        </div>
      </section>

      <!-- Progress Section -->
      <section class="progress-section">
        <h2><i class="fas fa-chart-line"></i> Progress Mingguan</h2>
        <div class="progress-cards">
          <div class="progress-card">
            <div class="progress-header">
              <h3>Hafalan Baru</h3>
              <span class="progress-count"><?php echo $stats['hafalan_baru']; ?></span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: <?php echo min(($stats['hafalan_baru'] / 10) * 100, 100); ?>%"></div>
            </div>
            <p>Target: 10 hafalan baru</p>
          </div>
          
          <div class="progress-card">
            <div class="progress-header">
              <h3>Murojaah</h3>
              <span class="progress-count"><?php echo $stats['murojaah']; ?></span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: <?php echo min(($stats['murojaah'] / 20) * 100, 100); ?>%"></div>
            </div>
            <p>Target: 20 murojaah</p>
          </div>
          
          <div class="progress-card">
            <div class="progress-header">
              <h3>Aktivitas 7 Hari</h3>
              <span class="progress-count"><?php echo $stats['hafalan_7_hari']; ?></span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width: <?php echo min(($stats['hafalan_7_hari'] / 7) * 100, 100); ?>%"></div>
            </div>
            <p>Target: Aktif 7 hari</p>
          </div>
        </div>
      </section>
    </main>
  </div>

  <style>
    /* Additional styles for new sections */
    .quick-actions-section {
      margin-bottom: 3rem;
    }

    .quick-actions-section h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .quick-actions-section h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .quick-action {
      border-left: 4px solid #4facfe;
    }

    .resource {
      border-left: 4px solid #48bb78;
    }

    .upcoming-reminders {
      margin-bottom: 3rem;
    }

    .upcoming-reminders h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .upcoming-reminders h2 i {
      margin-right: 1rem;
      color: #ed8936;
    }

    .reminders-list {
      display: grid;
      gap: 1rem;
    }

    .reminder-item {
      background: white;
      padding: 1rem;
      border-radius: 12px;
      border: 1px solid #e2e8f0;
      display: flex;
      align-items: center;
      gap: 1rem;
      transition: all 0.3s ease;
    }

    .reminder-item:hover {
      border-color: #4facfe;
      transform: translateX(5px);
    }

    .reminder-icon {
      width: 40px;
      height: 40px;
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      border-radius: 50%;
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
    }

    .reminder-content {
      flex: 1;
    }

    .reminder-content h4 {
      margin: 0 0 0.25rem 0;
      color: #2d3748;
    }

    .reminder-time {
      margin: 0;
      color: #718096;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .btn-snooze {
      background: #f7fafc;
      border: 1px solid #e2e8f0;
      color: #4a5568;
      width: 32px;
      height: 32px;
      border-radius: 6px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .btn-snooze:hover {
      background: #edf2f7;
      border-color: #cbd5e0;
    }

    .learning-resources {
      margin-bottom: 3rem;
    }

    .learning-resources h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .learning-resources h2 i {
      margin-right: 1rem;
      color: #48bb78;
    }

    .btn-start-hafalan {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      text-decoration: none;
      padding: 1rem 2rem;
      border-radius: 12px;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      font-weight: 600;
      margin-top: 1rem;
      transition: all 0.3s ease;
    }

    .btn-start-hafalan:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
    }

    .view-all-link {
      text-align: center;
      margin-top: 1rem;
      padding-top: 1rem;
      border-top: 1px solid #e2e8f0;
    }

    .view-all-link a {
      color: #4facfe;
      text-decoration: none;
      font-weight: 600;
      display: inline-flex;
      align-items: center;
      gap: 0.5rem;
      transition: all 0.3s ease;
    }

    .view-all-link a:hover {
      color: #00f2fe;
      gap: 1rem;
    }

    .progress-section {
      margin-bottom: 2rem;
    }

    .progress-section h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .progress-section h2 i {
      margin-right: 1rem;
      color: #9f7aea;
    }

    .progress-cards {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
      gap: 1rem;
    }

    .progress-card {
      background: white;
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .progress-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .progress-header h3 {
      color: #2d3748;
      margin: 0;
      font-size: 1rem;
    }

    .progress-count {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      padding: 0.25rem 0.75rem;
      border-radius: 12px;
      font-weight: 600;
      font-size: 0.9rem;
    }

    .progress-bar {
      width: 100%;
      height: 8px;
      background: #e2e8f0;
      border-radius: 4px;
      overflow: hidden;
      margin-bottom: 0.5rem;
    }

    .progress-fill {
      height: 100%;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
      border-radius: 4px;
      transition: width 0.8s ease;
    }

    .progress-card p {
      color: #718096;
      font-size: 0.9rem;
      margin: 0;
    }

    @media (max-width: 768px) {
      .reminder-item {
        flex-direction: column;
        align-items: stretch;
        text-align: center;
      }

      .progress-cards {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <script>
    // Enhanced dashboard functionality
    document.addEventListener("DOMContentLoaded", () => {
      initializeDashboard();
      startReminderCheck();
      animateProgressBars();
    });

    function initializeDashboard() {
      // Animate stats cards
      const statCards = document.querySelectorAll('.stat-card');
      statCards.forEach((card, index) => {
        card.style.animationDelay = `${index * 0.1}s`;
        card.style.animation = 'fadeInUp 0.6s ease forwards';
      });

      // Check for browser notifications permission
      if ('Notification' in window && Notification.permission === 'default') {
        setTimeout(() => {
          if (confirm('Ingin mengaktifkan notifikasi untuk pengingat hafalan?')) {
            Notification.requestPermission();
          }
        }, 3000);
      }
    }

    function startReminderCheck() {
      // Check for due reminders every minute
      setInterval(checkDueReminders, 60000);
      
      // Check immediately
      setTimeout(checkDueReminders, 5000);
    }

    function checkDueReminders() {
      // In a real implementation, this would make an AJAX call to check for due reminders
      // For now, we'll simulate it with JavaScript date checking
      const now = new Date();
      
      // Example: Check if it's 7 AM (default reminder time)
      if (now.getHours() === 7 && now.getMinutes() === 0) {
        showReminderNotification('Waktunya Murojaah Pagi!', 'Jangan lupa untuk murojaah hafalan Anda.');
      }
    }

    function showReminderNotification(title, body) {
      if ('Notification' in window && Notification.permission === 'granted') {
        const notification = new Notification(title, {
          body: body,
          icon: '/favicon.ico',
          badge: '/favicon.ico',
          tag: 'tahfeedz-reminder',
          requireInteraction: true
        });

        notification.onclick = function() {
          window.focus();
          notification.close();
        };

        // Auto close after 10 seconds
        setTimeout(() => {
          notification.close();
        }, 10000);
      }
    }

    function animateProgressBars() {
      const progressBars = document.querySelectorAll('.progress-fill');
      progressBars.forEach((bar, index) => {
        const width = bar.style.width;
        bar.style.width = '0%';
        
        setTimeout(() => {
          bar.style.width = width;
        }, index * 200 + 500);
      });
    }

    function trackClick(action) {
      // Analytics tracking
      console.log(`User clicked: ${action}`);
      
      // In a real implementation, you would send this to your analytics service
      // Example: gtag('event', 'click', { 'event_category': 'dashboard', 'event_label': action });
    }

    function snoozeReminder(reminderId) {
      if (confirm('Tunda pengingat ini selama 30 menit?')) {
        // In a real implementation, this would make an AJAX call to update the reminder
        fetch('api/snooze_reminder.php', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json',
          },
          body: JSON.stringify({
            reminder_id: reminderId,
            snooze_minutes: 30
          })
        })
        .then(response => response.json())
        .then(data => {
          if (data.success) {
            alert('Pengingat berhasil ditunda 30 menit.');
            location.reload();
          } else {
            alert('Gagal menunda pengingat.');
          }
        })
        .catch(error => {
          console.error('Error:', error);
          alert('Terjadi kesalahan saat menunda pengingat.');
        });
      }
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

    // Auto-refresh stats every 5 minutes
    setInterval(() => {
      location.reload();
    }, 300000);

    // Add CSS animations
    const style = document.createElement('style');
    style.textContent = `
      @keyframes fadeInUp {
        from {
          opacity: 0;
          transform: translateY(30px);
        }
        to {
          opacity: 1;
          transform: translateY(0);
        }
      }

      @keyframes pulse {
        0%, 100% {
          transform: scale(1);
        }
        50% {
          transform: scale(1.05);
        }
      }

      .stat-card:hover {
        animation: pulse 0.6s ease;
      }

      .card:hover {
        animation: pulse 0.6s ease;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>