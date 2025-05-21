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
$limit = 5; // Jumlah supplier per halaman
$offset = ($page - 1) * $limit; // Hitung offset

// Query untuk menghitung total supplier
$totalQuery = "SELECT COUNT(*) as total FROM supplier";
$totalResult = $conn->query($totalQuery);
$totalRow = $totalResult->fetch_assoc();
$totalSuppliers = $totalRow['total'];
$totalPages = ceil($totalSuppliers / $limit); // Hitung total halaman

// Query untuk mengambil data supplier dengan limit dan offset
$query = "SELECT * FROM supplier LIMIT ? OFFSET ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("ii", $limit, $offset);
$stmt->execute();
$staffList = $stmt->get_result();
?>

        <!-- Content Wrapper -->
        <div id="content-wrapper" class="d-flex flex-column">

            <!-- Main Content -->
            <div id="content">

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
                            <a class="nav-link dropdown-toggle" href="#" id="searchDropdown" role="button"
                                data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                <i class="fas fa-search fa-fw"></i>
                            </a>
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

                <!-- Begin Page Content -->
                <div class="container-fluid">

            <!-- Page Heading -->
                <div class="d-sm-flex align-items-center justify-content-between mb-4">
                    <h1 class="h3 mb-0 text-gray-800">Supplier</h1>
                    <a href="admin/supplier/exportsupplier.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm
                    <?php echo (($_SESSION['role'] === 'customer' || $_SESSION['role'] === 'staff') ? 'disabled' : ''); ?>"
                    <?php echo (($_SESSION['role'] === 'customer' || $_SESSION['role'] === 'staff') ? 'onclick="return false;"' : ''); ?>>
                        <i class="fas fa-download fa-sm text-white-50"></i> Generate Report
                    </a>
                </div>

                <div class="container">
                    <a href="admin/supplier/addsupplier.php" class="btn btn-primary">Add New Supplier</a>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Supplier ID</th>
                                <th>Nama</th>
                                <th>Kontak</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $staffList->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo $row['supplierID']; ?></td>
                                    <td><?php echo $row['namaKontak']; ?></td>
                                    <td><?php echo $row['kontak']; ?></td>
                                    <td>
                                        <a href="listsupplier.php?supplierID=<?php echo $row['supplierID']; ?>" class="btn btn-info">Lihat Barang</a>
                                        <a href="admin/supplier/editsupplier.php?id=<?php echo $row['supplierID']; ?>" class="btn btn-warning">Edit</a>
                                        <a href="admin/supplier/deletesupplier.php?id=<?php echo urlencode($row['supplierID']); ?>" 
                                            class="btn btn-danger" 
                                            onclick="return confirm('Apakah Anda yakin ingin menghapus supplier ini?');">
                                            Hapus
                                        </a>

                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>

                    <!-- Pagination -->
                    <nav aria-label="Page navigation">
                        <ul class="pagination">
                            <?php if ($page > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page - 1; ?>">Previous</a>
                                </li>
                            <?php endif; ?>

                            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                                <li class="page-item <?php echo ($i === $page) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?page=<?php echo $i; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <?php if ($page < $totalPages): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?page=<?php echo $page + 1; ?>">Next</a>
                                </li>
                            <?php endif; ?>
                        </ul>
                    </nav>
                </div>
            </div>
        </div>

<?php
    require_once 'admin/scripts.php';
    require_once 'admin/footer.php';
?>