<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

if (!isset($_SESSION['username']) || !isset($_SESSION['role'])) {
    header("location: login.html");
    exit();
}

// Periksa apakah pengguna adalah admin/staff
if ($_SESSION['role'] !== 'owner' && $_SESSION['role'] !== 'staff') {
    echo "<script>alert('Anda tidak memiliki izin untuk mengakses fitur ini.');</script>";
    echo "<script>window.location.href='index2.php';</script>";
    exit();
}

// Ambil halaman saat ini
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 5;
$offset = ($page - 1) * $limit;

// Ambil filter dari URL
$pesananID = isset($_GET['pesananID']) ? $_GET['pesananID'] : '';
$userID = isset($_GET['userID']) ? $_GET['userID'] : '';
$menuID = isset($_GET['menuID']) ? $_GET['menuID'] : '';
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : '';
$status = isset($_GET['status']) ? $_GET['status'] : '';

$search = isset($_GET['search']) ? $_GET['search'] : ''; // Ambil nilai pencarian

$query = "SELECT p.*, s.statName, m.namaMenu
          FROM pesanan p
          LEFT JOIN status s ON p.statID = s.statID
          LEFT JOIN menu m ON p.menuID = m.menuID
          WHERE 1=1";

// Cek apakah ada parameter pencarian
if ($search) {
    $query .= " AND (
        p.pesananID LIKE ? OR
        p.userID LIKE ? OR
        p.menuID LIKE ? OR
        p.kuantitas LIKE ? OR
        p.tanggalPesanan LIKE ? OR
        s.statName LIKE ? OR
        m.namaMenu LIKE ?
    )";
}

if ($pesananID) {
    $query .= " AND p.pesananID = ?";
}

if ($userID) {
    $query .= " AND p.userID = ?";
}
if ($menuID) {
    $query .= " AND p.menuID = ?";
}
if ($bulan) {
    $query .= " AND MONTH(p.tanggalPesanan) = ?";
}
if ($status) {
    $query .= " AND p.statID = ?";
}

$query .= " ORDER BY p.tanggalPesanan DESC LIMIT ? OFFSET ?";

// Persiapkan statement
$stmt = $conn->prepare($query);

// Bind parameter
$params = [];
$types = '';

// Jika ada pencarian, bind parameter pencarian
if ($search) {
    $searchTerm = "%$search%";
    $params[] = $searchTerm; // pesananID
    $params[] = $searchTerm; // userID
    $params[] = $searchTerm; // menuID
    $params[] = $searchTerm; // kuantitas
    $params[] = $searchTerm; // tanggalPesanan
    $params[] = $searchTerm; // statName
    $params[] = $searchTerm; // namaMenu
    $types .= 'sssssss'; // Tujuh string
}


if ($pesananID) {
    $params[] = $pesananID;
    $types .= 's';
}

if ($userID) {
    $params[] = $userID;
    $types .= 's';
}
if ($menuID) {
    $params[] = $menuID;
    $types .= 's';
}
if ($bulan) {
    $params[] = $bulan;
    $types .= 'i';
}
if ($status) {
    $params[] = $status;
    $types .= 's';
}

$params[] = $limit;
$params[] = $offset;
$types .= 'ii';

// Bind parameter ke statement
$stmt->bind_param($types, ...$params);
$stmt->execute();
$pesananList = $stmt->get_result();
// Fungsi untuk mengurangi stok
function kurangiStok($conn, $menuID, $kuantitas) {
    $queryIngredients = "
        SELECT 
            ri.idBarang, 
            ri.kuantitas 
        FROM 
            recipe_ingredients ri 
        JOIN 
            resep r ON ri.resepID = r.resepID 
        WHERE 
            r.menuID = ?
    ";

    $stmtIngredients = $conn->prepare($queryIngredients);
    $stmtIngredients->bind_param("s", $menuID);
    $stmtIngredients->execute();
    $ingredientsResult = $stmtIngredients->get_result();

    while ($ingredient = $ingredientsResult->fetch_assoc()) {
        $idBarang = $ingredient['idBarang'];
        $jumlahDibutuhkan = $ingredient['kuantitas'] * $kuantitas;

        $queryUpdateStock = "
            UPDATE stok 
            SET jumlahBarang = jumlahBarang - ? 
            WHERE idBarang = ?
        ";

        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bind_param("is", $jumlahDibutuhkan, $idBarang);
        $stmtUpdateStock->execute();
    }
}

