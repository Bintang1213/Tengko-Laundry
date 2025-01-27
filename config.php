<?php
$host = 'localhost';
$user = 'root';
$password = '';
$dbname = 'laundry'; 

// Membuat koneksi
$conn = new mysqli($host, $user, $password, $dbname);

// Memeriksa koneksi
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
?>
