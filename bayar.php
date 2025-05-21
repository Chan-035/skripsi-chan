<?php
session_start();
require_once 'includes/config.php';
require_once 'midtrans-php-master/Midtrans.php';

// Inisialisasi Midtrans
\Midtrans\Config::$serverKey = 'SB-Mid-server-e9sEXEcMVyWcICl_5pIg9bYh'; // Ganti dengan server key yang benar
\Midtrans\Config::$isProduction = false;
\Midtrans\Config::$isSanitized = true;
\Midtrans\Config::$is3ds = true;

// Jika keranjang kosong, redirect ke halaman index
if (!isset($_SESSION['keranjang']) || count($_SESSION['keranjang']) == 0) {
    echo "<pre>";
    print_r($_SESSION['keranjang']);
    echo "</pre>";
    
    exit;
}


// Ambil data customer dari sesi
if (isset($_SESSION['username'])) {
    $username = $_SESSION['username'];
    $queryCustomer = "SELECT userID, namaDepan, email, alamat FROM customer WHERE username = ?";
    $stmtCustomer = $conn->prepare($queryCustomer);
    $stmtCustomer->bind_param("s", $username);
    $stmtCustomer->execute();
    $resultCustomer = $stmtCustomer->get_result();
    $customer = $resultCustomer->fetch_assoc();

    if (!$customer) {
        echo "<script>alert('Data pelanggan tidak ditemukan!'); window.location.href='index2.php';</script>";
        exit;
    }
} else {
    // Redirect ke halaman login jika tidak ada session username
    header("Location: login.html");
    exit;
}

// Ambil OrderID terakhir dari tabel orderpesanan
// Ambil OrderID terakhir dari tabel orderpesanan (global, tanpa filter userID)
$queryLastOrder = "SELECT orderID FROM orderpesanan ORDER BY orderID DESC LIMIT 1";
$stmtLastOrder = $conn->prepare($queryLastOrder);
$stmtLastOrder->execute();
$resultLastOrder = $stmtLastOrder->get_result();
$lastOrder = $resultLastOrder->fetch_assoc();

// Buat orderID baru berdasarkan format
if ($lastOrder) {
    // Ambil angka dari orderID terakhir dan tambahkan 1
    $lastOrderNumber = (int)substr($lastOrder['orderID'], 2); // Mengambil angka setelah 'OP'
    $orderID = 'OP' . str_pad($lastOrderNumber, 3, '0', STR_PAD_LEFT);
} else {
    // Jika tidak ada order sebelumnya, mulai dari OP001
    $orderID = 'OP001';
}


// Memasukkan item ke dalam transaksi dan menghitung total harga
$items = [];
$totalPrice = 0;
foreach ($_SESSION['keranjang'] as $item) {
    $items[] = [
        'id' => $item['menuID'],
        'price' => $item['harga'],
        'quantity' => $item['kuantitas'],
        'name' => $item['namaMenu']
    ];
    $totalPrice += $item['harga'] * $item['kuantitas'];
}

// Siapkan transaksi untuk Midtrans
$transactionDetails = [
    'order_id' => $orderID,
    'gross_amount' => $totalPrice
];

// Menyiapkan data pelanggan yang berasal dari sesi
$midtransData = [
    'transaction_details' => $transactionDetails,
    'item_details' => $items,
    'customer_details' => [
        'first_name' => $customer['namaDepan'],
        'email' => $customer['email'],
        'phone' => '08123456789', // Ganti dengan nomor telepon pelanggan jika ada
        'address' => $customer['alamat']
    ]
];

// Menghasilkan token untuk pembayaran melalui Midtrans
try {
    $snapToken = Midtrans\Snap::getSnapToken($midtransData);
} catch (Exception $e) {
    echo json_encode(['error' => $e->getMessage()]);
    exit;
}

?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Page</title>
    <script type="text/javascript" src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-nW9BijQwSXfHTA3Z"></script>
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.0.2/dist/js/bootstrap.bundle.min.js"></script>
</head>
<body>
    <h1>Proceed to Payment</h1>
    <p>Total Price: Rp <?php echo number_format($totalPrice, 0, ',', '.'); ?></p>

    <script type="text/javascript">
        // Auto trigger Midtrans Snap popup after page loads
        window.snap.pay('<?php echo $snapToken; ?>', {
            onSuccess: function(result) {
                alert("Pembayaran berhasil!");
                console.log(result);
                // Redirect ke halaman sukses pembayaran
                window.location.href = "payment_success.php?orderID=<?php echo $orderID; ?>";
            },
            onPending: function(result) {
                alert("Pembayaran tertunda!");
                console.log(result);
                // Redirect ke halaman status pembayaran pending
                window.location.href = "payment_pending.php?orderID=<?php echo $orderID; ?>";
            },
            onError: function(result) {
                alert("Terjadi kesalahan!");
                console.log(result);
                // Redirect ke halaman gagal pembayaran
                window.location.href = "payment_failed.php?orderID=<?php echo $orderID; ?>";
            }
        });
    </script>
</body>
</html>
