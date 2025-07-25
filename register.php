
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Registrasi | TAHFEEDZ</title>
<script src="https://cdn.tailwindcss.com">
  fetch('http://localhost/get_users.php')
      .then(response => response.json())
      .then(data => {
        let html = '';
        data.forEach(user => {
          html += `<p>${user["Nama Lengkap"]} (${user.Email})</p>`;
        });
        document.getElementById('user-list').innerHTML = html;
      })
      .catch(error => {
        console.error('Error:', error);
      });
</script>
</head> 
<body class="bg-gradient-to-br from-blue-800 to-cyan-700 min-h-screen flex items-center justify-center">

  <div class="bg-white p-8 rounded-2xl shadow-lg w-full max-w-md">
    <div class="text-center mb-6">
      <img src="logotahfeedz1.png" alt="TAHFEEDZ Logo" class="mx-auto w-16 h-16 mb-2" />
      <h1 class="text-2xl font-bold text-gray-800">Daftar di <span class="text-cyan-600">TAHFEEDZ</span></h1>
      <p class="text-sm text-gray-500 mt-1">Silakan isi data untuk membuat akun</p>
    </div>

    <form>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Nama Lengkap</label>
        <input type="text" placeholder="Masukkan nama lengkap" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Email</label>
        <input type="email" placeholder="Masukkan email aktif" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Username</label>
        <input type="text" placeholder="Masukkan username" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
      </div>
      <div class="mb-4">
        <label class="block text-gray-700 text-sm font-medium mb-1">Password</label>
        <input type="password" placeholder="Masukkan password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
      </div>
      <div class="mb-6">
        <label class="block text-gray-700 text-sm font-medium mb-1">Konfirmasi Password</label>
        <input type="password" placeholder="Ulangi password" class="w-full px-4 py-2 border border-gray-300 rounded-lg focus:outline-none focus:ring-2 focus:ring-cyan-500">
      </div>


      <button type="submit" class="w-full bg-cyan-600 hover:bg-cyan-700 text-white font-semibold py-2 rounded-lg transition duration-300">
        Daftar Sekarang
      </button>

    </form>

    <p class="text-center text-sm text-gray-600 mt-6">
      Sudah punya akun? <a href="login.html" class="text-cyan-600 hover:underline">Login di sini</a>
    </p>
  </div>

</body>
</html>


<!DOCTYPE html>
<html>
<head>
  <title>Data User</title>
</head>
<body>
  <h1></h1>
  <div id="user-list"></div>

  <script>
    fetch('http://localhost/get_users.php')
      .then(response => response.json())
      .then(data => {
        let html = '';
        data.forEach(user => {
          html += `<p>${user["Nama Lengkap"]} (${user.Email})</p>`;
        });
        document.getElementById('user-list').innerHTML = html;
      })
      .catch(error => {
        console.error('Error:', error);
      });
  </script>
</body>
</html>
