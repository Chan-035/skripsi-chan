<?php
require 'includes/config.php';
session_start();

// Jika pengguna belum login, arahkan ke halaman login
if (!isset($_SESSION['username']) || $_SESSION['role'] != 'customer') {
    header("location: login.html");
    exit;
}

// Cek apakah keranjang ada isinya
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    header("Location: index2.php?error=empty_cart");
    exit;
}

// Ambil data customer
$username = $_SESSION['username'];
$queryCustomer = "SELECT userID, namaDepan, email, alamat FROM customer WHERE username = ?";
$stmtCustomer = $conn->prepare($queryCustomer);
$stmtCustomer->bind_param("s", $username);
$stmtCustomer->execute();
$resultCustomer = $stmtCustomer->get_result();
$customer = $resultCustomer->fetch_assoc();

// Ambil data keranjang belanja
$totalHarga = 0;
$items = [];
foreach ($_SESSION['keranjang'] as $item) {
    $subTotal = $item['harga'] * $item['kuantitas'];
    $totalHarga += $subTotal;
    $items[] = $item;
}

// Cek jika tanggalPengambilan sudah diset di sesi
if (isset($_POST['tanggalPengambilan'])) {
    $_SESSION['tanggalPengambilan'] = $_POST['tanggalPengambilan']; // Simpan tanggal pengambilan di sesi
}

$tanggalPengambilan = isset($_SESSION['tanggalPengambilan']) ? $_SESSION['tanggalPengambilan'] : '';

// Jika tanggalPengambilan kosong, beri pesan untuk memilih tanggal pengambilan
if (empty($tanggalPengambilan)) {
    echo "<script>alert('Tanggal pengambilan belum dipilih!'); window.location.href='pilih_tanggal.php';</script>";
    exit;
}

// Ambil pesananID terakhir dari tabel pesanan untuk melanjutkan
$queryLastPesanan = "SELECT pesananID FROM pesanan ORDER BY pesananID DESC LIMIT 1";
$resultLastPesanan = $conn->query($queryLastPesanan);

// Periksa error pada query
if (!$resultLastPesanan) {
    die("Error: " . $conn->error); // Menampilkan error jika query gagal
}

// Ambil orderID terakhir dari tabel orderpesanan
$queryLastOrder = "SELECT orderID FROM orderpesanan ORDER BY orderID DESC LIMIT 1";
$resultLastOrder = $conn->query($queryLastOrder);

// Periksa error pada query
if (!$resultLastOrder) {
    die("Error: " . $conn->error); // Menampilkan error jika query gagal
}

// Jika ada orderID terakhir, ambil angka urutnya, jika tidak, mulai dari 1
if ($resultLastOrder->num_rows > 0) {
    $lastOrder = $resultLastOrder->fetch_assoc();
    $lastOrderID = $lastOrder['orderID'];

    // Ekstrak angka urut dan tambahkan 1
    $lastNumber = substr($lastOrderID, 2); // Mengambil angka setelah 'OP'

    // Pastikan angka urut bukan kosong dan tambahkan 1
    if (is_numeric($lastNumber) && $lastNumber != "") {
        $newNumber = str_pad($lastNumber + 1, 4, '0', STR_PAD_LEFT); // Menambah angka urut, dengan panjang 4 digit
    } else {
        // Jika lastNumber kosong atau tidak valid, mulailah dari 1
        $newNumber = str_pad(1, 4, '0', STR_PAD_LEFT); 
    }

    $orderID = 'OP' . $newNumber;
} else {
    // Jika belum ada order, mulai dengan angka 1
    $orderID = 'OP' . str_pad(1, 4, '0', STR_PAD_LEFT);
}

// Jika ada pesananID terakhir, ambil angka urutnya, jika tidak, mulai dari 1
if ($resultLastPesanan->num_rows > 0) {
    $lastPesanan = $resultLastPesanan->fetch_assoc();
    $lastPesananID = $lastPesanan['pesananID'];

    // Ekstrak angka urut dan tambahkan 1
    $lastNumber = substr($lastPesananID, 1); // Mengambil angka setelah 'O'

    // Pastikan angka urut bukan kosong dan tambahkan 1
    if (is_numeric($lastNumber) && $lastNumber != "") {
        $newNumber = str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Menambah angka urut, dengan panjang 3 digit
    } else {
        // Jika lastNumber kosong atau tidak valid, mulailah dari 1
        $newNumber = str_pad(1, 3, '0', STR_PAD_LEFT); 
    }

    $newPesananID = 'O' . $newNumber;
} else {
    // Jika belum ada pesanan, mulai dengan angka 1
    $newPesananID = 'O' . str_pad(1, 3, '0', STR_PAD_LEFT);
}

