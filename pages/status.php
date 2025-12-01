<?php
session_start();

$servername = "localhost";
$username   = "root";
$password   = "";
$dbname     = "tk_pertiwi_db";

$error = "";
$status_data = null;

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Cek login
    if (!isset($_SESSION['user_id'])) {
        $error = "Silakan login terlebih dahulu.";
    } else {
        $user_id = $_SESSION['user_id'];

        // Ambil data pendaftaran terbaru user
        $stmt = $pdo->prepare("
            SELECT p.*, pay.status_pembayaran
            FROM pendaftaran p
            LEFT JOIN payments pay ON p.id = pay.pendaftar_id
            WHERE p.user_id = ?
            ORDER BY pay.id DESC
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        $status_data = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$status_data) {
            $error = "Anda belum melakukan pendaftaran.";
        }
    }

} catch(PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Status Pendaftaran - TK Pertiwi</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
<style>
body {
    font-family: 'Poppins', sans-serif;
    background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
    display: flex;
    justify-content: center;
    align-items: center;
    min-height: 100vh;
}
.status-container {
    background: rgba(255,255,255,0.95);
    border-radius: 20px;
    box-shadow: 0 15px 40px rgba(0,0,0,0.15);
    padding: 40px;
    max-width: 700px;
    width: 100%;
}
.status-card {
    border: 2px solid #e0e0e0;
    border-radius: 10px;
    padding: 20px;
    background: #fff;
}
.status-card h4 { color: #1E90FF; }
.badge-status { font-size: 1rem; padding: 6px 12px; border-radius: 8px; }
.btn-home { margin-top: 15px; background: linear-gradient(45deg, #1E90FF, #00B7EB); border: none; border-radius: 10px; color: #fff; padding: 10px 20px; text-decoration: none; }
.btn-home:hover { background: linear-gradient(45deg, #104E8B, #009ACD); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(30,144,255,0.4); }
.btn-payment { margin-top: 10px; background: linear-gradient(45deg, #FFA500, #FF8C00); border: none; border-radius: 10px; color: #fff; padding: 10px 20px; text-decoration: none; }
.btn-payment:hover { background: linear-gradient(45deg, #FF8C00, #FF7F50); transform: translateY(-2px); box-shadow: 0 5px 15px rgba(255,140,0,0.4); }
</style>
</head>
<body>

<div class="status-container">
    <h2 class="text-center mb-4 text-primary">Status Pendaftaran</h2>

    <?php if ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php elseif ($status_data): ?>
        <div class="status-card">
            <h4 class="mb-3">Detail Pendaftaran</h4>
            <p><strong>ID Pendaftaran:</strong> <?= htmlspecialchars($status_data['id']) ?></p>
            <p><strong>Nama Anak:</strong> <?= htmlspecialchars($status_data['nama_anak']) ?></p>
            <p><strong>Nama Orang Tua:</strong> <?= htmlspecialchars($status_data['nama_ortu']) ?></p>
            <p><strong>Tanggal Lahir Anak:</strong> <?= htmlspecialchars($status_data['tanggal_lahir_anak']) ?></p>
            <p><strong>Alamat:</strong> <?= htmlspecialchars($status_data['alamat'] ?? 'Tidak diisi') ?></p>
            <p><strong>Nomor Telepon:</strong> <?= htmlspecialchars($status_data['nomor_telepon'] ?? 'Tidak diisi') ?></p>
            <p><strong>Email:</strong> <?= htmlspecialchars($status_data['email'] ?? 'Tidak diisi') ?></p>
            <p><strong>Tanggal Daftar:</strong> <?= date('d M Y', strtotime($status_data['tanggal_daftar'])) ?></p>
            <hr>
            <p><strong>Status Pembayaran:</strong> 
                <span class="badge badge-status
                    <?php 
                        if ($status_data['status_pembayaran'] === 'dibayar') echo 'bg-success';
                        elseif ($status_data['status_pembayaran'] === 'menunggu_verifikasi') echo 'bg-info text-dark';
                        else echo 'bg-warning text-dark';
                    ?>">
                    <?php
                        if ($status_data['status_pembayaran'] === 'dibayar') echo 'Selesai (Lunas)';
                        elseif ($status_data['status_pembayaran'] === 'menunggu_verifikasi') echo 'Menunggu Verifikasi Admin';
                        else echo 'Belum Bayar';
                    ?>
                </span>
            </p>
           <p><strong>Status PPDB:</strong>
    <span class="badge badge-status
        <?php
            if ($status_data['status_pembayaran'] === 'belum_bayar') {
                echo 'bg-secondary'; // bisa pakai abu-abu untuk belum bayar
            } elseif ($status_data['status_ppdb'] === 'diterima') {
                echo 'bg-success';
            } elseif ($status_data['status_ppdb'] === 'pending') {
                echo 'bg-warning text-dark';
            } else {
                echo 'bg-danger';
            }
        ?>
    ">
        <?php
            if ($status_data['status_pembayaran'] === 'belum_bayar') {
                echo '-'; // tampilkan kosong atau teks "Belum Bayar"
            } elseif ($status_data['status_ppdb'] === 'diterima') {
                echo 'Diterima';
            } elseif ($status_data['status_ppdb'] === 'pending') {
                echo 'Menunggu Verifikasi Dokumen';
            } else {
                echo 'Ditolak';
            }
        ?>
    </span>
</p>


            <?php if ($status_data['status_pembayaran'] !== 'dibayar'): ?>
                <div class="text-center">
                    <a href="/project-semester-3-/pages/payment.php" class="btn btn-payment">Lanjutkan Pembayaran</a>
                </div>
            <?php endif; ?>

        </div>
        <div class="text-center">
            <a href="/project-semester-3-/pages/index.php" class="btn btn-home">Kembali ke Beranda</a>
        </div>
    <?php endif; ?>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
