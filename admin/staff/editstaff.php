<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

// Ambil ID staff dari URL
$staffID = $_GET['id'];

// Proses form jika dikirim
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $namaDepan = $_POST['namaDepan'];
    $namaBelakang = $_POST['namaBelakang'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $noHP = $_POST['noHP'];
    $email = $_POST['email'];

    $query = "UPDATE staff SET namaDepan = '$namaDepan', namaBelakang = '$namaBelakang', username = '$username', password = '$password', alamat = '$alamat', noHP = '$noHP', email = '$email' WHERE staffID = '$staffID'";
    if ($conn->query($query)) {
        header("Location: ../../staff.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

// Ambil data staff yang akan diedit
$query = "SELECT * FROM staff WHERE staffID = '$staffID'";
$result = $conn->query($query);
$staff = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Staff</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="container">
        <h1>Edit Staff</h1>
        <form method="POST">
            <input type="text" name="namaDepan" value="<?php echo $staff['namaDepan']; ?>" required>
            <input type="text" name="namaBelakang" value="<?php echo $staff['namaBelakang']; ?>" required>
            <input type="text" name="username" value="<?php echo $staff['username']; ?>" required>
            <input type="password" name="password" value="<?php echo $staff['password']; ?>" required>
            <input type="text" name="alamat" value="<?php echo $staff['alamat']; ?>" required>
            <input type="text" name="noHP" value="<?php echo $staff['noHP']; ?>" required>
            <input type="email" name="email" value="<?php echo $staff['email']; ?>" required>
            <button type="submit" class="btn btn-primary">Update Staff</button>
        </form>
    </div>
</body>
</html>