<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Proses simpan pengaturan
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'save_settings') {
    global $koneksi;
    
    $notifications = isset($_POST['notifications']) ? 1 : 0;
    $email_reminders = isset($_POST['email_reminders']) ? 1 : 0;
    $theme = mysqli_real_escape_string($koneksi, $_POST['theme']);
    $language = mysqli_real_escape_string($koneksi, $_POST['language']);
    $timezone = mysqli_real_escape_string($koneksi, $_POST['timezone']);
    $reminder_time = mysqli_real_escape_string($koneksi, $_POST['reminder_time']);
    
    // Cek apakah ada record pengaturan untuk user ini
    $check_settings = "SELECT id FROM user_settings WHERE user_id = $user_id";
    $check_result = mysqli_query($koneksi, $check_settings);
    
    if (mysqli_num_rows($check_result) > 0) {
        // Update existing settings
        $update_query = "UPDATE user_settings SET
                        notifications = $notifications,
                        email_reminders = $email_reminders,
                        theme = '$theme',
                        language = '$language',
                        timezone = '$timezone',
                        reminder_time = '$reminder_time',
                        updated_at = NOW()
                        WHERE user_id = $user_id";
        $result = mysqli_query($koneksi, $update_query);
    } else {
        // Insert new settings
        $insert_query = "INSERT INTO user_settings 
                        (user_id, notifications, email_reminders, theme, language, timezone, reminder_time, created_at)
                        VALUES ($user_id, $notifications, $email_reminders, '$theme', '$language', '$timezone', '$reminder_time', NOW())";
        $result = mysqli_query($koneksi, $insert_query);
    }
    
    if ($result) {
        $success_message = "Pengaturan berhasil disimpan!";
    } else {
        $error_message = "Gagal menyimpan pengaturan: " . mysqli_error($koneksi);
    }
}

// Ambil pengaturan user
global $koneksi;
$settings_query = "SELECT * FROM user_settings WHERE user_id = $user_id";
$settings_result = mysqli_query($koneksi, $settings_query);
$settings = mysqli_fetch_assoc($settings_result);

