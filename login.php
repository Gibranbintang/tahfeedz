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
    
    // Validasi input
    if (empty($email) || empty($password)) {
        $error = "Email dan password harus diisi.";
    } else {
        // Panggil fungsi login
        $loginResult = loginUser($email, $password);
        
        if ($loginResult['status'] === 'success') {
            $success = $loginResult['message'];
            // Redirect dengan JavaScript setelah 1 detik
            echo "<script>
                setTimeout(function() {
                    window.location.href = 'dashboard.php';
                }, 1000);
            </script>";
        } else {
            $error = $loginResult['message'];
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | TAHFEEDZ</title>
  <link rel="stylesheet" href="css/auth-style.css">
</head>
<body class="bg-gradient-to-br from-cyan-700 to-blue-900 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <!-- Logo dan Judul -->
    <div class="text-center mb-6">
      <h1 class="text-2xl font-bold text-gray-800">
        Masuk ke <span class="text-cyan-600">TAHFEEDZ</span>
      </h1>
      <p class="text-sm text-gray-500 mt-1">Silakan login untuk melanjutkan</p>
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
        <div class="text-sm mt-1">Mengalihkan ke dashboard...</div>
      </div>
    <?php endif; ?>

    <!-- Form Login -->
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Email</label>
        <input 
          type="email" 
          name="email"
          value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>"
          placeholder="Masukkan email Anda" 
          required 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Password</label>
        <input 
          type="password" 
          name="password"
          placeholder="Masukkan password" 
          required 
          class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500"
        >
      </div>

      <button 
        type="submit" 
        class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-bold py-2 rounded-lg transition duration-300"
      >
        Login
      </button>
    </form>

    <!-- Link Daftar -->
    <p class="text-center text-sm text-gray-500 mt-6">
      Belum punya akun? 
      <a href="register.php" class="text-cyan-600 hover:underline">Daftar di sini</a>
    </p>
    
    <!-- Link Kembali ke Home -->
    <p class="text-center text-sm text-gray-500 mt-2">
      <a href="index.html" class="text-gray-600 hover:underline">← Kembali ke Beranda</a>
    </p>
  </div>

</body>
</html>