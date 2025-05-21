<?php
session_start();
require_once '../../includes/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Dapatkan supplierID terakhir dari tabel supplier sebelum form ditampilkan
$queryLastID = "SELECT supplierID FROM supplier ORDER BY supplierID DESC LIMIT 1";
$resultLastID = $conn->query($queryLastID);
if ($resultLastID->num_rows > 0) {
    $rowLastID = $resultLastID->fetch_assoc();
    $lastID = $rowLastID['supplierID'];
    $numericPart = intval(substr($lastID, 2)) + 1; // Mengambil bagian angka dan menambah 1
    $supplierID = 'S' . str_pad($numericPart, 3, '0', STR_PAD_LEFT); // Format SP001, SP002, dst.
} else {
    $supplierID = 'S001'; // ID pertama jika belum ada supplier
}

// Proses form ketika disubmit
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $namaKontak = trim($_POST['namaKontak']);
    $kontak = trim($_POST['kontak']);
    $idBarangList = $_POST['idBarang'] ?? []; // Mengambil daftar idBarang sebagai array

    // Validasi input
    if (empty($namaKontak) || empty($kontak)) {
        $error = "Semua kolom wajib diisi.";
    } else {
        // Mulai transaksi
        $conn->begin_transaction();
        try {
            // Masukkan data ke tabel supplier
            $querySupplier = "INSERT INTO supplier (supplierID, namaKontak, kontak) VALUES (?, ?, ?)";
            $stmtSupplier = $conn->prepare($querySupplier);
            $stmtSupplier->bind_param("sss", $supplierID, $namaKontak, $kontak);
            $stmtSupplier->execute();

            // Masukkan data ke tabel listsupplier
            if (!empty($idBarangList)) {
                $queryListSupplier = "INSERT INTO listsupplier (supplierID, idBarang) VALUES (?, ?)";
                $stmtListSupplier = $conn->prepare($queryListSupplier);

                foreach ($idBarangList as $idBarang) {
                    $stmtListSupplier->bind_param("ss", $supplierID, $idBarang);
                    $stmtListSupplier->execute();
                }
            }

            // Commit transaksi
            $conn->commit();
            $success = "Data supplier berhasil ditambahkan.";
            // Refresh halaman untuk menampilkan Supplier ID baru
            header("Location: addsupplier.php");
            exit();
        } catch (Exception $e) {
            $conn->rollback();
            $error = "Terjadi kesalahan: " . $e->getMessage();
        }
    }
}

// Ambil daftar barang dari tabel stok untuk ditampilkan dalam form
$queryBarang = "SELECT idBarang, namaBarang FROM stok";
$resultBarang = $conn->query($queryBarang);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Supplier</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="container">
        <h1>Tambah Supplier</h1>

        <?php if (isset($error)): ?>
            <p class="error"><?php echo htmlspecialchars($error); ?></p>
        <?php endif; ?>

        <?php if (isset($success)): ?>
            <p class="success"><?php echo htmlspecialchars($success); ?></p>
        <?php endif; ?>

        <form action="addsupplier.php" method="POST">
            <div class="form-group">
                <label for="supplierID">Supplier ID</label>
                <input type="text" name="supplierID" id="supplierID" value="<?php echo htmlspecialchars($supplierID); ?>" readonly>
            </div>

            <div class="form-group">
                <label for="namaKontak">Nama Kontak</label>
                <input type="text" name="namaKontak" id="namaKontak" maxlength="15" required>
            </div>

            <div class="form-group">
                <label for="kontak">Kontak</label>
                <input type="text" name="kontak" id="kontak" maxlength="15" required>
            </div>

            <div class="form-group">
                <label for="idBarang">Barang yang Disuplai</label>
                <select name="idBarang[]" id="idBarang" multiple>
                    <?php if ($resultBarang->num_rows > 0): ?>
                        <?php while ($row = $resultBarang->fetch_assoc()): ?>
                            <option value="<?php echo htmlspecialchars($row['idBarang']); ?>">
                                <?php echo htmlspecialchars($row['namaBarang']); ?>
                            </option>
                        <?php endwhile; ?>
                    <?php else: ?>
                        <option value="">Tidak ada barang yang tersedia</option>
                    <?php endif; ?>
                </select>
                <small>Tekan Ctrl untuk memilih beberapa barang.</small>
            </div>

            <div class="form-group">
                <button type="submit">Tambah Supplier</button>
            </div>
        </form>

        <a href="../../supplier.php" class="btn-back">Kembali ke Daftar Supplier</a>
    </div>
</body>
</html>
