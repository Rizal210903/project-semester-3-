<?php
session_start();

ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

include '../includes/config.php';

// --- 0. Pastikan user login ---
if (!isset($_SESSION['user_id'])) {
    die("❌ Harus login terlebih dahulu.");
}
$user_id = $_SESSION['user_id'];

// --- 1. Ambil data POST ---
$pendaftar_id = $_POST['pendaftar_id'] ?? 0;
$metode       = $_POST['metode'] ?? '';
$status       = "dibayar"; // langsung LUNAS
$tanggal      = date("Y-m-d H:i:s");

// --- 2. Ambil data pendaftar dari tabel pendaftaran ---
$stmt = $conn->prepare("SELECT nama_anak, nama_ortu, user_id FROM pendaftaran WHERE id = ?");
$stmt->bind_param("i", $pendaftar_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    die("❌ ID pendaftaran tidak ditemukan!");
}

$data = $result->fetch_assoc();
$nama_anak = $data['nama_anak'];
$nama_ortu = $data['nama_ortu'];
$user_id_pendaftar = $data['user_id']; // pastikan user_id pendaftar

$stmt->close();

// --- 3. Validasi file upload ---
if (!isset($_FILES['bukti_pembayaran'])) {
    die("❌ Error: File bukti pembayaran tidak diterima dari form.");
}

$file = $_FILES['bukti_pembayaran'];

if ($file['error'] !== UPLOAD_ERR_OK) {
    die("❌ Error upload file: " . $file['error']);
}

// --- 4. Simpan file ke folder uploads ---
$upload_dir = "../uploads/bukti_pembayaran/";
if (!is_dir($upload_dir)) mkdir($upload_dir, 0777, true);

$ext = pathinfo($file['name'], PATHINFO_EXTENSION);
$namaFile = "bukti_" . time() . "_" . uniqid() . "." . $ext;
$tujuan = $upload_dir . $namaFile;

if (!move_uploaded_file($file['tmp_name'], $tujuan)) {
    die("❌ Gagal menyimpan file ke folder upload.");
}

// --- 5. Insert data ke tabel payments ---
$stmt = $conn->prepare("
    INSERT INTO payments
    (pendaftar_id, nama_anak, nama_ortu, metode_pembayaran, tanggal_bayar, bukti_path, status_pembayaran)
    VALUES (?, ?, ?, ?, ?, ?, ?)
");

$stmt->bind_param(
    "issssss",
    $pendaftar_id,
    $nama_anak,
    $nama_ortu,
    $metode,
    $tanggal,
    $namaFile,
    $status
);

if (!$stmt->execute()) {
    die("❌ Gagal insert payment: " . $stmt->error);
}
$stmt->close();

// --- 6. Update status_pembayaran di tabel pendaftaran ---
$update = $conn->prepare("UPDATE pendaftaran SET status_pembayaran = 'dibayar' WHERE id = ?");
$update->bind_param("i", $pendaftar_id);

if (!$update->execute()) {
    die("❌ Gagal update status pendaftaran: " . $update->error);
}
$update->close();

// --- 7. Tambahkan notifikasi pembayaran ---
$message = "User '{$nama_ortu}' telah melakukan pembayaran untuk anak '{$nama_anak}'";
$type = "pembayaran";

$stmt_notif = $conn->prepare("
    INSERT INTO notifications (user_id, pendaftaran_id, message, type, created_at)
    VALUES (?, ?, ?, ?, NOW())
");
$stmt_notif->bind_param("iiss", $user_id_pendaftar, $pendaftar_id, $message, $type);

if (!$stmt_notif->execute()) {
    die("❌ Gagal menambahkan notifikasi: " . $stmt_notif->error);
}
$stmt_notif->close();

// --- 8. Redirect ke halaman status ---
$_SESSION['pendaftaran_id'] = $pendaftar_id;
header("Location: status.php?upload=berhasil");
exit();
?>
