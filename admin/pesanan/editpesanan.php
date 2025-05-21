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

if (isset($_GET['id'])) {
    $pesananID = $_GET['id'];

    if ($_SERVER['REQUEST_METHOD'] == 'POST') {
        // Ambil data dari form
        $pesananID = $_POST['pesananID'];
        $userID = $_POST['userID'];
        $menuID = $_POST['menuID'];
        $tanggalPesanan = $_POST['tanggalPesanan'];
        $kuantitas = $_POST['kuantitas'];
        $totalHarga = $_POST['totalHarga'];
        $newStatID = $_POST['statID'];

        // Ambil data pesanan lama
        $query = "SELECT * FROM pesanan WHERE pesananID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $pesananID);
        $stmt->execute();
        $pesananLama = $stmt->get_result()->fetch_assoc();

        $oldStatID = $pesananLama['statID'];
        $oldKuantitas = $pesananLama['kuantitas'];

        // Jika status berubah ke S001, kurangi stok
        if ($newStatID == 'S001' && $oldStatID != 'S001') {
            kurangiStok($conn, $menuID, $kuantitas);
        } 
        // Jika status berubah dari S001 ke status lain, kembalikan stok
        elseif ($oldStatID == 'S001' && $newStatID != 'S001') {
            kembalikanStok($conn, $menuID, $oldKuantitas);
        }

        // Update data pesanan
        $queryUpdate = "UPDATE pesanan SET userID = ?, menuID = ?, tanggalPesanan = ?, kuantitas = ?, totalHarga = ?, statID = ? WHERE pesananID = ?";
        $stmtUpdate = $conn->prepare($queryUpdate);
        $stmtUpdate->bind_param("sssisss", $userID, $menuID, $tanggalPesanan, $kuantitas, $totalHarga, $newStatID, $pesananID);
        $stmtUpdate->execute();

        $_SESSION['message'] = "Pesanan berhasil diupdate!";
        header("Location: ../../pesanan.php");
        exit();
    } else {
        // Ambil data pesanan berdasarkan pesananID
        $query = "SELECT * FROM pesanan WHERE pesananID = ?";
        $stmt = $conn->prepare($query);
        $stmt->bind_param("s", $pesananID);
        $stmt->execute();
        $pesanan = $stmt->get_result()->fetch_assoc();
    }
} else {
    $_SESSION['error'] = "ID pesanan tidak ditemukan!";
    header("Location: ../../pesanan.php");
    exit();
}

// Fungsi untuk mengurangi stok
function kurangiStok($conn, $menuID, $kuantitas) {
    $queryIngredients = "
        SELECT 
            ri.idBarang, 
            ri.kuantitas 
        FROM 
            recipe_ingredients ri 
        JOIN 
            resep r ON ri.resepID = r.resepID 
        WHERE 
            r.menuID = ?
    ";

    $stmtIngredients = $conn->prepare($queryIngredients);
    $stmtIngredients->bind_param("s", $menuID);
    $stmtIngredients->execute();
    $ingredientsResult = $stmtIngredients->get_result();

    while ($ingredient = $ingredientsResult->fetch_assoc()) {
        $idBarang = $ingredient['idBarang'];
        $jumlahDikurangi = $ingredient['kuantitas'] * $kuantitas;

        $queryUpdateStock = "
            UPDATE stok 
            SET jumlahBarang = jumlahBarang - ? 
            WHERE idBarang = ?
        ";

        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bind_param("is", $jumlahDikurangi, $idBarang);
        $stmtUpdateStock->execute();
    }
}

// Fungsi untuk mengembalikan stok
function kembalikanStok($conn, $menuID, $kuantitas) {
    $queryIngredients = "
        SELECT 
            ri.idBarang, 
            ri.kuantitas 
        FROM 
            recipe_ingredients ri 
        JOIN 
            resep r ON ri.resepID = r.resepID 
        WHERE 
            r.menuID = ?
    ";

    $stmtIngredients = $conn->prepare($queryIngredients);
    $stmtIngredients->bind_param("s", $menuID);
    $stmtIngredients->execute();
    $ingredientsResult = $stmtIngredients->get_result();

    while ($ingredient = $ingredientsResult->fetch_assoc()) {
        $idBarang = $ingredient['idBarang'];
        $jumlahDitambah = $ingredient['kuantitas'] * $kuantitas;

        $queryUpdateStock = "
            UPDATE stok 
            SET jumlahBarang = jumlahBarang + ? 
            WHERE idBarang = ?
        ";

        $stmtUpdateStock = $conn->prepare($queryUpdateStock);
        $stmtUpdateStock->bind_param("is", $jumlahDitambah, $idBarang);
        $stmtUpdateStock->execute();
    }
}
?>


