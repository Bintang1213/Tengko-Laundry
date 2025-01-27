<?php 
session_start();

// Periksa apakah pengguna sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
include('config.php');

// Ambil data pengguna dari session
$user_id = $_SESSION['user_id'];
$username = $_SESSION['nama'];

// Ambil data pengguna termasuk gambar profil dari database
$sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Cek apakah ada gambar profil, jika tidak set gambar default
$profile_image = $user_data['gambar_profil'] ? "uploads/" . $user_data['gambar_profil'] : "img/default-profile.png";

// Ambil data layanan dari database
$layanan_sql = "SELECT * FROM layanan_laundry";
$layanan_result = $conn->query($layanan_sql);
?>
<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link href='https://unpkg.com/boxicons@2.0.9/css/boxicons.min.css' rel='stylesheet'>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="2.css">
    <title>Layanan Laundry</title>
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
            <li class="active"><a href="layanan.php"><i class='bx bxs-package'></i> <span
                        class="text">Layanan</span></a></li>
        </ul>
        <ul class="side-menu">
            <li><a href="edit_profile.php"><i class='bx bxs-user'></i> <span class="text">Edit Profil</span></a></li>
            <li><a href="logout.php"><i class='bx bxs-log-out-circle'></i> <span class="text">Logout</span></a></li>
        </ul>
    </section>
    <!-- END SIDEBAR -->

    <!-- CONTENT -->
    <section id="content">
        <nav>
            <i class='bx bx-menu'></i>
            <a href="edit_profile.php" class="profile">
                <img src="<?php echo $profile_image; ?>" alt="profile">
            </a>
        </nav>
        <main>
            <div class="head-title">
                <div class="left">
                    <h1>Layanan</h1>
                    <ul class="breadcrumb">
                        <li><a href="#">Layanan</a></li>
                    </ul>
                </div>
            </div>
            <div class="table-data">
                <div class="order">
                    <div class="head">
                        <h3>Daftar Layanan</h3>
                    </div>
                    <div class="services-section">
                        <h3 class="services-header">Daftar Layanan</h3>
                        <div class="services-wrapper">
                            <?php if ($layanan_result->num_rows > 0): ?>
                                <?php while ($layanan = $layanan_result->fetch_assoc()): ?>
                                    <div class="service-item">
                                        <!-- Periksa jika gambar ada -->
                                        <img src="/pemilik/images/layanan/<?php echo $layanan['gambar']; ?>"
                                            alt="<?php echo $layanan['nama_layanan']; ?>">
                                        <div class="service-info">
                                            <h4><?php echo $layanan['nama_layanan']; ?></h4>
                                            <p><?php echo "Rp " . number_format($layanan['harga'], 0, ',', '.'); ?></p>
                                            <p><?php echo nl2br($layanan['deskripsi']); ?></p>
                                            <!-- Form untuk mengarahkan ke pemesanan -->
                                            <form method="GET" action="pemesanan.php">
                                                <input type="hidden" name="layanan_id"
                                                    value="<?php echo $layanan['id_layanan']; ?>">
                                                <button type="submit">Pesan</button>
                                            </form>
                                        </div>
                                    </div>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <p>Tidak ada layanan tersedia.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </section>
    <script src="script1.js"></script>
</body>

</html>
