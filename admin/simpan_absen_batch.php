<?php
session_start();
include '../includes/config.php';

// Cek apakah user sudah login sebagai admin
if (!isset($_SESSION['admin_id']) && !isset($_SESSION['admin_username'])) {
    http_response_code(403);
    echo json_encode(['status' => 'error', 'message' => 'Unauthorized access']);
    exit;
}

// Cek method POST
if ($_SERVER['REQUEST_METHOD'] != 'POST') {
    http_response_code(405);
    echo json_encode(['status' => 'error', 'message' => 'Method not allowed']);
    exit;
}

// Cek apakah data absensi ada
if (!isset($_POST['absensi'])) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Data absensi tidak ditemukan']);
    exit;
}

// Decode JSON data
$absensi = json_decode($_POST['absensi'], true);

if (!is_array($absensi) || empty($absensi)) {
    http_response_code(400);
    echo json_encode(['status' => 'error', 'message' => 'Format data tidak valid']);
    exit;
}

$success = 0;
$failed = 0;
$errors = [];

// Mulai transaction
$conn->begin_transaction();

try {
    foreach ($absensi as $data) {
        // Validasi data
        if (!isset($data['guru_id']) || !isset($data['tanggal']) || !isset($data['status'])) {
            $failed++;
            $errors[] = "Data tidak lengkap untuk salah satu guru";
            continue;
        }

        $guru_id = intval($data['guru_id']);
        $tanggal = $conn->real_escape_string($data['tanggal']);
        $status = $conn->real_escape_string($data['status']);
        $catatan = isset($data['catatan']) ? $conn->real_escape_string($data['catatan']) : '';
        
        // Handle waktu_absen
        $waktu_absen = null;
        if (isset($data['waktu_absen']) && !empty($data['waktu_absen'])) {
            $waktu_absen = $conn->real_escape_string($data['waktu_absen']);
        }

        // Validasi status
        $valid_status = ['Hadir', 'Terlambat', 'Izin', 'Sakit', 'Alfa'];
        if (!in_array($status, $valid_status)) {
            $failed++;
            $errors[] = "Status tidak valid untuk guru ID: $guru_id";
            continue;
        }

        // Validasi tanggal format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $tanggal)) {
            $failed++;
            $errors[] = "Format tanggal tidak valid untuk guru ID: $guru_id";
            continue;
        }

        // Cek apakah guru_id valid
        $check_guru = $conn->query("SELECT id FROM guru WHERE id = $guru_id");
        if ($check_guru->num_rows == 0) {
            $failed++;
            $errors[] = "Guru dengan ID $guru_id tidak ditemukan";
            continue;
        }

        // Cek apakah sudah ada data absensi untuk guru dan tanggal tersebut
        $check = $conn->query("SELECT id FROM absensi_guru WHERE guru_id = $guru_id AND tanggal = '$tanggal'");
        
        if ($check->num_rows > 0) {
            // Update jika sudah ada
            if ($waktu_absen !== null) {
                $sql = "UPDATE absensi_guru SET 
                        status = '$status', 
                        waktu_absen = '$waktu_absen',
                        catatan = '$catatan',
                        updated_at = NOW()
                        WHERE guru_id = $guru_id AND tanggal = '$tanggal'";
            } else {
                $sql = "UPDATE absensi_guru SET 
                        status = '$status', 
                        waktu_absen = NULL,
                        catatan = '$catatan',
                        updated_at = NOW()
                        WHERE guru_id = $guru_id AND tanggal = '$tanggal'";
            }
        } else {
            // Insert jika belum ada
            if ($waktu_absen !== null) {
                $sql = "INSERT INTO absensi_guru (guru_id, tanggal, status, waktu_absen, catatan, created_at) 
                        VALUES ($guru_id, '$tanggal', '$status', '$waktu_absen', '$catatan', NOW())";
            } else {
                $sql = "INSERT INTO absensi_guru (guru_id, tanggal, status, waktu_absen, catatan, created_at) 
                        VALUES ($guru_id, '$tanggal', '$status', NULL, '$catatan', NOW())";
            }
        }
        
        if ($conn->query($sql)) {
            $success++;
        } else {
            $failed++;
            $errors[] = "Error untuk guru ID $guru_id: " . $conn->error;
        }
    }

    // Commit transaction jika semua berhasil
    if ($failed == 0) {
        $conn->commit();
        http_response_code(200);
        echo json_encode([
            'status' => 'success', 
            'message' => "Berhasil menyimpan $success data absensi",
            'success_count' => $success
        ]);
    } else {
        // Rollback jika ada yang gagal
        $conn->rollback();
        http_response_code(207); // Multi-Status
        echo json_encode([
            'status' => 'partial', 
            'message' => "Berhasil: $success, Gagal: $failed",
            'success_count' => $success,
            'failed_count' => $failed,
            'errors' => $errors
        ]);
    }

} catch (Exception $e) {
    // Rollback jika terjadi error
    $conn->rollback();
    http_response_code(500);
    echo json_encode([
        'status' => 'error', 
        'message' => 'Terjadi kesalahan server: ' . $e->getMessage()
    ]);
}

$conn->close();
?>