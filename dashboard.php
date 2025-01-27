<?php
session_start();

// Cek apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

if (isset($_SESSION['success_message'])) {
    echo "<div class='success-message'>" . htmlspecialchars($_SESSION['success_message']) . "</div>";
    unset($_SESSION['success_message']); // Hapus pesan setelah ditampilkan
}

require_once 'midtrans_config.php';
// Ambil data pengguna dari session
$nama = $_SESSION['nama'];
$user_id = $_SESSION['user_id'];

// Koneksi ke database
include('config.php');

// Ambil data pengguna termasuk gambar profil dari database
$sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Cek apakah ada gambar profil, jika tidak set gambar default
$profile_image = $user_data['gambar_profil'] ? "uploads/" . $user_data['gambar_profil'] : "img/default-profile.png";

// Ambil daftar pesanan pengguna dengan harga layanan dan jenis layanan
$order_sql = "SELECT p.id_pesanan, p.tanggal_pesanan, l.nama_layanan, l.harga, p.status, p.berat, t.total_bayar, p.jenis_layanan, t.metode_pembayaran
FROM pesanan p
JOIN layanan_laundry l ON p.id_layanan = l.id_layanan
JOIN transaksi t ON p.id_pesanan = t.id_pesanan
WHERE p.id_pengguna = ? AND p.status != 'Selesai' 
ORDER BY p.tanggal_pesanan DESC";

$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

// Menampilkan notifikasi jika ada
if (isset($_SESSION['notif'])) {
    $notif_class = isset($_SESSION['notif_type']) ? $_SESSION['notif_type'] : 'notif-message';
    echo "<div class='$notif_class'>" . htmlspecialchars($_SESSION['notif']) . "</div>";
    unset($_SESSION['notif']); // Hapus pesan setelah ditampilkan
    unset($_SESSION['notif_type']); // Hapus tipe notifikasi setelah ditampilkan
}

// Menutup koneksi
$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="res.css">
    <style>
        
        /* CSS untuk notifikasi */
        .notif-message {
            background-color: #4CAF50; /* Warna hijau terang untuk sukses */
            color: white;
            padding: 15px;
            margin: 15px;
            border-radius: 5px;
            text-align: center;
            font-size: 16px;
            border: 2px solid #3e8e41; /* Tambahkan border */
            box-shadow: 0px 4px 6px rgba(0, 0, 0, 0.1); /* Shadow untuk efek kedalaman */
            animation: fadeIn 0.5s ease-in-out;
        }

        /* Efek animasi untuk notifikasi */
        @keyframes fadeIn {
            0% {
                opacity: 0;
                transform: translateY(-20px);
            }
            100% {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Notifikasi ketika ada error */
        .notif-error {
            background-color: #f44336; /* Warna merah untuk error */
            color: white;
            border: 2px solid #d32f2f;
        }

        /* Notifikasi ketika proses loading atau informasi */
        .notif-info {
            background-color: #2196F3; /* Warna biru untuk informasi */
            color: white;
            border: 2px solid #1976D2;
        }
    </style>
    <title>Laundry Dashboard</title>
</head>

<body>
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-wash'></i>
            <span class="text">Tengko Laundry</span>
        </a>
        <ul class="side-menu top">
            <li class="active">
                <a href="dashboard.php">
                    <i class='bx bxs-shopping-bag'></i>
                    <span class="text">Pesanan Saya</span>
                </a>
            </li>
            <li>
                <a href="riwayat.php">
                    <i class='bx bxs-history'></i>
                    <span class="text">Riwayat</span>
                </a>
            </li>
            <li>
                <a href="layanan.php">
                    <i class='bx bxs-package'></i>
                    <span class="text">Layanan</span>
                </a>
            </li>
        </ul>
        <ul class="side-menu">
            <li>
                <a href="edit_profile.php" class="logout">
                    <i class='bx bxs-user'></i>
                    <span class="text">Edit Profil</span>
                </a>
            </li>
            <li>
                <a href="logout.php" class="logout">
                    <i class='bx bxs-log-out-circle'></i>
                    <span class="text">Logout</span>
                </a>
            </li>
        </ul>
    </section>

    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="#" class="nav-link">Selamat datang, <strong><?php echo $nama; ?></strong>!</a>
            <a href="edit_profile.php" class="profile">
                <img src="<?php echo $profile_image; ?>" alt="profile">
            </a>
        </nav>

        <main>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Pesanan</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pesan</th>
                                <th>Layanan</th>
                                <th>Harga Awal</th>
                                <th>Status</th>
                                <th>Berat (Kg)</th>
                                <th>Total</th>
                                <th>Jenis Layanan</th>
                                <th>Aksi</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php
                            $no = 1;
                            while ($order = $order_result->fetch_assoc()) {
                                $formatted_date = date('d-m-Y H:i:s', strtotime($order['tanggal_pesanan']));
                                echo "<tr>
                                    <td>{$no}</td>
                                    <td>{$formatted_date}</td>
                                    <td>" . htmlspecialchars($order['nama_layanan']) . "</td>
                                    <td>Rp" . number_format($order['harga'], 0, ',', '.') . "</td>
                                    <td>" . htmlspecialchars($order['status']) . "</td>
                                    <td>{$order['berat']} Kg</td>
                                    <td>Rp" . number_format($order['total_bayar'], 0, ',', '.') . "</td>
                                    <td>" . htmlspecialchars($order['jenis_layanan']) . "</td>
                                    <td>
                                        <a href='detail_pesanan1.php?id_pesanan=" . htmlspecialchars($order['id_pesanan']) . "' class='btn detail-btn'>
                                            <i class='bx bx-info-circle'></i> Detail
                                        </a>
                                    </td>
                                </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
    </section>
    <script src="script1.js"></script>
</body>
</html>
