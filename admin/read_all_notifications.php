<?php
session_start();
include '../includes/config.php';

$admin_id = $_SESSION['admin_id'];

// update semua notifikasi jadi read
$stmt = $conn->prepare("UPDATE notifications SET is_read = 1 WHERE admin_id = ?");
$stmt->bind_param("i", $admin_id);
$stmt->execute();
$result = $stmt->get_result();