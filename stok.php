<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

// Ambil halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 10; // Jumlah barang per halaman
$offset = ($page - 1) * $limit; // Hitung offset

// Ambil parameter sort dari URL dan validasi kolom yang diperbolehkan
$allowedSortColumns = ['idBarang', 'namaBarang', 'namaKategori', 'namaJenis', 'jumlahBarang'];
$sortColumn = isset($_GET['sort']) && in_array($_GET['sort'], $allowedSortColumns) ? $_GET['sort'] : 'jumlahBarang'; // Default sort by jumlahBarang
$sortOrder = isset($_GET['order']) && $_GET['order'] === 'desc' ? 'DESC' : 'ASC'; // Default order is ASC

// Query untuk menghitung total barang
$totalQuery = "SELECT COUNT(*) as total FROM Stok";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit); // Hitung total halaman

// Query untuk mengambil data stok dengan limit, offset, dan sorting
$query = "
    SELECT 
        s.idBarang, 
        s.namaBarang, 
        k.namaKategori,
        j.namaJenis,
        s.jumlahBarang, 
        s.leadTime,
        (SELECT SUM(ri.kuantitas * p.kuantitas) / 7 * s.leadTime FROM pesanan p 
        JOIN menu m ON p.menuID = m.menuID JOIN resep r ON m.menuID = r.menuID 
        JOIN recipe_ingredients ri ON r.resepID = ri.resepID 
        WHERE ri.idBarang = s.idBarang AND p.tanggalPesanan >= NOW() - INTERVAL 7 DAY) AS rop
    FROM 
        Stok s
    LEFT JOIN 
        Kategori k ON s.kategoriID = k.kategoriID
    LEFT JOIN 
        Jenis j ON s.jenisID = j.jenisID
    ORDER BY 
        CASE WHEN s.jumlahBarang <= (SELECT SUM(ri.kuantitas * p.kuantitas) / 7 * s.leadTime FROM pesanan p 
        JOIN menu m ON p.menuID = m.menuID JOIN resep r ON m.menuID = r.menuID 
        JOIN recipe_ingredients ri ON r.resepID = ri.resepID 
        WHERE ri.idBarang = s.idBarang AND p.tanggalPesanan >= NOW() - INTERVAL 7 DAY) THEN 0 ELSE 1 END ASC,
        s.idBarang ASC
    LIMIT ? OFFSET ?
";

$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$stokList = $stmt->get_result();

// Fungsi untuk menghitung ROP
function calculateROP($idBarang, $leadTime, $conn) {
    // Query untuk menghitung total demand dalam 7 hari terakhir
    $queryDemand = "
        SELECT 
            SUM(ri.kuantitas * p.kuantitas) AS totalDemand
        FROM 
            pesanan p
        JOIN 
            menu m ON p.menuID = m.menuID
        JOIN 
            resep r ON m.menuID = r.menuID
        JOIN 
            recipe_ingredients ri ON r.resepID = ri.resepID
        WHERE 
            p.tanggalPesanan >= NOW() - INTERVAL 7 DAY
            AND ri.idBarang = ?
    ";
    $stmtDemand = $conn->prepare($queryDemand);
    $stmtDemand->bind_param("s", $idBarang);
    $stmtDemand->execute();
    $demandResult = $stmtDemand->get_result()->fetch_assoc();
    
    // Ambil total demand dan hitung rata-rata permintaan per hari
    $totalDemand = isset($demandResult['totalDemand']) ? $demandResult['totalDemand'] : 0;
    $averageDemandPerDay = $totalDemand / 7; // Rata-rata permintaan per hari (dalam 7 hari terakhir)
    
    // Hitung ROP
    $rop = $averageDemandPerDay * $leadTime;
    return round($rop, 0); // Bulatkan ROP ke 2 angka desimal
}

// Fungsi untuk memeriksa dan menyimpan barang dengan stok kurang dari ROP
function checkLowStock($conn) {
    // Query untuk mengambil stok yang kurang dari ROP
    $query = "
        SELECT 
            s.idBarang, 
            s.namaBarang, 
            s.jumlahBarang, 
            s.leadTime
        FROM 
            Stok s
    ";

    $result = $conn->query($query);
    $lowStockItems = [];

    while ($row = $result->fetch_assoc()) {
        $ropValue = calculateROP($row['idBarang'], $row['leadTime'], $conn);
        if ($row['jumlahBarang'] <= $ropValue) {
            $lowStockItems[] = $row['namaBarang']; // Menyimpan nama barang yang stoknya kurang dari ROP
        }
    }

    // Menyimpan item stok rendah dalam sesi
    $_SESSION['lowStockItems'] = $lowStockItems;
}

// Panggil fungsi untuk memeriksa stok rendah
checkLowStock($conn);


?>



