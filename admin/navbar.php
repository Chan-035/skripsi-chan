<?php


// Cek role dari session
$role = isset($_SESSION['role']) ? $_SESSION['role'] : 'guest'; // default 'guest' jika belum login
?>

<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0-beta3/css/all.min.css">

<!-- Sidebar -->
<ul class="navbar-nav bg-gradient-primary sidebar sidebar-dark accordion" id="accordionSidebar">

<!-- Sidebar - Brand -->
<a class="sidebar-brand d-flex align-items-center justify-content-center" href="dashboard.php">
    <div class="sidebar-brand-icon rotate-n-15">
        <i class="fas fa-laugh-wink"></i>
    </div>
    <div class="sidebar-brand-text mx-3">Dashboard E'K</div>
</a>

<!-- Divider -->
<hr class="sidebar-divider my-0">

<!-- Nav Item - Dashboard -->
<li class="nav-item active">
    <a class="nav-link" href="dashboard.php">
        <i class="fas fa-fw fa-tachometer-alt"></i>
        <span>Dashboard</span></a>
</li>

<!-- Divider -->
<hr class="sidebar-divider">

<!-- Heading -->
<div class="sidebar-heading">
    Interface
</div>

<?php if ($role == 'owner'): ?>
    <li class="nav-item">
        <a class="nav-link" href="staff.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Staff</span></a>
    </li>

    <li class="nav-item">
        <a class="nav-link" href="pelanggan.php">
            <i class="fas fa-fw fa-chart-area"></i>
            <span>Pelanggan</span></a>
    </li>
<?php endif; ?>

<li class="nav-item">
    <a class="nav-link" href="pesanan.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Pesanan</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="menu.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Menu</span></a>
</li>

<li class="nav-item">
    <a class="nav-link" href="supplier.php">
        <i class="fas fa-fw fa-chart-area"></i>
        <span>Supplier</span></a>
</li>

<!-- Nav Item - Menu Baru -->
<li class="nav-item">
    <a class="nav-link collapsed" href="#" data-toggle="collapse" data-target="#collapseMenuBaru"
        aria-expanded="true" aria-controls="collapseMenuBaru">
        <i class="fas fa-fw fa-icon-baru"></i>
        <span>Inventori</span>
    </a>
    <div id="collapseMenuBaru" class="collapse" aria-labelledby="headingMenuBaru"
        data-parent="#accordionSidebar">
        <div class="bg-white py-2 collapse-inner rounded">
            <h6 class="collapse-header">Submenu Inventori:</h6>
            <a class="collapse-item" href="stok.php">Stok Barang</a>
            <!-- <a class="collapse-item" href="rop.php">ROP</a> -->
        </div>
    </div>
</li>

<!-- Divider -->
<hr class="sidebar-divider d-none d-md-block">

<!-- Sidebar Toggler (Sidebar) -->
<div class="text-center d-none d-md-inline">
    <button class="rounded-circle border-0" id="sidebarToggle"></button>
</div>

</ul>
<!-- End of Sidebar -->
