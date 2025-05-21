<?php
session_start();
require_once '../../includes/config.php';

// Ambil data customer
$queryCustomer = "SELECT userID, namaDepan, namaBelakang FROM customer";
$resultCustomer = $conn->query($queryCustomer);

// Ambil data menu beserta harganya
$queryMenu = "SELECT menuID, namaMenu, harga FROM menu";
$resultMenu = $conn->query($queryMenu);

// Ambil data status
$queryStatus = "SELECT statID, statName FROM status";
$resultStatus = $conn->query($queryStatus);

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Ambil data dari form
    $pesananID = $_POST['pesananID'];
    $userID = $_POST['userID'];
    $menuID = $_POST['menuID'];
    $tanggalPesanan = $_POST['tanggalPesanan'];
    $kuantitas = $_POST['kuantitas'];
    $totalHarga = $_POST['totalHarga'];
    $statID = $_POST['statID'];

    // Query untuk memasukkan data ke tabel pesanan
    $query = "INSERT INTO pesanan (pesananID, userID, menuID, tanggalPesanan, kuantitas, totalHarga, statID) VALUES (?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ssssiis", $pesananID, $userID, $menuID, $tanggalPesanan, $kuantitas, $totalHarga, $statID);

    // Eksekusi query
    try {
        $stmt->execute();
        $_SESSION['message'] = "Pesanan berhasil ditambahkan!";
        header("Location: ../../pesanan.php");
        exit();
    } catch (mysqli_sql_exception $e) {
        echo "Error: " . $e->getMessage();
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Tambah Pesanan</title>
    <link rel="stylesheet" href="pesanan.css">
</head>
<body>
<div class="container">
    <h1>Tambah Pesanan</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="pesananID">Pesanan ID</label>
            <input type="text" class="form-control" id="pesananID" name="pesananID" required>
        </div>
        <div class="form-group">
            <label for="userID">User  </label>
            <select class="form-control" id="userID" name="userID" required>
                <option value="">Pilih User</option>
                <?php while($row = $resultCustomer->fetch_assoc()): ?>
                    <option value="<?php echo $row['userID']; ?>">
                        <?php echo $row['userID'] . ' - ' . $row['namaDepan'] . ' ' . $row['namaBelakang']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="menuID">Menu</label>
            <select class="form-control" id="menuID" name="menuID" required onchange="updateHarga()">
                <option value="">Pilih Menu</option>
                <?php while($row = $resultMenu->fetch_assoc()): ?>
                    <option value="<?php echo $row['menuID']; ?>" data-harga="<?php echo $row['harga']; ?>">
                        <?php echo $row['namaMenu']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tanggalPesanan">Tanggal Pesanan</label>
            <input type="date" class="form-control" id="tanggalPesanan" name="tanggalPesanan" required>
        </div>
        <div class="form-group">
            <label for="kuantitas">Kuantitas</label>
            <input type="number" class="form-control" id="kuantitas" name="kuantitas" required onchange="hitungTotal()">
        </div>
        <div class="form-group">
            <label for="totalHarga">Total Harga</label>
            <input type="number" class="form-control" id="totalHarga" name="totalHarga" readonly>
        </div>
        <div class="form-group">
            <label for="statID">Status</label>
            <select class="form-control" id="statID" name="statID" required>
                <option value="">Pilih Status</option>
                <?php while($row = $resultStatus->fetch_assoc()): ?>
                    <option value="<?php echo $row['statID']; ?>">
                        <?php echo $row['statName']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Simpan Pesanan</button>
        <a href="../../pesanan.php" class="btn btn-secondary">Kembali</a>
    </form>
</div>

<script>
let hargaMenu = 0;

function updateHarga() {
    const menuSelect = document.getElementById('menuID');
    const selectedOption = menuSelect.options[menuSelect.selectedIndex];
    hargaMenu = selectedOption.getAttribute('data-harga');
    hitungTotal();
}

function hitungTotal() {
    const kuantitas = document.getElementById('kuantitas').value;
    const totalHarga = kuantitas * hargaMenu;
    document.getElementById('totalHarga').value = totalHarga;
}
</script>

</body>
</html>