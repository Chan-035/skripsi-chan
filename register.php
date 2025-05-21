<?php
// Koneksi ke database
require 'includes/config.php';

// Inisialisasi variabel $newUserID
$newUserID = '';

// Proses registrasi
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Ambil data dari form
    $namaDepan = $_POST['namaDepan'];
    $namaBelakang = $_POST['namaBelakang'];
    $username = $_POST['username'];
    $password = $_POST['password'];
    $noHP = $_POST['noHP'];
    $email = $_POST['email'];
    $alamat = $_POST['alamat'];

    // Menentukan userID baru
    $sql = "SELECT userID FROM customer ORDER BY userID DESC LIMIT 1";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $lastUserID = $row['userID']; // Ambil userID terakhir
        $lastNumber = (int)substr($lastUserID, 1); // Ambil angka setelah "U"
        $newUserID = 'U' . str_pad($lastNumber + 1, 3, '0', STR_PAD_LEFT); // Menambahkan angka
    } else {
        $newUserID = 'U001'; // Jika belum ada data, mulai dari U001
    }

    // Query untuk memasukkan data ke database
    $sql = "INSERT INTO customer (userID, namaDepan, namaBelakang, username, password, noHP, email, alamat) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssssss", $newUserID, $namaDepan, $namaBelakang, $username, $password, $noHP, $email, $alamat);

    if ($stmt->execute()) {
        $success_message = "Registrasi berhasil!";
        header("Location: login.html");
        exit();
    } else {
        $error_message = "Error: " . $sql . "<br>" . $conn->error;
    }

    $stmt->close();
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="css/regis.css">
</head>
<body>
    <div class="register-container">
        <h2>Register</h2>
        <?php if (isset($error_message)): ?>
            <div class="error-message"><?php echo $error_message; ?></div>
        <?php endif; ?>
        <?php if (isset($success_message)): ?>
            <div class="success-message"><?php echo $success_message; ?></div>
        <?php endif; ?>
        
        <form action="<?php echo htmlspecialchars($_SERVER["PHP_SELF"]); ?>" method="post">
            <!-- Kolom UserID dihapus dari form -->
            
            <div class="form-group">
                <label for="namaDepan">Nama Depan</label>
                <input type="text" id="namaDepan" name="namaDepan" required>
            </div>
            
            <div class="form-group">
                <label for="namaBelakang">Nama Belakang</label>
                <input type="text" id="namaBelakang" name="namaBelakang" required>
            </div>
            
            <div class="form-group">
                <label for="username">Username</label>
                <input type="text" id="username" name="username" required>
            </div>
            
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            
            <div class="form-group">
                <label for="noHP">Nomor HP</label>
                <input type="tel" id="noHP" name="noHP" required>
            </div>
            
            <div class="form-group">
                <label for="email">Email</label>
                <input type="email" id="email" name="email" required>
            </div>
            
            <div class="form-group">
                <label for="alamat">Alamat</label>
                <textarea id="alamat" name="alamat" required></textarea>
            </div>
            
            <input type="submit" value="Register">
        </form>
        
        <div class="login-link">
            <p>Sudah punya akun? <a href="login.html">Login di sini</a></p>
        </div>
    </div>
</body>
</html>
