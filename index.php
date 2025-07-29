<?php
    $koneksi = mysqli_connect("localhost:3306","root","","tahfeedz");

    if(!$koneksi)
    {
        die("koneksi Gagal!".mysqli_connect_error());
    }
    else{
        echo "Koneksi berhasil!!!";
    }




?>

<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>TAHFEEDZ - Rekap Hafalanmu disini!</title>
  <link rel="stylesheet" href="css/style.css" />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
</head>
<body>
  <header class="navbar">
    <img src="Foto/logotahfeedz1.png" alt="Logo" class="logo" />
    <div class="logo"></div>
    <nav>
      <a href="index.php" class="active">Home</a>
      <a href="tentang.html">Tentang</a>
      <a href="kontak.php">Kontak</a>
      <a href="dashboard.html">Dashboard</a>
    </nav>
  </header>

  <main class="hero">
    <div class="hero-text">
      <h1>Selamat Datang di <span class="highlight">TAHFEEDZ</span></h1>
      <p>
        Rekapan Hafalan atau Murojaan dan Informasi Seputar Al-Quran<br/>
        <strong>Pondok Pesantren Teknologi Informasi UNIMUS</strong>
      </p>
      <div class="buttons">
        <a href="login.php" class="btn">👥 Login </a>
        <a href="rekaphafalan.html" class="btn">📄 Rekap Hafalan</a>
      </div>
    </div>
    <div class="hero-image">
      <img src="Foto/kaligrafi2.png" alt="Dashboard App" />
    </div>
  </main>

  <footer class="footer">
    <img src="Foto/logotahfeedz1.png" alt="TAHFEEDZ" class="logo"/>
    <h2>PPTI UNIMUS <span class="subtext">TAHFEEDZ App</span></h2>
  </footer>
</body>
</html>