// Set default values jika belum ada pengaturan
if (!$settings) {
    $settings = [
        'notifications' => 1,
        'email_reminders' => 1,
        'theme' => 'light',
        'language' => 'id',
        'timezone' => 'Asia/Jakarta',
        'reminder_time' => '07:00'
    ];
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengaturan - Tahfeedz</title>
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
          <li>
            <a href="dashboard.php"><i class="fas fa-home"></i> Dashboard</a>
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
          <li class="active">
            <span><i class="fas fa-cog"></i> Pengaturan</span>
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
        <h1><i class="fas fa-cog"></i> Pengaturan Dashboard</h1>
      </header>

      <!-- Alerts -->
      <?php if (isset($success_message)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?php echo $success_message; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($error_message)): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo $error_message; ?>
        </div>
      <?php endif; ?>

      <!-- Settings Form -->
      <section class="settings-container">
        <form method="POST" class="settings-form">
          <input type="hidden" name="action" value="save_settings">

          <!-- Notifikasi Settings -->
          <div class="settings-card">
            <h2><i class="fas fa-bell"></i> Notifikasi</h2>
            <div class="settings-group">
              <div class="setting-item">
                <div class="setting-info">
                  <h3>Notifikasi Push</h3>
                  <p>Terima notifikasi langsung di browser</p>
                </div>
                <div class="setting-toggle">
                  <label class="switch">
                    <input type="checkbox" name="notifications" <?php echo $settings['notifications'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                  </label>
                </div>
              </div>

              <div class="setting-item">
                <div class="setting-info">
                  <h3>Email Reminder</h3>
                  <p>Kirim pengingat melalui email</p>
                </div>
                <div class="setting-toggle">
                  <label class="switch">
                    <input type="checkbox" name="email_reminders" <?php echo $settings['email_reminders'] ? 'checked' : ''; ?>>
                    <span class="slider"></span>
                  </label>
                </div>
              </div>

              <div class="setting-item">
                <div class="setting-info">
                  <h3>Waktu Pengingat</h3>
                  <p>Atur waktu pengingat harian</p>
                </div>
                <div class="setting-input">
                  <input type="time" name="reminder_time" value="<?php echo htmlspecialchars($settings['reminder_time']); ?>">
                </div>
              </div>
            </div>
          </div>

          <!-- Appearance Settings -->
          <div class="settings-card">
            <h2><i class="fas fa-palette"></i> Tampilan</h2>
            <div class="settings-group">
              <div class="setting-item">
                <div class="setting-info">
                  <h3>Theme</h3>
                  <p>Pilih tema yang Anda sukai</p>
                </div>
                <div class="setting-select">
                  <select name="theme">
                    <option value="light" <?php echo $settings['theme'] === 'light' ? 'selected' : ''; ?>>Light</option>
                    <option value="dark" <?php echo $settings['theme'] === 'dark' ? 'selected' : ''; ?>>Dark</option>
                    <option value="auto" <?php echo $settings['theme'] === 'auto' ? 'selected' : ''; ?>>Auto</option>
                  </select>
                </div>
              </div>

              <div class="setting-item">
                <div class="setting-info">
                  <h3>Bahasa</h3>
                  <p>Pilih bahasa interface</p>
                </div>
                <div class="setting-select">
                  <select name="language">
                    <option value="id" <?php echo $settings['language'] === 'id' ? 'selected' : ''; ?>>Indonesia</option>
                    <option value="en" <?php echo $settings['language'] === 'en' ? 'selected' : ''; ?>>English</option>
                    <option value="ar" <?php echo $settings['language'] === 'ar' ? 'selected' : ''; ?>>العربية</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- General Settings -->
          <div class="settings-card">
            <h2><i class="fas fa-globe"></i> Umum</h2>
            <div class="settings-group">
              <div class="setting-item">
                <div class="setting-info">
                  <h3>Zona Waktu</h3>
                  <p>Atur zona waktu Anda</p>
                </div>
                <div class="setting-select">
                  <select name="timezone">
                    <option value="Asia/Jakarta" <?php echo $settings['timezone'] === 'Asia/Jakarta' ? 'selected' : ''; ?>>WIB (Jakarta)</option>
                    <option value="Asia/Makassar" <?php echo $settings['timezone'] === 'Asia/Makassar' ? 'selected' : ''; ?>>WITA (Makassar)</option>
                    <option value="Asia/Jayapura" <?php echo $settings['timezone'] === 'Asia/Jayapura' ? 'selected' : ''; ?>>WIT (Jayapura)</option>
                  </select>
                </div>
              </div>
            </div>
          </div>

          <!-- Data Management -->
          <div class="settings-card">
            <h2><i class="fas fa-database"></i> Manajemen Data</h2>
            <div class="settings-group">
              <div class="setting-item full-width">
                <div class="danger-zone">
                  <h3><i class="fas fa-exclamation-triangle"></i> Zona Bahaya</h3>
                  <p>Tindakan berikut tidak dapat dibatalkan. Pastikan Anda sudah membackup data penting.</p>
                  
                  <div class="danger-actions">
                    <button type="button" onclick="exportData()" class="btn-export-data">
                      <i class="fas fa-download"></i> Export Data
                    </button>
                    <button type="button" onclick="confirmDeleteAllData()" class="btn-danger">
                      <i class="fas fa-trash"></i> Hapus Semua Data Hafalan
                    </button>
                    <button type="button" onclick="confirmDeleteAccount()" class="btn-danger">
                      <i class="fas fa-user-times"></i> Hapus Akun
                    </button>
                  </div>
                </div>
              </div>
            </div>
          </div>

          <!-- Save Button -->
          <div class="settings-actions">
            <button type="submit" class="btn-save">
              <i class="fas fa-save"></i> Simpan Pengaturan
            </button>
            <button type="button" onclick="resetToDefault()" class="btn-reset">
              <i class="fas fa-undo"></i> Reset ke Default
            </button>
          </div>
        </form>
      </section>

      <!-- Quick Actions -->
      <section class="quick-actions">
        <h2><i class="fas fa-bolt"></i> Aksi Cepat</h2>
        <div class="quick-actions-grid">
          <button onclick="testNotification()" class="quick-action-btn">
            <i class="fas fa-bell"></i>
            <span>Test Notifikasi</span>
          </button>
          <button onclick="clearCache()" class="quick-action-btn">
            <i class="fas fa-broom"></i>
            <span>Bersihkan Cache</span>
          </button>
          <a href="pengingat.php" class="quick-action-btn">
            <i class="fas fa-clock"></i>
            <span>Atur Pengingat</span>
          </a>
          <button onclick="downloadBackup()" class="quick-action-btn">
            <i class="fas fa-cloud-download-alt"></i>
            <span>Download Backup</span>
          </button>
        </div>
      </section>
    </main>
  </div>

  <style>
    .alert {
      padding: 1rem 1.5rem;
      border-radius: 12px;
      margin-bottom: 2rem;
      display: flex;
      align-items: center;
      font-weight: 500;
    }

    .alert i {
      margin-right: 0.5rem;
      font-size: 1.2rem;
    }

    .alert-success {
      background: linear-gradient(135deg, #d4edda 0%, #c3e6cb 100%);
      color: #155724;
      border: 1px solid #c3e6cb;
    }

    .alert-error {
      background: linear-gradient(135deg, #f8d7da 0%, #f5c6cb 100%);
      color: #721c24;
      border: 1px solid #f5c6cb;
    }

    .settings-container {
      display: grid;
      gap: 2rem;
    }

    .settings-card {
      background: white;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .settings-card h2 {
      color: #2d3748;
      margin-bottom: 2rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .settings-card h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .settings-group {
      display: grid;
      gap: 1.5rem;
    }

    .setting-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
      transition: all 0.3s ease;
    }

    .setting-item:hover {
      border-color: #4facfe;
      background: #f7fafc;
    }

    .setting-item.full-width {
      flex-direction: column;
      align-items: stretch;
    }

    .setting-info h3 {
      color: #2d3748;
      margin-bottom: 0.25rem;
      font-size: 1.1rem;
    }

    .setting-info p {
      color: #718096;
      font-size: 0.9rem;
      margin: 0;
    }

    /* Toggle Switch */
    .switch {
      position: relative;
      display: inline-block;
      width: 60px;
      height: 34px;
    }

    .switch input {
      opacity: 0;
      width: 0;
      height: 0;
    }

    .slider {
      position: absolute;
      cursor: pointer;
      top: 0;
      left: 0;
      right: 0;
      bottom: 0;
      background-color: #ccc;
      transition: 0.4s;
      border-radius: 34px;
    }

    .slider:before {
      position: absolute;
      content: "";
      height: 26px;
      width: 26px;
      left: 4px;
      bottom: 4px;
      background-color: white;
      transition: 0.4s;
      border-radius: 50%;
    }

    input:checked + .slider {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
    }

    input:checked + .slider:before {
      transform: translateX(26px);
    }

    /* Select styling */
    .setting-select select,
    .setting-input input {
      padding: 0.8rem;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 1rem;
      min-width: 150px;
      transition: all 0.3s ease;
    }

    .setting-select select:focus,
    .setting-input input:focus {
      outline: none;
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }

    /* Danger Zone */
    .danger-zone {
      background: linear-gradient(135deg, #fed7d7 0%, #feb2b2 100%);
      padding: 1.5rem;
      border-radius: 12px;
      border: 1px solid #fc8181;
    }

    .danger-zone h3 {
      color: #742a2a;
      margin-bottom: 0.5rem;
      display: flex;
      align-items: center;
    }

    .danger-zone h3 i {
      margin-right: 0.5rem;
      color: #e53e3e;
    }

    .danger-zone p {
      color: #742a2a;
      margin-bottom: 1rem;
    }

    .danger-actions {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn-export-data {
      background: #48bb78;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-export-data:hover {
      background: #38a169;
    }

    .btn-danger {
      background: #e53e3e;
      color: white;
      border: none;
      padding: 0.8rem 1.5rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-danger:hover {
      background: #c53030;
    }

    /* Action Buttons */
    .settings-actions {
      display: flex;
      gap: 1rem;
      justify-content: center;
      margin-top: 2rem;
    }

    .btn-save, .btn-reset {
      padding: 1rem 2rem;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
      border: none;
    }

    .btn-save {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    .btn-save:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
    }

    .btn-reset {
      background: #f7fafc;
      color: #2d3748;
      border: 2px solid #e2e8f0;
    }

    .btn-reset:hover {
      background: #edf2f7;
      border-color: #cbd5e0;
    }

    /* Quick Actions */
    .quick-actions {
      margin-top: 2rem;
    }

    .quick-actions h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .quick-actions h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .quick-action-btn {
      background: white;
      border: 2px solid #e2e8f0;
      padding: 1.5rem;
      border-radius: 15px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      flex-direction: column;
      align-items: center;
      gap: 0.5rem;
      text-decoration: none;
      color: #2d3748;
    }

    .quick-action-btn:hover {
      border-color: #4facfe;
      color: #4facfe;
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .quick-action-btn i {
      font-size: 2rem;
    }

    .quick-action-btn span {
      font-weight: 600;
      text-align: center;
    }

    @media (max-width: 768px) {
      .setting-item {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }

      .settings-actions {
        flex-direction: column;
      }

      .danger-actions {
        flex-direction: column;
      }

      .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
      }
    }
  </style>

  <script>
    // Test notification
    function testNotification() {
      if ('Notification' in window) {
        Notification.requestPermission().then(function(permission) {
          if (permission === 'granted') {
            new Notification('Test Notifikasi TAHFEEDZ', {
              body: 'Notifikasi berhasil! Pengaturan berfungsi dengan baik.',
              icon: 'https://cdn-icons-png.flaticon.com/512/1827/1827392.png'
            });
          } else {
            alert('Izin notifikasi ditolak. Aktifkan notifikasi di pengaturan browser.');
          }
        });
      } else {
        alert('Browser tidak mendukung notifikasi.');
      }
    }

    // Clear cache
    function clearCache() {
      if (confirm('Hapus cache aplikasi? Ini akan me-refresh halaman.')) {
        if ('caches' in window) {
          caches.keys().then(function(names) {
            names.forEach(function(name) {
              caches.delete(name);
            });
          });
        }
        localStorage.clear();
        sessionStorage.clear();
        alert('Cache berhasil dibersihkan!');
        location.reload();
      }
    }

    // Export data
    function exportData() {
      alert('Fitur export data akan segera tersedia!');
      // Implementasi export ke JSON/CSV
    }

    // Download backup
    function downloadBackup() {
      alert('Fitur download backup akan segera tersedia!');
      // Implementasi backup otomatis
    }

    // Confirm delete all data
    function confirmDeleteAllData() {
      if (confirm('PERINGATAN: Ini akan menghapus SEMUA data hafalan Anda secara permanen. Tindakan ini tidak dapat dibatalkan!\n\nKetik "HAPUS" untuk konfirmasi:')) {
        const confirmation = prompt('Ketik "HAPUS" untuk mengkonfirmasi:');
        if (confirmation === 'HAPUS') {
          // Implement delete all data
          alert('Fitur hapus data akan diimplementasikan dengan keamanan tambahan.');
        }
      }
    }

    // Confirm delete account
    function confirmDeleteAccount() {
      if (confirm('PERINGATAN: Ini akan menghapus akun Anda beserta semua data secara permanen!\n\nTindakan ini tidak dapat dibatalkan!')) {
        const confirmation = prompt('Ketik email Anda untuk konfirmasi:');
        if (confirmation) {
          // Implement account deletion
          alert('Fitur hapus akun akan diimplementasikan dengan verifikasi email.');
        }
      }
    }

    // Reset to default
    function resetToDefault() {
      if (confirm('Reset semua pengaturan ke nilai default?')) {
        // Reset form values
        document.querySelector('input[name="notifications"]').checked = true;
        document.querySelector('input[name="email_reminders"]').checked = true;
        document.querySelector('select[name="theme"]').value = 'light';
        document.querySelector('select[name="language"]').value = 'id';
        document.querySelector('select[name="timezone"]').value = 'Asia/Jakarta';
        document.querySelector('input[name="reminder_time"]').value = '07:00';
        
        alert('Pengaturan direset ke default. Jangan lupa klik Simpan!');
      }
    }

    // Auto-save on change (optional)
    document.querySelectorAll('input, select').forEach(element => {
      element.addEventListener('change', function() {
        // Show unsaved changes indicator
        const saveBtn = document.querySelector('.btn-save');
        if (!saveBtn.classList.contains('unsaved')) {
          saveBtn.classList.add('unsaved');
          saveBtn.innerHTML = '<i class="fas fa-exclamation-circle"></i> Simpan Perubahan';
          saveBtn.style.animation = 'pulse 2s infinite';
        }
      });
    });

    // Form submission
    document.querySelector('.settings-form').addEventListener('submit', function(e) {
      const saveBtn = document.querySelector('.btn-save');
      saveBtn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menyimpan...';
      saveBtn.disabled = true;
    });
  </script>
</body>
</html>