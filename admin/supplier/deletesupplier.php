<?php
session_start();
require_once '../../includes/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userID'])) {
    header("Location: ../../login.php");
    exit();
}

// Cek apakah parameter ID diterima
if (isset($_GET['id'])) {
    $supplierID = $_GET['id'];

    $conn->begin_transaction(); // Mulai transaksi
    try {
        // Hapus dari tabel listsupplier
        $queryDeleteListSupplier = "DELETE FROM listsupplier WHERE supplierID = ?";
        $stmtListSupplier = $conn->prepare($queryDeleteListSupplier);

        if (!$stmtListSupplier) {
            throw new Exception("Error prepare listsupplier: " . $conn->error);
        }

        $stmtListSupplier->bind_param("s", $supplierID);
        if (!$stmtListSupplier->execute()) {
            throw new Exception("Error execute listsupplier: " . $stmtListSupplier->error);
        }

        // Hapus dari tabel supplier
        $queryDeleteSupplier = "DELETE FROM supplier WHERE supplierID = ?";
        $stmtSupplier = $conn->prepare($queryDeleteSupplier);

        if (!$stmtSupplier) {
            throw new Exception("Error prepare supplier: " . $conn->error);
        }

        $stmtSupplier->bind_param("s", $supplierID);
        if (!$stmtSupplier->execute()) {
            throw new Exception("Error execute supplier: " . $stmtSupplier->error);
        }

        // Commit transaksi jika semua berhasil
        $conn->commit();

        // Redirect ke halaman daftar supplier dengan pesan sukses
        header("Location: ../../supplier.php?success=Data supplier berhasil dihapus.");
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi kesalahan
        $conn->rollback();
        // Redirect ke halaman daftar supplier dengan pesan error
        header("Location: ../../supplier.php?error=Terjadi kesalahan: " . urlencode($e->getMessage()));
        exit();
    }
} else {
    // Jika ID tidak ada, redirect ke halaman supplier dengan pesan error
    header("Location: ../../supplier.php?error=Invalid request.");
    exit();
}
