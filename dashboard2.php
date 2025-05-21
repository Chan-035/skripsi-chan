<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

$pesanan = "SELECT 
            MONTH(p.tanggalPesanan) AS bulan,
            COUNT(p.pesananID) AS total_pesanan,
            SUM(p.kuantitas) AS total_kuantitas,
            SUM(p.totalHarga) AS total_pendapatan,
            m.namaMenu,
            s.statName
          FROM pesanan p
          LEFT JOIN menu m ON p.menuID = m.menuID
          LEFT JOIN status s ON p.statID = s.statID
          GROUP BY MONTH(p.tanggalPesanan), m.namaMenu, s.statName
          ORDER BY MONTH(p.tanggalPesanan)";

$result = $conn->query($pesanan);

$currentMonth = date('m');
$currentYear = date('Y');

$monthlyEarningsQuery = "SELECT SUM(p.totalHarga) as monthlyEarnings 
                         FROM pesanan p
                         JOIN status s ON p.statID = s.statID
                         WHERE MONTH(p.tanggalPesanan) = $currentMonth 
                         AND YEAR(p.tanggalPesanan) = $currentYear
                         AND s.statName = 'Done'";

$monthlyEarningsResult = $conn->query($monthlyEarningsQuery);
$monthlyEarnings = $monthlyEarningsResult->fetch_assoc()['monthlyEarnings'];

// Query untuk menghitung total pendapatan tahunan
$currentYear = date('Y');
$annualEarningsQuery = "SELECT SUM(p.totalHarga) as annualEarnings 
                        FROM pesanan p
                        JOIN status s ON p.statID = s.statID
                        WHERE YEAR(p.tanggalPesanan) = $currentYear
                        AND s.statName = 'Done'";

$annualEarningsResult = $conn->query($annualEarningsQuery);
$annualEarnings = $annualEarningsResult->fetch_assoc()['annualEarnings'];

// Hitung total pesanan
$totalOrdersQuery = "SELECT COUNT(*) as totalOrders FROM pesanan";
$totalOrdersResult = $conn->query($totalOrdersQuery);
$totalOrders = $totalOrdersResult->fetch_assoc()['totalOrders'];

// Hitung pesanan yang belum selesai
$notDoneOrdersQuery = "SELECT COUNT(*) as notDoneOrders FROM pesanan p
                       JOIN status s ON p.statID = s.statID
                       WHERE s.statName != 'Done'";
$notDoneOrdersResult = $conn->query($notDoneOrdersQuery);
$notDoneOrders = $notDoneOrdersResult->fetch_assoc()['notDoneOrders'];

// Hitung persentase
$percentageNotDone = ($totalOrders > 0) ? ($notDoneOrders / $totalOrders) * 100 : 0;

// Query untuk menghitung jumlah pesanan yang belum selesai
$pendingOrdersQuery = "SELECT COUNT(*) as pendingOrders FROM pesanan p
                       JOIN status s ON p.statID = s.statID
                       WHERE s.statName != 'Done'";
$pendingOrdersResult = $conn->query($pendingOrdersQuery);
$pendingOrders = $pendingOrdersResult->fetch_assoc()['pendingOrders'];

// Siapkan array untuk data grafik
$bulan = [];
$total_pesanan = [];
$total_kuantitas = [];
$total_pendapatan = [];
$nama_menu = []; // Tambahkan array baru untuk nama menu
$status_pesanan = [];

while($row = $result->fetch_assoc()) {
    $nama_bulan = date("F", mktime(0, 0, 0, $row['bulan'], 10));
    
    $bulan[] = $nama_bulan;
    $total_pesanan[] = $row['total_pesanan'];
    $total_kuantitas[] = $row['total_kuantitas'];
    $total_pendapatan[] = $row['total_pendapatan'];
    $nama_menu[] = $row['namaMenu']; // Tambahkan nama menu ke array
    $status_pesanan[] = $row['statName'];
}
$selectedMonth = isset($_GET['bulan']) ? intval($_GET['bulan']) : null;

