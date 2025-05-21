<?php
session_start();
require_once '../../includes/config.php';

// Ambil ID supplier dari URL
$supplierID = $_GET['id'];

// Proses form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaKontak = $_POST['namaKontak'];
    $kontak = $_POST['kontak'];

    // Pastikan query menggunakan parameter yang benar
    $query = "UPDATE supplier SET namaKontak = ?, kontak = ? WHERE supplierID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sss", $namaKontak, $kontak, $supplierID); // Pastikan tipe data sesuai

    if ($stmt->execute()) {
        $_SESSION['message'] = "Supplier berhasil diperbarui!";
        header("Location: ../../supplier.php");
        exit();
    } else {
        $_SESSION['error'] = "Gagal memperbarui supplier!";
    }
}

// Ambil data supplier yang akan diedit
$query = "SELECT * FROM supplier WHERE supplierID = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $supplierID); // Pastikan tipe data sesuai
$stmt->execute();
$result = $stmt->get_result();
$supplier = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Supplier</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="container">
        <h1>Edit Supplier</h1>
        <form method="POST">
            <div class="form-group">
                <label>Nama Kontak</label>
                <input type="text" name="namaKontak" class="form-control" value="<?php echo $supplier['namaKontak']; ?>" required>
            </div>
            <div class="form-group">
                <label>Kontak</label>
                <input type="text" name="kontak" class="form-control" value="<?php echo $supplier['kontak']; ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Update Supplier</button>
            <a href="../../supplier.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>