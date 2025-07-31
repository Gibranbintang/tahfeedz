<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Proses simpan hafalan
if ($_POST && isset($_POST['action']) && $_POST['action'] === 'simpan_hafalan') {
    global $koneksi;
    
    $jenis = mysqli_real_escape_string($koneksi, $_POST['jenis']);
    $nama_santri = mysqli_real_escape_string($koneksi, $_POST['nama']);
    $surat = mysqli_real_escape_string($koneksi, $_POST['surat']);
    $ayat = mysqli_real_escape_string($koneksi, $_POST['ayat']);
    $tanggal = mysqli_real_escape_string($koneksi, $_POST['tanggal']);
    
    $query = "INSERT INTO hafalan (user_id, jenis, nama_santri, surat, ayat, tanggal, created_at) 
              VALUES ('$user_id', '$jenis', '$nama_santri', '$surat', '$ayat', '$tanggal', NOW())";
    
    if (mysqli_query($koneksi, $query)) {
        $success_message = "Data hafalan berhasil disimpan!";
    } else {
        $error_message = "Gagal menyimpan data: " . mysqli_error($koneksi);
    }
}

// Ambil data hafalan user
global $koneksi;
$hafalan_query = "SELECT * FROM hafalan WHERE user_id = $user_id ORDER BY created_at DESC";
$hafalan_result = mysqli_query($koneksi, $hafalan_query);
$hafalan_data = [];
while ($row = mysqli_fetch_assoc($hafalan_result)) {
    $hafalan_data[] = $row;
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Rekap Hafalan - Tahfeedz</title>
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
          <li class="active">
            <span><i class="fas fa-chart-line"></i> Rekap Hafalan</span>
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
            <a href="logout.php"><i class="fas fa-sign-out-alt"></i> Keluar</a>
          </li>
        </ul>
      </nav>
    </aside>

    <!-- Main Content -->
    <main class="main-content">
      <header class="header">
        <h1><i class="fas fa-chart-line"></i> Rekap Hafalan</h1>
      </header>

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

      <!-- Form Input Hafalan -->
      <section class="form-section">
        <div class="card form-card">
          <h2><i class="fas fa-plus-circle"></i> Input Hafalan Baru</h2>
          <form method="POST" class="hafalan-form">
            <input type="hidden" name="action" value="simpan_hafalan">
            
            <div class="form-group">
              <label for="jenis"><i class="fas fa-tag"></i> Jenis Hafalan</label>
              <select id="jenis" name="jenis" required>
                <option value="">Pilih Jenis Hafalan</option>
                <option value="Hafalan Baru">Hafalan Baru</option>
                <option value="Murojaah">Murojaah</option>
                <option value="Tasmi'">Tasmi'</option>
              </select>
            </div>

            <div class="form-group">
              <label for="nama"><i class="fas fa-user"></i> Nama Mahasantri</label>
              <input type="text" id="nama" name="nama" placeholder="Masukkan nama mahasantri" required />
            </div>

            <div class="form-group">
              <label for="surat"><i class="fas fa-book-open"></i> Surat</label>
              <input type="text" id="surat" name="surat" placeholder="Contoh: Al-Fatihah" required />
            </div>

            <div class="form-group">
              <label for="ayat"><i class="fas fa-list-ol"></i> Ayat</label>
              <input type="text" id="ayat" name="ayat" placeholder="Contoh: 1-7" required />
            </div>

            <div class="form-group">
              <label for="tanggal"><i class="fas fa-calendar"></i> Tanggal</label>
              <input type="date" id="tanggal" name="tanggal" required />
            </div>

            <button type="submit" class="btn-submit">
              <i class="fas fa-save"></i> Simpan Hafalan
            </button>
          </form>
        </div>
      </section>

      <!-- Daftar Hafalan -->
      <section class="rekap-box">
        <h2><i class="fas fa-list"></i> Daftar Hafalan Tersimpan</h2>
        <div id="hafalanContainer">
          <?php if (empty($hafalan_data)): ?>
            <div class="empty-state">
              <i class="fas fa-book-open"></i>
              <p>Belum ada data hafalan yang tersimpan.</p>
              <p>Mulai input hafalan pertama Anda!</p>
            </div>
          <?php else: ?>
            <?php foreach ($hafalan_data as $index => $item): ?>
              <div class="rekap-item">
                <div class="rekap-header">
                  <strong><?php echo htmlspecialchars($item['nama_santri']); ?></strong>
                  <button onclick="hapusHafalan(<?php echo $item['id']; ?>)" class="btn-delete" title="Hapus">
                    <i class="fas fa-trash"></i>
                  </button>
                </div>
                <div class="meta">
                  <span><i class="fas fa-tag"></i><?php echo htmlspecialchars($item['jenis']); ?></span>
                  <span><i class="fas fa-book"></i><?php echo htmlspecialchars($item['surat']); ?></span>
                  <span><i class="fas fa-list-ol"></i>Ayat: <?php echo htmlspecialchars($item['ayat']); ?></span>
                  <span><i class="fas fa-calendar"></i><?php echo date('d F Y', strtotime($item['tanggal'])); ?></span>
                </div>
              </div>
            <?php endforeach; ?>
          <?php endif; ?>
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

    .hafalan-form {
      display: grid;
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
    .form-group select {
      padding: 1rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 1rem;
      transition: all 0.3s ease;
      background: white;
    }

    .form-group input:focus,
    .form-group select:focus {
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

    .rekap-header {
      display: flex;
      justify-content: space-between;
      align-items: center;
      margin-bottom: 0.5rem;
    }

    .btn-delete {
      background: #e53e3e;
      color: white;
      border: none;
      padding: 0.5rem;
      border-radius: 8px;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
    }

    .btn-delete:hover {
      background: #c53030;
      transform: scale(1.1);
    }

    @media (max-width: 768px) {
      .hafalan-form {
        grid-template-columns: 1fr;
      }
    }
  </style>

  <script>
    // Set tanggal hari ini sebagai default
    document.getElementById('tanggal').value = new Date().toISOString().split('T')[0];

    // Hapus hafalan
    function hapusHafalan(id) {
      if (confirm('Apakah Anda yakin ingin menghapus data hafalan ini?')) {
        // Create form to delete
        const form = document.createElement('form');
        form.method = 'POST';
        form.innerHTML = `
          <input type="hidden" name="action" value="hapus_hafalan">
          <input type="hidden" name="hafalan_id" value="${id}">
        `;
        document.body.appendChild(form);
        form.submit();
      }
    }

    // Form validation
    document.querySelector('.hafalan-form').addEventListener('submit', function(e) {
      const inputs = this.querySelectorAll('input[required], select[required]');
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
  </script>
</body>
</html>