<?php
session_start();
require 'koneksi.php';
if (!isset($_SESSION['status']) || $_SESSION['status'] != 'login') {
    header("Location: login.php");
    exit;
}
if (isset($_GET['aksi']) && $_GET['aksi'] == 'hapus' && isset($_GET['id'])) {
    $id = (int) $_GET['id'];
    $sultan_del = mysqli_prepare($koneksi, "DELETE FROM users WHERE id = ?");
    mysqli_stmt_bind_param($sultan_del, "i", $id);
    mysqli_stmt_execute($sultan_del);
    echo "<script>alert('Data berhasil dihapus!'); window.location.href='index.php?page=data_user';</script>";
}
$error = "";
$success = "";
if (isset($_POST['tambah_user'])) {
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];

    // Cek username menggunakan prepared statement
    $sultan_check = mysqli_prepare($koneksi, "SELECT id FROM users WHERE username = ?");
    mysqli_stmt_bind_param($sultan_check, "s", $username);
    mysqli_stmt_execute($sultan_check);
    mysqli_stmt_store_result($sultan_check);

    if (mysqli_stmt_num_rows($sultan_check) > 0) {
        $error = "Username sudah ada!";
    } else {
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        $sultan_insert = mysqli_prepare($koneksi, "INSERT INTO users (nama_lengkap, username, password) VALUES (?, ?, ?)");
        mysqli_stmt_bind_param($sultan_insert, "sss", $nama_lengkap, $username, $password_hash);
        if (mysqli_stmt_execute($sultan_insert)) {
            $success = "User berhasil ditambahkan!";
        } else {
            $error = "Gagal menambahkan user!";
        }
    }
}
if (isset($_POST['edit_user'])) {
    $id = $_POST['id'];
    $nama_lengkap = $_POST['nama_lengkap'];
    $username = $_POST['username'];
    $password = $_POST['password'];

if (!empty($password)) {
    $password_hash = password_hash($password, PASSWORD_DEFAULT);
    $sultan_upd = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap=?, username=?, password=? WHERE id=?");
    mysqli_stmt_bind_param($sultan_upd, "sssi", $nama_lengkap, $username, $password_hash, $id);
} else {
    $sultan_upd = mysqli_prepare($koneksi, "UPDATE users SET nama_lengkap=?, username=? WHERE id=?");
    mysqli_stmt_bind_param($sultan_upd, "ssi", $nama_lengkap, $username, $id);
}

if (mysqli_stmt_execute($sultan_upd)) {
    echo "<script>alert('Data berhasil diupdate!'); window.location.href='index.php?page=data_user';</script>";
} else {
    echo "<script>alert('Gagal update data!');</script>";
}
}
$page = isset($_GET['page']) ? $_GET['page'] : 'dashboard';
?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>DASHBOARD</title>
  <link rel="stylesheet" href="ini.css?v=<?= time(); ?>">
</head>
<body>
  
 <!-- Navbar -->
    <div class="navbar">
        <div class="links">
            <a href="index.php?page=dashboard">Dashboard</a>
            <a href="index.php?page=data_user">Data User</a>
            <a href="index.php?page=tambah_user">Tambah User</a>
        </div>
        <a href="logout.php" class="btn-logout" onclick="return confirm('Yakin ingin logout?');">Logout</a>
    </div>

    <!-- Content -->
    <?php if ($page == 'dashboard'): ?>
        <div class="dashboard-container">
            <h2>Selamat Datang, <?= $_SESSION['nama_lengkap']; ?> 👋</h2>
            <p style="color: #666;">Ini adalah halaman dashboard.</p>
        </div>

    <?php elseif ($page == 'data_user'): ?>
        <div class="dashboard-container">
            <h2>Data User</h2>
            <table>
                <thead>
                    <tr>
                        <th>Nama</th>
                        <th>Username</th>
                        <th>Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    $result = mysqli_query($koneksi, "SELECT * FROM users");
                    while ($row = mysqli_fetch_assoc($result)):
                    ?>
                    <tr>
                        <td><?= $row['nama_lengkap']; ?></td>
                        <td><?= $row['username']; ?></td>
                        <td class="action-links">
                            <a href="index.php?page=edit_user&id=<?= $row['id']; ?>">Edit</a> | 
                            <a href="index.php?aksi=hapus&id=<?= $row['id']; ?>" class="delete" onclick="return confirm('Yakin hapus?');">Hapus</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>

    <?php elseif ($page == 'tambah_user'): ?>
        <div class="centered-page">
            <div class="container" style="max-width:420px;">
                <h2>Tambah User</h2>

                <?php if($error): ?>
                    <div class="alert alert-danger"><?= $error ?></div>
                <?php endif; ?>

                <?php if($success): ?>
                    <div class="alert" style="background-color: #d4edda; color: #155724; border: 1px solid #c3e6cb;"><?= $success ?></div>
                <?php endif; ?>

                <form action="" method="post">
                    <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" required value="<?= isset($nama_lengkap) ? htmlspecialchars($nama_lengkap) : '' ?>">
                    <input type="text" name="username" placeholder="Username" required value="<?= isset($username) ? htmlspecialchars($username) : '' ?>">
                    <input type="password" name="password" placeholder="Password" required>
                    <button type="submit" name="tambah_user">Tambah</button>
                </form>
            </div>
        </div>
    <?php elseif ($page == 'edit_user'): 
        if(isset($_GET['id'])) {
            $id = (int) $_GET['id'];
            $sultan_get = mysqli_prepare($koneksi, "SELECT id, nama_lengkap, username FROM users WHERE id = ?");
            mysqli_stmt_bind_param($sultan_get, "i", $id);
            mysqli_stmt_execute($sultan_get);
            mysqli_stmt_bind_result($sultan_get, $data_id, $data_nama, $data_username);
            if (mysqli_stmt_fetch($sultan_get)) {
                $data = ['id' => $data_id, 'nama_lengkap' => $data_nama, 'username' => $data_username];
            } else {
                $data = null;
            }
        }
    ?>
        <div class="dashboard-container" style="max-width: 500px;">
            <h2 style="text-align: center;">Edit User</h2>
            <form action="" method="post">
                <input type="hidden" name="id" value="<?= $data['id']; ?>">
                <input type="text" name="nama_lengkap" placeholder="Nama Lengkap" value="<?= $data['nama_lengkap']; ?>" required>
                <input type="text" name="username" placeholder="Username" value="<?= $data['username']; ?>" required>
                <input type="password" name="password" placeholder="Password (Kosongkan jika tidak ingin mengganti)">
                <button type="submit" name="edit_user">Update</button>
            </form>
        </div>
    <?php endif; ?>

</body>
</html>