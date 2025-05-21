<?php
session_start();
require_once '../../includes/config.php';

// Fungsi untuk menghasilkan beliID baru
$queryLastID = "SELECT beliID FROM pembelian ORDER BY beliID DESC LIMIT 1";
$resultLastID = $conn->query($queryLastID);
$lastID = $resultLastID->fetch_assoc()['beliID'] ?? 'BE000';

// Generate beliID baru
$newBeliID = sprintf("BE%03d", intval(substr($lastID, 2)) + 1);

// Mengambil data supplier untuk dropdown
$querySupplier = "SELECT * FROM Supplier";
$resultSupplier = $conn->query($querySupplier);

// Mengambil data stok untuk dropdown barang (hanya akan dipakai jika supplier dipilih)
$queryStok = "SELECT s.*, k.namaKategori, j.namaJenis 
              FROM Stok s
              LEFT JOIN Kategori k ON s.kategoriID = k.kategoriID
              LEFT JOIN Jenis j ON s.jenisID = j.jenisID";
$resultStok = $conn->query($queryStok);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    try {
        // Mulai transaksi
        $conn->begin_transaction();

        // Ambil data dari form
        $beliID = $_POST['beliID']; // Pastikan beliID ada di form
        $supplierID = $_POST['supplierID'];
        $idBarang = $_POST['idBarang'];
        $tanggal = $_POST['tanggal'];
        $jumlah = $_POST['jumlah'];
        $harga = $_POST['harga'];

        // Insert ke tabel Pembelian
        $queryPembelian = "INSERT INTO Pembelian (beliID, supplierID, idBarang, tanggal, jumlah, harga) 
        VALUES (?, ?, ?, ?, ?, ?)";
        $stmtPembelian = $conn->prepare($queryPembelian);
        $stmtPembelian->bind_param("ssssss", $beliID, $supplierID, $idBarang, $tanggal, $jumlah, $harga);
        $stmtPembelian->execute();

        // Update stok barang di tabel Stok
        $queryUpdateStok = "UPDATE Stok SET jumlahBarang = jumlahBarang + ? WHERE idBarang = ?";
        $stmtUpdateStok = $conn->prepare($queryUpdateStok);
        $stmtUpdateStok->bind_param("is", $jumlah, $idBarang);
        $stmtUpdateStok->execute();

        // Commit transaksi
        $conn->commit();

        $_SESSION['message'] = "Pembelian berhasil ditambahkan dan stok diperbarui!";
        header("Location: ../../stok.php");
        exit();
    } catch (Exception $e) {
        // Rollback jika terjadi error
        $conn->rollback();
        $_SESSION['error'] = "Terjadi kesalahan: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Tambah Pembelian Baru</title>
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .form-container {
            max-width: 800px;
            margin: 20px auto;
            padding: 20px;
            background-color: #fff;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0,0,0,0.1);
        }
        .form-title { text-align: center; margin-bottom: 30px; color: #333; }
        .form-group { margin-bottom: 20px; }
        .form-label { font-weight: bold; color: #555; }
        .form-control { border-radius: 5px; border: 1px solid #ddd; padding: 8px 12px; }
        .form-control:focus { border-color: #80bdff; box-shadow: 0 0 0 0.2rem rgba(0,123,255,.25); }
        .btn-container { text-align: center; margin-top: 30px; }
        .btn-submit { padding: 10px 30px; font-size: 16px; }
        .alert { margin-top: 20px; }
    </style>
</head>
<body class="bg-light">
    <div class="container">
        <div class="form-container">
            <h2 class="form-title">Tambah Pembelian Baru</h2>

            <?php if (isset($_SESSION['error'])): ?>
                <div class="alert alert-danger">
                    <?php 
                    echo $_SESSION['error'];
                    unset($_SESSION['error']);
                    ?>
                </div>
            <?php endif; ?>

            <form method="POST" action="">
                <div class="row">
                    <div class="col-md-6">
                        <!-- ID Pembelian -->
                        <div class="form-group">
                            <label class="form-label" for="beliID">ID Pembelian (beliID)</label>
                            <input type="text" class="form-control" id="beliID" name="beliID" value="<?php echo $newBeliID; ?>" readonly required>
                        </div>
                        <!-- Dropdown Supplier -->
                        <div class="form-group">
                            <label class="form-label" for="supplierID">Supplier</label>
                            <select class="form-control" id="supplierID" name="supplierID" required>
                                <option value="">Pilih Supplier</option>
                                <?php while ($supplier = $resultSupplier->fetch_assoc()): ?>
                                    <option value="<?php echo $supplier['supplierID']; ?>">
                                        <?php echo $supplier['namaKontak']; ?>
                                    </option>
                                <?php endwhile; ?>
                            </select>
                        </div>

                        <!-- Dropdown Barang -->
                        <div class="form-group">
                            <label class="form-label" for="idBarang">Barang</label>
                            <select class="form-control" id="idBarang" name="idBarang" required>
                                <option value="">Pilih Barang</option>
                            </select>
                        </div>

                        <!-- Tanggal Pembelian -->
                        <div class="form-group">
                            <label class="form-label" for="tanggal">Tanggal Pembelian</label>
                            <input type="date" class="form-control" id="tanggal" name="tanggal" required>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <!-- Jumlah -->
                        <div class="form-group">
                            <label class="form-label" for="jumlah">Jumlah</label>
                            <input type="number" class="form-control" id="jumlah" name="jumlah" required>
                        </div>

                        <!-- Harga per Unit -->
                        <div class="form-group">
                            <label class="form-label" for="harga">Harga per Unit</label>
                            <input type="number" class="form-control" id="harga" name="harga" required>
                        </div>

                        <!-- Total Harga -->
                        <div class="form-group">
                            <label class="form-label" for="totalHarga">Total Harga</label>
                            <input type="text" class="form-control" id="totalHarga" readonly>
                        </div>
                    </div>
                </div>

                <div class="btn-container">
                    <button type="submit" class="btn btn-primary btn-submit">Simpan Pembelian</button>
                    <a href="../../stok.php" class=" btn btn-secondary">Kembali</a>
                </div>
            </form>
        </div>
    </div>

    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        const jumlahInput = document.getElementById('jumlah');
        const hargaInput = document.getElementById('harga');
        const totalHargaInput = document.getElementById('totalHarga');
        const supplierSelect = document.getElementById('supplierID');
        const barangSelect = document.getElementById('idBarang');

        jumlahInput.addEventListener('input', calculateTotalHarga);
        hargaInput.addEventListener('input', calculateTotalHarga);
        supplierSelect.addEventListener('change', fetchBarangBySupplier);

        function calculateTotalHarga() {
            const jumlah = parseInt(jumlahInput.value);
            const harga = parseInt(hargaInput.value);
            const totalHarga = jumlah * harga;
            totalHargaInput.value = totalHarga.toLocaleString('id-ID', { style: 'currency', currency: 'IDR' });
        }

        // Fungsi untuk mengambil barang berdasarkan supplier
        function fetchBarangBySupplier() {
            const supplierID = supplierSelect.value;

            if (!supplierID) {
                barangSelect.innerHTML = '<option value="">Pilih Barang</option>';
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open('GET', 'getBarangBySupplier.php?supplierID=' + supplierID, true);
            xhr.onload = function() {
                if (xhr.status === 200) {
                    const barangData = JSON.parse(xhr.responseText);
                    let options = '<option value="">Pilih Barang</option>';
                    barangData.forEach(function(item) {
                        options += `<option value="${item.idBarang}">${item.namaBarang} - ${item.namaKategori} - ${item.namaJenis}</option>`;
                    });
                    barangSelect.innerHTML = options;
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>
