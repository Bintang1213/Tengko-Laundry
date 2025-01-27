<?php
// Memulai session
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Ambil data pengguna dari session
$user_id = $_SESSION['user_id'];

// Koneksi ke database
include('config.php');

// Ambil data pengguna berdasarkan id
$sql = "SELECT * FROM pengguna WHERE id_pengguna = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();
$user_data = $result->fetch_assoc();

// Inisialisasi pesan error atau sukses
$error_message = "";
$success_message = "";

// Proses update data pengguna
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $new_name = $_POST['username'];
    $new_phone = $_POST['phone'];
    $new_address = $_POST['address'];
    $new_image = $_FILES['profile_image']['name'];

    try {
        // Validasi input
        if (empty($new_name) || empty($new_phone) || empty($new_address)) {
            throw new Exception("Semua kolom wajib diisi.");
        }

        // Jika gambar di-upload, proses uploadnya
        if ($new_image) {
            $target_dir = "uploads/";
            if (!is_dir($target_dir)) {
                mkdir($target_dir, 0777, true);
            }
            $imageFileType = strtolower(pathinfo($_FILES["profile_image"]["name"], PATHINFO_EXTENSION));
            $allowed_types = ['jpg', 'jpeg', 'png', 'gif'];

            // Validasi jenis file
            if (!in_array($imageFileType, $allowed_types)) {
                throw new Exception("Hanya file JPG, JPEG, PNG, dan GIF yang diperbolehkan.");
            }

            // Validasi ukuran file (maksimal 5MB)
            if ($_FILES["profile_image"]["size"] > 5000000) {
                throw new Exception("Ukuran file terlalu besar (maksimal 5MB).");
            }

            // Nama file unik
            $new_image_name = uniqid() . '.' . $imageFileType;
            $target_file = $target_dir . $new_image_name;

            // Upload file
            if (!move_uploaded_file($_FILES["profile_image"]["tmp_name"], $target_file)) {
                throw new Exception("Gagal mengupload gambar.");
            }
        } else {
            $new_image_name = $user_data['gambar_profil'];
        }

        // Query untuk memperbarui data pengguna
        $sql = "UPDATE pengguna SET nama = ?, no_telepon = ?, alamat = ?, gambar_profil = ? WHERE id_pengguna = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $new_name, $new_phone, $new_address, $new_image_name, $user_id);

        if (!$stmt->execute()) {
            throw new Exception("Gagal memperbarui data pengguna.");
        }

        // Simpan notifikasi ke session
        $_SESSION['success_message'] = "Profil berhasil diperbarui.";

        // Update session nama dengan nama baru
        $_SESSION['nama'] = $new_name;

        // Redirect ke dashboard
        header("Location: dashboard.php");
        exit();
    } catch (Exception $e) {
        $error_message = $e->getMessage();
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profile</title>
    <link rel="stylesheet" href="dashboard.css">
    <link rel="stylesheet" href="1.css">
</head>

<body>
    <!-- SIDEBAR -->
    <section id="sidebar">
        <a href="#" class="brand">
            <i class='bx bxs-wash'></i>
            <span class="text">Tengko Laundry</span>
        </a>
        <ul class="side-menu top">
            <li><a href="dashboard.php"><i class='bx bxs-home'></i><span class="text">Dashboard</span></a></li>
            <li class="active"><a href="edit_profile.php"><i class='bx bxs-user'></i><span class="text">Edit Profile</span></a></li>
            <li><a href="logout.php"><i class='bx bxs-log-out-circle'></i><span class="text">Logout</span></a></li>
        </ul>
    </section>

    <!-- CONTENT -->
    <section id="content">
        <nav>
            <a href="dashboard.php" class="nav-link">Dashboard</a>
            <a href="logout.php" class="logout">Logout</a>
        </nav>
        <main>
            <div class="head-title">
                <h1>Edit Profile</h1>
            </div>

            <!-- Display Error or Success Message -->
            <?php if (!empty($error_message)): ?>
                <div class="error-message">
                    <p><?php echo htmlspecialchars($error_message); ?></p>
                </div>
            <?php elseif (isset($_SESSION['success_message'])): ?>
                <div class="success-message">
                    <p><?php echo htmlspecialchars($_SESSION['success_message']); ?></p>
                    <?php unset($_SESSION['success_message']); ?>
                </div>
            <?php endif; ?>

            <div class="container">
                <form action="edit_profile.php" method="POST" enctype="multipart/form-data">
                    <!-- Profile Image -->
                    <div class="form-group">
                        <label for="profile_image">Foto Profil</label>
                        <input type="file" name="profile_image" id="profile_image" accept="image/*">
                        <div class="image-preview">
                            <?php $profile_image = $user_data['gambar_profil'] ? 'uploads/' . htmlspecialchars($user_data['gambar_profil']) : 'img/default-profile.png'; ?>
                            <a href="javascript:void(0);" onclick="openModal()">
                                <img src="<?php echo $profile_image; ?>" alt="Profile Image" class="preview-img" id="preview-img">
                            </a>
                        </div>
                    </div>

                    <!-- Name -->
                    <div class="form-group">
                        <label for="username">Nama</label>
                        <input type="text" name="username" id="username" value="<?php echo htmlspecialchars($user_data['nama'] ?? ''); ?>" required>
                    </div>

                    <!-- Phone -->
                    <div class="form-group">
                        <label for="phone">Nomor Telepon</label>
                        <input type="text" name="phone" id="phone" value="<?php echo htmlspecialchars($user_data['no_telepon'] ?? ''); ?>" required>
                    </div>

                    <!-- Address -->
                    <div class="form-group">
                        <label for="address">Alamat</label>
                        <input type="text" name="address" id="address" value="<?php echo htmlspecialchars($user_data['alamat'] ?? ''); ?>" required>
                    </div>

                    <!-- Submit Button -->
                    <button type="submit">Simpan Perubahan</button>
                </form>
            </div>
        </main>
    </section>

    <!-- Modal for Image Preview -->
    <div id="imageModal" class="modal">
        <span class="close">&times;</span>
        <img class="modal-content" id="modal-img">
    </div>

    <script>
        // Function to open the modal
        function openModal() {
            var modal = document.getElementById("imageModal");
            var modalImg = document.getElementById("modal-img");
            var previewImg = document.getElementById("preview-img");
            modal.style.display = "block";
            modalImg.src = previewImg.src;
        }

        // Function to close the modal
        var closeModal = document.querySelector('.modal .close');
        closeModal.addEventListener('click', function() {
            var modal = document.getElementById("imageModal");
            modal.style.display = "none";
        });

        // Close modal if clicked outside of image
        var modal = document.getElementById("imageModal");
        modal.addEventListener('click', function(e) {
            if (e.target === modal) {
                modal.style.display = "none";
            }
        });
    </script>
</body>

</html>
