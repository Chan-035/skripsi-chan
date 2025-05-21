<?php
session_start();
require_once '../../includes/config.php'; // Koneksi ke database

// Ambil ID menu dari parameter URL
$idMenu = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idMenu) {
    echo "ID menu tidak valid.";
    exit();
}

// Ambil data menu berdasarkan menuID
$query = "SELECT * FROM menu WHERE menuID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $idMenu);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "Menu tidak ditemukan.";
    exit();
}

$menu = $result->fetch_assoc();

// Proses pembaruan data menu
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaMenu = $_POST['namaMenu'];
    $deskripsi = $_POST['deskripsi'];
    $harga = $_POST['harga'];
    $gambarLama = $menu['gambar']; // Simpan nama gambar lama
    $gambarBaru = $gambarLama; // Default ke gambar lama

    // Cek apakah ada gambar baru yang diupload
    if (isset($_FILES['gambar']) && $_FILES['gambar']['error'] == UPLOAD_ERR_OK) {
        $targetDir = "image/"; // Folder untuk menyimpan gambar
        $fileName = basename($_FILES['gambar']['name']);
        $targetFilePath = $targetDir . $fileName;
        $fileType = pathinfo($targetFilePath, PATHINFO_EXTENSION);

        // Validasi tipe file
        $allowedTypes = array('jpg', 'png', 'jpeg', 'gif');
        if (in_array($fileType, $allowedTypes)) {
            // Upload gambar baru
            if (move_uploaded_file($_FILES['gambar']['tmp_name'], $targetFilePath)) {
                // Jika berhasil, hapus gambar lama jika ada
                if (!empty($gambarLama)) {
                    unlink($targetDir . $gambarLama);
                }
                $gambarBaru = $fileName; // Gambar baru
            } else {
                $_SESSION['error'] = "Gagal mengupload gambar.";
            }
        } else {
            $_SESSION['error'] = "Tipe file tidak diizinkan.";
        }
    }

    // Query untuk memperbarui data menu
    $updateQuery = "UPDATE menu SET namaMenu = ?, deskripsi = ?, harga = ?, gambar = ? WHERE menuID = ?";
    $updateStmt = $conn->prepare($updateQuery);
    $updateStmt->bind_param("ssiss", $namaMenu, $deskripsi, $harga, $gambarBaru, $idMenu);

    if ($updateStmt->execute()) {
        $_SESSION['message'] = "Menu berhasil diperbarui!";
        header("Location: ../../menu.php");
        exit();
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memperbarui menu: " . $updateStmt->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Menu</title>
    <link rel="stylesheet" href="menu.css"> <!-- Ganti dengan stylesheet Anda -->
</head>
<body>

<div class="container">
    <h2>Edit Menu</h2>

    <?php if (isset($_SESSION['error'])): ?>
        <div class="alert alert-danger">
            <?php 
            echo $_SESSION['error'];
            unset($_SESSION['error']);
            ?>
        </div>
    <?php endif; ?>

    <form method="POST" action="" enctype="multipart/form-data">
        <div class="form-group">
            <label for="namaMenu">Nama Menu</label>
            <input type="text" id="namaMenu" name="namaMenu" value="<?php echo htmlspecialchars($menu['namaMenu']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="deskripsi">Deskripsi</label>
            <textarea id="deskripsi" name="deskripsi" required><?php echo htmlspecialchars($menu['deskripsi']); ?></textarea>
        </div>
        
        <div class="form-group">
            <label for="harga">Harga</label>
            <input type="number" id="harga" name="harga" value="<?php echo htmlspecialchars($menu['harga']); ?>" required>
        </div>
        
        <div class="form-group">
            <label for="gambar">Gambar Menu</label>
            <input type="file" id="gambar" name="gambar">
        </div>
        
        <button type="submit">Ubah Menu</button>
    </form>

    <a href="menu.php">Kembali ke Menu</a>
</div>

</body>
</html>