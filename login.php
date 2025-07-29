<?php
include 'fungsi.php';
session_start();

$error = ""; // Menyimpan pesan kesalahan

if ($_SERVER["REQUEST_METHOD"] === "POST" && isset($_POST['email']) && isset($_POST['password'])) {
    $email = mysqli_real_escape_string($koneksi, $_POST['email']);
    $password = $_POST['password'];

    $sql = "SELECT * FROM users WHERE email='$email' LIMIT 1";
    $query = mysqli_query($koneksi, $sql);

    if (mysqli_num_rows($query) === 1) {
        $user = mysqli_fetch_assoc($query);

        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['email'] = $user['email'];
            header("Location: dashboard.html");
            exit;
        } else {
            $error = "Password salah.";
        }
    } else {
        $error = "Email tidak ditemukan.";
    }
}
?>



<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Login | TAHFEEDZ</title>
  <script src="https://cdn.tailwindcss.com"></script>
</head>
<body class="bg-gradient-to-br from-cyan-700 to-blue-900 min-h-screen flex items-center justify-center">

  <?php if (!empty($error)): ?>
        <p style="color:red;"><?php echo $error; ?></p>
    <?php endif; ?>


  <div class="bg-white p-8 rounded-2xl shadow-xl w-full max-w-md">
    <!-- Logo dan Judul -->
    <div class="text-center mb-6">
      <img src="logotahfeedz1.png" alt="TAHFEEDZ Logo" class="mx-auto w-16 h-16 mb-2" />
      <h1 class="text-2xl font-bold text-gray-800">
        Masuk ke <span class="text-cyan-600">TAHFEEDZ</span>
      </h1>
      <p class="text-sm text-gray-500 mt-1">Silakan login untuk melanjutkan</p>
    </div>

    <!-- Form Login -->
    <form method="POST" action="">
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-semibold mb-2">Email atau Username</label>
        <input 
          type="email" 
          name="email"
          placeholder="Email" 
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
  </div>

</body>
</html>


