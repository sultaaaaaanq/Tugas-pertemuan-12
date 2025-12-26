<?php
require 'koneksi.php';

$error = "";

if (isset($_POST['register'])) {
    $nama_lengkap = trim($_POST['nama_lengkap']);
    $username = trim($_POST['username']);
    $password = $_POST['password'];

    // Cek username sudah ada atau belum (prepared statement)
    $stmt = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($stmt, "s", $username);
    mysqli_stmt_execute($stmt);
    mysqli_stmt_store_result($stmt);

    if (mysqli_stmt_num_rows($stmt) > 0) {
        $error = "Username sudah digunakan!";
    } else {
        // Enkripsi password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert ke database (prepared)
        $stmt2 = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, username, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($stmt2, "sss", $nama_lengkap, $username, $password_hash);
        if (mysqli_stmt_execute($stmt2)) {
            echo "<script>
                alert('Register berhasil.');
                window.location.href = 'login.php';
            </script>";
            exit; // Stop execution after redirect
        } else {
            $error = "Terjadi kesalahan: " . mysqli_error($koneksi);
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <link rel="stylesheet" href="ini.css?v=<?= time(); ?>">
</head>
<body class="centered-page">
    <div class="container">
        <h2>Register</h2>
        
        <?php if($error): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required value="<?= isset($nama_lengkap) ? $nama_lengkap : '' ?>">
            <input type="text" name="username" placeholder="Username" required value="<?= isset($username) ? $username : '' ?>">
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="register">Register</button>
        </form>
        
        <div class="footer-link">
            Sudah punya akun? <a href="login.php">Login</a>
        </div>
    </div>
</body>
</html>