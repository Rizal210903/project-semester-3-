<?php
// update_status.php
header('Content-Type: application/json');
include '../includes/config.php'; // koneksi ke database

$response = ['success' => false, 'message' => ''];

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['ubah_status'], $_POST['id'], $_POST['status'])) {
    $id = intval($_POST['id']);
    $status = $_POST['status'];

    // Validasi status
    $valid_status = ['pending', 'diterima', 'ditolak'];
    if (!in_array($status, $valid_status)) {
        $response['message'] = 'Status tidak valid.';
        echo json_encode($response);
        exit;
    }

    // Update database
    $stmt = $conn->prepare("UPDATE pendaftaran SET status_ppdb = ? WHERE id = ?");
    if ($stmt->execute([$status, $id])) {
        $response['success'] = true;
        $response['message'] = 'Status berhasil diperbarui.';
    } else {
        $response['message'] = 'Gagal memperbarui status.';
    }
} else {
    $response['message'] = 'Permintaan tidak valid.';
}

echo json_encode($response);
