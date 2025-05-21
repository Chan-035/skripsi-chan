<?php
session_start();
require_once 'includes/config.php';
require_once 'admin/header.php';
require_once 'admin/navbar.php';

// Ambil ID barang dari parameter URL
$idBarang = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idBarang) {
    $_SESSION['error'] = "ID Barang tidak valid!";
    header("Location: stok.php");
    exit();
}

// Untuk debugging
// echo "ID Barang yang diterima: " . $idBarang;

// Query untuk mengambil detail barang
$queryBarang = "
    SELECT 
        s.idBarang,
        s.namaBarang,
        k.namaKategori,
        j.namaJenis,
        s.jumlahBarang
    FROM 
        Stok s
        LEFT JOIN Kategori k ON s.kategoriID = k.kategoriID
        LEFT JOIN Jenis j ON s.jenisID = j.jenisID
    WHERE 
        s.idBarang = ?
";
$stmtBarang = $conn->prepare($queryBarang);
$stmtBarang->bind_param("s", $idBarang);  // Ubah "i" menjadi "s" jika idBarang adalah string
$stmtBarang->execute();
$barang = $stmtBarang->get_result()->fetch_assoc();

// Untuk debugging, tambahkan ini:
if (!$barang) {
    echo "Tidak ada data barang ditemukan untuk ID: " . $idBarang;
    exit();
}

// Query untuk mengambil riwayat pembelian
$queryPembelian = "
    SELECT 
        p.beliID,
        p.tanggal,
        s.namaKontak AS namaSupplier,
        p.jumlah,
        p.harga,
        (p.jumlah * p.harga) as total_harga
    FROM 
        Pembelian p
        LEFT JOIN Supplier s ON p.supplierID = s.supplierID
    WHERE 
        p.idBarang = ?
    ORDER BY 
        p.tanggal DESC
";
$stmtPembelian = $conn->prepare($queryPembelian);
$stmtPembelian->bind_param("s", $idBarang);  // Gunakan "s" jika idBarang adalah string
$stmtPembelian->execute();
$pembelianList = $stmtPembelian->get_result();
?>

<!-- Content Wrapper -->
<div id="content-wrapper" class="d-flex flex-column">

    <!-- Main Content -->
    <div id="content">
        <!-- Begin Page Content -->
        <div class="container-fluid">

            <!-- Page Heading -->
            <div class="d-sm-flex align-items-center justify-content-between mb-4">
                <h1 class="h3 mb-0 text-gray-800">Detail Stok Barang</h1>
                <a href="stok.php" class="btn btn-secondary btn-sm">
                    <i class="fas fa-arrow-left"></i> Kembali
                </a>
            </div>

            <!-- Detail Barang Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Informasi Barang</h6>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <table class="table table-borderless">
                                <tr>
                                    <th width="30%">ID Barang</th>
                                    <td>: <?php echo $barang['idBarang']; ?></td>
                                </tr>
                                <tr>
                                    <th>Nama Barang</th>
                                    <td>: <?php echo $barang['namaBarang']; ?></td>
                                </tr>
                                <tr>
                                    <th>Kategori</th>
                                    <td>: <?php echo $barang['namaKategori']; ?></td>
                                </tr>
                                <tr>
                                    <th>Jenis</th>
                                    <td>: <?php echo $barang['namaJenis']; ?></td>
                                </tr>
                                <tr>
                                    <th>Stok Tersedia</th>
                                    <td>: <span class="badge badge-<?php echo $barang['jumlahBarang'] > 0 ? 'success' : 'danger'; ?>">
                                        <?php echo $barang['jumlahBarang']; ?>
                                    </span></td>
                                </tr>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Riwayat Pembelian Card -->
            <div class="card shadow mb-4">
                <div class="card-header py-3">
                    <h6 class="m-0 font-weight-bold text-primary">Riwayat Pembelian</h6>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-bordered" width="100%" cellspacing="0">
                            <thead>
                                <tr>
                                    <th>No</th>
                                    <th>ID Pembelian</th>
                                    <th>Tanggal</th>
                                    <th>Supplier</th>
                                    <th>Jumlah</th>
                                    <th>Harga Satuan</th>
                                    <th>Total Harga</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php 
                                $no = 1;
                                $totalPembelian = 0;
                                while ($row = $pembelianList->fetch_assoc()): 
                                    $totalPembelian += $row['total_harga'];
                                ?>
                                    <tr>
                                        <td><?php echo $no++; ?></td>
                                        <td><?php echo $row['beliID']; ?></td>
                                        <td><?php echo date('d/m/Y', strtotime($row['tanggal'])); ?></td>
                                        <td><?php echo $row['namaSupplier']; ?></td>
                                        <td><?php echo $row['jumlah']; ?></td>
                                        <td>Rp <?php echo number_format($row['harga'], 0, ',', '.'); ?></td>
                                        <td>Rp <?php echo number_format($row['total_harga'], 0, ',', '.'); ?></td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                            <tfoot>
                                <tr>
                                    <td colspan="6" class="text-right">Total Pembelian:</td>
                                    <td>Rp <?php echo number_format($totalPembelian, 0 , ',', '.'); ?></td>
                                </tr>
                            </tfoot>
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