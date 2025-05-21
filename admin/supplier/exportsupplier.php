<?php
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Query untuk mengambil data supplier dan barang
$query = "
    SELECT 
        s.supplierID, 
        s.namaKontak, 
        s.kontak, 
        ls.idBarang, 
        b.namaBarang
    FROM 
        supplier s
    LEFT JOIN 
        listsupplier ls ON s.supplierID = ls.supplierID
    LEFT JOIN 
        stok b ON ls.idBarang = b.idBarang
    ORDER BY 
        s.supplierID, ls.idBarang";

$result = $conn->query($query);

// Ambil data hasil query dan gabungkan barang berdasarkan supplierID
$groupedData = [];
while ($row = $result->fetch_assoc()) {
    $supplierID = $row['supplierID'];
    if (!isset($groupedData[$supplierID])) {
        $groupedData[$supplierID] = [
            'namaKontak' => $row['namaKontak'],
            'kontak' => $row['kontak'],
            'barang' => [],
        ];
    }
    if ($row['namaBarang']) {
        $groupedData[$supplierID]['barang'][] = $row['namaBarang'];
    }
}

// Ambil tanggal saat ini untuk nama file
$currentDate = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Supplier</title>
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
    <h1>Data Supplier</h1>
    <table>
        <thead>
            <tr>
                <th>Supplier ID</th>
                <th>Nama Kontak</th>
                <th>Kontak</th>
                <th>Barang</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($groupedData as $supplierID => $data): ?>
                <tr>
                    <td><?= $supplierID ?></td>
                    <td><?= $data['namaKontak'] ?></td>
                    <td><?= $data['kontak'] ?></td>
                    <td>
                        <?php if (!empty($data['barang'])): ?>
                            <?php foreach ($data['barang'] as $barang): ?>
                                <?= $barang ?><br>
                            <?php endforeach; ?>
                        <?php else: ?>
                            -
                        <?php endif; ?>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>

    <!-- Tombol Cetak -->
    <a href="#" onclick="window.print();" class="btn">Cetak Laporan</a>
</body>
<script>
    // Set nama file unduhan
    document.title = "DataSupplier<?= $currentDate ?>";

    // Optional: Remove the click event listener since it's no longer needed
    document.querySelector('.btn').addEventListener('click', function() {
        window.print();
    });
</script>
</html>
