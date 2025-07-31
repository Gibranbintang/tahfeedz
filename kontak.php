<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nama = $_POST['nama'];
    $email = $_POST['email'];
    $pesan = $_POST['pesan'];

    // Contoh: Kirim ke email admin
    $to = "tahfeedzcorp@gmail.com";
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
  <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600;700&display=swap" rel="stylesheet" />
  <style>
    * {
      margin: 0;
      padding: 0;
      box-sizing: border-box;
    }

    body {
      font-family: 'Inter', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
      background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
      min-height: 100vh;
      overflow-x: hidden;
      color: #2d3748;
    }

    /* Main Container */
    .main-container {
      min-height: 100vh;
      background: rgba(255,255,255,0.95);
      backdrop-filter: blur(10px);
      position: relative;
      overflow: hidden;
    }

    .main-container::before {
      content: '';
      position: absolute;
      top: -50%;
      right: -50%;
      width: 100%;
      height: 100%;
      background: radial-gradient(circle, rgba(79, 172, 254, 0.1) 0%, transparent 70%);
      pointer-events: none;
      animation: float 6s ease-in-out infinite;
    }

    @keyframes float {
      0%, 100% { transform: translateY(0px) rotate(0deg); }
      50% { transform: translateY(-20px) rotate(180deg); }
    }

    /* Navbar */
    .navbar {
      display: flex;
      justify-content: space-between;
      align-items: center;
      padding: 1.5rem 3rem;
      background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
      box-shadow: 0 4px 20px rgba(0,0,0,0.1);
      position: relative;
      z-index: 10;
    }

    .navbar::after {
      content: '';
      position: absolute;
      bottom: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }

    .navbar .logo {
      height: 50px;
      transition: all 0.3s ease;
      filter: drop-shadow(0 2px 4px rgba(0,0,0,0.3));
    }

    .navbar .logo:hover {
      transform: scale(1.05);
    }

    .navbar nav {
      display: flex;
      gap: 2rem;
    }

    .navbar nav a {
      color: white;
      text-decoration: none;
      font-weight: 600;
      padding: 0.8rem 1.5rem;
      border-radius: 50px;
      transition: all 0.3s ease;
      position: relative;
      overflow: hidden;
    }

    .navbar nav a::before {
      content: '';
      position: absolute;
      top: 0;
      left: -100%;
      width: 100%;
      height: 100%;
      background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
      transition: left 0.5s;
    }

    .navbar nav a:hover::before {
      left: 100%;
    }

    .navbar nav a:hover,
    .navbar nav a.active {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      transform: translateY(-2px);
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }

    /* Container */
    .container {
      max-width: 800px;
      margin: 0 auto;
      padding: 4rem 2rem;
      position: relative;
      z-index: 2;
      animation: fadeInUp 0.8s ease forwards;
      opacity: 0;
    }

    .container h2 {
      font-size: 3rem;
      font-weight: 700;
      text-align: center;
      margin-bottom: 1rem;
      color: #2d3748;
    }

    .container h2::after {
      content: '';
      display: block;
      width: 100px;
      height: 4px;
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      margin: 1rem auto;
      border-radius: 2px;
    }

    .container > p {
      font-size: 1.2rem;
      color: #4a5568;
      text-align: center;
      line-height: 1.8;
      margin-bottom: 3rem;
      max-width: 600px;
      margin-left: auto;
      margin-right: auto;
    }

    /* Form Styling */
    form {
      background: rgba(255, 255, 255, 0.9);
      backdrop-filter: blur(10px);
      padding: 3rem;
      border-radius: 20px;
      box-shadow: 0 20px 40px rgba(0,0,0,0.1);
      border: 1px solid rgba(255,255,255,0.2);
      position: relative;
      overflow: hidden;
    }

    form::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }

    label {
      display: block;
      font-weight: 600;
      font-size: 1.1rem;
      color: #2d3748;
      margin-bottom: 0.8rem;
      margin-top: 1.5rem;
    }

    label:first-of-type {
      margin-top: 0;
    }

    input[type="text"],
    input[type="email"],
    textarea {
      width: 100%;
      padding: 1.2rem 1.5rem;
      border: 2px solid #e2e8f0;
      border-radius: 12px;
      font-size: 1rem;
      font-family: inherit;
      background: rgba(255, 255, 255, 0.8);
      transition: all 0.3s ease;
      resize: vertical;
    }

    input[type="text"]:focus,
    input[type="email"]:focus,
    textarea:focus {
      outline: none;
      border-color: #4facfe;
      background: rgba(255, 255, 255, 1);
      box-shadow: 0 0 0 3px rgba(79, 172, 254, 0.1);
      transform: translateY(-2px);
    }

    textarea {
      min-height: 150px;
      font-family: inherit;
    }

    button[type="submit"] {
      background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
      color: white;
      border: none;
      padding: 1.2rem 3rem;
      border-radius: 50px;
      font-size: 1.1rem;
      font-weight: 600;
      cursor: pointer;
      transition: all 0.3s ease;
      margin-top: 2rem;
      position: relative;
      overflow: hidden;
      display: block;
      margin-left: auto;
      margin-right: auto;
      box-shadow: 0 4px 15px rgba(79, 172, 254, 0.3);
    }

    button[type="submit"]::before {
      content: '';
      position: absolute;
      top: 50%;
      left: 50%;
      width: 0;
      height: 0;
      background: rgba(255,255,255,0.2);
      transition: all 0.3s ease;
      border-radius: 50%;
      transform: translate(-50%, -50%);
    }

    button[type="submit"]:hover::before {
      width: 300px;
      height: 300px;
    }

    button[type="submit"]:hover {
      transform: translateY(-3px);
      box-shadow: 0 8px 25px rgba(79, 172, 254, 0.4);
    }

    /* Footer */
    footer {
      background: linear-gradient(180deg, #2d3748 0%, #1a202c 100%);
      color: white;
      padding: 3rem 2rem;
      position: relative;
      z-index: 2;
      margin-top: auto;
    }

    footer::before {
      content: '';
      position: absolute;
      top: 0;
      left: 0;
      right: 0;
      height: 4px;
      background: linear-gradient(90deg, #4facfe 0%, #00f2fe 100%);
    }

    footer p {
      text-align: center;
      font-size: 1rem;
      margin-bottom: 2rem;
    }

    .socials {
      max-width: 600px;
      margin: 0 auto;
      text-align: center;
    }

    .socials p {
      font-size: 1.2rem;
      font-weight: 600;
      margin-bottom: 1rem;
      color: #4facfe;
    }

    .socials ul {
      list-style: none;
      display: flex;
      justify-content: center;
      gap: 2rem;
      flex-wrap: wrap;
    }

    .socials ul li {
      font-size: 1rem;
      padding: 0.5rem 1rem;
      background: rgba(79, 172, 254, 0.1);
      border-radius: 25px;
      transition: all 0.3s ease;
    }

    .socials ul li:hover {
      background: rgba(79, 172, 254, 0.2);
      transform: translateY(-2px);
    }

    .socials ul li a {
      color: #4facfe;
      text-decoration: none;
      font-weight: 600;
      transition: all 0.3s ease;
    }

    .socials ul li a:hover {
      color: #00f2fe;
    }

    /* Success/Error Messages */
    .message {
      position: fixed;
      top: 20px;
      right: 20px;
      padding: 1rem 2rem;
      border-radius: 10px;
      font-weight: 600;
      z-index: 1000;
      animation: slideInRight 0.5s ease;
    }

    .message.success {
      background: linear-gradient(135deg, #48bb78 0%, #38a169 100%);
      color: white;
    }

    .message.error {
      background: linear-gradient(135deg, #f56565 0%, #e53e3e 100%);
      color: white;
    }

    /* Animations */
    @keyframes fadeInUp {
      from {
        opacity: 0;
        transform: translateY(30px);
      }
      to {
        opacity: 1;
        transform: translateY(0);
      }
    }

    @keyframes slideInRight {
      from {
        transform: translateX(100%);
        opacity: 0;
      }
      to {
        transform: translateX(0);
        opacity: 1;
      }
    }

    /* Responsive Design */
    @media (max-width: 768px) {
      .navbar {
        padding: 1rem 1.5rem;
        flex-direction: column;
        gap: 1rem;
      }

      .navbar nav {
        gap: 1rem;
      }

      .navbar nav a {
        padding: 0.6rem 1rem;
        font-size: 0.9rem;
      }

      .container {
        padding: 2rem 1rem;
      }

      .container h2 {
        font-size: 2.2rem;
      }

      .container > p {
        font-size: 1rem;
      }

      form {
        padding: 2rem 1.5rem;
      }

      .socials ul {
        flex-direction: column;
        align-items: center;
      }
    }

    @media (max-width: 480px) {
      .container h2 {
        font-size: 1.8rem;
      }

      form {
        padding: 1.5rem 1rem;
      }

      button[type="submit"] {
        width: 100%;
        padding: 1rem 2rem;
      }

      .navbar nav {
        flex-wrap: wrap;
        justify-content: center;
      }
    }

    /* Focus states for accessibility */
    input:focus,
    textarea:focus,
    button:focus {
      outline: 2px solid #4facfe;
      outline-offset: 2px;
    }

    /* Smooth scrolling */
    html {
      scroll-behavior: smooth;
    }
  </style>
</head>
<body>
  <div class="main-container">
    <header class="navbar">
      <img src="Foto/logotahfeedz1.png" alt="Logo" class="logo" />
      <div class="logo"></div>
      <nav>
        <a href="index.html"><i class="fas fa-home"></i> Home</a>
        <a href="tentang.html"><i class="fas fa-info-circle"></i> Tentang</a>
        <a href="kontak.php" class="active"><i class="fas fa-envelope"></i> Kontak</a>
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
      <p>&copy; Tahfeedz Corporation.</p>
      <div class="socials">
        <p>Hubungi kami:</p>
        <ul>
          <li>Email: tahfeedzcorp@gmail.com</li>
          <li>GitHub: <a href="https://github.com/Gibranbintang/tahfeedz.git" target="_blank">github.com/tahfeedz</a></li>
        </ul>
      </div>
    </footer>
  </div>

  <script>
    // Enhanced form functionality
    document.addEventListener("DOMContentLoaded", () => {
      initializeContactPage();
      addFormInteractions();
    });

    function initializeContactPage() {
      // Add form validation animations
      const inputs = document.querySelectorAll('input, textarea');
      inputs.forEach(input => {
        input.addEventListener('blur', () => {
          if (input.value.trim() === '') {
            input.style.borderColor = '#f56565';
            input.style.boxShadow = '0 0 0 3px rgba(245, 101, 101, 0.1)';
          } else {
            input.style.borderColor = '#48bb78';
            input.style.boxShadow = '0 0 0 3px rgba(72, 187, 120, 0.1)';
          }
        });

        input.addEventListener('focus', () => {
          input.style.borderColor = '#4facfe';
          input.style.boxShadow = '0 0 0 3px rgba(79, 172, 254, 0.1)';
        });
      });
    }

    function addFormInteractions() {
      const form = document.querySelector('form');
      const submitButton = document.querySelector('button[type="submit"]');

      // Add loading state to submit button
      form.addEventListener('submit', () => {
        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Mengirim...';
        submitButton.disabled = true;
      });

      // Add ripple effect to submit button
      submitButton.addEventListener('click', (e) => {
        const ripple = document.createElement('span');
        const rect = submitButton.getBoundingClientRect();
        const size = Math.max(rect.width, rect.height);
        const x = e.clientX - rect.left - size / 2;
        const y = e.clientY - rect.top - size / 2;
        
        ripple.style.cssText = `
          position: absolute;
          width: ${size}px;
          height: ${size}px;
          left: ${x}px;
          top: ${y}px;
          background: rgba(255, 255, 255, 0.3);
          border-radius: 50%;
          transform: scale(0);
          animation: ripple 0.6s linear;
          pointer-events: none;
        `;
        
        submitButton.appendChild(ripple);
        
        setTimeout(() => {
          ripple.remove();
        }, 600);
      });

      // Add character count for textarea
      const textarea = document.querySelector('textarea');
      const charCount = document.createElement('div');
      charCount.style.cssText = `
        text-align: right;
        font-size: 0.9rem;
        color: #718096;
        margin-top: 0.5rem;
      `;
      textarea.parentNode.insertBefore(charCount, textarea.nextSibling);

      textarea.addEventListener('input', () => {
        const count = textarea.value.length;
        charCount.textContent = `${count} karakter`;
        
        if (count > 500) {
          charCount.style.color = '#f56565';
        } else if (count > 400) {
          charCount.style.color = '#ed8936';
        } else {
          charCount.style.color = '#718096';
        }
      });
    }

    // Add CSS animations for ripple effect
    const style = document.createElement('style');
    style.textContent = `
      @keyframes ripple {
        to {
          transform: scale(4);
          opacity: 0;
        }
      }

      button[type="submit"] {
        position: relative;
        overflow: hidden;
      }
    `;
    document.head.appendChild(style);
  </script>
</body>
</html>