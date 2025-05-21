<?php
session_start();
require_once '../../includes/config.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $menuID = $_POST['menuID'];
    $namaMenu = $_POST['namaMenu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];

    // Mengambil nama asli file
    $originalName = $_FILES['gambar']['name'];
    $extension = pathinfo($originalName, PATHINFO_EXTENSION); // Mendapatkan ekstensi file

    // Menggunakan nama asli file dengan menambahkan timestamp untuk menghindari konflik
    $newName = pathinfo($originalName, PATHINFO_FILENAME)  . '.' . $extension;

    // Upload gambar ke folder dengan nama baru
    if (move_uploaded_file($_FILES['gambar']['tmp_name'], "../../image" . $newName)) {
        // Simpan ke database
        $query = "INSERT INTO menu (menuID,namaMenu, deskripsi, harga, gambar) VALUES (?, ?, ?, ?, ?)";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("sssis", $menuID, $namaMenu, $deskripsi, $harga, $newName);

        if ($stmt->execute()) {
            $_SESSION['message'] = "Menu berhasil ditambahkan!";
            header("Location: ../../menu.php");
            exit();
        } else {
            $_SESSION['error'] = "Terjadi kesalahan saat menambahkan menu.";
        }
    } else {
        $_SESSION['error'] = "Gagal meng-upload gambar.";
    }
}
?>
<head>
    <link rel="stylesheet" href="menu.css">
</head>
<!-- HTML Form -->
<a href="../../menu.php" class="btn btn-secondary">Kembali</a>
<div class="container">
    <h2>Tambah Menu Baru</h2>
    <form method="POST" enctype="multipart/form-data">
        <div class="form-group">
            <label>Menu ID</label>
            <input type="text" name="menuID" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Nama Menu</label>
            <input type="text" name="namaMenu" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Deskripsi</label>
            <textarea name="deskripsi" class="form-control"></textarea>
        </div>
        <div class="form-group">
            <label>Harga</label>
            <input type="number" name="harga" class="form-control" required>
        </div>
        <div class="form-group">
            <label>Gambar</label>
            <input type="file" name="gambar" class="form-control-file">
            <small class="form-text text-muted">Format yang diizinkan: JPG, JPEG, PNG, GIF</small>
        </div>
        <button type="submit" class="btn btn-primary">Simpan</button>
        
    </form>
</div>