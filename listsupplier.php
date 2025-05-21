<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

// Ambil supplierID dari parameter URL
$supplierID = isset($_GET['supplierID']) ? $_GET['supplierID'] : null;

if (!$supplierID) {
    echo "Supplier ID tidak valid!";
    exit();
}

// Proses form jika ada data yang dikirim
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['idBarang'])) {
        $idBarang = $_POST['idBarang'];

        // Cek apakah barang sudah ada di daftar supplier
        $checkQuery = "SELECT * FROM listsupplier WHERE supplierID = ? AND idBarang = ?";
        $checkStmt = $conn->prepare($checkQuery);
        $checkStmt->bind_param("ss", $supplierID, $idBarang);
        $checkStmt->execute();
        $checkResult = $checkStmt->get_result();

        if ($checkResult->num_rows > 0) {
            echo "<script>alert('Barang sudah ada dalam daftar supplier.');</script>";
        } else {
            // Tambahkan barang ke supplier
            $insertQuery = "INSERT INTO listsupplier (supplierID, idBarang) VALUES (?, ?)";
            $insertStmt = $conn->prepare($insertQuery);
            $insertStmt->bind_param("ss", $supplierID, $idBarang);

            if ($insertStmt->execute()) {
                echo "<script>alert('Barang berhasil ditambahkan.'); window.location.href='listsupplier.php?supplierID=$supplierID';</script>";
            } else {
                echo "<script>alert('Gagal menambahkan barang.');</script>";
            }
        }
    }
}

// Proses penghapusan barang
if (isset($_GET['delete']) && isset($_GET['idBarang'])) {
    $idBarang = $_GET['idBarang'];

    $deleteQuery = "DELETE FROM listsupplier WHERE supplierID = ? AND idBarang = ?";
    $deleteStmt = $conn->prepare($deleteQuery);
    $deleteStmt->bind_param("ss", $supplierID, $idBarang);

    if ($deleteStmt->execute()) {
        echo "<script>alert('Barang berhasil dihapus.'); window.location.href='listsupplier.php?supplierID=$supplierID';</script>";
    } else {
        echo "<script>alert('Gagal menghapus barang.');</script>";
    }
}

// Query untuk mengambil data dari tabel listsupplier
$query = "
    SELECT 
        ls.supplierID,
        ls.idBarang,
        s.namaBarang
    FROM 
        listsupplier ls
    JOIN 
        stok s ON ls.idBarang = s.idBarang
    WHERE 
        ls.supplierID = ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("s", $supplierID);
$stmt->execute();
$result = $stmt->get_result();

// Query untuk mengambil daftar barang dari tabel stok
$stokQuery = "SELECT idBarang, namaBarang FROM stok";
$stokResult = $conn->query($stokQuery);
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Daftar Barang Supplier</h1>
            </div>

            <div class="container">
                <table class="table">
                    <thead>
                        <tr>
                            <th>ID Barang</th>
                            <th>Nama Barang</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td><?php echo htmlspecialchars($row['idBarang']); ?></td>
                                <td><?php echo htmlspecialchars($row['namaBarang']); ?></td>
                                <td>
                                    <a href="listsupplier.php?supplierID=<?php echo $supplierID; ?>&delete=1&idBarang=<?php echo urlencode($row['idBarang']); ?>" 
                                       class="btn btn-danger btn-sm"
                                       onclick="return confirm('Apakah Anda yakin ingin menghapus barang ini?');">
                                        Hapus
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>

            <!-- Form untuk menambahkan barang ke supplier -->
            <div class="container mt-4">
                <h4>Tambah Barang ke Supplier</h4>
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="idBarang">Pilih Barang</label>
                        <select name="idBarang" id="idBarang" class="form-control" required>
                            <option value="">-- Pilih Barang --</option>
                            <?php while ($stokRow = $stokResult->fetch_assoc()): ?>
                                <option value="<?php echo htmlspecialchars($stokRow['idBarang']); ?>">
                                    <?php echo htmlspecialchars($stokRow['namaBarang']); ?>
                                </option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Tambah Barang</button>
                </form>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

<?php
    require_once 'admin/scripts.php';
    require_once 'admin/footer.php';
?>
