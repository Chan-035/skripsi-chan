<?php
session_start();

// Menghapus item dari keranjang berdasarkan menuID
if (isset($_POST['menuID'])) {
    $menuID = $_POST['menuID'];

    // Hapus item dari keranjang
    if (isset($_SESSION['keranjang'][$menuID])) {
        unset($_SESSION['keranjang'][$menuID]);
    }
}

header("Location: index2.php");
exit;
