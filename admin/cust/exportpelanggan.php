<?php
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Query untuk mengambil data pelanggan
$query = "SELECT * FROM customer ORDER BY userID";
$result = $conn->query($query);

// Ambil tanggal saat ini untuk nama file
$currentDate = date('Y-m-d');
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Data Pelanggan</title>
    <style>
        @page {
            size: A4; /* Set ukuran kertas */
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
                display: none; /* Sembunyikan tombol cetak saat di-print */
            }
        }
    </style>
</head>
<body>
    <h1>Data Pelanggan</h1>
    <table>
        <thead>
            <tr>
                <th>User ID</th>
                <th>Nama Depan</th>
                <th>Nama Belakang</th>
                <th>Username</th>
                <th>No HP</th>
                <th>Email</th>
                <th>Alamat</th>
            </tr>
        </thead>
        <tbody>
        <?php while ($row = $result->fetch_assoc()): ?>
            <tr>
                <td><?= $row['userID']; ?></td>
                <td><?= $row['namaDepan']; ?></td>
                <td><?= $row['namaBelakang']; ?></td>
                <td><?= $row['username']; ?></td>
                <td><?= $row['noHP']; ?></td>
                <td><?= $row['email']; ?></td>
                <td><?= $row['alamat']; ?></td>
            </tr>
        <?php endwhile; ?>
        </tbody>
    </table>

    <!-- Tombol Cetak -->
    <a href="#" onclick="window.print();" class="btn">Cetak Laporan</a>
</body>
<script>
    // Set nama file unduhan
    document.querySelector('.btn').addEventListener('click', function () {
        document.title = "Data_Pelanggan_<?= $currentDate ?>";
    });
</script>
</html>
