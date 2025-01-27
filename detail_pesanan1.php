<?php
// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil ID pesanan dari URL
if (!isset($_GET['id_pesanan'])) {
    echo "ID Pesanan tidak ditemukan.";
    exit();
}

$id_pesanan = $_GET['id_pesanan'];

// Koneksi ke database
include('config.php');

// Query untuk mendapatkan detail pesanan dan metode pembayaran
$sql = "SELECT 
            p.id_pesanan, 
            p.tanggal_pesanan, 
            l.nama_layanan, 
            p.harga_per_kg AS harga, 
            p.berat, 
            p.total_price AS total_bayar, 
            p.status, 
            p.alamat_pengiriman, 
            p.jadwal_pengantaran, 
            p.jenis_layanan,
            t.metode_pembayaran  -- Mengambil metode pembayaran
        FROM pesanan p
        JOIN layanan_laundry l ON p.id_layanan = l.id_layanan
        JOIN transaksi t ON p.id_pesanan = t.id_pesanan  -- Join dengan tabel transaksi
        WHERE p.id_pesanan = ?";

$stmt = $conn->prepare($sql);
if (!$stmt) {
    die("Error preparing statement: " . $conn->error);
}
$stmt->bind_param("i", $id_pesanan);
$stmt->execute();
$result = $stmt->get_result();

// Periksa apakah data ditemukan
if ($result->num_rows == 0) {
    die("Pesanan dengan ID tersebut tidak ditemukan.");
}

$detail = $result->fetch_assoc();
$conn->close();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <link rel="stylesheet" href="s.css">
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Detail Pesanan</title>
</head>
<body>
    <div class="order-detail-container">
        <h1>Detail Pesanan</h1>
        <div class="order-summary">
            <div class="info">
                <span>ID Pesanan:</span>
                <p><?php echo $detail['id_pesanan'] ?? '-'; ?></p>
            </div>
            <div class="info">
                <span>Tanggal Pemesanan:</span>
                <p><?php echo isset($detail['tanggal_pesanan']) ? date('d-m-Y H:i', strtotime($detail['tanggal_pesanan'])) : '-'; ?></p>
            </div>
            <div class="info">
                <span>Layanan:</span>
                <p><?php echo $detail['nama_layanan'] ?? '-'; ?></p>
            </div>
            <div class="info">
                <span>Harga per Kg:</span>
                <p>Rp<?php echo isset($detail['harga']) ? number_format($detail['harga'], 0, ',', '.') : '0'; ?></p>
            </div>
            <div class="info">
                <span>Berat:</span>
                <p><?php echo $detail['berat'] ?? '0'; ?> Kg</p>
            </div>
            <div class="info">
                <span>Total Bayar:</span>
                <p>Rp<?php echo isset($detail['total_bayar']) ? number_format($detail['total_bayar'], 0, ',', '.') : '0'; ?></p>
            </div>
            <div class="info">
                <span>Status:</span>
                <p class="status <?php echo strtolower($detail['status'] ?? ''); ?>">
                    <?php echo $detail['status'] ?? '-'; ?>
                </p>
            </div>
            <div class="info">
                <span>Alamat Pengiriman:</span>
                <p><?php echo $detail['alamat_pengiriman'] ?? '-'; ?></p>
            </div>
            <div class="info">
                <span>Jadwal Pengantaran:</span>
                <p><?php echo isset($detail['jadwal_pengantaran']) ? date('d-m-Y H:i', strtotime($detail['jadwal_pengantaran'])) : '-'; ?></p>
            </div>
            <div class="info">
                <span>Jenis Layanan:</span>
                <p><?php echo $detail['jenis_layanan'] ?? '-'; ?></p>
            </div>
            <div class="info">
                <span>Metode Pembayaran:</span>
                <p><?php echo $detail['metode_pembayaran'] ?? '-'; ?></p>
            </div>
        </div>
        <a href="dashboard.php" class="back-button">Kembali ke Dashboard</a>
    </div>
</body>
</html>
