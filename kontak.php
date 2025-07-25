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
