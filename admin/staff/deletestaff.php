<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Ambil ID staff dari URL
$staffID = $_GET['id'];

// Hapus staff dari database
$query = "DELETE FROM staff WHERE staffID = '$staffID'";
if ($conn->query($query)) {
    header("Location: ../../staff.php");
} else {
    echo "Error: " . $conn->error;
}
?>