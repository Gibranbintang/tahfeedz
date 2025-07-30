<?php
$host = "localhost";
$username = "root";
$password = "";
$database = "tahfeedz";
$port = 3306;

$koneksi = mysqli_connect($host, $username, $password, $database, $port);

if(!$koneksi) {
    die("Koneksi Gagal: " . mysqli_connect_error());
}

mysqli_set_charset($koneksi, "utf8");
?>