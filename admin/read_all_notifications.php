<?php
session_start();
include '../includes/config.php';

$admin_id = $_SESSION['admin_id'];

$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();

echo "OK";
