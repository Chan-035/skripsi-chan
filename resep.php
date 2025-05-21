<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

// Ambil ID dari parameter URL
$idMenu = isset($_GET['id']) ? $_GET['id'] : null;

if ($idMenu) {
    // Query untuk mengambil resep berdasarkan menuID
    $query = "SELECT r.resepID, r.resepNama
              FROM resep r 
              WHERE r.menuID = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $idMenu);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $resep = $result->fetch_assoc();
    } else {
        // Jika resep tidak ditemukan, buat resep baru
        $queryLastID = "SELECT resepID FROM resep ORDER BY resepID DESC LIMIT 1";
        $resultLastID = $conn->query($queryLastID);
        $lastID = $resultLastID->fetch_assoc()['resepID'] ?? 'R000';

        // Generate resepID baru
        $newID = sprintf("R%03d", intval(substr($lastID, 1)) + 1);

        // Masukkan data baru ke tabel resep
        $resepNamaDefault = "Resep "; // Nama default untuk resep baru
        $insertQuery = "INSERT INTO resep (resepID, menuID, resepNama) VALUES (?, ?, ?)";
        $stmtInsert = $conn->prepare($insertQuery);
        $stmtInsert->bind_param("ssd", $newID, $idMenu, $resepNamaDefault);
        $stmtInsert->execute();

        // Ambil kembali data resep yang baru saja ditambahkan
        $resep = [
            'resepID' => $newID,
            'resepNama' => $resepNamaDefault,
        ];
    }

    // Query untuk mengambil bahan-bahan resep berdasarkan resepID
    $queryBahan = "SELECT ri.kuantitas, s.namaBarang 
                   FROM recipe_ingredients ri 
                   JOIN stok s ON ri.idBarang = s.idBarang 
                   WHERE ri.resepID = ?";
    $stmtBahan = $conn->prepare($queryBahan);
    $stmtBahan->bind_param("s", $resep['resepID']);
    $stmtBahan->execute();
    $resultBahan = $stmtBahan->get_result();
} else {
    echo "ID menu tidak valid.";
    exit();
}

// Flag untuk mengecek apakah bahan berhasil ditambahkan
$successMessage = "";

// Proses penambahan bahan baru
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Ambil data bahan dan kuantitas dari form
    $idBarangList = $_POST['idBarang']; // List bahan yang dipilih
    $kuantitasList = $_POST['kuantitas']; // List kuantitas bahan

    if ($idBarangList && $kuantitasList) {
        // Pastikan kuantitas adalah angka desimal valid
        foreach ($idBarangList as $key => $idBarang) {
            $kuantitas = floatval($kuantitasList[$key]); // Pastikan kuantitas diproses sebagai angka desimal

            // Masukkan bahan baru ke tabel recipe_ingredients
            $insertQuery = "INSERT INTO recipe_ingredients (resepID, idBarang, kuantitas) VALUES (?, ?, ?)";
            $stmtInsert = $conn->prepare($insertQuery);
            $stmtInsert->bind_param("ssd", $resep['resepID'], $idBarang, $kuantitas);

            if (!$stmtInsert->execute()) {
                echo "<script>alert('Gagal menambahkan bahan: $idBarang');</script>";
            }
        }
        // Set pesan sukses
        $successMessage = "Bahan berhasil ditambahkan.";

        // Redirect setelah berhasil menambahkan bahan
        header("Location: " . $_SERVER['REQUEST_URI']);
        exit(); // Penting untuk menghentikan eksekusi script setelah redirect
    } else {
        echo "<script>alert('Silakan pilih bahan dan masukkan kuantitas.');</script>";
    }
}
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Detail Resep</h1>
                <a href="menu.php" class="btn btn-secondary">Kembali ke Menu</a>
            </div>

            <!-- Resep Detail Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary"><?php echo $resep['resepNama']; ?></h6>
                </div>
                <div class="card-body">
                    <h5>Bahan-bahan:</h5>
                    <ul>
                        <?php while ($bahan = $resultBahan->fetch_assoc()): ?>
                            <li><?php echo $bahan['namaBarang'] . " - " . number_format($bahan['kuantitas'], 2); ?></li>
                        <?php endwhile; ?>
                    </ul>
                </div>
            </div>

            <!-- Form Tambah Bahan -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Tambah Bahan Baru</h6>
                </div>
                <div class="card-body">
                    <?php if ($successMessage): ?>
                        <div class="alert alert-success"><?php echo $successMessage; ?></div>
                    <?php endif; ?>
                    <form id="bahanForm" method="POST" action="">
                        <div id="dynamicBahanFields">
                            <!-- Form input bahan akan muncul di sini -->
                        </div>
                        <button type="button" id="addBahanBtn" class="btn btn-secondary">Tambah Bahan</button>
                        <button type="submit" class="btn btn-primary">Simpan Bahan</button>
                    </form>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

    <!-- Footer -->
    <?php require_once 'admin/footer.php'; ?>
    <!-- End of Footer -->

</div>
<!-- End of Content Wrapper -->

<script>
    // Fungsi untuk menambahkan form bahan baru
    document.getElementById('addBahanBtn').addEventListener('click', function() {
        var bahanFields = document.getElementById('dynamicBahanFields');
        var newField = document.createElement('div');
        newField.classList.add('form-group');
        newField.innerHTML = `
            <div class="row">
                <div class="col-md-6">
                    <label for="idBarang">Pilih Bahan</label>
                    <select class="form-control" name="idBarang[]" required>
                        <option value="">-- Pilih Bahan --</option>
                        <?php
                        // Ambil daftar barang dari tabel stok
                        $barangQuery = "SELECT idBarang, namaBarang FROM stok";
                        $barangResult = $conn->query($barangQuery);

                        while ($barang = $barangResult->fetch_assoc()): ?>
                            <option value="<?php echo $barang['idBarang']; ?>">
                                <?php echo $barang['namaBarang']; ?>
                            </option>
                        <?php endwhile; ?>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="kuantitas">Kuantitas</label>
                    <input type="number" class="form-control" name="kuantitas[]" min="0.01" step="0.01" required>
                </div>
            </div>
        `;
        bahanFields.appendChild(newField);
    });
</script>
