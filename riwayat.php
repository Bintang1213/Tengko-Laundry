<?php
// Memulai session
session_start();

// Memeriksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari session
$user_name = $_SESSION['nama'];
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
$profile_image = $user_data['gambar_profil'] ? "uploads/" . htmlspecialchars($user_data['gambar_profil']) : "img/default-profile.png";

// Ambil riwayat pesanan pengguna
$order_sql = "SELECT p.id_pesanan, p.tanggal_pesanan, l.nama_layanan, l.harga, p.status, p.berat, t.total_bayar
FROM pesanan p
JOIN layanan_laundry l ON p.id_layanan = l.id_layanan
JOIN transaksi t ON p.id_pesanan = t.id_pesanan
WHERE p.id_pengguna = ? AND p.status = 'Selesai'
ORDER BY p.tanggal_pesanan DESC";

$order_stmt = $conn->prepare($order_sql);
$order_stmt->bind_param("i", $user_id);
$order_stmt->execute();
$order_result = $order_stmt->get_result();

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
    <link rel="stylesheet" href="1.css">
    <style>
        /* CSS khusus untuk tombol "Detail" */
        .btn.detail-btn {
            display: inline-block;
            padding: 8px 15px;
            background-color: #007bff;
            /* Warna biru */
            color: white;
            text-decoration: none;
            border-radius: 5px;
            font-size: 14px;
            transition: background-color 0.3s ease, transform 0.2s ease;
            border: none;
            cursor: pointer;
        }

        .btn.detail-btn:hover {
            background-color: #0056b3;
            /* Biru gelap saat hover */
            transform: scale(1.05);
        }

        .btn.detail-btn i {
            margin-right: 5px;
        }
    </style>
    <title>Riwayat Pesanan</title>
</head>


<body>

    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-wash'></i>
            <span class="text">Tengko Laundry</span>
        </a>
        <ul class="side-menu top">
            <li>
                <a href="dashboard.php">
                    <i class='bx bxs-shopping-bag'></i>
                    <span class="text">Pesanan Saya</span>
                </a>
            </li>
            <li class="active">
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
    <!-- SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="edit_profile.php" class="profile">
                <img src="<?php echo $profile_image; ?>" alt="profile">
            </a>
        </nav>

        <!-- MAIN -->
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Riwayat Pesanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Riwayat</a></li>
                    </ul>
                </div>
            </div>

            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Riwayat Pesanan</h3>
                    </div>
                    <table>
                        <thead>
                            <tr>
                                <th>No</th>
                                <th>Tanggal Pesan</th>
                                <th>Layanan</th>
                                <th>Harga Awal</th>
                                <th>Berat (Kg)</th>
                                <th>Total</th>
                                <th>Status</th>
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
                                    <td>{$order['berat']} Kg</td>
                                    <td>Rp" . number_format($order['total_bayar'], 0, ',', '.') . "</td>
                                    <td>" . htmlspecialchars($order['status']) . "</td>
                                    <td><a href='detail_pesanan.php?id_pesanan=" . htmlspecialchars($order['id_pesanan']) . "' class='btn detail-btn'><i class='bx bx-info-circle'></i> Detail</a></td>
                                  </tr>";
                                $no++;
                            }
                            ?>
                        </tbody>
                    </table>
                </div>
            </div>
        </main>
        <!-- MAIN -->

    </section>
    <script src="script1.js"></script>
</body>

</html>