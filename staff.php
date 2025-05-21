<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

function getStaff() {
    global $conn;
    $query = "SELECT * FROM staff";
    return $conn->query($query);
}

$staffList = getStaff();
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
                        <h1 class="h3 mb-0 text-gray-800">Staff</h1>
                        <a href="admin/staff/exportstaff.php" class="d-none d-sm-inline-block btn btn-sm btn-primary shadow-sm">
                        <i class="fas fa-download fa-sm text-white-50"></i> Generate Report</a>
                    </div>

                    <!-- Content Row -->
                    <div class="container">
                        <a href="admin/staff/addstaff.php" class="btn btn-primary">Add New Staff</a>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Staff ID</th>
                                    <th>Nama Depan</th>
                                    <th>Nama Belakang</th>
                                    <th>Username</th>
                                    <th>Alamat</th>
                                    <th>No HP</th>
                                    <th>Email</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $staffList->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['staffID']; ?></td>
                                        <td><?php echo $row['namaDepan']; ?></td>
                                        <td><?php echo $row['namaBelakang']; ?></td>
                                        <td><?php echo $row['username']; ?></td>
                                        <td><?php echo $row['alamat']; ?></td>
                                        <td><?php echo $row['noHP']; ?></td>
                                        <td><?php echo $row['email']; ?></td>
                                        <td>
                                            <a href="admin/staff/editstaff.php?id=<?php echo $row['staffID']; ?>" class="btn btn-warning">Edit</a>
                                            <a href="admin/staff/deletestaff.php?id=<?php echo $row['staffID']; ?>" class="btn btn-danger" onclick="return confirm('Are you sure?');">Delete</a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>
                    <!-- Content Row -->
                    

                </div>
                <!-- /.container-fluid -->


            <!-- Footer -->
            
        <!-- End of Content Wrapper -->
    <?php
    require_once 'admin/scripts.php';
    require_once 'admin/footer.php';
    ?>