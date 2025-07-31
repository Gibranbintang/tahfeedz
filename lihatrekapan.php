<?php
session_start();
include 'fungsi.php';

// Pastikan user sudah login
requireLogin();

// Ambil data user dari session
$user_id = $_SESSION['user_id'];
$email = $_SESSION['email'];
$nama = $_SESSION['nama'] ?? 'User';

// Ambil parameter filter
$filter_jenis = $_GET['jenis'] ?? '';
$filter_bulan = $_GET['bulan'] ?? '';
$filter_tahun = $_GET['tahun'] ?? date('Y');

// Build query dengan filter
global $koneksi;
$where_conditions = ["user_id = $user_id"];

if ($filter_jenis) {
    $where_conditions[] = "jenis = '" . mysqli_real_escape_string($koneksi, $filter_jenis) . "'";
}

if ($filter_bulan) {
    $where_conditions[] = "MONTH(tanggal) = " . intval($filter_bulan);
}

if ($filter_tahun) {
    $where_conditions[] = "YEAR(tanggal) = " . intval($filter_tahun);
}

$where_clause = implode(' AND ', $where_conditions);

// Ambil data hafalan dengan filter
$hafalan_query = "SELECT * FROM hafalan WHERE $where_clause ORDER BY tanggal DESC, created_at DESC";
$hafalan_result = mysqli_query($koneksi, $hafalan_query);
$hafalan_data = [];
while ($row = mysqli_fetch_assoc($hafalan_result)) {
    $hafalan_data[] = $row;
}

// Hitung statistik
$stats_query = "SELECT 
    COUNT(*) as total,
    COUNT(CASE WHEN jenis = 'Hafalan Baru' THEN 1 END) as hafalan_baru,
    COUNT(CASE WHEN jenis = 'Murojaah' THEN 1 END) as murojaah,
    COUNT(CASE WHEN jenis = 'Tasmi' THEN 1 END) as tasmi,
    COUNT(CASE WHEN MONTH(tanggal) = MONTH(CURDATE()) AND YEAR(tanggal) = YEAR(CURDATE()) THEN 1 END) as bulan_ini
    FROM hafalan WHERE user_id = $user_id";
