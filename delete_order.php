<?php
session_start();
require_once 'includes/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['username'])) {
    echo "<script>alert('Anda harus login terlebih dahulu!');</script>";
    echo "<script>window.location.href='login.html';</script>";
    exit;
}

// Ambil menuID dari URL
$menuID = isset($_GET['menuID']) ? $_GET['menuID'] : null;

if ($menuID) {
    // Ambil ID pesanan berdasarkan menuID yang baru dibuat
    $query = "SELECT pesananID FROM pesanan WHERE menuID = ? AND statID = 'S002' ORDER BY tanggalPesanan DESC LIMIT 1";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $menuID);
    $stmt->execute();
    $result = $stmt->get_result();

    $pesanan = $result->fetch_assoc();

    if ($pesanan) {
        $orderID = $pesanan['pesananID'];

        // Hapus pesanan yang baru dibuat
        $queryDelete = "DELETE FROM pesanan WHERE pesananID = ?";
        $stmtDelete = $conn->prepare($queryDelete);
        $stmtDelete->bind_param("s", $orderID);

        if ($stmtDelete->execute()) {
            echo "<script>alert('Pesanan dibatalkan!');</script>";
            echo "<script>window.location.href='index2.php';</script>";  // Kembali ke halaman utama atau yang sesuai
        } else {
            echo "Gagal menghapus pesanan!";
        }
    } else {
        echo "Pesanan tidak ditemukan!";
    }
} else {
    echo "ID menu tidak valid!";
}
?>
