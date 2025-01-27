<?php
// Mengambil data notifikasi dari Midtrans
$order_id = $_POST['order_id']; // ID pesanan yang dikirimkan
$transaction_status = $_POST['transaction_status']; // Status transaksi dari Midtrans

// Jika status transaksi adalah 'settlement', berarti sudah berhasil dibayar
if ($transaction_status == 'settlement') {
    // Koneksi ke database
    include('config.php'); // Koneksi ke database

    // Query untuk memperbarui status pembayaran
    $update_sql = "UPDATE transaksi SET status_pembayaran = 'Sudah Bayar' WHERE id_pesanan = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $order_id); // Menggunakan ID pesanan untuk memperbarui transaksi

    if ($update_stmt->execute()) {
        echo "Status pembayaran berhasil diperbarui.";
    } else {
        echo "Gagal memperbarui status pembayaran.";
    }
} else {
    // Status selain 'settlement' dianggap belum dibayar
    echo "Pembayaran gagal atau pending.";
}
?>
