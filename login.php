<?php
session_start();
include('config.php');

// Proses Registrasi
if (isset($_POST['register'])) {
    $name = $_POST['name'];
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Periksa apakah email sudah terdaftar
    $sql = "SELECT * FROM Pengguna WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $register_error = "Email sudah terdaftar.";
    } else {
        // Enkripsi password sebelum menyimpan
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Insert data pengguna baru
        $sql = "INSERT INTO Pengguna (nama, email, password) VALUES (?, ?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sss", $name, $email, $hashed_password);

        if ($stmt->execute()) {
            $register_success = "Registrasi berhasil! Silakan login untuk melanjutkan.";
        } else {
            $register_error = "Error: " . $stmt->error;
        }
    }
}

// Proses Login
if (isset($_POST['login'])) {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Ambil data pengguna dari database
    $sql = "SELECT * FROM Pengguna WHERE email = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $user = $result->fetch_assoc();
        // Verifikasi password
        if (password_verify($password, $user['password'])) {
            // Mulai session dan simpan data session
            $_SESSION['user_id'] = $user['id_pengguna'];         // menyimpan user_id di session
            $_SESSION['nama'] = $user['nama'];                   // menyimpan nama di session
            $_SESSION['email'] = $user['email'];                 // menyimpan email di session
            $_SESSION['no_telepon'] = $user['no_telepon'];       // menyimpan no_telepon di session

            // Arahkan ke dashboard setelah login sukses
            header("Location: dashboard.php");
            exit();
        } else {
            $login_error = "Password salah.";
        }
    } else {
        $login_error = "Email tidak terdaftar.";
    }
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.2/css/all.min.css">
    <link rel="stylesheet" href="style.css">
    <title>Login Laundry | Web Laundry</title>
</head>
<body>
    <div class="container" id="container">
        <!-- Form Registrasi -->
        <div class="form-container sign-up">
            <form action="login.php" method="POST">
                <h1>Buat Akun Laundry</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                </div>
                <span>atau gunakan email Anda untuk mendaftar</span>
                <input type="text" name="name" placeholder="Nama" required>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Kata Sandi" required>

                <!-- Menampilkan pesan error atau sukses registrasi -->
                <?php if (isset($register_error)) { echo "<p style='color:red;'>$register_error</p>"; } ?>
                <?php if (isset($register_success)) { echo "<p style='color:green;'>$register_success</p>"; } ?>
                
                <button type="submit" name="register">Daftar</button>
            </form>
        </div>

        <!-- Form Login -->
        <div class="form-container sign-in">
            <form action="login.php" method="POST">
                <h1>Masuk ke Akun Laundry</h1>
                <div class="social-icons">
                    <a href="#" class="icon"><i class="fa-brands fa-google-plus-g"></i></a>
                    <a href="#" class="icon"><i class="fa-brands fa-facebook-f"></i></a>
                </div>
                <span>atau gunakan email dan kata sandi Anda</span>
                <input type="email" name="email" placeholder="Email" required>
                <input type="password" name="password" placeholder="Kata Sandi" required>
                <a href="#">Lupa Kata Sandi?</a>

                <!-- Menampilkan pesan error atau sukses login -->
                <?php if (isset($login_error)) { echo "<p style='color:red;'>$login_error</p>"; } ?>
                <?php if (isset($login_success)) { echo "<p style='color:green;'>$login_success</p>"; } ?>
                
                <button type="submit" name="login">Masuk</button>
            </form>
        </div>

        <div class="toggle-container">
            <div class="toggle">
                <div class="toggle-panel toggle-left">
                    <h1>Selamat Datang Kembali!</h1>
                    <p>Masuk untuk melacak layanan laundry Anda dengan mudah</p>
                    <button class="hidden" id="login">Masuk</button>
                </div>
                <div class="toggle-panel toggle-right">
                    <h1>Halo, Pelanggan Baru!</h1>
                    <p>Daftarkan diri Anda untuk memesan layanan laundry dengan mudah</p>
                    <button class="hidden" id="register">Daftar</button>
                </div>
            </div>
        </div>
    </div>

    <script src="script.js"></script>
</body>
</html>
