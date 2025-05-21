<?php
session_start();

if (!isset($_SESSION['keranjang'])) {
    $_SESSION['keranjang'] = [];
}

$menuID = $_POST['menuID'];
$namaMenu = $_POST['namaMenu'];
$harga = $_POST['harga'];
$kuantitas = $_POST['kuantitas'];

// Cek apakah item sudah ada di keranjang
$found = false;
foreach ($_SESSION['keranjang'] as &$item) {
    if ($item['menuID'] === $menuID) {
        $item['kuantitas'] += $kuantitas;
        $found = true;
        break;
    }
}

if (!$found) {
    $_SESSION['keranjang'][] = [
        'menuID' => $menuID,
        'namaMenu' => $namaMenu,
        'harga' => $harga,
        'kuantitas' => $kuantitas
    ];
}

header('Location: index2.php');
exit;
?>
