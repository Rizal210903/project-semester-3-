<?php
session_start();
header('Content-Type: application/json');

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "tk_pertiwi_db";

if (!isset($_SESSION['user_id'])) {
    echo json_encode(["status" => "not_login"]);
    exit;
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // CEK USER SUDAH MENDAFTAR ATAU BELUM
    $stmt = $pdo->prepare("SELECT id FROM pendaftaran WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);

    if ($stmt->rowCount() > 0) {
        echo json_encode(["status" => "terdaftar"]);
    } else {
        echo json_encode(["status" => "belum"]);
    }

} catch (PDOException $e) {
    echo json_encode(["status" => "error"]);
}
