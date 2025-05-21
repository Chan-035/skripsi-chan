<?php
session_start();
require_once 'includes/config.php';

// Periksa apakah ada parameter orderID di URL
$orderID = isset($_GET['orderID']) ? $_GET['orderID'] : null;

if ($orderID) {
    // Ambil detail pesanan berdasarkan orderID
    $query = "SELECT * FROM pesanan WHERE pesananID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $orderID);
    $stmt->execute();
    $result = $stmt->get_result();

    $pesanan = $result->fetch_assoc();
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
    <title>Payment Failed</title>
    <link rel="stylesheet" href="css/payment.css">
</head>
<body>
    <div class="payment-status">
        <h1>Gagal Pembayaran</h1>
        <p>Terjadi kesalahan saat memproses pembayaran Anda. Silakan coba lagi.</p>
        <p>Order ID: <?php echo $orderID; ?></p>
        <p>Total Pembayaran: Rp <?php echo number_format($pesanan['totalHarga'], 0, ',', '.'); ?></p>
        <a href="index2.php" class="btn">Kembali ke Halaman Utama</a>
    </div>
</body>
</html>
