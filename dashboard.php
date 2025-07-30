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
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Dashboard | TAHFEEDZ</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gray-100">
  
  <!-- Header -->
  <header class="bg-white shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex justify-between h-16">
        <div class="flex items-center">
          <img src="logotahfeedz1.png" alt="TAHFEEDZ" class="h-8 w-8">
          <span class="ml-2 text-xl font-semibold text-gray-900">TAHFEEDZ</span>
        </div>
        <div class="flex items-center space-x-4">
          <span class="text-gray-700">Selamat datang, <?php echo htmlspecialchars($nama); ?>!</span>
          <a href="logout.php" class="bg-red-600 hover:bg-red-700 text-white px-4 py-2 rounded-lg transition duration-300">
            Logout
          </a>
        </div>
      </div>
    </div>
  </header>

  <!-- Navigation Menu -->
  <nav class="bg-cyan-600 shadow-sm">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
      <div class="flex space-x-8">
        <a href="dashboard.php" class="text-white hover:text-cyan-200 px-3 py-4 text-sm font-medium border-b-2 border-cyan-400">
          Dashboard
        </a>
        <a href="rekaphafalan.php" class="text-cyan-200 hover:text-white px-3 py-4 text-sm font-medium">
          Rekap Hafalan
        </a>
        <a href="profile.php" class="text-cyan-200 hover:text-white px-3 py-4 text-sm font-medium">
          Profil
        </a>
      </div>
    </div>
  </nav>

  <!-- Content -->
  <main class="max-w-7xl mx-auto py-6 sm:px-6 lg:px-8">
    <div class="px-4 py-6 sm:px-0">
      
      <!-- Welcome Card -->
      <div class="bg-white overflow-hidden shadow rounded-lg mb-6">
        <div class="px-4 py-5 sm:p-6">
          <div class="text-center">
            <h1 class="text-3xl font-bold text-gray-900 mb-4">Dashboard TAHFEEDZ</h1>
            <p class="text-lg text-gray-600 mb-2">Selamat datang di sistem rekap hafalan Al-Quran</p>
            <p class="text-sm text-gray-500">
              <strong>Email:</strong> <?php echo htmlspecialchars($email); ?>
            </p>
            <p class="text-sm text-gray-500">
              <strong>User ID:</strong> <?php echo htmlspecialchars($user_id); ?>
            </p>
            <?php if ($user_data['created_at']): ?>
            <p class="text-sm text-gray-500">
              <strong>Bergabung sejak:</strong> <?php echo date('d F Y', strtotime($user_data['created_at'])); ?>
            </p>
            <?php endif; ?>
          </div>
        </div>
      </div>

      <!-- Stats Cards -->
      <div class="grid grid-cols-1 md:grid-cols-3 gap-6 mb-6">
        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-cyan-500 rounded-md flex items-center justify-center">
                  <span class="text-white text-sm font-bold">📖</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Total Hafalan</dt>
                  <dd class="text-lg font-medium text-gray-900">0 Ayat</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-green-500 rounded-md flex items-center justify-center">
                  <span class="text-white text-sm font-bold">✓</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Selesai Hari Ini</dt>
                  <dd class="text-lg font-medium text-gray-900">0 Ayat</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>

        <div class="bg-white overflow-hidden shadow rounded-lg">
          <div class="p-5">
            <div class="flex items-center">
              <div class="flex-shrink-0">
                <div class="w-8 h-8 bg-yellow-500 rounded-md flex items-center justify-center">
                  <span class="text-white text-sm font-bold">⭐</span>
                </div>
              </div>
              <div class="ml-5 w-0 flex-1">
                <dl>
                  <dt class="text-sm font-medium text-gray-500 truncate">Streak Hari</dt>
                  <dd class="text-lg font-medium text-gray-900">0 Hari</dd>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>

      <!-- Quick Actions -->
      <div class="bg-white overflow-hidden shadow rounded-lg">
        <div class="px-4 py-5 sm:p-6">
          <h3 class="text-lg leading-6 font-medium text-gray-900 mb-4">Aksi Cepat</h3>
          <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
            <a href="rekaphafalan.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-300">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <span class="text-2xl">📝</span>
                </div>
                <div class="ml-4">
                  <h4 class="text-lg font-medium text-gray-900">Tambah Rekap Hafalan</h4>
                  <p class="text-sm text-gray-500">Catat progress hafalan Anda hari ini</p>
                </div>
              </div>
            </a>
            
            <a href="profile.php" class="block p-4 border border-gray-200 rounded-lg hover:bg-gray-50 transition duration-300">
              <div class="flex items-center">
                <div class="flex-shrink-0">
                  <span class="text-2xl">👤</span>
                </div>
                <div class="ml-4">
                  <h4 class="text-lg font-medium text-gray-900">Edit Profil</h4>
                  <p class="text-sm text-gray-500">Perbarui informasi profil Anda</p>
                </div>
              </div>
            </a>
          </div>
        </div>
      </div>

    </div>
  </main>

  <!-- Footer -->
  <footer class="bg-white border-t border-gray-200 mt-12">
    <div class="max-w-7xl mx-auto py-4 px-4 sm:px-6 lg:px-8">
      <div class="flex justify-center items-center">
        <img src="logotahfeedz1.png" alt="TAHFEEDZ" class="h-6 w-6 mr-2">
        <p class="text-sm text-gray-500">
          © 2025 TAHFEEDZ - PPTI UNIMUS. Semua hak dilindungi.
        </p>
      </div>
    </div>
  </footer>

</body>
</html>