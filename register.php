<?php
session_start();
include 'fungsi.php';

$error = "";
$success = "";

// Jika user sudah login, redirect ke dashboard
if (isLoggedIn()) {
    header("Location: dashboard.php");
    exit;
}

if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $email = $_POST['email'] ?? '';
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    $nama = $_POST['nama'] ?? '';
    
    // Validasi input
    if (empty($email) || empty($password) || empty($confirm_password)) {
        $error = "Email, password, dan konfirmasi password harus diisi.";
    } elseif ($password !== $confirm_password) {
        $error = "Password dan konfirmasi password tidak cocok.";
    } elseif (strlen($password) < 6) {
        $error = "Password minimal 6 karakter.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = "Format email tidak valid.";
    } else {
        // Panggil fungsi register
        $registerResult = registerUser($email, $password, $nama);
        
        if ($registerResult['status'] === 'success') {
            $success = $registerResult['message'] . " Silakan login dengan akun Anda.";
            // Reset form setelah berhasil
            $_POST = array();
        } else {
            $error = $registerResult['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Daftar | TAHFEEDZ</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-cyan-700 to-blue-900 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <!-- Logo dan Judul -->
    <div class="text-center mb-6">
      <img src="logotahfeedz1.png" alt="TAHFEEDZ Logo" class="mx-auto w-16 h-16 mb-2" />
      <h1 class="text-2xl font-bold text-gray-800">
        Daftar ke <span class="text-cyan-600">TAHFEEDZ</span>
      </h1>
      <p class="text-sm text-gray-500 mt-1">Buat akun baru untuk melanjutkan</p>
    </div>

    <!-- Pesan Error dan Success -->
    <?php if (!empty($error)): ?>
      <div class="mb-4 p-3 bg-red-100 border border-red-400 text-red-700 rounded-lg">
        <?php echo htmlspecialchars($error); ?>
      </div>
    <?php endif; ?>

    <?php if (!empty($success)): ?>
      <div class="mb-4 p-3 bg-green-100 border border-green-400 text-green-700 rounded-lg">
        <?php echo htmlspecialchars($success); ?>
      </div>
    <?php endif; ?>

    <!-- Form Register -->
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Nama (Opsional)</label>
        <input 
          type="text" 
          name="nama"
          value="<?php echo isset($_POST['nama']) ? htmlspecialchars($_POST['nama']) : ''; ?>"
          placeholder="Masukkan nama Anda" 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Email *</label>
        <input 
          type="email" 
          name="email"
          value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          placeholder="Masukkan email Anda" 
          required 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Password *</label>
        <input 
          type="password" 
          name="password"
          placeholder="Minimal 6 karakter" 
          required 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Konfirmasi Password *</label>
        <input 
          type="password" 
          name="confirm_password"
          placeholder="Ulangi password" 
          required 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <button 
        type="submit" 
        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 rounded-lg transition duration-300"
      >
        Daftar
      </button>
    </form>

    <!-- Link Login -->
    <p class="text-center text-sm text-gray-500 mt-6">
      Sudah punya akun? 
      <a href="login.php" class="text-cyan-600 hover:underline">Login di sini</a>
    </p>
    
    <!-- Link Kembali ke Home -->
    <p class="text-center text-sm text-gray-500 mt-2">
      <a href="index.html" class="text-gray-600 hover:underline">← Kembali ke Beranda</a>
    </p>
  </div>

</body>
</html>