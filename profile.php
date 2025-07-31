<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Proses update profil
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'update_profile') {
    global $koneksi;
    
    $nama_baru = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $email_baru = mysqli_real_escape_string($koneksi, $_POST['email']);
    $telepon = mysqli_real_escape_string($koneksi, $_POST['telepon']);
    $alamat = mysqli_real_escape_string($koneksi, $_POST['alamat']);
    $bio = mysqli_real_escape_string($koneksi, $_POST['bio']);
    
    // Cek apakah email sudah digunakan user lain
    $check_email = "SELECT id FROM users WHERE email = '$email_baru' AND id != $user_id";
    $check_result = mysqli_query($koneksi, $check_email);
    
    if (mysqli_num_rows($check_result) > 0) {
        $error_message = "Email sudah digunakan oleh pengguna lain.";
    } else {
        $update_query = "UPDATE users SET 
                        nama = '$nama_baru',
                        email = '$email_baru',
                        telepon = '$telepon',
                        alamat = '$alamat',
                        bio = '$bio',
                        updated_at = NOW()
                        WHERE id = $user_id";
        
        if (mysqli_query($koneksi, $update_query)) {
            $_SESSION['nama'] = $nama_baru;
            $_SESSION['email'] = $email_baru;
            $success_message = "Profil berhasil diperbarui!";
            $nama = $nama_baru;
            $email = $email_baru;
        } else {
            $error_message = "Gagal memperbarui profil: " . mysqli_error($koneksi);
        }
    }
}

// Proses ubah password
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'change_password') {
    global $koneksi;
    
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Ambil password lama dari database
    $user_query = "SELECT password FROM users WHERE id = $user_id";
    $user_result = mysqli_query($koneksi, $user_query);
    $user_data = mysqli_fetch_assoc($user_result);
    
    if (!password_verify($current_password, $user_data['password'])) {
        $password_error = "Password lama tidak sesuai.";
    } elseif ($new_password !== $confirm_password) {
        $password_error = "Konfirmasi password tidak cocok.";
    } elseif (strlen($new_password) < 6) {
        $password_error = "Password baru minimal 6 karakter.";
    } else {
        $hashed_password = password_hash($new_password, PASSWORD_DEFAULT);
        $update_password = "UPDATE users SET password = '$hashed_password', updated_at = NOW() WHERE id = $user_id";
        
        if (mysqli_query($koneksi, $update_password)) {
            $password_success = "Password berhasil diubah!";
        } else {
            $password_error = "Gagal mengubah password: " . mysqli_error($koneksi);
        }
    }
}

// Ambil data user terbaru
global $koneksi;
$user_query = "SELECT * FROM users WHERE id = $user_id";
$user_result = mysqli_query($koneksi, $user_query);
$user_data = mysqli_fetch_assoc($user_result);

// Ambil statistik hafalan user
$stats_query = "SELECT 
    COUNT(*) as total_hafalan,
    COUNT(CASE WHEN jenis = 'Hafalan Baru' THEN 1 END) as hafalan_baru,
    COUNT(CASE WHEN jenis = 'Murojaah' THEN 1 END) as murojaah,
    COUNT(CASE WHEN DATE(created_at) = CURDATE() THEN 1 END) as hari_ini
    FROM hafalan WHERE user_id = $user_id";
