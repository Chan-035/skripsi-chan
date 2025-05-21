<?php
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Inisialisasi variabel filter
$filterNamaDepan = isset($_GET['namaDepan']) ? $_GET['namaDepan'] : '';
$filterTanggalMulai = isset($_GET['tanggalMulai']) ? $_GET['tanggalMulai'] : '';
$filterTanggalSelesai = isset($_GET['tanggalSelesai']) ? $_GET['tanggalSelesai'] : '';
$filterStatus = isset($_GET['statName']) ? $_GET['statName'] : '';

// Query untuk mengambil data pesanan dan order pesanan dengan filter
$query = "
    SELECT 
        op.orderID, 
        op.tanggalPesanan, 
        op.tanggalPengambilan, 
        p.pesananID, 
        c.namaDepan, 
        p.menuID, 
        p.kuantitas, 
        p.totalHarga, 
        s.statName 
    FROM 
        pesanan p
    LEFT JOIN 
        orderpesanan op ON p.pesananID = op.pesananID
    LEFT JOIN 
        customer c ON p.userID = c.userID
    LEFT JOIN 
        status s ON p.statID = s.statID
    WHERE 
        ('$filterNamaDepan' = '' OR c.namaDepan LIKE '%$filterNamaDepan%') AND
        ('$filterTanggalMulai' = '' OR op.tanggalPesanan >= '$filterTanggalMulai') AND
        ('$filterTanggalSelesai' = '' OR op.tanggalPesanan <= '$filterTanggalSelesai') AND
        ('$filterStatus' = '' OR s.statName = '$filterStatus')
    ORDER BY 
        op.orderID IS NULL, 
        op.orderID ASC, 
        p.pesananID ASC";

$result = $conn->query($query);

// Ambil data hasil query dan gabungkan orderID yang sama
$groupedData = [];
$noOrderData = [];

while ($row = $result->fetch_assoc()) {
    if ($row['orderID']) {
        $groupedData[$row['orderID']]['orderInfo'] = [
            'tanggalPesanan' => $row['tanggalPesanan'],
            'tanggalPengambilan' => $row['tanggalPengambilan'],
        ];
        $groupedData[$row['orderID']]['items'][] = $row;
    } else {
        $noOrderData[] = $row;
    }

// Ambil tanggal saat ini untuk nama file
$currentDate = date('Y-m-d');
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan</title>
</head>
<body>
    <h1>Filter Data Pesanan</h1>
    <form method="GET" action="">
        <label for="namaDepan">Nama Depan Customer:</label>
        <input type="text" name="namaDepan" id="namaDepan" value="<?= $filterNamaDepan ?>"><br><br>

        <label for="tanggalMulai">Tanggal Pesanan Mulai:</label>
        <input type="date" name="tanggalMulai" id="tanggalMulai" value="<?= $filterTanggalMulai ?>"><br><br>

        <label for="tanggalSelesai">Tanggal Pesanan Selesai:</label>
        <input type="date" name="tanggalSelesai" id="tanggalSelesai" value="<?= $filterTanggalSelesai ?>"><br><br>

        <label for="statName">Status:</label>
        <input type="text" name="statName" id="statName" value="<?= $filterStatus ?>"><br><br>

        <button type="submit" name="filter">Tampilkan Data</button>
        <button type="submit" name="export">Export ke CSV</button>
    </form>

    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Tanggal Pesanan</th>
                <th>Tanggal Pengambilan</th>
                <th>Nama Depan</th>
                <th>Menu ID</th>
                <th>Kuantitas</th>
                <th>Total Harga</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($groupedData as $orderID => $data) {
                foreach ($data['items'] as $item) {
                    echo "<tr>";
                    echo "<td>{$item['orderID']}</td>";
                    echo "<td>{$item['tanggalPesanan']}</td>";
                    echo "<td>{$item['tanggalPengambilan']}</td>";
                    echo "<td>{$item['namaDepan']}</td>";
                    echo "<td>{$item['menuID']}</td>";
                    echo "<td>{$item['kuantitas']}</td>";
                    echo "<td>{$item['totalHarga']}</td>";
                    echo "<td>{$item['statName']}</td>";
                    echo "</tr>";
                }
            }
            ?>
        </tbody>
    </table>
</body>
</html>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pesanan</title>
    <style>
        @page {
            size: A4;
            margin: 20mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            margin: 20px auto;
        }
        th, td {
            border: 1px solid #000;
            padding: 8px;
            text-align: center;
        }
        th {
            background-color: #f2f2f2;
        }
        h1 {
            text-align: center;
            margin: 20px 0;
        }
        .btn {
            margin: 20px auto;
            display: block;
            width: 200px;
            text-align: center;
            padding: 10px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 5px;
        }
        @media print {
            .btn {
                display: none;
            }
        }
    </style>
</head>
<body>
    <h1>Data Pesanan</h1>
    <table>
        <thead>
            <tr>
                <th>Order ID</th>
                <th>Tanggal Pesanan</th>
                <th>Tanggal Pengambilan</th>
                <th>Nama Depan Customer</th>
                <th>Menu ID</th>
                <th>Jumlah</th>
                <th>Total Harga (Rp)</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groupedData as $orderID => $data): ?>
                <!-- Baris untuk Order ID -->
                <tr>
                    <td rowspan="<?= count($data['items']) ?>"><?= $orderID ?></td>
                    <td rowspan="<?= count($data['items']) ?>"><?= $data['orderInfo']['tanggalPesanan'] ?></td>
                    <td rowspan="<?= count($data['items']) ?>"><?= $data['orderInfo']['tanggalPengambilan'] ?></td>
                    <?php foreach ($data['items'] as $index => $item): ?>
                        <?php if ($index > 0): ?><tr><?php endif; ?>
                        <td><?= $item['namaDepan'] ?></td>
                        <td><?= $item['menuID'] ?></td>
                        <td><?= $item['kuantitas'] ?></td>
                        <td><?= number_format($item['totalHarga'], 0, ',', '.') ?></td>
                        <td><?= $item['statName'] ?></td>
                        </tr>
                    <?php endforeach; ?>
            <?php endforeach; ?>

            <!-- Pesanan tanpa Order ID -->
            <?php foreach ($noOrderData as $item): ?>
                <tr>
                    <td colspan="3">-</td>
                    <td><?= $item['namaDepan'] ?></td>
                    <td><?= $item['menuID'] ?></td>
                    <td><?= $item['kuantitas'] ?></td>
                    <td><?= number_format($item['totalHarga'], 0, ',', '.') ?></td>
                    <td><?= $item['statName'] ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tombol Cetak -->
    <a href="#" onclick="window.print();" class="btn">Cetak Laporan</a>
</body>
<script>
    // Set nama file unduhan
    // Automatically set document title when page loads
    document.title = "DataPesanan<?= $currentDate ?>";

    // Optional: Remove the click event listener since it's no longer needed
    document.querySelector('.btn').addEventListener('click', function() {
        window.print();
    });
</script>
</html>
