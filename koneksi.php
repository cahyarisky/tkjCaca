<?php
$host = "localhost";
$user = "root";
$pass = ""; // Masukkan password root MariaDB jika ada
$db   = "db_pendaftaran";

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi Database Gagal: " . mysqli_connect_error());
}
?>
