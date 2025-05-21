<?php
session_start();
require_once 'C:\xampp\htdocs\skripsi2\includes\config.php';

$userID = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $userID = $_POST['userID'];
    $namaDepan = $_POST['namaDepan'];
    $namaBelakang = $_POST['namaBelakang'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $alamat = $_POST['alamat'];
    $noHP = $_POST['noHP'];
    $email = $_POST['email'];

    $query = "UPDATE customer SET userID = '$userID', namaDepan = '$namaDepan', namaBelakang = '$namaBelakang', username = '$username', password = '$password', alamat = '$alamat', noHP = '$noHP', email = '$email' WHERE userID = '$userID'";
    if ($conn->query($query)) {
        header("Location: ../../pelanggan.php");
    } else {
        echo "Error: " . $conn->error;
    }
}

$query = "SELECT * FROM customer WHERE userID = '$userID'";
$result = $conn->query($query);
$staff = $result->fetch_assoc();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Edit Pelanggan</title>
    <link rel="stylesheet" href="edit.css">
</head>
<body>
    <div class="container">
        <h1>Edit Pelanggan</h1>
        <form method="POST">
            <input type="text" name="userID" value="<?php echo $staff['userID']; ?>" required>
            <input type="text" name="namaDepan" value="<?php echo $staff['namaDepan']; ?>" required>
            <input type="text" name="namaBelakang" value="<?php echo $staff['namaBelakang']; ?>" required>
            <input type="text" name="username" value="<?php echo $staff['username']; ?>" required>
            <input type="password" name="password" value="<?php echo $staff['password']; ?>" required>
            <input type="text" name="alamat" value="<?php echo $staff['alamat']; ?>" required>
            <input type="text" name="noHP" value="<?php echo $staff['noHP']; ?>" required>
            <input type="email" name="email" value="<?php echo $staff['email']; ?>" required>
            <button type="submit" class="btn btn-primary">Update Pelanggan</button>
        </form>
    </div>
</body>
</html>