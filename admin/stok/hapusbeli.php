<?php
session_start();
require_once '../../includes/config.php';

// Mendapatkan id pembelian dari parameter URL
$beliID = isset($_GET['beliID']) ? $_GET['beliID'] : null;

if (!$beliID) {
    $_SESSION['error'] = "ID Pembelian tidak valid!";
    header("Location: ../stok.php");
    exit();
}

try {
    // Mulai transaksi
    $conn->begin_transaction();

    // Mendapatkan detail pembelian untuk mengambil idBarang dan jumlah pembelian
    $queryPembelian = "SELECT idBarang, jumlah FROM Pembelian WHERE beliID = ?";
    $stmtPembelian = $conn->prepare($queryPembelian);
    $stmtPembelian->bind_param("s", $beliID);
    $stmtPembelian->execute();
    $resultPembelian = $stmtPembelian->get_result();
    $pembelian = $resultPembelian->fetch_assoc();

    if (!$pembelian) {
        throw new Exception("Data pembelian tidak ditemukan!");
    }

    $idBarang = $pembelian['idBarang'];
    $jumlah = $pembelian['jumlah'];

    // Mengurangi stok barang di tabel Stok
    $queryUpdateStok = "UPDATE Stok SET jumlahBarang = jumlahBarang - ? WHERE idBarang = ?";
    $stmtUpdateStok = $conn->prepare($queryUpdateStok);
    $stmtUpdateStok->bind_param("ii", $jumlah, $idBarang);
    $stmtUpdateStok->execute();

    // Hapus data pembelian
    $queryDeletePembelian = "DELETE FROM Pembelian WHERE beliID = ?";
    $stmtDeletePembelian = $conn->prepare($queryDeletePembelian);
    $stmtDeletePembelian->bind_param("s", $beliID);
    $stmtDeletePembelian->execute();

    // Commit transaksi
    $conn->commit();

    $_SESSION['message'] = "Pembelian berhasil dihapus dan stok diperbarui!";
    header("Location: ../../stok.php");
    exit();

} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    header("Location: ../../stok.php");
    exit();
}
?>
