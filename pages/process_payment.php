<?php
session_start();
require_once '../includes/config.php';
require_once '../includes/notification_helper.php'; // ← TAMBAHKAN INI (BARIS BARU)

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Validasi POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header("Location: payment.php");
    exit;
}

// Ambil data dari form
$pendaftar_id = intval($_POST['pendaftar_id']);
$jumlah = intval($_POST['jumlah']);
$metode = trim($_POST['metode']);

// Validasi pendaftar_id
if (empty($pendaftar_id)) {
    $_SESSION['error'] = "ID Pendaftar tidak valid!";
    header("Location: payment.php");
    exit;
}

// Cek apakah pendaftar milik user yang login
$stmt_cek = $conn->prepare("SELECT user_id, status_pembayaran FROM pendaftaran WHERE id = ?");
$stmt_cek->bind_param("i", $pendaftar_id);
$stmt_cek->execute();
$result_cek = $stmt_cek->get_result();

if ($result_cek->num_rows == 0) {
    $_SESSION['error'] = "Data pendaftaran tidak ditemukan!";
    header("Location: payment.php");
    exit;
}

$data_cek = $result_cek->fetch_assoc();
if ($data_cek['user_id'] != $_SESSION['user_id']) {
    $_SESSION['error'] = "Akses ditolak!";
    header("Location: payment.php");
    exit;
}

if ($data_cek['status_pembayaran'] !== 'belum_bayar') {
    $_SESSION['error'] = "Pembayaran sudah pernah dilakukan!";
    header("Location: status_pendaftaran.php");
    exit;
}

$stmt_cek->close();

// Folder untuk upload bukti pembayaran
$upload_dir = '../uploads/pembayaran/';
if (!is_dir($upload_dir)) {
    mkdir($upload_dir, 0777, true);
}

// Upload bukti pembayaran
if (!isset($_FILES['bukti_pembayaran']) || $_FILES['bukti_pembayaran']['error'] !== UPLOAD_ERR_OK) {
    $_SESSION['error'] = "Gagal upload bukti pembayaran!";
    header("Location: payment.php");
    exit;
}

$file = $_FILES['bukti_pembayaran'];
$allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
$max_size = 2 * 1024 * 1024; // 2MB

// Validasi tipe file
if (!in_array($file['type'], $allowed_types)) {
    $_SESSION['error'] = "Tipe file tidak diizinkan! Hanya JPG, PNG, atau PDF.";
    header("Location: payment.php");
    exit;
}

// Validasi ukuran file
if ($file['size'] > $max_size) {
    $_SESSION['error'] = "Ukuran file terlalu besar! Maksimal 2MB.";
    header("Location: payment.php");
    exit;
}

// Generate nama file unik
$extension = pathinfo($file['name'], PATHINFO_EXTENSION);
$filename = 'bukti_' . $pendaftar_id . '_' . time() . '.' . $extension;
$filepath = $upload_dir . $filename;

// Pindahkan file
if (!move_uploaded_file($file['tmp_name'], $filepath)) {
    $_SESSION['error'] = "Gagal memindahkan file bukti pembayaran!";
    header("Location: payment.php");
    exit;
}

// Ambil nama_anak dan nama_ortu dari tabel pendaftaran
$stmt_nama = $conn->prepare("SELECT nama_anak, nama_ortu FROM pendaftaran WHERE id = ?");
$stmt_nama->bind_param("i", $pendaftar_id);
$stmt_nama->execute();
$result_nama = $stmt_nama->get_result();
$data_nama = $result_nama->fetch_assoc();
$nama_anak = $data_nama['nama_anak'];
$nama_ortu = $data_nama['nama_ortu'];
$stmt_nama->close();

// Insert ke tabel payments dengan status dibayar otomatis
$stmt_bayar = $conn->prepare("
    INSERT INTO payments 
    (pendaftar_id, nama_anak, nama_ortu, metode_pembayaran, tanggal_bayar, bukti_path, status_pembayaran) 
    VALUES (?, ?, ?, ?, NOW(), ?, 'dibayar')
");

$stmt_bayar->bind_param(
    "issss", 
    $pendaftar_id, 
    $nama_anak, 
    $nama_ortu, 
    $metode,
    $filename
);

if (!$stmt_bayar->execute()) {
    $_SESSION['error'] = "Gagal menyimpan data pembayaran: " . $stmt_bayar->error;
    // Hapus file yang sudah diupload
    unlink($filepath);
    header("Location: payment.php");
    exit;
}

$stmt_bayar->close();

// Update status pembayaran di tabel pendaftaran menjadi dibayar otomatis
$stmt_update = $conn->prepare("
    UPDATE pendaftaran 
    SET status_pembayaran = 'dibayar' 
    WHERE id = ?
");
$stmt_update->bind_param("i", $pendaftar_id);
$stmt_update->execute();
$stmt_update->close();

// ============================================
// BUAT NOTIFIKASI PEMBAYARAN - BAGIAN BARU
// ============================================
$user_id = $_SESSION['user_id'];
$message = "Bukti pembayaran baru dari {$nama_anak} (Orang Tua: {$nama_ortu}) - Status: DIBAYAR";
$notif_result = createNotification($conn, $user_id, $pendaftar_id, 'pembayaran', $message);

// Debug log (opsional - bisa dihapus setelah berhasil)
if (!$notif_result) {
    error_log("GAGAL membuat notifikasi pembayaran untuk pendaftaran ID: " . $pendaftar_id);
}
// ============================================

// Berhasil - Status otomatis DIBAYAR
$_SESSION['success'] = "Pembayaran berhasil! Status: DIBAYAR";
header("Location: status_pendaftaran.php");
exit;
?>