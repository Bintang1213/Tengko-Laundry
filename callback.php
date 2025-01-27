<?php
// Include Midtrans PHP SDK
require_once 'midtrans/Midtrans.php';

// Set server key dan konfigurasi
\Midtrans\Config::$serverKey = 'SB-Mid-server-gsvFAmh7DGyxYP6VyGqNXiNp'; // Ganti dengan server key Anda
\Midtrans\Config::$clientKey = 'SB-Mid-client-toSWynbN7eStOqbr'; // Ganti dengan client key Anda
\Midtrans\Config::$isProduction = false;  // Gunakan 'true' jika sudah live, 'false' untuk sandbox
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Inisialisasi koneksi database
include('config.php');

// Menyimpan data notifikasi untuk debugging
file_put_contents('notification.log', "Received Notification: " . print_r($_POST, true), FILE_APPEND);

// Ambil notifikasi dari Midtrans
try {
    $notification = new \Midtrans\Notification();

    // Debugging untuk melihat data yang diterima dari Midtrans
    file_put_contents('notification.log', "Parsed Notification: " . print_r($notification, true), FILE_APPEND);

    // Ambil data dari notifikasi
    $order_id = $notification->order_id;  // Ini adalah order_id dari Midtrans
    $status = $notification->transaction_status;  // status transaksi seperti 'settlement', 'pending', dll.

    // Memeriksa apakah order_id ada
    if (!isset($order_id)) {
        file_put_contents('notification_error.log', "Error: order_id is missing in the notification. \n", FILE_APPEND);
        echo "order_id is missing in the notification.";
        exit();  // Berhenti jika tidak ada order_id
    }

    // Cari pesanan yang sesuai berdasarkan order_id
    $sql = "SELECT * FROM pesanan WHERE id_pesanan = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $order_id);
    $stmt->execute();
    $result = $stmt->get_result();

    // Jika pesanan ditemukan
    if ($result->num_rows > 0) {
        // Update status pembayaran sesuai dengan status transaksi
        if ($status == 'settlement') {
            // Pembayaran berhasil
            echo "Pembayaran berhasil.";

            // Update status pembayaran di database
            $update_sql = "UPDATE pesanan SET status = 'Sudah Bayar' WHERE id_pesanan = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $order_id);
            $update_stmt->execute();
        } elseif ($status == 'pending') {
            // Pembayaran masih pending
            echo "Pembayaran masih pending.";
        } elseif ($status == 'cancel') {
            // Pembayaran dibatalkan
            echo "Pembayaran dibatalkan.";

            // Update status pembayaran di database
            $update_sql = "UPDATE pesanan SET status = 'Batal' WHERE id_pesanan = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $order_id);
            $update_stmt->execute();
        }
    } else {
        file_put_contents('notification_error.log', "Error: Pesanan dengan order_id $order_id tidak ditemukan. \n", FILE_APPEND);
        echo "Pesanan tidak ditemukan.";
    }
} catch (Exception $e) {
    // Jika ada error dalam menerima notifikasi atau proses lainnya
    file_put_contents('notification_error.log', "Exception: " . $e->getMessage() . "\n", FILE_APPEND);
    echo "Terjadi kesalahan saat memproses notifikasi: " . $e->getMessage();
}
?>
