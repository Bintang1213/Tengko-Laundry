<?php
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include('config.php');

// Ambil ID pengguna dari session
$user_id = $_SESSION['user_id'];

// Ambil data pengguna dari database berdasarkan ID pengguna
$user_sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $user_id);
$user_stmt->execute();
$user_result = $user_stmt->get_result();

// Pastikan data pengguna ditemukan
if ($user_result->num_rows > 0) {
    $user = $user_result->fetch_assoc();
} else {
    echo "Pengguna tidak ditemukan.";
    exit();
}

// Ambil ID layanan dari URL
if (isset($_GET['layanan_id'])) {
    $layanan_id = $_GET['layanan_id'];

    // Ambil data layanan berdasarkan ID
    $layanan_sql = "SELECT * FROM layanan_laundry WHERE id_layanan = ?";
    $layanan_stmt = $conn->prepare($layanan_sql);
    $layanan_stmt->bind_param("i", $layanan_id);
    $layanan_stmt->execute();
    $layanan_result = $layanan_stmt->get_result();

    if ($layanan_result->num_rows > 0) {
        $layanan = $layanan_result->fetch_assoc();
    } else {
        echo "Layanan tidak ditemukan.";
        exit();
    }
} else {
    echo "ID Layanan tidak ditemukan.";
    exit();
}

// Inisialisasi harga total
$total_harga = 0;
$harga_per_kg = $layanan['harga']; // Mengambil harga per kg dari layanan

// Format uang Rupiah
function format_rupiah($angka)
{
    return 'Rp ' . number_format($angka, 2, ',', '.');
}

// Proses pemesanan jika form disubmit
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $tanggal_pesanan = date('Y-m-d H:i:s');
    $status = 'Menunggu';
    $berat = $_POST['berat'];
    $jenis_layanan = $_POST['jenis_layanan'];
    $alamat_pengiriman = ($jenis_layanan === 'antar') ? $_POST['alamat'] : null;
    $jadwal_pengantaran = ($jenis_layanan === 'antar') ? $_POST['jadwal_pengantaran'] : null; // Jadwal hanya untuk antar
    $total_harga = $harga_per_kg * $berat; // Menghitung total harga berdasarkan berat
    $metode_pembayaran = $_POST['metode_pembayaran'];
    // Masukkan data pesanan ke database
    $pesanan_sql = "INSERT INTO pesanan (id_pengguna, id_layanan, tanggal_pesanan, status, total_price, berat, harga_per_kg, alamat_pengiriman, jadwal_pengantaran, jenis_layanan) 
                VALUES (?, ?, NOW(), ?, ?, ?, ?, ?, ?, ?)";
    $pesanan_stmt = $conn->prepare($pesanan_sql);
    $pesanan_stmt->bind_param("iissddsss", $user_id, $layanan_id, $status, $total_harga, $berat, $harga_per_kg, $alamat_pengiriman, $jadwal_pengantaran, $jenis_layanan);

    if ($pesanan_stmt->execute()) {
        $order_id = $conn->insert_id;
    
        // Simpan data transaksi dengan status pembayaran 'Belum Bayar'
        $transaksi_sql = "INSERT INTO transaksi (id_pesanan, metode_pembayaran, total_bayar, tgl_transaksi, status_pembayaran) 
              VALUES (?, ?, ?, NOW(), 'Belum Bayar')";
        $transaksi_stmt = $conn->prepare($transaksi_sql);
        $transaksi_stmt->bind_param("isd", $order_id, $metode_pembayaran, $total_harga);
        if ($transaksi_stmt->execute()) {
            if ($metode_pembayaran === "Tunai") {
                // Redirect langsung ke detail pesanan untuk pembayaran tunai
                $_SESSION['notif'] = "Pesanan berhasil dibuat. Silakan bayar di tempat.";
                header("Location: detail_pesanan1.php?id_pesanan=$order_id");
                exit();
            } else {
                require_once 'midtrans_config.php';
                // Ambil data customer dari tabel pengguna
                $customerDetails = [
                    'first_name' => $user['nama'],
                    'email' => $user['email'],
                    'phone' => $user['no_telepon'],
                    'address' => $user['alamat']
                ];
    
                // Detail transaksi untuk Midtrans
                $transactionDetails = [
                    'order_id' => $order_id,
                    'gross_amount' => $total_harga,
                ];
    
                $itemDetails = [
                    [
                        'id' => $layanan_id,
                        'price' => $harga_per_kg,
                        'quantity' => $berat,
                        'name' => $layanan['nama_layanan']
                    ]
                ];
    
                // Set parameter transaksi
                $params = [
                    'transaction_details' => $transactionDetails,
                    'item_details' => $itemDetails,
                    'customer_details' => $customerDetails,
                   'finish_redirect_url' => "http://jln9mhd4-80.asse.devtunnels.ms/project/detail_pesanan1.php?id_pesanan=$order_id"
                ];
    
                try {
                    $snapToken = \Midtrans\Snap::getSnapToken($params);
                    // Arahkan ke Midtrans untuk melakukan pembayaran
                    header("Location: https://app.sandbox.midtrans.com/snap/v2/vtweb/$snapToken");
                    exit();
                } catch (Exception $e) {
                    echo "Error: " . $e->getMessage();
                }
            }
        } else {
            echo "Terjadi kesalahan saat menyimpan transaksi.";
        }
    } else {
        echo "Terjadi kesalahan saat menyimpan pesanan, Coba lagi.";
    }
    
}

