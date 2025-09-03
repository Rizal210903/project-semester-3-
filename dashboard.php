<?php
session_start();
if (!isset($_SESSION['id_admin'])) {
    header("Location: index.html");
    exit();
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
  <meta charset="UTF-8">
  <title>Dashboard Admin</title>
</head>
<body>
  <h2>Selamat Datang, <?php echo $_SESSION['nama_lengkap']; ?>!</h2>
  <p>Ini adalah halaman dashboard admin.</p>
  <a href="logout.php">Logout</a>
</body>
</html>
