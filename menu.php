<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

function getMenu() {
    global $conn;
    $query = "SELECT menuID, namaMenu, deskripsi, harga, gambar FROM menu";
    return $conn->query($query);
}

$menuList = getMenu();
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
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Menu</h1>

            </div>

            <div class="container">
                <a href="admin/menu/addmenu.php" class="btn btn-primary mb-3">Tambah Menu Baru</a>
                <div class="card shadow mb-4">
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-bordered" id="dataTable" width="100%" cellspacing="0">
                                <thead>
                                    <tr>
                                        <th>Menu ID</th>
                                        <th>Nama Menu</th>
                                        <th>Deskripsi</th>
                                        <th>Harga</th>
                                        <th>Gambar</th>
                                        <th>Action</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php while ($row = $menuList->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo $row['menuID']; ?></td>
                                            <td>
                                                <a href="resep.php?id=<?php echo $row['menuID']; ?>">
                                                    <?php echo htmlspecialchars($row['namaMenu']); ?>
                                                </a>
                                            </td>
                                            <td><?php echo htmlspecialchars($row['deskripsi']); ?></td>
                                            <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                            <td>
                                                <?php if($row['gambar']): ?>
                                                    <img src="image/<?php echo htmlspecialchars($row['gambar']); ?>" 
                                                         alt="<?php echo htmlspecialchars($row['namaMenu']); ?>" 
                                                         style="max-width: 100px;">
                                                <?php else: ?>
                                                    <span class="text-muted">No image </span>
                                                <?php endif; ?>
                                            </td>
                                            <td>
                                                <a href="admin/menu/editmenu.php?id=<?php echo $row['menuID']; ?>" class="btn btn-warning btn-sm">Edit</a>
                                                <a href="admin/menu/deletemenu.php?id=<?php echo $row['menuID']; ?>" class="btn btn-danger btn-sm" onclick="return confirm('Are you sure you want to delete this menu?');">Delete</a>
                                            </td>
                                        </tr>
                                    <?php endwhile; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <!-- End of Page Content -->

    </div>
    <!-- End of Main Content -->


<?php 
require_once 'admin/scripts.php';
require_once 'admin/footer.php'; 
?>