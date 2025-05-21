<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Ambil ID staff dari URL
$staffID = $_GET['id'];

// Hapus staff dari database
$query = "DELETE FROM customer WHERE userID = '$userID'";
if ($conn->query($query)) {
    header("Location: ../../pelanggan.php");
} else {
    echo "Error: " . $conn->error;
}
?>