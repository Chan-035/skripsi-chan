<?php
session_start();


// Pastikan data menuID dan jumlah diterima lewat POST
$menuID = isset($_POST['menuID']) ? $_POST['menuID'] : null;
$menuName = isset($_POST['menuName']) ? $_POST['menuName'] : null;
$price = isset($_POST['price']) ? $_POST['price'] : null;
$quantity = isset($_POST['quantity']) ? $_POST['quantity'] : 1;
$paymentMethod = isset($_POST['paymentMethod']) ? $_POST['paymentMethod'] : null;

if (!$menuID || !$paymentMethod) {
    echo "Data tidak valid!";
    exit;
}

// Hitung total harga
$totalPrice = $price * $quantity;
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran - Evelyn's Kitchen</title>
    <link rel="stylesheet" href="css/buy.css">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-MrcW6ZMFYlzcLA8Nl+NtUVF0sA7MsXsP1UyJoMp4YLEuNSfAP+JcXn/tWtIaxVXM" crossorigin="anonymous"></script>
</head>
<body>
    <header>
        <h1>Evelyn's Kitchen</h1>
        <div class="logout">
            <button onclick="window.location.href='logout.php'">Logout</button>
        </div>
    </header>

    <div class="container mt-5">
        <h2>Pembayaran untuk <?php echo htmlspecialchars($menuName); ?></h2>
        <p>Jumlah Pesanan: <?php echo $quantity; ?></p>
        <p>Total Harga: Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></p>

        <h3>Metode Pembayaran: <?php echo ucfirst($paymentMethod); ?></h3>
        
        <!-- Menampilkan QRIS atau metode pembayaran lain -->
        <?php if ($paymentMethod == 'ovo'): ?>
            <div>
                <h4>Pembayaran dengan OVO</h4>
                <p>Scan QRIS di bawah ini untuk melakukan pembayaran:</p>
                <img src="images/ovo_qris.png" alt="QRIS OVO" width="200">
            </div>
        <?php elseif ($paymentMethod == 'gopay'): ?>
            <div>
                <h4>Pembayaran dengan GoPay</h4>
                <p>Scan QRIS di bawah ini untuk melakukan pembayaran:</p>
                <img src="images/gopay_qris.png" alt="QRIS GoPay" width="200">
            </div>
        <?php elseif ($paymentMethod == 'dana'): ?>
            <div>
                <h4>Pembayaran dengan DANA</h4>
                <p>Scan QRIS di bawah ini untuk melakukan pembayaran:</p>
                <img src="images/dana_qris.png" alt="QRIS DANA" width="200">
            </div>
        <?php else: ?>
            <p>Metode pembayaran tidak dikenali!</p>
        <?php endif; ?>

        <p>Pastikan untuk melakukan pembayaran sesuai dengan jumlah yang tertera.</p>
    </div>

    <footer>
        <p>&copy; Evelyn's Kitchen. All rights reserved.</p>
    </footer>
</body>
</html>
