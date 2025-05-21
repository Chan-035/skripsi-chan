<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

// Fetching filters from GET request
$month = $_GET['bulan'] ?? null;
$menu = $_GET['menu'] ?? null;
$status = $_GET['status'] ?? null;

// Converting month name to number
$monthNum = date("m", strtotime($month));

// Preparing the query to fetch order details
$orderDetailsQuery = "
    SELECT p.pesananID, p.tanggalPesanan, m.namaMenu, p.kuantitas, p.totalHarga, s.statName
    FROM pesanan p
    JOIN menu m ON p.menuID = m.menuID
    JOIN status s ON p.statID = s.statID
    WHERE MONTH(p.tanggalPesanan) = ? AND m.namaMenu = ? AND s.statName = ?
    ORDER BY p.tanggalPesanan ASC
";
$stmt = $conn->prepare($orderDetailsQuery);
$stmt->bind_param("iss", $monthNum, $menu, $status);
$stmt->execute();
$result = $stmt->get_result();

?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">

        <!-- Topbar -->
        <nav class="navbar navbar-expand navbar-light bg-white topbar mb-4 static-top shadow">
            <button id="sidebarToggleTop" class="btn btn-link d-md-none rounded-circle mr-3">
                <i class="fa fa-bars"></i>
            </button>
            <ul class="navbar-nav ml-auto">
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
            </ul>
        </nav>
        <!-- End of Topbar -->

        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <h1 class="h3 mb-4 text-gray-800">Detail Pesanan</h1>

            <!-- Order Details Table -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">
                        Pesanan - Bulan: <?php echo htmlspecialchars($month); ?> | Menu: <?php echo htmlspecialchars($menu); ?> | Status: <?php echo htmlspecialchars($status); ?>
                    </h6>
                </div>
                <div class="table-responsive">
                    <table class="table table-bordered" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>ID Pesanan</th>
                                <th>Tanggal Pesanan</th>
                                <th>Nama Menu</th>
                                <th>Kuantitas</th>
                                <th>Total Harga</th>
                                <th>Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result->num_rows > 0): ?>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($row['pesananID']); ?></td>
                                        <td><?php echo htmlspecialchars($row['tanggalPesanan']); ?></td>
                                        <td><?php echo htmlspecialchars($row['namaMenu']); ?></td>
                                        <td><?php echo htmlspecialchars($row['kuantitas']); ?></td>
                                        <td>Rp <?php echo number_format($row['totalHarga'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($row['statName']); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="6" class="text-center">Tidak ada data pesanan untuk filter yang dipilih.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>
            </div>

        </div>
        <!-- /.container-fluid -->

    </div>
    <!-- End of Main Content -->

<?php
require_once 'admin/scripts.php';
require_once 'admin/footer.php';
?>
