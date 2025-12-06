<?php
function createNotification($conn, $user_id, $pendaftaran_id, $type, $message) {
    try {
        $stmt = $conn->prepare("
            INSERT INTO notifications (user_id, pendaftaran_id, type, message, is_read, created_at) 
            VALUES (?, ?, ?, ?, 0, NOW())
        ");
        $stmt->bind_param("iiss", $user_id, $pendaftaran_id, $type, $message);
        $result = $stmt->execute();
        $stmt->close();
        return $result;
    } catch (Exception $e) {
        error_log("Notifikasi Error: " . $e->getMessage());
        return false;
    }
}
?>