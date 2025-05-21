<?php
session_start();
require_once 'includes/config.php';

// Ambil ID barang dari parameter URL
$idBarang = isset($_GET['id']) ? $_GET['id'] : null;

if (!$idBarang) {
    echo "ID Barang tidak valid!";
    exit();
}

// Query untuk mengambil detail barang
$queryBarang = "
    SELECT 
        s.idBarang,
        s.namaBarang,
        s.jumlahBarang,
        s.leadTime
    FROM 
        Stok s
    WHERE 
        s.idBarang = ?
";
$stmtBarang = $conn->prepare($queryBarang);
$stmtBarang->bind_param("s", $idBarang);
$stmtBarang->execute();
$barang = $stmtBarang->get_result()->fetch_assoc();

if (!$barang) {
    echo "Tidak ada data barang ditemukan untuk ID: " . $idBarang;
    exit();
}

// // Debugging: Tampilkan data barang yang dipilih
// echo "Barang yang Dipilih:<br>";
// echo "ID Barang: " . htmlspecialchars($barang['idBarang']) . "<br>";
// echo "Nama Barang: " . htmlspecialchars($barang['namaBarang']) . "<br>";
// echo "Jumlah Barang: " . htmlspecialchars($barang['jumlahBarang']) . "<br>";
// echo "Lead Time: " . htmlspecialchars($barang['leadTime']) . " hari<br>";

// Query untuk menghitung total demand dari pesanan dalam seminggu
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

// // Debugging: Tampilkan query yang digunakan
// echo "Query Demand:<br>";
// echo $queryDemand . "<br>";

$stmtDemand = $conn->prepare($queryDemand);
$stmtDemand->bind_param("s", $idBarang);
$stmtDemand->execute();
$demandResult = $stmtDemand->get_result()->fetch_assoc();

// Debugging: Periksa hasil demand
// echo "Total Demand:<br>";
if ($demandResult) {
    $totalDemand = $demandResult['totalDemand'] ?? 0;
    // Bulatkan total demand ke 2 angka desimal
    $averageDemand = round($totalDemand, 2);
} else {
    echo "Tidak ada data demand untuk barang ini.<br>";
    $averageDemand = 0;
}

// Debugging: Tampilkan data barang yang digunakan dalam pesanan
$queryPesanan = "
    SELECT 
        p.pesananID, 
        p.menuID, 
        p.tanggalPesanan, 
        p.kuantitas AS kuantitasPesanan,
        m.namaMenu 
    FROM 
        pesanan p
    JOIN 
        menu m ON p.menuID = m.menuID
    WHERE 
        p.tanggalPesanan >= NOW() - INTERVAL 7 DAY
";
// $resultPesanan = $conn->query($queryPesanan);
// if ($resultPesanan->num_rows > 0) {
//     echo "<h3>Data Pesanan dalam 7 Hari Terakhir:</h3>";
//     while ($row = $resultPesanan->fetch_assoc()) {
//         echo "Pesanan ID: " . $row['pesananID'] . ", Menu: " . $row['namaMenu'] . ", Tanggal Pesanan: " . $row['tanggalPesanan'] . ", Kuantitas: " . $row['kuantitasPesanan'] . "<br>";
//     }
// } else {
//     echo "Tidak ada pesanan dalam 7 hari terakhir.<br>";
// }

// Proses form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $leadTime = $_POST['leadTime']; // Waktu tunggu

    // Hitung ROP
    $rop = $averageDemand * $leadTime / 7;
    echo "<h2>Reorder Point untuk " . htmlspecialchars($barang['namaBarang']) . " adalah: " . $rop . "</h2>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Hitung ROP</title>
    <link rel="stylesheet" href="css/rop.css">
</head>
<body>
    <div class="container">
        <h1>Hitung ROP untuk <?php echo htmlspecialchars($barang['namaBarang']); ?></h1>
        <form method="POST">
            <div class="form-group">
                <label for="averageDemand">Rata-rata Permintaan (per minggu):</label>
                <input type="number" name="averageDemand" id="averageDemand" value="<?php echo $averageDemand; ?>" required readonly>
            </div>
            <div class="form-group">
                <label for="leadTime">Lead Time (dalam hari):</label>
                <input type="number" name="leadTime" id="leadTime" value="<?php echo htmlspecialchars($barang['leadTime']); ?>" required>
            </div>
            <button type="submit" class="btn btn-primary">Hitung ROP</button>
            <a href="stok.php" class="btn btn-secondary">Kembali</a>
        </form>
    </div>
</body>
</html>
