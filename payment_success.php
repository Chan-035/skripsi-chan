<?php
session_start();
require_once 'includes/config.php';

// Ambil orderID dari URL dan pastikan formatnya sesuai dengan yang ada di database
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : null;

// Jika orderID memiliki panjang 5 karakter, tambahkan 0 di depan untuk mencocokkan format di database
if ($orderID && strlen($orderID) == 5) {
    $orderID = 'OP0' . substr($orderID, 2); // Menambahkan 0 di depan jika perlu
}

if ($orderID) {
    // Update status statID menjadi 'S002' (berhasil) untuk semua pesanan yang terkait dengan orderID
    $updateQuery = "UPDATE pesanan 
                    SET statID = 'S002' 
                    WHERE pesananID IN (SELECT pesananID FROM orderpesanan WHERE orderID = ?)";
    $stmtUpdate = $conn->prepare($updateQuery);
    $stmtUpdate->bind_param("s", $orderID);
    $stmtUpdate->execute();

    // Query untuk menjumlahkan total harga berdasarkan orderID
    $query = "
        SELECT SUM(p.totalHarga) AS totalPembayaran
        FROM pesanan p
        JOIN orderpesanan op ON p.pesananID = op.pesananID
        WHERE op.orderID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    // Ambil hasil query
    $pesanan = $result->fetch_assoc();

    // Periksa apakah totalPembayaran ada dan valid
    $totalPembayaran = isset($pesanan['totalPembayaran']) ? $pesanan['totalPembayaran'] : 0;

    // Jika totalPembayaran adalah 0 atau NULL, berarti tidak ada data yang sesuai
    if ($totalPembayaran == 0) {
        echo "Tidak ada pesanan terkait dengan Order ID: " . $orderID;
        exit;
    }

    // Hapus cart setelah pembayaran berhasil
    unset($_SESSION['keranjang']);
} else {
    echo "Order ID tidak valid!";
    exit;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Success</title>
    <link rel="stylesheet" href="css/payment.css">
</head>
<body>
    <div class="payment-status">
        <h1>Pembayaran Berhasil!</h1>
        <p>Terima kasih, pembayaran Anda telah berhasil diproses.</p>
        <p>Order ID: <?php echo $orderID; ?></p>
        <p>Total Pembayaran: Rp <?php echo number_format($totalPembayaran, 0, ',', '.'); ?></p>
        <a href="index2.php" class="btn">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>
