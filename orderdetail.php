<?php
session_start();
require_once 'includes/config.php';

if (isset($_GET['pesananID'])) {
    $pesananID = $_GET['pesananID'];

    // Query untuk mengambil detail pesanan berdasarkan pesananID
    $query = "SELECT p.*, m.namaMenu, s.statName
              FROM pesanan p
              LEFT JOIN menu m ON p.menuID = m.menuID
              LEFT JOIN status s ON p.statID = s.statID
              WHERE p.pesananID = ?";

    $stmt = $conn->prepare($query);
    $stmt->bind_param("s", $pesananID);
    $stmt->execute();
    $pesananDetail = $stmt->get_result()->fetch_assoc();
}
?>

<div class="container-fluid">
    <h1 class="h3 mb-4 text-gray-800">Order Details</h1>
    <div class="card">
        <div class="card-header">
            Pesanan ID: <?php echo $pesananDetail['pesananID']; ?>
        </div>
        <div class="card-body">
            <p><strong>User ID:</strong> <?php echo $pesananDetail['userID']; ?></p>
            <p><strong>Menu:</strong> <?php echo $pesananDetail['namaMenu']; ?></p>
            <p><strong>Quantity:</strong> <?php echo $pesananDetail['kuantitas']; ?></p>
            <p><strong>Date:</strong> <?php echo date('d-m-Y', strtotime($pesananDetail['tanggalPesanan'])); ?></p>
            <p><strong>Status:</strong> <?php echo $pesananDetail['statName']; ?></p>
            <p><strong>Total Price:</strong> <?php echo $pesananDetail['totalHarga']; ?></p>
        </div>
    </div>
</div>

<?php
require_once 'admin/scripts.php';
require_once 'admin/footer.php';
?>
