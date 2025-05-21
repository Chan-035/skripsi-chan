<?php
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Query untuk mengambil data stok
$query = "
    SELECT 
        s.idBarang, 
        s.namaBarang, 
        k.namaKategori,
        j.namaJenis,
        s.jumlahBarang, 
        s.leadTime
    FROM 
        stok s
    LEFT JOIN 
        kategori k ON s.kategoriID = k.kategoriID
    LEFT JOIN 
        jenis j ON s.jenisID = j.jenisID
";
$result = $conn->query($query);

// Fungsi untuk menghitung ROP
function calculateROP($idBarang, $leadTime, $conn) {
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
    
    $totalDemand = isset($demandResult['totalDemand']) ? $demandResult['totalDemand'] : 0;
    $averageDemandPerDay = $totalDemand / 7; // Rata-rata permintaan harian
    return round($averageDemandPerDay * $leadTime, 0);
}

// Fungsi untuk mendapatkan riwayat pembelian stok
function getPurchaseHistory($idBarang, $conn) {
    $queryHistory = "
        SELECT 
            p.tanggal, 
            p.jumlah, 
            p.harga
        FROM 
            pembelian p
        WHERE 
            p.idBarang = ?
            AND p.tanggal >= NOW() - INTERVAL 7 DAY
        ORDER BY 
            p.tanggal DESC
    ";
    $stmtHistory = $conn->prepare($queryHistory);
    $stmtHistory->bind_param("s", $idBarang);
    $stmtHistory->execute();
    return $stmtHistory->get_result();
}

// Ambil tanggal untuk nama file
$currentDate = date('Y-m-d');
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Laporan Stok</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
        }
        h1 {
            text-align: center;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px 0;
        }
        th, td {
            border: 1px solid black;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        .red {
            color: red; /* Warna merah */
            font-weight: bold; /* Tebal untuk menonjolkan */
        }
        @media print {
            .btn {
                display: none;
            }
        }
        .btn {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
        }
    </style>
</head>
<body>
    <h1>Laporan Stok</h1>
    <a href="#" onclick="window.print();" class="btn">Cetak PDF</a>
    <table>
        <thead>
            <tr>
                <th>ID Barang</th>
                <th>Nama Barang</th>
                <th>Kategori</th>
                <th>Jenis</th>
                <th>Jumlah Stok</th>
                <th>ROP</th>
                <th>Riwayat Pembelian (1 Minggu Terakhir)</th>
            </tr>
        </thead>
        <tbody>
            <?php while ($row = $result->fetch_assoc()): ?>
                <?php 
                    $ropValue = calculateROP($row['idBarang'], $row['leadTime'], $conn); 
                    $history = getPurchaseHistory($row['idBarang'], $conn);
                    $purchaseHistory = "";
                    while ($historyRow = $history->fetch_assoc()) {
                        $purchaseHistory .= "Tanggal: {$historyRow['tanggal']}, Jumlah: {$historyRow['jumlah']}, Harga: Rp " . number_format($historyRow['harga'], 0, ',', '.') . "<br>";
                    }

                    // Tentukan apakah jumlah stok di bawah ROP
                    $classROP = ($row['jumlahBarang'] < $ropValue) ? "red" : "";
                ?>
                <tr>
                    <td><?= $row['idBarang'] ?></td>
                    <td><?= $row['namaBarang'] ?></td>
                    <td><?= $row['namaKategori'] ?></td>
                    <td><?= $row['namaJenis'] ?></td>
                    <td class="<?= $classROP ?>"><?= $row['jumlahBarang'] ?></td>
                    <td><?= $ropValue ?></td>
                    <td><?= $purchaseHistory ?></td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