$pesananQuery = "SELECT 
                    m.namaMenu, 
                    COUNT(p.pesananID) AS total_pesanan
                 FROM pesanan p
                 LEFT JOIN menu m ON p.menuID = m.menuID
                 WHERE 1=1";

if ($selectedMonth) {
    $pesananQuery .= " AND MONTH(p.tanggalPesanan) = $selectedMonth";
}

$pesananQuery .= " GROUP BY m.namaMenu ORDER BY total_pesanan DESC";

$resultPesanan = $conn->query($pesananQuery);

// Siapkan data untuk grafik
$namaMenu = [];
$totalPesanan = [];

while ($row = $resultPesanan->fetch_assoc()) {
    $namaMenu[] = $row['namaMenu'];
    $totalPesanan[] = $row['total_pesanan'];
}

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

                    <!-- Topbar Search -->
                    <form
                        class="d-none d-sm-inline-block form-inline mr-auto ml-md-3 my-2 my-md-0 mw-100 navbar-search">
                        <div class="input-group">
                            <input type="text" class="form-control bg-light border-0 small" placeholder="Search for..."
                                aria-label="Search" aria-describedby="basic-addon2">
                            <div class="input-group-append">
                                <button class="btn btn-primary" type="button">
                                    <i class="fas fa-search fa-sm"></i>
                                </button>
                            </div>
                        </div>
                    </form>

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
                        <h1 class="h3 mb-0 text-gray-800">Dashboard</h1>
                        <a href="#" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm"><i
                                class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="row">

                    <!-- Earnings (Monthly) Card Example -->
                    <div class="col-xl-3 col-md-6 mb-4">
                    <a href="monthly_sales_detail.php?month=<?php echo $currentMonth; ?>&year=<?php echo $currentYear; ?>" class="text-decoration-none">
                        <div class="card border-left-primary shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                                            Earnings (Monthly)
                                        </div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            Rp <?php echo number_format($monthlyEarnings ?? 0, 0, ',', '.'); ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-calendar fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                            </a>
                    </div>

                        <!-- Earnings (Annual) Card Example -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-success shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                                                Earnings (Annual)</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                Rp <?php echo number_format($annualEarnings ?? 0, 0, ',', '.'); ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Persentase Pesanan Belum Done -->
                    <div class="col-xl-3 col-md-6 mb-4">
                        <div class="card border-left-warning shadow h-100 py-2">
                            <div class="card-body">
                                <div class="row no-gutters align-items-center">
                                    <div class="col mr-2">
                                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                                            Pesanan Belum Selesai</div>
                                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                                            <?php echo number_format($percentageNotDone, 2) . '%'; ?>
                                        </div>
                                    </div>
                                    <div class="col-auto">
                                        <i class="fas fa-tasks fa-2x text-gray-300"></i>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                        <!-- Pending Orders Card -->
                        <div class="col-xl-3 col-md-6 mb-4">
                            <div class="card border-left-danger shadow h-100 py-2">
                                <div class="card-body">
                                    <div class="row no-gutters align-items-center">
                                        <div class="col mr-2">
                                            <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                                                Pesanan Belum Selesai</div>
                                            <div class="h5 mb-0 font-weight-bold text-gray-800">
                                                <?php echo $pendingOrders; ?>
                                            </div>
                                        </div>
                                        <div class="col-auto">
                                            <i class="fas fa-clock fa-2x text-gray-300"></i>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Content Row -->
                    <form method="GET" action="dashboard.php" class="mb-4">
                        <label for="bulanSelect">Pilih Bulan:</label>
                        <select id="bulanSelect" name="bulan" onchange="this.form.submit()">
                            <option value="">Semua Bulan</option>
                            <?php 
                            for ($m = 1; $m <= 12; $m++) {
                                $monthName = date('F', mktime(0, 0, 0, $m, 10));
                                $selected = ($m == $_GET['bulan']) ? 'selected' : '';
                                echo "<option value='$m' $selected>$monthName</option>";
                            }
                            ?>
                        </select>
                    </form>

                    <div class="row">



                        <!-- Grafik Total Pesanan -->
                        <div class="col-xl-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Total Pesanan per Bulan</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartPesanan"></canvas>
                                </div>
                            </div>
                        </div>

                        <!-- Grafik Total Pendapatan -->
                        <div class="col-xl-6">
                            <div class="card shadow mb-4">
                                <div class="card-header py-3">
                                    <h6 class="m-0 font-weight-bold text-primary">Total Pendapatan per Bulan</h6>
                                </div>
                                <div class="card-body">
                                    <canvas id="chartPendapatan"></canvas>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Tabel Data Penjualan -->
                    <div class="card shadow mb-4">
                        <div class="card-header py-3">
                            <h6 class="m-0 font-weight-bold text-primary">Data Penjualan</h6>
                        </div>
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Bulan</th>
                                        <th>Nama Menu</th>
                                        <th>Total Kuantitas</th>
                                        <th>Total Pendapatan</th>
                                        <th>Status</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php for($i = 0; $i < count($bulan); $i++): ?>
                                        <tr onclick="window.location='datapenujualan.php'" style="cursor: pointer;">
                                            <td><?php echo $bulan[$i]; ?></td>
                                            <td><?php echo $nama_menu[$i]; ?></td>
                                            <td><?php echo $total_kuantitas[$i]; ?></td>
                                            <td>Rp <?php echo number_format($total_pendapatan[$i], 0, ',', '.'); ?></td>
                                            <td><?php echo $status_pesanan[$i]; ?></td>
                                        </tr>
                                    <?php endfor; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                        
                    </div>
                    <!-- Content Row -->
                    

                </div>
                <!-- /.container-fluid -->


            <!-- Footer -->
            
        <!-- End of Content Wrapper -->


