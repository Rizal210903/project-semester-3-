<?php
$host = "localhost";
$user = "root"; 
$pass = ""; // default Laragon kosong
$db   = "db_perguruan_silat"; 

$koneksi = mysqli_connect($host, $user, $pass, $db);

if (!$koneksi) {
    die("Koneksi gagal: " . mysqli_connect_error());
}
?>