<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Topbar -->
    <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">

    <!-- Sidebar Toggle (Topbar) -->
    <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
        <i class="fa fa-bars"></i>
    </button>

    <!-- Topbar Navbar -->
    <ul class="navbar-nav ml-auto">

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <!-- Dropdown - Messages -->
            <div class="dropdown-menu dropdown-menu-right p-3 shadow animated--grow-in"
                aria-labelledby="searchDropdown">
                <form class="form-inline mr-auto w-100 navbar-search">
                    <div class="input-group">
                        <input type="text" class="form-control bg-light border-0 small"
                            placeholder="Search for..." aria-label="Search"
                            aria-describedby="basic-addon2">
                        <div class="input-group-append">
                            <button class="btn btn-primary" type="button">
                                <i class="fas fa-search fa-sm"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
        </li>

        <li class="nav-item no-arrow mx-1">
            <div class="nav-link">
                <span class="mr-2 d-none d-lg-inline text-gray-600">
                    <i class="fas fa-calendar fa-fw"></i>
                    <span id="tanggalHariIni"></span>
                    <br>
                    <i class="fas fa-clock fa-fw"></i>
                    <span id="jamDigital"></span>
                </span>
            </div>
        </li>

        <!-- Nav Item - User Information -->
        <li class="nav-item dropdown no-arrow">
            <a class="nav-link dropdown-toggle" href="#" id="userDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <span class="mr-2 d-none d-lg-inline text-gray-600 small">
                <?php
                if (isset($_SESSION['username'])) {
                    echo "" . $_SESSION['username'];
                } ?>
                </span>
                <img class="img-profile rounded-circle"
                    src="img/undraw_profile.svg">
            </a>
            <!-- Dropdown - User Information -->
            <div class="dropdown-menu dropdown-menu-right shadow animated--grow-in"
                aria-labelledby="userDropdown">
                <div class="dropdown-divider"></div>
                <a class="dropdown-item" href="logout.php">
                    <i class="fas fa-sign-out-alt fa-sm fa-fw mr-2 text-gray-400"></i>
                    Logout
                </a>
            </div>
        </li>

    </ul>

    </nav>
    <!-- End of Topbar -->
    <!-- Main Content -->
    <div id="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Stok Barang</h1>
                <a href="admin/stok/addstok.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                    <i class="fas fa-plus fa-sm text-white-50"></i> Tambah Stok
                </a>
                <a href="admin/stok/reportstok.php" class="btn btn-success btn-sm">
                    <i class="fas fa-file-excel"></i> Download Laporan Mingguan
                </a>

            </div>

            <!-- Content Row -->
            <div class="card shadow mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                            <thead class="bg-primary text-white">
                                <tr>
                                    <th>ID Barang</th>
                                    <th>Nama Barang</th>
                                    <th>Kategori</th>
                                    <th>Jenis</th>
                                    <th>Stok Tersedia & ROP</th> <!-- Gabungkan kolom Stok dan ROP -->
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody id="stokTable">
                                <?php while ($row = $stokList->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['idBarang']; ?></td>
                                        <td><?php echo $row['namaBarang']; ?></td>
                                        <td><?php echo $row['namaKategori']; ?></td>
                                        <td><?php echo $row['namaJenis']; ?></td>
                                        <td>
                                            <?php
                                            // Menghitung ROP dan menampilkan stok
                                            $ropValue = calculateROP($row['idBarang'], $row['leadTime'], $conn);
                                            if ($row['jumlahBarang'] <= $ropValue) {
                                                echo '<span class="low-stock" style="color: red;">' . $row['jumlahBarang'] . ' (ROP: ' . $ropValue . ')</span>';
                                            } else {
                                                echo $row['jumlahBarang'] . ' (ROP: ' . $ropValue . ')';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <a href="detailstok.php?id=<?php echo $row['idBarang']; ?>" class="btn btn-info btn-sm">
                                                <i class="fas fa-history"></i> Riwayat
                                            </a>
                                            <a href="admin/stok/editstok.php?id=<?php echo $row['idBarang']; ?>" class="btn btn-warning btn-sm">
                                                <i class="fas fa-edit"></i> Edit
                                            </a>
                                            <a href="rop.php?id=<?php echo $row['idBarang']; ?>" class="btn btn-info btn-sm">Hitung ROP</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>

            <!-- Pagination -->
            <nav aria-label="Page navigation">
                <ul class="pagination justify-content-center">
                    <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?php echo $i === $page ? 'active' : ''; ?>">
                            <a class="page-link" href="?page=<?php echo $i; ?>&sort=<?php echo $sortColumn; ?>&order=<?php echo $sortOrder; ?>">
                                <?php echo $i; ?>
                            </a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>

        </div>
        <!-- /.container-fluid -->
    </div>
    <!-- End of Main Content -->


<!-- JavaScript untuk Notifikasi -->
<script type="text/javascript">
    window.onload = function() {
        // Cek apakah ada elemen dengan kelas "low-stock" (stok kurang dari ROP)
        var lowStockItems = document.querySelectorAll('.low-stock');
        
        if (lowStockItems.length > 0) {
            // Tampilkan notifikasi jika ada stok yang kurang dari ROP
            alert("Peringatan: Ada stok barang yang kurang dari ROP!");
        }
    };
</script>

<?php
require_once 'admin/footer.php';
require_once 'admin/scripts.php';
?>
