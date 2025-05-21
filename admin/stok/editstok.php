<?php
session_start();
require_once '../../includes/config.php';

$idBarang = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaBarang = $_POST['namaBarang'];
    $kategoriID = $_POST['kategoriID'];
    $jenisID = $_POST['jenisID'];
    $jumlahBarang = $_POST['jumlahBarang'];

    $query = "UPDATE Stok SET namaBarang = ?, kategoriID = ?, jenisID = ?, jumlahBarang = ? WHERE idBarang = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("sssis", $namaBarang, $kategoriID, $jenisID, $jumlahBarang, $idBarang);

    if ($stmt->execute()) {
        $_SESSION['message'] = "Stok berhasil diperbarui!";
        header("Location: ../../stok.php");
    } else {
        $_SESSION['error'] = "Terjadi kesalahan saat memper barui stok.";
    }
}

$query = "SELECT * FROM Stok WHERE idBarang = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $idBarang);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
<div id="content-wrapper" class="d-flex flex-column">
    <div id="content">
        <div class="container-fluid">
            <h1 class="h3 mb-4 text-gray-800">Edit Stok Barang</h1>
            <form method="POST" action="">
                <div class="form-group">
                    <label for="namaBarang">Nama Barang</label>
                    <input type="text" class="form-control" id="namaBarang" name="namaBarang" value="<?php echo $row['namaBarang']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="kategoriID">Kategori</label>
                    <input type="text" class="form-control" id="kategoriID" name="kategoriID" value="<?php echo $row['kategoriID']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="jenisID">Jenis Bahan</label>
                    <input type="text" class="form-control" id="jenisID" name="jenisID" value="<?php echo $row['jenisID']; ?>" required>
                </div>
                <div class="form-group">
                    <label for="jumlahBarang">Jumlah Barang</label>
                    <input type="number" class="form-control" id="jumlahBarang" name="jumlahBarang" value="<?php echo $row['jumlahBarang']; ?>" required>
                </div>
                <button type="submit" class="btn btn-primary">Simpan Perubahan</button>
            </form>
        </div>
    </div>
</div>
</body>
</html>