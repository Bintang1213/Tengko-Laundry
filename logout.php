<?php
// Memulai session
session_start();

// Menghapus semua session variables
session_unset();

// Menghancurkan session
session_destroy();

// Arahkan pengguna kembali ke halaman login
header("Location: login.php");
exit();
?>
