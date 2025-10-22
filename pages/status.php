<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";
$error = "";
$status_data = null;

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Koneksi DB berhasil untuk status");

    if (isset($_SESSION['pendaftaran_id'])) {
        $stmt = $pdo->prepare("SELECT * FROM pendaftaran WHERE id = ?");
        $stmt->execute([$_SESSION['pendaftaran_id']]);
        $status_data = $stmt->fetch(PDO::FETCH_ASSOC);
        if (!$status_data) {
            $error = "Data pendaftaran tidak ditemukan!";
            error_log("Data pendaftaran tidak ditemukan untuk ID: " . $_SESSION['pendaftaran_id']);
        }
    } else {
        $error = "Anda belum melakukan pendaftaran atau session habis. Silakan daftar terlebih dahulu.";
        error_log("Session pendaftaran_id tidak ada");
    }
} catch(PDOException $e) {
    $error = "Gagal mengambil data: " . $e->getMessage();
    error_log("Error status: " . $e->getMessage());
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #E0F7FA;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .status-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .status-card {
            border: 2px solid #e0e0e0;
            border-radius: 10px;
            padding: 20px;
            background: #fff;
        }
        .status-card h4 {
            color: #1E90FF;
        }
        .btn-primary {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #104E8B, #009ACD);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }
        .btn-secondary {
            background: #6c757d;
            border: none;
            border-radius: 10px;
            padding: 10px 20px;
            color: #fff;
        }
        .btn-secondary:hover {
            background: #5a6268;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(108, 117, 125, 0.4);
        }
        .alert {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="status-container">
        <h2 class="text-center mb-4 text-primary">Status Pendaftaran</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php elseif ($status_data): ?>
            <div class="status-card">
                <h4 class="mb-3">Detail Pendaftaran</h4>
                <p><strong>ID Pendaftaran:</strong> <?php echo htmlspecialchars($status_data['id']); ?></p>
                <p><strong>Nama Anak:</strong> <?php echo htmlspecialchars($status_data['nama_anak']); ?></p>
                <p><strong>Nama Orang Tua:</strong> <?php echo htmlspecialchars($status_data['nama_ortu']); ?></p>
                <p><strong>Tanggal Lahir Anak:</strong> <?php echo htmlspecialchars($status_data['tanggal_lahir_anak']); ?></p>
                <p><strong>Alamat:</strong> <?php echo htmlspecialchars($status_data['alamat'] ?? 'Tidak diisi'); ?></p>
                <p><strong>Nomor Telepon:</strong> <?php echo htmlspecialchars($status_data['nomor_telepon'] ?? 'Tidak diisi'); ?></p>
                <p><strong>Email:</strong> <?php echo htmlspecialchars($status_data['email'] ?? 'Tidak diisi'); ?></p>
                <p><strong>Akta Kelahiran:</strong> <?php echo htmlspecialchars($status_data['akta_kelahiran'] ? 'Diunggah' : 'Tidak diunggah'); ?></p>
                <p><strong>Kartu Keluarga:</strong> <?php echo htmlspecialchars($status_data['kartu_keluarga'] ? 'Diunggah' : 'Tidak diunggah'); ?></p>
                <p><strong>Pas Foto:</strong> <?php echo htmlspecialchars($status_data['pas_foto'] ? 'Diunggah' : 'Tidak diunggah'); ?></p>
                <p><strong>Surat Sehat:</strong> <?php echo htmlspecialchars($status_data['surat_sehat'] ? 'Diunggah' : 'Tidak diunggah'); ?></p>
                <p><strong>Tanggal Daftar:</strong> <?php echo htmlspecialchars($status_data['tanggal_daftar']); ?></p>
                <p><strong>Status Pembayaran:</strong> 
                    <span class="badge <?php echo $status_data['status_pembayaran'] === 'sudah_bayar' ? 'bg-success' : 'bg-warning'; ?>">
                        <?php echo htmlspecialchars(ucfirst($status_data['status_pembayaran'])); ?>
                    </span>
                </p>
            </div>
            <div class="text-center mt-4">
                <a href="/project-semester-3-/pages/index.php" class="btn btn-secondary mt-2">Kembali ke Beranda</a>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>