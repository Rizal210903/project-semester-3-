<?php
session_start();

// Cek user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: /project-semester-3-/login.php");
    exit;
}

// Koneksi database
$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi database gagal: " . $e->getMessage());
}

// Pastikan form di-submit
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    die("Akses tidak valid.");
}

// Ambil data
$nama_anak            = $_POST['nama_anak'];
$nama_ortu            = $_POST['nama_ortu'];
$tanggal_lahir_anak   = $_POST['tanggal_lahir_anak'];
$alamat               = $_POST['alamat'];
$nomor_telepon        = $_POST['nomor_telepon'];
$email                = $_POST['email'];

$user_id = $_SESSION['user_id'];

// Folder upload
$uploadDir = "../uploads/pendaftaran/";
if (!file_exists($uploadDir)) {
    mkdir($uploadDir, 0777, true);
}

// Fungsi upload file
function uploadDokumen($fileKey, $uploadDir) {
    if (isset($_FILES[$fileKey]) && $_FILES[$fileKey]["error"] == 0) {
        $ext = pathinfo($_FILES[$fileKey]["name"], PATHINFO_EXTENSION);
        $filename = $fileKey . "_" . time() . "." . $ext;
        $path = $uploadDir . $filename;

        if (move_uploaded_file($_FILES[$fileKey]["tmp_name"], $path)) {
            return $filename;
        }
    }
    return null;
}

// Upload dokumen
$akta_kelahiran  = uploadDokumen('akta_kelahiran', $uploadDir);
$kartu_keluarga  = uploadDokumen('kartu_keluarga', $uploadDir);
$pas_foto        = uploadDokumen('pas_foto', $uploadDir);
$surat_sehat     = uploadDokumen('surat_sehat', $uploadDir);

// Simpan ke database
$stmt = $pdo->prepare("
    INSERT INTO pendaftaran 
    (user_id, nama_anak, nama_ortu, tanggal_lahir_anak, alamat, nomor_telepon, email, 
     akta_kelahiran, kartu_keluarga, pas_foto, surat_sehat, status_pembayaran, status_ppdb)
    VALUES 
    (:user_id, :nama_anak, :nama_ortu, :tanggal_lahir_anak, :alamat, :nomor_telepon, :email,
     :akta_kelahiran, :kartu_keluarga, :pas_foto, :surat_sehat, 'belum_bayar', 'pending')
");

$stmt->execute([
    ':user_id'            => $user_id,
    ':nama_anak'          => $nama_anak,
    ':nama_ortu'          => $nama_ortu,
    ':tanggal_lahir_anak' => $tanggal_lahir_anak,
    ':alamat'             => $alamat,
    ':nomor_telepon'      => $nomor_telepon,
    ':email'              => $email,
    ':akta_kelahiran'     => $akta_kelahiran,
    ':kartu_keluarga'     => $kartu_keluarga,
    ':pas_foto'           => $pas_foto,
    ':surat_sehat'        => $surat_sehat
]);

// Ambil ID terakhir
$pendaftaran_id = $pdo->lastInsertId();

// Simpan ke session agar dapat dilihat di status
$_SESSION['pendaftaran_id'] = $pendaftaran_id;

// Redirect ke halaman status
header("Location: status.php");
exit;
?>
