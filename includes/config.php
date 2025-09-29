<?php
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Koneksi gagal: " . $conn->connect_error);
}

$conn->set_charset("utf8mb4");

function sanitize($data) {
    global $conn;
    return $conn->real_escape_string(trim($data));
}
?>