$stats_result = mysqli_query($koneksi, $stats_query);
$user_stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Profil - Tahfeedz</title>
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
          <li class="active">
            <span><i class="fas fa-user"></i> Profil</span>
          </li>
          <li>
            <a href="pengaturan.php"><i class="fas fa-cog"></i> Pengaturan</a>
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
        <h1><i class="fas fa-user"></i> Profil Pengguna</h1>
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

      <?php if (isset($password_success)): ?>
        <div class="alert alert-success">
          <i class="fas fa-check-circle"></i>
          <?php echo $password_success; ?>
        </div>
      <?php endif; ?>

      <?php if (isset($password_error)): ?>
        <div class="alert alert-error">
          <i class="fas fa-exclamation-circle"></i>
          <?php echo $password_error; ?>
        </div>
      <?php endif; ?>

      <!-- Profile Header -->
      <section class="profile-header">
        <div class="profile-card">
          <div class="profile-avatar">
            <div class="avatar-circle">
              <i class="fas fa-user"></i>
            </div>
            <button class="btn-upload">
              <i class="fas fa-camera"></i>
            </button>
          </div>
          <div class="profile-info">
            <h2><?php echo htmlspecialchars($user_data['nama']); ?></h2>
            <p class="profile-email"><?php echo htmlspecialchars($user_data['email']); ?></p>
            <p class="profile-join">Bergabung sejak: <?php echo date('d F Y', strtotime($user_data['created_at'])); ?></p>
          </div>
        </div>
      </section>

      <!-- User Stats -->
      <section class="stats-grid">
        <div class="stat-card">
          <i class="fas fa-book-open"></i>
          <h3><?php echo $user_stats['total_hafalan']; ?></h3>
          <p>Total Hafalan</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-plus-circle"></i>
          <h3><?php echo $user_stats['hafalan_baru']; ?></h3>
          <p>Hafalan Baru</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-repeat"></i>
          <h3><?php echo $user_stats['murojaah']; ?></h3>
          <p>Murojaah</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-calendar-day"></i>
          <h3><?php echo $user_stats['hari_ini']; ?></h3>
          <p>Hari Ini</p>
        </div>
      </section>

      <!-- Profile Forms -->
      <div class="profile-forms">
        <!-- Update Profile Form -->
        <section class="form-section">
          <div class="card form-card">
            <h2><i class="fas fa-edit"></i> Edit Profil</h2>
            <form method="POST" class="profile-form">
              <input type="hidden" name="action" value="update_profile">
              
              <div class="form-group">
                <label for="nama"><i class="fas fa-user"></i> Nama Lengkap</label>
                <input type="text" id="nama" name="nama" value="<?php echo htmlspecialchars($user_data['nama']); ?>" required />
              </div>

              <div class="form-group">
                <label for="email"><i class="fas fa-envelope"></i> Email</label>
                <input type="email" id="email" name="email" value="<?php echo htmlspecialchars($user_data['email']); ?>" required />
              </div>

              <div class="form-group">
                <label for="telepon"><i class="fas fa-phone"></i> Nomor Telepon</label>
                <input type="tel" id="telepon" name="telepon" value="<?php echo htmlspecialchars($user_data['telepon'] ?? ''); ?>" />
              </div>

              <div class="form-group">
                <label for="alamat"><i class="fas fa-map-marker-alt"></i> Alamat</label>
                <textarea id="alamat" name="alamat" rows="3"><?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?></textarea>
              </div>

              <div class="form-group">
                <label for="bio"><i class="fas fa-info-circle"></i> Bio</label>
                <textarea id="bio" name="bio" rows="4" placeholder="Ceritakan sedikit tentang diri Anda..."><?php echo htmlspecialchars($user_data['bio'] ?? ''); ?></textarea>
              </div>

              <button type="submit" class="btn-submit">
                <i class="fas fa-save"></i> Perbarui Profil
              </button>
            </form>
          </div>
        </section>

        <!-- Change Password Form -->
        <section class="form-section">
          <div class="card form-card">
            <h2><i class="fas fa-lock"></i> Ubah Password</h2>
            <form method="POST" class="password-form">
              <input type="hidden" name="action" value="change_password">
              
              <div class="form-group">
                <label for="current_password"><i class="fas fa-key"></i> Password Lama</label>
                <input type="password" id="current_password" name="current_password" required />
              </div>

              <div class="form-group">
                <label for="new_password"><i class="fas fa-lock"></i> Password Baru</label>
                <input type="password" id="new_password" name="new_password" minlength="6" required />
                <small>Minimal 6 karakter</small>
              </div>

              <div class="form-group">
                <label for="confirm_password"><i class="fas fa-lock"></i> Konfirmasi Password Baru</label>
                <input type="password" id="confirm_password" name="confirm_password" minlength="6" required />
              </div>

              <button type="submit" class="btn-submit btn-password">
                <i class="fas fa-shield-alt"></i> Ubah Password
              </button>
            </form>
          </div>
        </section>
      </div>
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

    .profile-header {
      margin-bottom: 3rem;
    }

    .profile-card {
      background: white;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(79, 172, 254, 0.1);
      display: flex;
      align-items: center;
      gap: 2rem;
    }

    .profile-avatar {
      position: relative;
    }

    .avatar-circle {
      width: 120px;
      height: 120px;
      border-radius: 50%;
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      display: flex;
      align-items: center;
      justify-content: center;
      color: white;
      font-size: 3rem;
      position: relative;
    }

    .btn-upload {
      position: absolute;
      bottom: 5px;
      right: 5px;
      width: 40px;
      height: 40px;
      border-radius: 50%;
      background: #4facfe;
      color: white;
      border: none;
      cursor: pointer;
      display: flex;
      align-items: center;
      justify-content: center;
      transition: all 0.3s ease;
    }

    .btn-upload:hover {
      background: #00f2fe;
      transform: scale(1.1);
    }

    .profile-info h2 {
      color: #2d3748;
      margin-bottom: 0.5rem;
      font-size: 2rem;
    }

    .profile-email {
      color: #4facfe;
      font-size: 1.1rem;
      margin-bottom: 0.5rem;
    }

    .profile-join {
      color: #718096;
      font-size: 0.9rem;
    }

    .profile-forms {
      display: grid;
      grid-template-columns: 1fr 1fr;
      gap: 2rem;
    }

    .form-section {
      margin-bottom: 2rem;
    }

    .form-card {
      background: white;
      padding: 2.5rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(79, 172, 254, 0.1);
      height: fit-content;
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

    .profile-form, .password-form {
      display: flex;
      flex-direction: column;
      gap: 1.5rem;
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
    .form-group textarea:focus {
      outline: none;
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }

    .form-group small {
      color: #718096;
      font-size: 0.8rem;
      margin-top: 0.25rem;
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

    .btn-password {
      background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
    }

    .btn-password:hover {
      box-shadow: 0 6px 20px rgba(72, 187, 120, 0.3);
    }

    @media (max-width: 768px) {
      .profile-card {
        flex-direction: column;
        text-align: center;
      }

      .profile-forms {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <script>
    // Password confirmation validation
    document.getElementById('confirm_password').addEventListener('input', function() {
      const newPassword = document.getElementById('new_password').value;
      const confirmPassword = this.value;
      
      if (newPassword !== confirmPassword) {
        this.setCustomValidity('Password tidak cocok');
      } else {
        this.setCustomValidity('');
      }
    });

    // Form validation
    document.querySelectorAll('form').forEach(form => {
      form.addEventListener('submit', function(e) {
        const inputs = this.querySelectorAll('input[required]');
        let valid = true;

        inputs.forEach(input => {
          if (!input.value.trim()) {
            valid = false;
            input.style.borderColor = '#e53e3e';
          } else {
            input.style.borderColor = '#e2e8f0';
          }
        });

        if (!valid) {
          e.preventDefault();
          alert('Mohon lengkapi semua kolom yang diperlukan!');
        }
      });
    });

    // Avatar upload (placeholder)
    document.querySelector('.btn-upload').addEventListener('click', function() {
      alert('Fitur upload avatar akan segera tersedia!');
    });
  </script>
</body>
</html>