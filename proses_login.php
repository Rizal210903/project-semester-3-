<?php
session_start();
include "koneksi.php";

$username = $_POST['username'];
$password = $_POST['password'];

// cek ke database
$sql = "SELECT * FROM admin WHERE username='$username' AND password='$password'";
$result = mysqli_query($koneksi, $sql);

if (mysqli_num_rows($result) > 0) {
    $row = mysqli_fetch_assoc($result);
    $_SESSION['id_admin'] = $row['id_admin'];
    $_SESSION['nama_lengkap'] = $row['nama_lengkap'];
    header("Location: dashboard.php");
} else {
    echo "<script>alert('Username atau Password salah!'); window.location='index.html';</script>";
}
?>