?>


<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="2.css">
    <title>Pemesanan Layanan</title>
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-wash'></i> <span class="text">Tengko Laundry</span>
        </a>
        <ul class="side-menu top">
            <li><a href="dashboard.php"><i class='bx bxs-shopping-bag'></i> <span class="text">Pesanan Saya</span></a></li>
            <li><a href="riwayat.php"><i class='bx bxs-history'></i> <span class="text">Riwayat</span></a></li>
            <li><a href="layanan.php"><i class='bx bxs-package'></i> <span class="text">Layanan</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="edit_profile.php"><i class='bx bxs-user'></i> <span class="text">Edit Profil</span></a></li>
            <li><a href="logout.php"><i class='bx bxs-log-out-circle'></i> <span class="text">Logout</span></a></li>
        </ul>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
        </nav>
        <main>
            <div class="head-title">
                <div class="form-group">
                    <label>Nama Layanan:</label>
                    <p><?= htmlspecialchars($layanan['nama_layanan'], ENT_QUOTES, 'UTF-8'); ?></p>
                </div>
            </div>
            <div class="order-form">
                <h3>Pesan Layanan</h3>
                <form action="pemesanan.php?layanan_id=<?= $layanan_id ?>" method="POST">
                    <div class="form-group">
                        <label for="berat">Berat (kg):</label>
                        <input type="number" name="berat" id="berat" value="1" min="1" required>
                    </div>
                    <div class="form-group">
                        <label for="jenis_layanan">Jenis Layanan:</label>
                        <select name="jenis_layanan" id="jenis_layanan" required>
                            <option value="ambil">Ambil</option>
                            <option value="antar">Antar</option>
                        </select>
                    </div>
                    <div id="alamat-container" class="form-group" style="display:none;">
                        <label for="alamat">Alamat Pengiriman:</label>
                        <input type="text" name="alamat" id="alamat">
                    </div>
                    <div id="jadwal-container" class="form-group" style="display:none;">
                        <label for="jadwal_pengantaran">Jadwal Pengantaran:</label>
                        <input type="datetime-local" name="jadwal_pengantaran" id="jadwal_pengantaran">
                    </div>
                    <div class="form-group">
                        <label for="metode_pembayaran">Metode Pembayaran:</label>
                        <select name="metode_pembayaran" id="metode_pembayaran" required>
                            <option value="Tunai">Tunai</option>
                            <option value="Non Tunai">Non Tunai</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Total Harga:</label>
                        <p id="total-harga"><?= format_rupiah($total_harga); ?></p>
                    </div>
                    <button type="submit" class="btn">Pesan Layanan</button>
                </form>
            </div>
        </main>
    </section>
    <script>
        document.getElementById('berat').addEventListener('input', function() {
            const berat = this.value;
            const hargaPerKg = <?= $harga_per_kg ?>;
            const totalHarga = berat * hargaPerKg;
            document.getElementById('total-harga').innerText = "Total: Rp " + totalHarga.toLocaleString();
        });

        document.getElementById('jenis_layanan').addEventListener('change', function() {
            const jenisLayanan = this.value;
            if (jenisLayanan === 'antar') {
                document.getElementById('alamat-container').style.display = 'block';
                document.getElementById('jadwal-container').style.display = 'block';
            } else {
                document.getElementById('alamat-container').style.display = 'none';
                document.getElementById('jadwal-container').style.display = 'none';
            }
        });
    </script>
</body>

</html>