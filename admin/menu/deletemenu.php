<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Ambil ID staff dari URL
$menuID = $_GET['id'];

// Hapus staff dari database
$query = "DELETE FROM menu WHERE menuID = '$menuID'";
if ($conn->query($query)) {
    header("Location: ../../menu.php");
} else {
    echo "Error: " . $conn->error;
}
?>