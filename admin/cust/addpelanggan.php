<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

if(isset($_POST['submit'])) {
    $userID = mysqli_real_escape_string($conn, $_POST['userID']);
    $namaDepan = mysqli_real_escape_string($conn, $_POST['namaDepan']);
    $namaBelakang = mysqli_real_escape_string($conn, $_POST['namaBelakang']);
    $username = mysqli_real_escape_string($conn, $_POST['username']);
    $password = mysqli_real_escape_string($conn, $_POST['password']);
    $noHP = mysqli_real_escape_string($conn, $_POST['noHP']);
    $email = mysqli_real_escape_string($conn, $_POST['email']);
    $alamat = mysqli_real_escape_string($conn, $_POST['alamat']);

    // ngecek username
    $check_query = "SELECT * FROM customer WHERE username = '$username'";
    $check_result = mysqli_query($conn, $check_query);

    if(mysqli_num_rows($check_result) > 0) {
        echo "<script>alert('Username sudah digunakan!');</script>";
    } else {
        
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        $query = "INSERT INTO customer (userID,namaDepan, namaBelakang, username, password, noHP, email, alamat) 
                  VALUES ('$userID','$namaDepan', '$namaBelakang', '$username', '$hashed_password', '$noHP', '$email', '$alamat')";

        if(mysqli_query($conn, $query)) {
            echo "<script>alert('Customer berhasil ditambahkan!');</script>";
            echo "<script>window.location.href='../../pelanggan.php';</script>";
        } else {
            echo "<script>alert('Error: " . mysqli_error($conn) . "');</script>";
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Add Customer</title>
    <link rel="stylesheet" href="add.css">
</head>
<body>
    <div class="container">
        <h2>Tambah Customer Baru</h2>
        <form action="" method="POST">
            <div class="form-group">
                <label>User ID</label>
                <input type="text" name="userID" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Depan</label>
                <input type="text" name="namaDepan" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Nama Belakang</label>
                <input type="text" name="namaBelakang" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            <div class="form-group">
                <label>No HP</label>
                <input type="text" name="noHP" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Email</label>
                <input type="email" name="email" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Alamat</label>
                <textarea name="alamat" class="form-control" required></textarea>
            </div>
            <button type="submit" name="submit" class="btn btn-primary">Simpan</button>
            <a href="../../pelanggan.php" class="btn btn-secondary">Batal</a>
        </form>
    </div>
</body>
</html>