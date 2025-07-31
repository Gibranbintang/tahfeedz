<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Proses simpan reminder
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'save_reminder') {
    global $koneksi;
    
    $title = mysqli_real_escape_string($koneksi, $_POST['title']);
    $description = mysqli_real_escape_string($koneksi, $_POST['description']);
    $reminder_date = mysqli_real_escape_string($koneksi, $_POST['reminder_date']);
    $reminder_time = mysqli_real_escape_string($koneksi, $_POST['reminder_time']);
    $reminder_type = mysqli_real_escape_string($koneksi, $_POST['reminder_type']);
    $repeat_type = mysqli_real_escape_string($koneksi, $_POST['repeat_type']);
    
    $reminder_datetime = $reminder_date . ' ' . $reminder_time;
    
    $query = "INSERT INTO reminders (user_id, title, description, reminder_datetime, reminder_type, repeat_type, is_active, created_at) 
              VALUES ('$user_id', '$title', '$description', '$reminder_datetime', '$reminder_type', '$repeat_type', 1, NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Pengingat berhasil dibuat!";
    } else {
        $error_message = "Gagal membuat pengingat: " . mysqli_error($koneksi);
    }
}

// Proses hapus reminder
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'delete_reminder') {
    global $koneksi;
    $reminder_id = intval($_POST['reminder_id']);
    
    $delete_query = "DELETE FROM reminders WHERE id = $reminder_id AND user_id = $user_id";
    if (mysqli_query($koneksi, $delete_query)) {
        $success_message = "Pengingat berhasil dihapus!";
    } else {
        $error_message = "Gagal menghapus pengingat: " . mysqli_error($koneksi);
    }
}

// Proses toggle status reminder
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'toggle_reminder') {
    global $koneksi;
    $reminder_id = intval($_POST['reminder_id']);
    $is_active = intval($_POST['is_active']);
    
    $toggle_query = "UPDATE reminders SET is_active = $is_active WHERE id = $reminder_id AND user_id = $user_id";
    if (mysqli_query($koneksi, $toggle_query)) {
        $success_message = "Status pengingat berhasil diubah!";
    } else {
        $error_message = "Gagal mengubah status: " . mysqli_error($koneksi);
    }
}

