<?php
session_start();
require 'koneksi.php';
if (isset($_SESSION['status']) && $_SESSION['status'] == "login") {
    header("Location: index.php");
    exit;
}
$error = "";
$success = "";
if (isset($_GET['logged_out'])) {
    $success = "Anda berhasil logout.";
}
if (isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    $sultan = mysqli_prepare($koneksi, "SELECT id, nama_lengkap, password FROM users WHERE username = ?");
    mysqli_stmt_bind_param($sultan, "s", $username);
    mysqli_stmt_execute($sultan);
    mysqli_stmt_bind_result($sultan, $id, $nama_lengkap_db, $password_hash_db);
    if (mysqli_stmt_fetch($sultan)) {
        if (password_verify($password, $password_hash_db)) {
        $_SESSION['username'] = $username;
        $_SESSION['nama_lengkap'] = $nama_lengkap_db;
        $_SESSION['status'] = "login";
        header("Location: index.php");
        exit;
        }
    }
    
    $error = "Username atau password salah!";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="ini.css?v=<?= time(); ?>">
</head>
<body class="centered-page">
    <div class="container">
        <h2>Login</h2>
        
        <?php if($success): ?>
            <div class="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;">
                <?= $success ?>
            </div>
        <?php endif; ?>

        <?php if($error): ?>
            <div class="alert alert-danger">
                <?= $error ?>
            </div>
        <?php endif; ?>

        <form action="" method="post">
            <input type="text" name="username" placeholder="Username" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        
        <div class="footer-link">
            Belum punya akun? <a href="register.php">Register</a>
        </div>
    </div>
</body>
</html>