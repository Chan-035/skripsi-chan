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

// Query untuk mendapatkan data customer
$queryCustomer = "SELECT * FROM customer WHERE userID = ?";
$stmtCustomer = $conn->prepare($queryCustomer);
$stmtCustomer->bind_param("s", $userID);
$stmtCustomer->execute();
$resultCustomer = $stmtCustomer->get_result();
$customer = $resultCustomer->fetch_assoc();

// Pastikan data customer ada
if (!$customer) {
    die("Data pelanggan tidak ditemukan.");
}

// Ambil `orderID` dari request
if (!isset($_GET['orderID'])) {
    die("Order ID tidak diberikan.");
}
$orderID = $_GET['orderID'];

// Query untuk mendapatkan data order
$queryOrder = "
    SELECT 
        op.orderID,
        op.tanggalPesanan,
        op.tanggalPengambilan,
        p.pesananID,
        m.namaMenu,
        p.kuantitas,
        p.totalHarga,
        s.statName
    FROM 
        orderpesanan op
    JOIN 
        pesanan p ON op.pesananID = p.pesananID
    JOIN 
        menu m ON p.menuID = m.menuID
    JOIN 
        status s ON p.statID = s.statID
    WHERE 
        op.orderID = ?
";
$stmtOrder = $conn->prepare($queryOrder);
$stmtOrder->bind_param("s", $orderID);
$stmtOrder->execute();
$resultOrder = $stmtOrder->get_result();

// Periksa apakah data order ada
if ($resultOrder->num_rows === 0) {
    die("Data order tidak ditemukan.");
}

// Konversi data order ke array
$orders = [];
while ($row = $resultOrder->fetch_assoc()) {
    $orders[] = $row;
}

// Hitung total harga dari semua item
$totalHarga = array_reduce($orders, function ($total, $item) {
    return $total + $item['totalHarga'];
}, 0);

// Buat PDF menggunakan FPDF
require_once 'includes/fpdf/fpdf.php';

$pdf = new FPDF();
$pdf->AddPage();
$pdf->SetFont('Arial', 'B', 16);

// Header Invoice
$pdf->Cell(0, 10, 'INVOICE', 0, 1, 'C');
$pdf->SetFont('Arial', '', 12);
$pdf->Ln(5);

// Detail Pelanggan
$pdf->Cell(0, 10, 'Detail Pelanggan:', 0, 1);
$pdf->Cell(50, 10, 'Nama: ' . $customer['namaDepan'] . ' ' . $customer['namaBelakang']);
$pdf->Ln(5);
$pdf->Cell(50, 10, 'No HP: ' . $customer['noHP']);
$pdf->Ln(5);
$pdf->Cell(50, 10, 'Email: ' . $customer['email']);
$pdf->Ln(5);
$pdf->Cell(50, 10, 'Alamat: ' . $customer['alamat']);
$pdf->Ln(10);

// Detail Order
$pdf->Cell(0, 10, 'Detail Order:', 0, 1);
$pdf->Cell(50, 10, 'Order ID: ' . $orderID);
$pdf->Ln(5);
$pdf->Cell(50, 10, 'Tanggal Pesanan: ' . date('d-m-Y', strtotime($orders[0]['tanggalPesanan'])));
$pdf->Ln(5);
$pdf->Cell(50, 10, 'Tanggal Pengambilan: ' . date('d-m-Y', strtotime($orders[0]['tanggalPengambilan'])));
$pdf->Ln(10);

// Tabel Item Pesanan
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(40, 10, 'Nama Menu', 1);
$pdf->Cell(30, 10, 'Kuantitas', 1);
$pdf->Cell(50, 10, 'Harga Satuan', 1);
$pdf->Cell(50, 10, 'Total Harga', 1);
$pdf->Ln();

$pdf->SetFont('Arial', '', 12);
foreach ($orders as $item) {
    $pdf->Cell(40, 10, $item['namaMenu'], 1);
    $pdf->Cell(30, 10, $item['kuantitas'], 1);
    $pdf->Cell(50, 10, 'Rp ' . number_format($item['totalHarga'] / $item['kuantitas'], 0, ',', '.'), 1);
    $pdf->Cell(50, 10, 'Rp ' . number_format($item['totalHarga'], 0, ',', '.'), 1);
    $pdf->Ln();
}

// Total Harga
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(120, 10, 'Total Harga', 1);
$pdf->Cell(50, 10, 'Rp ' . number_format($totalHarga, 0, ',', '.'), 1);
$pdf->Ln(20);

// Tanda Tangan
$pdf->SetFont('Arial', '', 12);
$pdf->Cell(0, 10, 'Terima kasih telah memesan!', 0, 1, 'C');

// Output PDF
$pdf->Output('I', 'Invoice_' . $orderID . '.pdf');
?>