// Ambil data reminders user
global $koneksi;
$reminders_query = "SELECT * FROM reminders WHERE user_id = $user_id ORDER BY reminder_datetime ASC";
$reminders_result = mysqli_query($koneksi, $reminders_query);
$reminders_data = [];
while ($row = mysqli_fetch_assoc($reminders_result)) {
    $reminders_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Pengingat - Tahfeedz</title>
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
          <li>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
          </li>
          <li class="active">
            <span><i class="fas fa-bell"></i> Pengingat</span>
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
        <h1><i class="fas fa-bell"></i> Sistem Pengingat</h1>
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

      <!-- Quick Actions -->
      <section class="quick-reminder-actions">
        <h2><i class="fas fa-clock"></i> Pengingat Cepat</h2>
        <div class="quick-actions-grid">
          <button onclick="setQuickReminder('hafalan', 60)" class="quick-reminder-btn">
            <i class="fas fa-book-open"></i>
            <span>Hafalan dalam 1 Jam</span>
          </button>
          <button onclick="setQuickReminder('murojaah', 1440)" class="quick-reminder-btn">
            <i class="fas fa-repeat"></i>
            <span>Murojaah Besok</span>
          </button>
          <button onclick="setQuickReminder('tasmi', 30)" class="quick-reminder-btn">
            <i class="fas fa-microphone"></i>
            <span>Tasmi' dalam 30 Menit</span>
          </button>
          <button onclick="setQuickReminder('doa', 10080)" class="quick-reminder-btn">
            <i class="fas fa-hands"></i>
            <span>Doa Mingguan</span>
          </button>
        </div>
      </section>

      <!-- Create Reminder Form -->
      <section class="form-section">
        <div class="card form-card">
          <h2><i class="fas fa-plus-circle"></i> Buat Pengingat Baru</h2>
          <form method="POST" class="reminder-form">
            <input type="hidden" name="action" value="save_reminder">
            
            <div class="form-row">
              <div class="form-group">
                <label for="title"><i class="fas fa-heading"></i> Judul Pengingat</label>
                <input type="text" id="title" name="title" placeholder="Contoh: Murojaah Juz 1" required />
              </div>

              <div class="form-group">
                <label for="reminder_type"><i class="fas fa-tag"></i> Jenis Pengingat</label>
                <select id="reminder_type" name="reminder_type" required>
                  <option value="">Pilih Jenis</option>
                  <option value="hafalan">Hafalan Baru</option>
                  <option value="murojaah">Murojaah</option>
                  <option value="tasmi">Tasmi'</option>
                  <option value="doa">Doa & Wirid</option>
                  <option value="custom">Lainnya</option>
                </select>
              </div>
            </div>

            <div class="form-group">
              <label for="description"><i class="fas fa-align-left"></i> Deskripsi (Opsional)</label>
              <textarea id="description" name="description" rows="3" placeholder="Tambahkan catatan atau detail pengingat..."></textarea>
            </div>

            <div class="form-row">
              <div class="form-group">
                <label for="reminder_date"><i class="fas fa-calendar"></i> Tanggal</label>
                <input type="date" id="reminder_date" name="reminder_date" required />
              </div>

              <div class="form-group">
                <label for="reminder_time"><i class="fas fa-clock"></i> Waktu</label>
                <input type="time" id="reminder_time" name="reminder_time" required />
              </div>
            </div>

            <div class="form-group">
              <label for="repeat_type"><i class="fas fa-sync"></i> Pengulangan</label>
              <select id="repeat_type" name="repeat_type">
                <option value="none">Tidak Berulang</option>
                <option value="daily">Harian</option>
                <option value="weekly">Mingguan</option>
                <option value="monthly">Bulanan</option>
              </select>
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-bell"></i> Buat Pengingat
            </button>
          </form>
        </div>
      </section>

      <!-- Active Reminders -->
      <section class="reminders-section">
        <h2><i class="fas fa-list"></i> Pengingat Aktif</h2>
        <div class="reminders-container">
          <?php if (empty($reminders_data)): ?>
            <div class="empty-state">
              <i class="fas fa-bell-slash"></i>
              <p>Belum ada pengingat yang dibuat.</p>
              <p>Buat pengingat pertama Anda untuk membantu rutinitas hafalan!</p>
            </div>
          <?php else: ?>
            <?php foreach ($reminders_data as $reminder): ?>
              <div class="reminder-card <?php echo $reminder['is_active'] ? 'active' : 'inactive'; ?>">
                <div class="reminder-header">
                  <div class="reminder-type">
                    <i class="fas <?php 
                      echo $reminder['reminder_type'] === 'hafalan' ? 'fa-book-open' : 
                          ($reminder['reminder_type'] === 'murojaah' ? 'fa-repeat' : 
                          ($reminder['reminder_type'] === 'tasmi' ? 'fa-microphone' : 
                          ($reminder['reminder_type'] === 'doa' ? 'fa-hands' : 'fa-bell')));
                    ?>"></i>
                    <span class="type-label"><?php echo ucfirst($reminder['reminder_type']); ?></span>
                  </div>
                  
                  <div class="reminder-actions">
                    <button onclick="toggleReminder(<?php echo $reminder['id']; ?>, <?php echo $reminder['is_active'] ? 0 : 1; ?>)" 
                            class="btn-toggle <?php echo $reminder['is_active'] ? 'active' : 'inactive'; ?>" 
                            title="<?php echo $reminder['is_active'] ? 'Nonaktifkan' : 'Aktifkan'; ?>">
                      <i class="fas <?php echo $reminder['is_active'] ? 'fa-pause' : 'fa-play'; ?>"></i>
                    </button>
                    
                    <button onclick="editReminder(<?php echo $reminder['id']; ?>)" class="btn-edit" title="Edit">
                      <i class="fas fa-edit"></i>
                    </button>
                    
                    <button onclick="deleteReminder(<?php echo $reminder['id']; ?>)" class="btn-delete" title="Hapus">
                      <i class="fas fa-trash"></i>
                    </button>
                  </div>
                </div>

                <div class="reminder-content">
                  <h3><?php echo htmlspecialchars($reminder['title']); ?></h3>
                  <?php if ($reminder['description']): ?>
                    <p class="reminder-description"><?php echo htmlspecialchars($reminder['description']); ?></p>
                  <?php endif; ?>
                  
                  <div class="reminder-datetime">
                    <span class="datetime">
                      <i class="fas fa-calendar"></i>
                      <?php echo date('d F Y', strtotime($reminder['reminder_datetime'])); ?>
                    </span>
                    <span class="datetime">
                      <i class="fas fa-clock"></i>
                      <?php echo date('H:i', strtotime($reminder['reminder_datetime'])); ?>
                    </span>
                    <?php if ($reminder['repeat_type'] !== 'none'): ?>
                      <span class="repeat-badge">
                        <i class="fas fa-sync"></i>
                        <?php echo ucfirst($reminder['repeat_type']); ?>
                      </span>
                    <?php endif; ?>
                  </div>

                  <div class="reminder-status">
                    <?php 
                    $now = new DateTime();
                    $reminder_time = new DateTime($reminder['reminder_datetime']);
                    $diff = $now->diff($reminder_time);
                    
                    if ($reminder_time > $now): ?>
                      <span class="status-upcoming">
                        <i class="fas fa-clock"></i>
                        <?php 
                        if ($diff->days > 0) {
                          echo $diff->days . ' hari lagi';
                        } elseif ($diff->h > 0) {
                          echo $diff->h . ' jam lagi';
                        } else {
                          echo $diff->i . ' menit lagi';
                        }
                        ?>
                      </span>
                    <?php else: ?>
                      <span class="status-overdue">
                        <i class="fas fa-exclamation-triangle"></i>
                        Terlewat
                      </span>
                    <?php endif; ?>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </section>

      <!-- Notification Settings -->
      <section class="notification-settings">
        <div class="card">
          <h2><i class="fas fa-cog"></i> Pengaturan Notifikasi</h2>
          <div class="notification-options">
            <div class="notification-item">
              <div class="notification-info">
                <h3>Notifikasi Browser</h3>
                <p>Izinkan notifikasi melalui browser</p>
              </div>
              <button onclick="requestNotificationPermission()" class="btn-permission" id="notificationBtn">
                <i class="fas fa-bell"></i> Izinkan Notifikasi
              </button>
            </div>

            <div class="notification-item">
              <div class="notification-info">
                <h3>Suara Notifikasi</h3>
                <p>Putar suara saat pengingat aktif</p>
              </div>
              <label class="switch">
                <input type="checkbox" id="soundEnabled" checked>
                <span class="slider"></span>
              </label>
            </div>

            <div class="notification-item">
              <div class="notification-info">
                <h3>Pengingat Berulang</h3>
                <p>Ulangi notifikasi setiap 5 menit jika belum dibaca</p>
              </div>
              <label class="switch">
                <input type="checkbox" id="repeatEnabled">
                <span class="slider"></span>
              </label>
            </div>
          </div>
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

    /* Quick Reminder Actions */
    .quick-reminder-actions {
      margin-bottom: 3rem;
    }

    .quick-reminder-actions h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .quick-reminder-actions h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .quick-actions-grid {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
    }

    .quick-reminder-btn {
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
      color: #2d3748;
    }

    .quick-reminder-btn:hover {
      border-color: #4facfe;
      color: #4facfe;
      transform: translateY(-5px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.1);
    }

    .quick-reminder-btn i {
      font-size: 2rem;
      margin-bottom: 0.5rem;
    }

    .quick-reminder-btn span {
      font-weight: 600;
      text-align: center;
    }

    /* Form Styles */
    .form-section {
      margin-bottom: 3rem;
    }

    .form-card {
      background: white;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .form-card h2 {
      color: #2d3748;
      margin-bottom: 2rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .form-card h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .reminder-form {
      display: grid;
      gap: 1.5rem;
    }

    .form-row {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 1rem;
    }

    .form-group {
      display: flex;
      flex-direction: column;
    }

    .form-group label {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #2d3748;
    }

    .form-group label i {
      margin-right: 0.5rem;
      color: #4facfe;
      width: 16px;
    }

    .form-group input,
    .form-group select,
    .form-group textarea {
      padding: 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
      font-family: inherit;
    }

    .form-group input:focus,
    .form-group select:focus,
    .form-group textarea:focus {
      outline: none;
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }

    .btn-submit {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      border: none;
      padding: 1rem 2rem;
      border-radius: 12px;
      font-size: 1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      margin-top: 1rem;
    }

    .btn-submit:hover {
      transform: translateY(-2px);
      box-shadow: 0 6px 20px rgba(79, 172, 254, 0.3);
    }

    /* Reminders Section */
    .reminders-section {
      margin-bottom: 3rem;
    }

    .reminders-section h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.5rem;
      display: flex;
      align-items: center;
    }

    .reminders-section h2 i {
      margin-right: 1rem;
      color: #4facfe;
    }

    .reminders-container {
      display: grid;
      gap: 1rem;
    }

    .reminder-card {
      background: white;
      padding: 1.5rem;
      border-radius: 15px;
      box-shadow: 0 4px 15px rgba(0,0,0,0.08);
      border: 1px solid #e2e8f0;
      transition: all 0.3s ease;
    }

    .reminder-card.active {
      border-left: 4px solid #4facfe;
    }

    .reminder-card.inactive {
      opacity: 0.6;
      border-left: 4px solid #cbd5e0;
    }

    .reminder-card:hover {
      transform: translateY(-2px);
      box-shadow: 0 8px 25px rgba(0,0,0,0.12);
    }

    .reminder-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 1rem;
    }

    .reminder-type {
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .reminder-type i {
      color: #4facfe;
      font-size: 1.2rem;
    }

    .type-label {
      background: #f0f9ff;
      color: #0369a1;
      padding: 0.25rem 0.75rem;
      border-radius: 12px;
      font-size: 0.8rem;
      font-weight: 600;
    }

    .reminder-actions {
      display: flex;
      gap: 0.5rem;
    }

    .btn-toggle, .btn-edit, .btn-delete {
      width: 32px;
      height: 32px;
      border: none;
      border-radius: 8px;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .btn-toggle.active {
      background: #48bb78;
      color: white;
    }

    .btn-toggle.inactive {
      background: #cbd5e0;
      color: #4a5568;
    }

    .btn-edit {
      background: #ed8936;
      color: white;
    }

    .btn-delete {
      background: #e53e3e;
      color: white;
    }

    .btn-toggle:hover, .btn-edit:hover, .btn-delete:hover {
      transform: scale(1.1);
    }

    .reminder-content h3 {
      color: #2d3748;
      margin-bottom: 0.5rem;
      font-size: 1.1rem;
    }

    .reminder-description {
      color: #718096;
      font-size: 0.9rem;
      margin-bottom: 1rem;
    }

    .reminder-datetime {
      display: flex;
      gap: 1rem;
      margin-bottom: 0.5rem;
      flex-wrap: wrap;
    }

    .datetime {
      display: flex;
      align-items: center;
      gap: 0.25rem;
      color: #4a5568;
      font-size: 0.9rem;
    }

    .datetime i {
      color: #4facfe;
    }

    .repeat-badge {
      background: #e6fffa;
      color: #00695c;
      padding: 0.25rem 0.5rem;
      border-radius: 8px;
      font-size: 0.8rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .reminder-status {
      margin-top: 0.5rem;
    }

    .status-upcoming {
      color: #48bb78;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    .status-overdue {
      color: #e53e3e;
      font-size: 0.9rem;
      display: flex;
      align-items: center;
      gap: 0.25rem;
    }

    /* Notification Settings */
    .notification-settings {
      margin-bottom: 2rem;
    }

    .notification-options {
      display: grid;
      gap: 1rem;
    }

    .notification-item {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1rem;
      border: 1px solid #e2e8f0;
      border-radius: 12px;
    }

    .notification-info h3 {
      color: #2d3748;
      margin-bottom: 0.25rem;
    }

    .notification-info p {
      color: #718096;
      font-size: 0.9rem;
      margin: 0;
    }

    .btn-permission {
      background: #4facfe;
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

    .btn-permission:hover {
      background: #00f2fe;
    }

    .btn-permission.granted {
      background: #48bb78;
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

    @media (max-width: 768px) {
      .form-row {
        grid-template-columns: 1fr;
      }

      .quick-actions-grid {
        grid-template-columns: repeat(2, 1fr);
      }

      .reminder-header {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }

      .reminder-actions {
        justify-content: center;
      }

      .notification-item {
        flex-direction: column;
        align-items: stretch;
        gap: 1rem;
      }
    }
  </style>

  <script>
    // Set default date and time
    document.addEventListener('DOMContentLoaded', function() {
      const now = new Date();
      const tomorrow = new Date(now);
      tomorrow.setDate(tomorrow.getDate() + 1);
      
      document.getElementById('reminder_date').value = tomorrow.toISOString().split('T')[0];
      document.getElementById('reminder_time').value = '07:00';
      
      checkNotificationPermission();
      initializeReminders();
    });

    // Quick reminder functions
    function setQuickReminder(type, minutesFromNow) {
      const now = new Date();
      const reminderTime = new Date(now.getTime() + minutesFromNow * 60000);
      
      const titles = {
        'hafalan': 'Waktunya Hafalan Baru',
        'murojaah': 'Waktunya Murojaah',
        'tasmi': 'Waktunya Tasmi\'',
        'doa': 'Waktunya Doa & Wirid'
      };
      
      document.getElementById('title').value = titles[type] || 'Pengingat';
      document.getElementById('reminder_type').value = type;
      document.getElementById('reminder_date').value = reminderTime.toISOString().split('T')[0];
      document.getElementById('reminder_time').value = reminderTime.toTimeString().split(':').slice(0,2).join(':');
      
      // Scroll to form
      document.querySelector('.form-section').scrollIntoView({ behavior: 'smooth' });
      
      // Focus on description
      setTimeout(() => {
        document.getElementById('description').focus();
      }, 500);
    }

    // Toggle reminder status
    function toggleReminder(id, status) {
      const form = document.createElement('form');
      form.method = 'POST';
      form.innerHTML = `
        <input type="hidden" name="action" value="toggle_reminder">
        <input type="hidden" name="reminder_id" value="${id}">
        <input type="hidden" name="is_active" value="${status}">
      `;
      document.body.appendChild(form);
      form.submit();
    }

    // Delete reminder
    function deleteReminder(id) {
      if (confirm('Apakah Anda yakin ingin menghapus pengingat ini?')) {
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="delete_reminder">
          <input type="hidden" name="reminder_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    // Edit reminder (placeholder)
    function editReminder(id) {
      alert('Fitur edit pengingat akan segera tersedia!');
    }

    // Notification permission
    function requestNotificationPermission() {
      if ('Notification' in window) {
        Notification.requestPermission().then(function(permission) {
          updateNotificationButton(permission);
          if (permission === 'granted') {
            new Notification('TAHFEEDZ Pengingat', {
              body: 'Notifikasi berhasil diaktifkan! Anda akan menerima pengingat sesuai jadwal.',
              icon: '/favicon.ico'
            });
          }
        });
      } else {
        alert('Browser Anda tidak mendukung notifikasi.');
      }
    }

    function checkNotificationPermission() {
      if ('Notification' in window) {
        updateNotificationButton(Notification.permission);
      }
    }

    function updateNotificationButton(permission) {
      const btn = document.getElementById('notificationBtn');
      if (permission === 'granted') {
        btn.innerHTML = '<i class="fas fa-check"></i> Notifikasi Aktif';
        btn.classList.add('granted');
        btn.disabled = true;
      } else if (permission === 'denied') {
        btn.innerHTML = '<i class="fas fa-times"></i> Notifikasi Ditolak';
        btn.style.background = '#e53e3e';
      }
    }

    // Initialize reminder system
    function initializeReminders() {
      // Check for upcoming reminders every minute
      setInterval(checkUpcomingReminders, 60000);
      
      // Check immediately
      checkUpcomingReminders();
    }

    function checkUpcomingReminders() {
      // In a real implementation, this would check database for due reminders
      // For now, we'll use a placeholder
      console.log('Checking for upcoming reminders...');
    }

    // Play notification sound
    function playNotificationSound() {
      if (document.getElementById('soundEnabled').checked) {
        // In a real implementation, you'd play an actual sound file
        // For now, we'll use the default system sound
        const audio = new Audio('data:audio/wav;base64,UklGRnoGAABXQVZFZm10IBAAAAABAAEAQB8AAEAfAAABAAgAZGF0YQoGAACBhYqFbF1fdJivrJBhNjVgodDbq2EcBj+a2/LDciUFLIHO8tiJNwgZaLvt559NEAxQp+PwtmMcBjiR1/LMeSwFJHfH8N2QQAoUXrTp66hVFApGn+DyvmIdBzuP1fbTgC4GM2q+8+OXRxINTKXh9LllHgU4n9z4v2YdBjoS2fbUeDEGOGm98+uaRggOSaTg7bNsJAIv');
      }
    }

    // Form validation
    document.querySelector('.reminder-form').addEventListener('submit', function(e) {
      const date = document.getElementById('reminder_date').value;
      const time = document.getElementById('reminder_time').value;
      
      if (date && time) {
        const reminderDateTime = new Date(date + 'T' + time);
        const now = new Date();
        
        if (reminderDateTime <= now) {
          e.preventDefault();
          alert('Waktu pengingat harus di masa depan!');
          return;
        }
      }
    });
  </script>
</body>
</html>