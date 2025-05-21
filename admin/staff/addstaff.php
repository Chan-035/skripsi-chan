<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Proses form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaDepan = $_POST['namaDepan'];
    $namaBelakang = $_POST['namaBelakang'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $noHP = $_POST['noHP'];
    $email = $_POST['email'];

    $query = "INSERT INTO staff (namaDepan, namaBelakang, username, password, alamat, noHP, email) VALUES ('$namaDepan', '$namaBelakang', '$username', '$password', '$alamat', '$noHP', '$email')";
    if ($conn->query($query)) {
        header("Location: ../../staff.php");
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Staff</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="container">
        <h1>Add New Staff</h1>
        <form method="POST">
            <input type="text" name="namaDepan" placeholder="Nama Depan" required>
            <input type="text" name="namaBelakang" placeholder="Nama Belakang" required>
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <input type="text" name="alamat" placeholder="Alamat" required>
            <input type="text" name="noHP" placeholder="No HP" required>
            <input type="email" name="email" placeholder="Email" required>
            <button type="submit" class="btn btn-primary">Add Staff</button>
        </form>
    </div>
</body>
</html>