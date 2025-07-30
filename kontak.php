<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pesan = $_POST['pesan'];

    // Contoh: Kirim ke email admin
    $to = "tahfeedz@unimus.ac.id";
    $subject = "Pesan dari: $nama";
    $body = "Email: $email\n\nPesan:\n$pesan";

    if (mail($to, $subject, $body)) {
        echo "Pesan berhasil dikirim!";
    } else {
        echo "Gagal mengirim pesan.";
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Kontak | TAHFEEDZ</title>
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
</head>
<body>
  <header class="navbar">
    <img src="Foto/logotahfeedz1.png" alt="Logo" class="logo" />
    <div class="logo"></div>
    <nav>
      <a href="index.html">Home</a>
      <a href="tentang.html">Tentang</a>
      <a href="kontak.php" class="active">Kontak</a>
    </nav>
  </header>

  <div class="container">
    <h2>Hubungi Kami</h2>
    <p>Silakan hubungi kami jika ada pertanyaan, masukan, atau ingin bekerja sama dengan TAHFEEDZ.</p>
    <form action="kontak.php" method="post">
      <label for="nama">Nama Lengkap</label>
      <input type="text" id="nama" name="nama" required />
      <label for="email">Email</label>
      <input type="email" id="email" name="email" required />
      <label for="pesan">Pesan</label>
      <textarea id="pesan" name="pesan" rows="5" required></textarea>
      <button type="submit">Kirim Pesan</button>
    </form>
  </div>

  <footer>
  <p>&copy; 2025 TAHFEEDZ UNIMUS. All rights reserved.</p>
  <div class="socials">
    <p>Hubungi kami:</p>
    <ul>
      <li>Email: tahfeedz@unimus.ac.id</li>
      <li>GitHub: <a href="https://github.com/Gibranbintang/tahfeedz.git" target="_blank">github.com/tahfeedz</a></li>
    </ul>
  </div>
</footer>

</body>
</html>

