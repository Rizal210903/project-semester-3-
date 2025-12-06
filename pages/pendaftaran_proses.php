<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/notification_helper.php'; // ← TAMBAHKAN INI

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Cek apakah sudah pernah daftar
$query_cek = "SELECT id FROM pendaftaran WHERE user_id = ?";
$stmt = $conn->prepare($query_cek);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $_SESSION['error'] = "Anda sudah pernah mendaftar sebelumnya!";
    header("Location: pendaftaran.php");
    exit;
}

// Validasi POST data
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: pendaftaran.php");
    exit;
}

// Ambil data dari form
$nama_anak = trim($_POST['nama_anak']);
$tanggal_lahir_anak = $_POST['tanggal_lahir_anak'];
$nama_ortu = trim($_POST['nama_ortu']);
$nomor_telepon = trim($_POST['nomor_telepon']);
$email = trim($_POST['email']);
$alamat = trim($_POST['alamat']);

// Generate access code (6 digit random)
$access_code = strtoupper(substr(md5(uniqid(rand(), true)), 0, 6));

// Folder untuk upload dokumen
$upload_dir = '../uploads/dokumen/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Fungsi untuk upload file
function uploadFile($file, $upload_dir, $prefix) {
    $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
    $max_size = 2 * 1024 * 1024; // 2MB
    
    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['success' => false, 'message' => 'Gagal upload file ' . $prefix];
    }
    
    if (!in_array($file['type'], $allowed_types)) {
        return ['success' => false, 'message' => 'Tipe file ' . $prefix . ' tidak diizinkan'];
    }
    
    if ($file['size'] > $max_size) {
        return ['success' => false, 'message' => 'Ukuran file ' . $prefix . ' terlalu besar'];
    }
    
    $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = $prefix . '_' . time() . '_' . uniqid() . '.' . $extension;
    $filepath = $upload_dir . $filename;
    
    if (move_uploaded_file($file['tmp_name'], $filepath)) {
        return ['success' => true, 'filename' => $filename];
    }
    
    return ['success' => false, 'message' => 'Gagal memindahkan file ' . $prefix];
}

// Upload semua dokumen
$akta = uploadFile($_FILES['akta_kelahiran'], $upload_dir, 'akta');
if (!$akta['success']) {
    $_SESSION['error'] = $akta['message'];
    header("Location: pendaftaran.php");
    exit;
}

$kk = uploadFile($_FILES['kartu_keluarga'], $upload_dir, 'kk');
if (!$kk['success']) {
    $_SESSION['error'] = $kk['message'];
    header("Location: pendaftaran.php");
    exit;
}

$foto = uploadFile($_FILES['pas_foto'], $upload_dir, 'foto');
if (!$foto['success']) {
    $_SESSION['error'] = $foto['message'];
    header("Location: pendaftaran.php");
    exit;
}

$surat_sehat = uploadFile($_FILES['surat_sehat'], $upload_dir, 'surat_sehat');
if (!$surat_sehat['success']) {
    $_SESSION['error'] = $surat_sehat['message'];
    header("Location: pendaftaran.php");
    exit;
}

// Insert data ke database
$query = "INSERT INTO pendaftaran (
    user_id, 
    nama_anak, 
    tanggal_lahir_anak, 
    nama_ortu, 
    nomor_telepon, 
    email, 
    alamat, 
    akta_kelahiran, 
    kartu_keluarga, 
    pas_foto, 
    surat_sehat, 
    access_code,
    status_ppdb,
    status_pembayaran,
    tanggal_daftar
) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', 'belum_bayar', NOW())";

$stmt = $conn->prepare($query);
$stmt->bind_param(
    "isssssssssss", 
    $user_id,
    $nama_anak,
    $tanggal_lahir_anak,
    $nama_ortu,
    $nomor_telepon,
    $email,
    $alamat,
    $akta['filename'],
    $kk['filename'],
    $foto['filename'],
    $surat_sehat['filename'],
    $access_code
);

if ($stmt->execute()) {
    $pendaftaran_id = $stmt->insert_id;
    
    // ============================================
    // BUAT NOTIFIKASI - BAGIAN YANG DITAMBAHKAN
    // ============================================
    $message = "Pendaftaran baru dari {$nama_anak} (Orang Tua: {$nama_ortu})";
    $notif_result = createNotification($conn, $user_id, $pendaftaran_id, 'pendaftaran', $message);
    
    // Debug log (opsional - bisa dihapus setelah berhasil)
    if (!$notif_result) {
        error_log("GAGAL membuat notifikasi untuk pendaftaran ID: " . $pendaftaran_id);
    }
    // ============================================
    
    $_SESSION['success'] = "Pendaftaran berhasil! Silakan lakukan pembayaran.";
    $_SESSION['pendaftaran_id'] = $pendaftaran_id;
    
    // Redirect ke halaman pembayaran
    header("Location: payment.php");
    exit;
} else {
    $_SESSION['error'] = "Gagal menyimpan data: " . $stmt->error;
    header("Location: pendaftaran.php");
    exit;
}
?>