$stats_result = mysqli_query($koneksi, $stats_query);
$stats = mysqli_fetch_assoc($stats_result);
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>Lihat Rekapan - Tahfeedz</title>
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
          <li class="active">
            <span><i class="fas fa-eye"></i> Lihat Rekapan</span>
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
        <h1><i class="fas fa-eye"></i> Lihat Rekapan Hafalan</h1>
      </header>

      <!-- Stats Grid -->
      <section class="stats-grid">
        <div class="stat-card">
          <i class="fas fa-book-open"></i>
          <h3><?php echo $stats['total']; ?></h3>
          <p>Total Hafalan</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-plus-circle"></i>
          <h3><?php echo $stats['hafalan_baru']; ?></h3>
          <p>Hafalan Baru</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-repeat"></i>
          <h3><?php echo $stats['murojaah']; ?></h3>
          <p>Murojaah</p>
        </div>
        <div class="stat-card">
          <i class="fas fa-calendar-day"></i>
          <h3><?php echo $stats['bulan_ini']; ?></h3>
          <p>Bulan Ini</p>
        </div>
      </section>

      <!-- Filter Section -->
      <section class="filter-section">
        <div class="card filter-card">
          <h2><i class="fas fa-filter"></i> Filter Rekapan</h2>
          <form method="GET" class="filter-form">
            <div class="filter-group">
              <label for="jenis"><i class="fas fa-tag"></i> Jenis Hafalan</label>
              <select id="jenis" name="jenis">
                <option value="">Semua Jenis</option>
                <option value="Hafalan Baru" <?php echo $filter_jenis === 'Hafalan Baru' ? 'selected' : ''; ?>>Hafalan Baru</option>
                <option value="Murojaah" <?php echo $filter_jenis === 'Murojaah' ? 'selected' : ''; ?>>Murojaah</option>
                <option value="Tasmi'" <?php echo $filter_jenis === "Tasmi'" ? 'selected' : ''; ?>>Tasmi'</option>
              </select>
            </div>

            <div class="filter-group">
              <label for="bulan"><i class="fas fa-calendar"></i> Bulan</label>
              <select id="bulan" name="bulan">
                <option value="">Semua Bulan</option>
                <?php for ($i = 1; $i <= 12; $i++): ?>
                  <option value="<?php echo $i; ?>" <?php echo $filter_bulan == $i ? 'selected' : ''; ?>>
                    <?php echo date('F', mktime(0, 0, 0, $i, 1)); ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="filter-group">
              <label for="tahun"><i class="fas fa-calendar-alt"></i> Tahun</label>
              <select id="tahun" name="tahun">
                <?php for ($year = date('Y'); $year >= date('Y') - 5; $year--): ?>
                  <option value="<?php echo $year; ?>" <?php echo $filter_tahun == $year ? 'selected' : ''; ?>>
                    <?php echo $year; ?>
                  </option>
                <?php endfor; ?>
              </select>
            </div>

            <div class="filter-actions">
              <button type="submit" class="btn-filter">
                <i class="fas fa-search"></i> Terapkan Filter
              </button>
              <a href="lihatrekapan.php" class="btn-reset">
                <i class="fas fa-undo"></i> Reset Filter
              </a>
            </div>
          </form>
        </div>
      </section>

      <!-- Export Section -->
      <section class="export-section">
        <div class="export-buttons">
          <button onclick="exportToPDF()" class="btn-export">
            <i class="fas fa-file-pdf"></i> Export PDF
          </button>
          <button onclick="exportToExcel()" class="btn-export">
            <i class="fas fa-file-excel"></i> Export Excel
          </button>
          <button onclick="printReport()" class="btn-export">
            <i class="fas fa-print"></i> Print
          </button>
        </div>
      </section>

      <!-- Rekapan Data -->
      <section class="rekap-box">
        <h2><i class="fas fa-list"></i> Data Rekapan Hafalan</h2>
        <div class="rekap-info">
          <p>Menampilkan <strong><?php echo count($hafalan_data); ?></strong> data hafalan</p>
        </div>
        
        <div id="rekapContainer">
          <?php if (empty($hafalan_data)): ?>
            <div class="empty-state">
              <i class="fas fa-search"></i>
              <p>Tidak ada data hafalan yang sesuai dengan filter.</p>
              <p>Coba ubah kriteria pencarian Anda.</p>
            </div>
          <?php else: ?>
            <div class="rekap-table">
              <div class="table-header">
                <div class="table-row">
                  <div class="table-cell">No</div>
                  <div class="table-cell">Nama</div>
                  <div class="table-cell">Jenis</div>
                  <div class="table-cell">Surat</div>
                  <div class="table-cell">Ayat</div>
                  <div class="table-cell">Tanggal</div>
                </div>
              </div>
              <div class="table-body">
                <?php foreach ($hafalan_data as $index => $item): ?>
                  <div class="table-row">
                    <div class="table-cell"><?php echo $index + 1; ?></div>
                    <div class="table-cell">
                      <strong><?php echo htmlspecialchars($item['nama_santri']); ?></strong>
                    </div>
                    <div class="table-cell">
                      <span class="badge badge-<?php echo strtolower(str_replace(' ', '-', $item['jenis'])); ?>">
                        <?php echo htmlspecialchars($item['jenis']); ?>
                      </span>
                    </div>
                    <div class="table-cell"><?php echo htmlspecialchars($item['surat']); ?></div>
                    <div class="table-cell"><?php echo htmlspecialchars($item['ayat']); ?></div>
                    <div class="table-cell"><?php echo date('d/m/Y', strtotime($item['tanggal'])); ?></div>
                  </div>
                <?php endforeach; ?>
              </div>
            </div>
          <?php endif; ?>
        </div>
      </section>
    </main>
  </div>

  <style>
    .filter-section {
      margin-bottom: 2rem;
    }

    .filter-card {
      background: white;
      padding: 2rem;
      border-radius: 20px;
      box-shadow: 0 8px 32px rgba(0,0,0,0.1);
      border: 1px solid rgba(79, 172, 254, 0.1);
    }

    .filter-card h2 {
      color: #2d3748;
      margin-bottom: 1.5rem;
      font-size: 1.3rem;
      display: flex;
      align-items: center;
    }

    .filter-card h2 i {
      margin-right: 0.5rem;
      color: #4facfe;
    }

    .filter-form {
      display: grid;
      grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
      gap: 1rem;
      align-items: end;
    }

    .filter-group {
      display: flex;
      flex-direction: column;
    }

    .filter-group label {
      display: flex;
      align-items: center;
      margin-bottom: 0.5rem;
      font-weight: 600;
      color: #2d3748;
      font-size: 0.9rem;
    }

    .filter-group label i {
      margin-right: 0.5rem;
      color: #4facfe;
      width: 16px;
    }

    .filter-group select {
      padding: 0.8rem;
      border: 2px solid #e2e8f0;
      border-radius: 8px;
      font-size: 0.9rem;
      transition: all 0.3s ease;
    }

    .filter-group select:focus {
      outline: none;
      border-color: #4facfe;
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
    }

    .filter-actions {
      display: flex;
      gap: 0.5rem;
      flex-direction: column;
    }

    .btn-filter, .btn-reset {
      padding: 0.8rem 1rem;
      border-radius: 8px;
      font-size: 0.9rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      justify-content: center;
      gap: 0.5rem;
      text-decoration: none;
      border: none;
    }

    .btn-filter {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    .btn-filter:hover {
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
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

    .export-section {
      margin-bottom: 2rem;
    }

    .export-buttons {
      display: flex;
      gap: 1rem;
      flex-wrap: wrap;
    }

    .btn-export {
      background: white;
      color: #2d3748;
      border: 2px solid #e2e8f0;
      padding: 0.8rem 1.5rem;
      border-radius: 12px;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      display: flex;
      align-items: center;
      gap: 0.5rem;
    }

    .btn-export:hover {
      border-color: #4facfe;
      color: #4facfe;
      transform: translateY(-2px);
    }

    .rekap-info {
      margin-bottom: 1rem;
      color: #718096;
    }

    .rekap-table {
      background: white;
      border-radius: 12px;
      overflow: hidden;
      border: 1px solid #e2e8f0;
    }

    .table-header {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
    }

    .table-row {
      display: grid;
      grid-template-columns: 60px 1fr 120px 1fr 100px 120px;
      gap: 1rem;
      padding: 1rem;
      align-items: center;
    }

    .table-header .table-row {
      font-weight: 600;
    }

    .table-body .table-row {
      border-bottom: 1px solid #e2e8f0;
      transition: all 0.3s ease;
    }

    .table-body .table-row:hover {
      background: #f7fafc;
    }

    .table-body .table-row:last-child {
      border-bottom: none;
    }

    .table-cell {
      font-size: 0.9rem;
    }

    .badge {
      display: inline-block;
      padding: 0.3rem 0.8rem;
      border-radius: 20px;
      font-size: 0.8rem;
      font-weight: 600;
      text-align: center;
    }

    .badge-hafalan-baru {
      background: #d4edda;
      color: #155724;
    }

    .badge-murojaah {
      background: #d1ecf1;
      color: #0c5460;
    }

    .badge-tasmi' {
      background: #fce4ec;
      color: #880e4f;
    }

    @media (max-width: 768px) {
      .filter-form {
        grid-template-columns: 1fr;
      }

      .export-buttons {
        justify-content: center;
      }

      .table-row {
        grid-template-columns: 1fr;
        gap: 0.5rem;
        text-align: left;
      }

      .table-cell:before {
        content: attr(data-label) ": ";
        font-weight: 600;
        color: #4facfe;
      }

      .table-header {
        display: none;
      }
    }
  </style>

  <script>
    function exportToPDF() {
      alert('Fitur export PDF akan segera tersedia!');
      // Implementasi export PDF
    }

    function exportToExcel() {
      alert('Fitur export Excel akan segera tersedia!');
      // Implementasi export Excel
    }

    function printReport() {
      window.print();
    }

    // Auto submit form on filter change
    document.querySelectorAll('.filter-form select').forEach(select => {
      select.addEventListener('change', function() {
        // Optional: auto submit on change
        // this.form.submit();
      });
    });
  </script>
</body>
</html>