<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    document.querySelectorAll('#dataTable tbody tr').forEach(row => {
        row.addEventListener('click', function() {
            // Ambil data dari baris yang diklik
            const bulan = this.cells[0].textContent;
            const menu = this.cells[1].textContent;
            const status = this.cells[4].textContent;                
            // Redirect ke pesanan.php dengan parameter
            window.location.href = `datapenjualan.php?bulan=${encodeURIComponent(bulan)}&menu=${encodeURIComponent(menu)}&status=${encodeURIComponent(status)}`;
        });
    });

     // Data untuk grafik dari PHP
     const namaMenu = <?php echo json_encode($namaMenu); ?>;
    const totalPesanan = <?php echo json_encode($totalPesanan); ?>;
    const bulanData = <?php echo json_encode($bulan); ?>;
    const totalPendapatanData = <?php echo json_encode($total_pendapatan); ?>;

    // Grafik Pesanan per Menu
    const ctxPesanan = document.getElementById('chartPesanan').getContext('2d');
    new Chart(ctxPesanan, {
        type: 'bar',
        data: {
            labels: namaMenu,
            datasets: [{
                label: 'Total Pesanan',
                data: totalPesanan,
                backgroundColor: 'rgba(75, 192, 192, 0.2)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            },
            plugins: {
                legend: {
                    display: true,
                    position: 'top',
                },
                title: {
                    display: true,
                    text: 'Total Pesanan per Menu'
                }
            }
        }
    });

    // Grafik Total Pendapatan per Bulan
    const ctxPendapatan = document.getElementById('chartPendapatan').getContext('2d');
    new Chart(ctxPendapatan, {
        type: 'line',
        data: {
            labels: bulanData,
            datasets: [{
                label: 'Total Pendapatan (Rp)',
                data: totalPendapatanData,
                backgroundColor: 'rgba(75, 192, 192, 0.6)',
                borderColor: 'rgba(75, 192, 192, 1)',
                borderWidth: 2,
                fill: true
            }]
        },
        options: {
            responsive: true,
            scales: {
                y: {
                    beginAtZero: true
                }
            }
        }
    });
</script>


    <?php
    require_once 'admin/scripts.php';
    require_once 'admin/footer.php';
    ?>