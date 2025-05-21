<?php
session_start();
require_once '../../includes/config.php';

if (isset($_GET['id'])) {
    $pesananID = $_GET['id'];

    $query = "DELETE FROM pesanan WHERE pesananID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pesananID);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Pesanan berhasil dihapus!";
    } else {
        $_SESSION['error'] = "Error: " . $stmt->error;
    }
    header("Location: ../../pesanan.php");
    exit();
} else {
    $_SESSION['error'] = "ID pesanan tidak ditemukan!";
    header("Location: ../../pesanan.php");
    exit();
}
?>