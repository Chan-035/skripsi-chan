<?php
session_start(); // Memulai sesi di awal file

require 'includes/config.php';

$username = $_POST['username'];
$password = $_POST['password'];

// Fungsi untuk menangani login berdasarkan role
function loginUser($username, $password, $role) {
    global $conn;
    $query = "SELECT * FROM $role WHERE username = ? AND password = ?";
    $stmt = $conn->prepare($query);
    $stmt->bind_param("ss", $username, $password);
    $stmt->execute();
    return $stmt->get_result();
}

// Cek apakah pengguna adalah owner
$resultOwner = loginUser($username, $password, 'owner');
if ($resultOwner->num_rows > 0) {
    $owner = $resultOwner->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'owner';
    $_SESSION['userID'] = $owner['ownerID'];
    header("location: dashboard.php");
    exit();
}

// Cek apakah pengguna adalah customer
$resultCustomer = loginUser($username, $password, 'customer');
if ($resultCustomer->num_rows > 0) {
    $customer = $resultCustomer->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'customer';
    $_SESSION['userID'] = $customer['userID'];
    header("location: index2.php");
    exit();
}

// Cek apakah pengguna adalah staff
$resultStaff = loginUser($username, $password, 'staff');
if ($resultStaff->num_rows > 0) {
    $staff = $resultStaff->fetch_assoc();
    $_SESSION['username'] = $username;
    $_SESSION['role'] = 'staff';
    $_SESSION['userID'] = $staff['staffID'];
    header("location: dashboard.php");
    exit();
}

// Jika login gagal
echo "<script>alert('Username atau Password Anda salah. Silahkan dicoba kembali');</script>";
echo "<script>window.location.href='login.html';</script>";
?>