// Tanggal pesanan
$tanggalPesanan = date('Y-m-d');

// Insert data pesanan untuk setiap item di keranjang
$pesananIDs = []; // Menyimpan semua pesananID yang di-generate
foreach ($_SESSION['keranjang'] as $item) {
    // Gunakan pesananID yang sudah di-generate
    $pesananIDs[] = $newPesananID; // Menyimpan pesananID untuk orderpesanan nanti

    $menuID = $item['menuID']; // Ambil menuID dari item
    $kuantitas = $item['kuantitas'];
    $totalHargaItem = $item['harga'] * $kuantitas;
    $statID = 'S004'; // Status pesanan, bisa disesuaikan
    $stokDikurangi = 0; // Belum dikurangi stok, bisa disesuaikan jika diperlukan

    // Insert data pesanan untuk setiap item
    $queryPesanan = "INSERT INTO pesanan (pesananID, userID, menuID, tanggalPesanan, kuantitas, totalHarga, statID, stokDikurangi) 
                     VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmtPesanan = $conn->prepare($queryPesanan);
    $stmtPesanan->bind_param("ssssiisi", $newPesananID, $customer['userID'], $menuID, $tanggalPesanan, $kuantitas, $totalHargaItem, $statID, $stokDikurangi);
    $stmtPesanan->execute();

    // Tambahkan untuk pesananID berikutnya
    $newPesananID = 'O' . str_pad((substr($newPesananID, 1) + 1), 3, '0', STR_PAD_LEFT);
}

// Insert data ke tabel orderpesanan
foreach ($pesananIDs as $pesananID) {
    // Menyimpan data ke dalam orderpesanan, termasuk userID dan tanggal pengambilan
    $queryOrderPesanan = "INSERT INTO orderpesanan (orderID, pesananID, tanggalPesanan, userID, tanggalPengambilan) 
                          VALUES (?, ?, ?, ?, ?)";
    $stmtOrderPesanan = $conn->prepare($queryOrderPesanan);
    $stmtOrderPesanan->bind_param("sssss", $orderID, $pesananID, $tanggalPesanan, $customer['userID'], $tanggalPengambilan);
    $stmtOrderPesanan->execute();
}


// Tampilkan ringkasan pesanan
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="css/buy.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout - Evelyn's Kitchen</title>
</head>
<body>
    <div class="container mt-5">
        <h2>Checkout</h2>
        <p><strong>Customer:</strong> <?php echo htmlspecialchars($customer['namaDepan']); ?></p>
        <p><strong>Email:</strong> <?php echo htmlspecialchars($customer['email']); ?></p>
        <p><strong>Alamat:</strong> <?php echo htmlspecialchars($customer['alamat']); ?></p>

        <h4>Items in Your Cart</h4>
        <table class="table">
            <thead>
                <tr>
                    <th>Menu</th>
                    <th>Qty</th>
                    <th>Total</th>
                    <th>Pesanan ID</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($items as $index => $item) { 
                    $subTotal = $item['harga'] * $item['kuantitas'];
                ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['namaMenu']); ?></td>
                        <td><?php echo $item['kuantitas']; ?></td>
                        <td><?php echo number_format($subTotal, 0, ',', '.'); ?></td>
                        <td><?php echo $pesananIDs[$index]; ?></td>
                    </tr>
                <?php } ?>
            </tbody>
        </table>

        <h4>Total Harga: <?php echo number_format($totalHarga, 0, ',', '.'); ?></h4>
        <p>Tanggal Pengambilan: <?php echo $tanggalPengambilan; ?></p>

        <a href="index2.php" class="btn btn-secondary">Back to Menu</a>
        <a href="bayar.php" class="btn btn-primary">Proceed to Payment</a>
    </div>
</body>
</html>