<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pesanan</title>
    <link rel="stylesheet" href="pesanan.css">
</head>
<body>
<div class="container">
    <h1>Edit Pesanan</h1>
    <form method="POST" action="">
        <div class="form-group">
            <label for="pesananID">Pesanan ID</label>
            <input type="text" class="form-control" id="pesananID" name="pesananID" value="<?php echo $pesanan['pesananID']; ?>" required>
        </div>
        <div class="form-group">
            <label for="userID">User </label>
            <select class="form-control" id="userID" name="userID" required>
                <?php while($row = $resultCustomer->fetch_assoc()): ?>
                    <option value="<?php echo $row['userID']; ?>" <?php echo ($row['userID'] == $pesanan['userID']) ? 'selected' : ''; ?>>
                        <?php echo $row['userID'] . ' - ' . $row['namaDepan'] . ' ' . $row['namaBelakang']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="menuID">Menu</label>
            <select class="form-control" id="menuID" name="menuID" required onchange="updateHarga()">
                <?php while($row = $resultMenu->fetch_assoc()): ?>
                    <option value="<?php echo $row['menuID']; ?>" data-harga="<?php echo $row['harga']; ?>" <?php echo ($row['menuID'] == $pesanan['menuID']) ? 'selected' : ''; ?>>
                        <?php echo $row['namaMenu']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <div class="form-group">
            <label for="tanggalPesanan ">Tanggal Pesanan</label>
            <input type="date" class="form-control" id="tanggalPesanan" name="tanggalPesanan" value="<?php echo $pesanan['tanggalPesanan']; ?>" required>
        </div>
        <div class="form-group">
            <label for="kuantitas">Kuantitas</label>
            <input type="number" class="form-control" id="kuantitas" name="kuantitas" value="<?php echo $pesanan['kuantitas']; ?>" required onchange="hitungTotal()">
        </div>
        <div class="form-group">
            <label for="totalHarga">Total Harga</label>
            <input type="number" class="form-control" id="totalHarga" name="totalHarga" value="<?php echo $pesanan['totalHarga']; ?>" readonly>
        </div>
        <div class="form-group">
            <label for="statID">Status</label>
            <select class="form-control" id="statID" name="statID" required>
                <?php while($row = $resultStatus->fetch_assoc()): ?>
                    <option value="<?php echo $row['statID']; ?>" <?php echo ($row['statID'] == $pesanan['statID']) ? 'selected' : ''; ?>>
                        <?php echo $row['statName']; ?>
                    </option>
                <?php endwhile; ?>
            </select>
        </div>
        <button type="submit" class="btn btn-primary">Update Pesanan</button>
    </form>
</div>

<script>
    function updateHarga() {
        // Ambil harga berdasarkan menu yang dipilih
        var harga = document.querySelector('select[name="menuID"] option:checked').getAttribute('data-harga');
        // Set harga di field totalHarga
        document.getElementById('totalHarga').value = harga;
        // Perbarui perhitungan total harga berdasarkan kuantitas jika sudah ada
        hitungTotal(); // Panggil hitungTotal untuk menghitung ulang berdasarkan harga baru
    }

    function hitungTotal() {
        // Ambil harga terbaru dari menu yang dipilih
        var harga = document.querySelector('select[name="menuID"] option:checked').getAttribute('data-harga');
        // Ambil kuantitas yang diinputkan
        var kuantitas = document.getElementById('kuantitas').value;
        // Hitung total harga berdasarkan harga dan kuantitas
        document.getElementById('totalHarga').value = harga * kuantitas;
    }

</script>
</body>
</html>