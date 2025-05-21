<?php
session_start();
require_once 'includes/config.php';

// Pastikan pengguna sudah login
if (!isset($_SESSION['userID'])) {
    header("Location: login.php");
    exit();
}

// Ambil userID dari sesi
$userID = $_SESSION['userID'];

// Query untuk mendapatkan data order dari `orderpesanan`
$query = "
    SELECT 
        DISTINCT op.orderID,
        op.tanggalPesanan,
        op.userID
    FROM 
        orderpesanan op
    WHERE 
        op.userID = ?
    ORDER BY 
        op.tanggalPesanan DESC
";
$stmt = $conn->prepare($query);
$stmt->bind_param("s", $userID);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Riwayat Pesanan</title>
    <link rel="stylesheet" href="css/riwayat.css">
    <style>

    </style>
</head>
<body>
    <div class="order-history">
        <h1>Riwayat Pesanan Anda</h1>
        <?php if ($result->num_rows > 0): ?>
            <table>
            <tr>
                <th>ID Pesanan</th>
                <th>Nama Menu</th>
                <th>Aksi</th> <!-- Kolom untuk tindakan -->
            </tr>

                <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($row['orderID']); ?></td>
                        <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($row['tanggalPesanan']))); ?></td>
                        <td>
                            <button onclick="toggleDropdown('<?php echo $row['orderID']; ?>')">Lihat Detail</button>
                        </td>
                    </tr>
                    <tr class="dropdown" id="dropdown-<?php echo $row['orderID']; ?>">
                        <td colspan="3">
                            <table>
                                <tr>
                                    <th>ID Pesanan</th>
                                    <th>Nama Menu</th>
                                    <th>Kuantitas</th>
                                    <th>Total Harga</th>
                                    <th>Status</th>
                                    <th>Tanggal Pengambilan</th> <!-- Tanggal Pengambilan ditambahkan -->
                                    <th>
                                        <a href="invoice.php?orderID=<?php echo $row['orderID']; ?>" target="_blank">Download Invoice</a>
                                    </th>
                                    <th>
                                        <a href="receipt.php?orderID=<?php echo $row['orderID']; ?>" target="_blank">Download Kuitansi</a>
                                    </th>
                                </tr>
                                <?php
                                // Query untuk mendapatkan detail pesanan berdasarkan `orderID`
                                $detailQuery = "
                                    SELECT 
                                        p.pesananID,
                                        m.namaMenu,
                                        p.kuantitas,
                                        p.totalHarga,
                                        s.statName,
                                        op.tanggalPengambilan
                                    FROM 
                                        pesanan p
                                    JOIN 
                                        menu m ON p.menuID = m.menuID
                                    JOIN 
                                        status s ON p.statID = s.statID
                                    JOIN 
                                        orderpesanan op ON p.pesananID = op.pesananID
                                    WHERE 
                                        op.orderID = ?
                                ";
                                $detailStmt = $conn->prepare($detailQuery);
                                $detailStmt->bind_param("s", $row['orderID']);
                                $detailStmt->execute();
                                $detailResult = $detailStmt->get_result();

                                if ($detailResult->num_rows > 0):
                                    while ($detailRow = $detailResult->fetch_assoc()):
                                ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($detailRow['pesananID']); ?></td>
                                        <td><?php echo htmlspecialchars($detailRow['namaMenu']); ?></td>
                                        <td><?php echo htmlspecialchars($detailRow['kuantitas']); ?></td>
                                        <td>Rp <?php echo number_format($detailRow['totalHarga'], 0, ',', '.'); ?></td>
                                        <td><?php echo htmlspecialchars($detailRow['statName']); ?></td>
                                        <td><?php echo htmlspecialchars(date('d-m-Y', strtotime($detailRow['tanggalPengambilan']))); ?></td>
                                    </tr>
                                <?php
                                    endwhile;
                                else:
                                ?>
                                    <tr>
                                        <td colspan="6">Tidak ada detail pesanan.</td>
                                    </tr>
                                <?php endif; ?>
                            </table>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </table>
        <?php else: ?>
            <p>Anda belum memiliki riwayat pesanan.</p>
        <?php endif; ?>

        <a href="index2.php" class="btn">Kembali ke Halaman Utama</a>
    </div>

    <script>
        function toggleDropdown(orderID) {
            const dropdown = document.getElementById('dropdown-' + orderID);
            dropdown.classList.toggle('active');
        }
    </script>
</body>
</html>