// Proses pengurangan stok jika status pesanan S001 dan stok belum dikurangi
while ($pesanan = $pesananList->fetch_assoc()) {
    if ($pesanan['statID'] == 'S001' && $pesanan['stokDikurangi'] == 0) {
        kurangiStok($conn, $pesanan['menuID'], $pesanan['kuantitas']);

        // Update pesanan untuk menandai stok telah dikurangi
        $updatePesanan = "UPDATE pesanan SET stokDikurangi = 1 WHERE pesananID = ?";
        $stmtUpdatePesanan = $conn->prepare($updatePesanan);
        $stmtUpdatePesanan->bind_param("s", $pesanan['pesananID']);
        $stmtUpdatePesanan->execute();
    }
}
$totalQuery = "SELECT COUNT(*) as total FROM pesanan";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalItems = $totalRow['total'];
$totalPages = ceil($totalItems / $limit); // Hitung total halaman
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

    <form method="GET" action="pesanan.php" class="form-inline mb-3">
        <div class="form-group mr-2">
            <input type="text" class="form-control" name="search" placeholder="Cari Nama Menu" value="<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
        </div>
        <button type="submit" class="btn btn-primary">Search</button>
    </form>    

        <!-- Nav Item - Search Dropdown (Visible Only XS) -->
        <li class="nav-item dropdown no-arrow d-sm-none">
            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                <i class="fas fa-search fa-fw"></i>
            </a>
            <!-- Dropdown - Messages -->

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
                <h1 class="h3 mb-0 text-gray-800">Pesanan</h1>
                <a href="admin/pesanan/exportpesanan.php" 
                    class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm 
                    <?php echo (($_SESSION['role'] === 'customer' || $_SESSION['role'] === 'staff') ? 'disabled' : ''); ?>"
                    <?php echo (($_SESSION['role'] === 'customer' || $_SESSION['role'] === 'staff') ? 'onclick="return false;"' : ''); ?>>
                    <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                </a>
            </div>
            <!-- <a href="admin/pesanan/addpesanan.php" class="btn btn-primary mb-3">Tambah Pesanan Baru</a> -->
            
            <div class="container-fluid">
                <form method="GET" action="pesanan.php">
                    <!-- Tabel pesanan -->
                    <table class="table">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Nama Menu</th>
                                <th>Kuantitas</th>
                                <th>Tanggal Pesanan</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($pesananList as $pesanan): ?>
                                <tr>
                                    <td><?php echo $pesanan['pesananID']; ?></td>
                                    <td><?php echo $pesanan['namaMenu']; ?></td>
                                    <td><?php echo $pesanan['kuantitas']; ?></td>
                                    <td><?php echo date('d-m-Y', strtotime($pesanan['tanggalPesanan'])); ?></td>
                                    <td><?php echo $pesanan['statName']; ?></td>
                                    <td>
                                        <a href="admin/pesanan/editpesanan.php?id=<?php echo $pesanan['pesananID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                        <a href="admin/pesanan/deletepesanan.php?id=<?php echo $pesanan['pesananID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i == $page) ? 'active' : ''; ?>">
                                <a class="page-link" href="pesanan.php?page=<?php echo $i; ?>&userID=<?php echo $userID; ?>&menuID=<?php echo $menuID; ?>&bulan=<?php echo $bulan; ?>&status=<?php echo $status; ?>&search=<?php echo isset($_GET['search']) ? $_GET['search'] : ''; ?>">
                                        <?php echo $i; ?>
                                    </a>
                                </li>
                            <?php endfor; ?>
                        </ul>
                    </nav>

                </form>
            </div>
        </div>
    </div>
<?php
require_once 'admin/scripts.php';
require_once 'admin/footer.php';
?>
