<?php
session_start();
error_log("Memulai payment.php");

ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";
$error = "";
$success = "";

// Cek apakah header.php ada
$header_path = __DIR__ . '/../';
if (file_exists($header_path)) {
    include $header_path;
    error_log("header.php ditemukan dan di-include: $header_path");
} else {
    error_log("header.php TIDAK ditemukan di: $header_path");
    die("Error: File header.php tidak ditemukan");
}

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Koneksi DB berhasil");
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil nominal dari tabel settings
try {
    $stmt = $pdo->query("SELECT value FROM settings WHERE key_name = 'nominal_pendaftaran'");
    $nominal = $stmt->fetchColumn();
    if (!$nominal) {
        $nominal = 500000; // Default jika gak ada di DB
        error_log("Nominal default digunakan: $nominal");
    }
} catch(PDOException $e) {
    error_log("Gagal ambil nominal: " . $e->getMessage());
    $error = "Gagal ambil nominal pembayaran: " . $e->getMessage();
}

// Cek session pendaftaran_id
if (!isset($_SESSION['pendaftaran_id']) || !isset($_SESSION['user_name'])) {
    error_log("Session pendaftaran_id atau user_name tidak ada");
    header('Location: /project-semester-3-/pages/pendaftaran.php');
    exit;
}
error_log("Session valid: pendaftaran_id=" . $_SESSION['pendaftaran_id'] . ", user_name=" . $_SESSION['user_name']);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $metode_pembayaran = trim($_POST['metode_pembayaran'] ?? '');
    $bukti_pembayaran = '';

    // Validasi metode pembayaran
    if (empty($metode_pembayaran)) {
        $error = "Pilih metode pembayaran!";
        error_log("Validasi gagal: metode pembayaran kosong");
    } else {
        // Handle upload bukti pembayaran
        $upload_dir = '../Uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            error_log("Folder Uploads dibuat: $upload_dir");
        }
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $max_size = 2 * 1024 * 1024; // 2MB

        if (isset($_FILES['bukti_pembayaran']) && $_FILES['bukti_pembayaran']['error'] === UPLOAD_ERR_OK) {
            $file_type = $_FILES['bukti_pembayaran']['type'];
            $file_size = $_FILES['bukti_pembayaran']['size'];
            $file_name = time() . '_bukti_pembayaran_' . basename($_FILES['bukti_pembayaran']['name']);
            $file_path = $upload_dir . $file_name;
            error_log("File bukti_pembayaran: type=$file_type, size=$file_size, path=$file_path");

            if (!in_array($file_type, $allowed_types)) {
                $error = "File bukti pembayaran harus berupa PDF, JPG, atau PNG!";
                error_log("File bukti_pembayaran ditolak: tipe tidak diizinkan ($file_type)");
            } elseif ($file_size > $max_size) {
                $error = "File bukti pembayaran terlalu besar, maksimum 2MB!";
                error_log("File bukti_pembayaran ditolak: ukuran terlalu besar ($file_size bytes)");
            } elseif (!move_uploaded_file($_FILES['bukti_pembayaran']['tmp_name'], $file_path)) {
                $error = "Gagal upload bukti pembayaran!";
                error_log("Gagal upload bukti_pembayaran: " . $_FILES['bukti_pembayaran']['error']);
            } else {
                $bukti_pembayaran = $file_name;
                error_log("File bukti_pembayaran berhasil diupload: $file_path");
            }
        }

        if (!$error) {
            try {
                // Cek struktur tabel untuk debug
                $stmt = $pdo->query("DESCRIBE pendaftaran");
                $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
                error_log("Kolom tabel pendaftaran: " . implode(", ", $columns));

                // Update data pembayaran
                $stmt = $pdo->prepare("UPDATE pendaftaran SET metode_pembayaran = ?, bukti_pembayaran = ?, status_pembayaran = 'menunggu_verifikasi', tanggal_pembayaran = NOW() WHERE id = ?");
                $stmt->execute([$metode_pembayaran, $bukti_pembayaran, $_SESSION['pendaftaran_id']]);
                error_log("Data pembayaran disimpan untuk ID: " . $_SESSION['pendaftaran_id']);
                $success = "Pembayaran berhasil dikirim, menunggu verifikasi!";

                // Cek apakah thankyou.php ada
                $redirect_url = '/project-semester-3-/pages/thankyou.php';
                $thankyou_path = __DIR__ . '/thankyou.php';
                if (file_exists($thankyou_path)) {
                    error_log("File thankyou.php ditemukan di: $thankyou_path");
                } else {
                    error_log("File thankyou.php TIDAK ditemukan di: $thankyou_path");
                    $error = "File thankyou.php tidak ditemukan di server";
                }

                if (!$error) {
                    error_log("Mencoba redirect ke: $redirect_url");
                    header("Location: $redirect_url");
                    exit;
                } else {
                    error_log("Redirect dibatalkan karena error: $error");
                }
            } catch(PDOException $e) {
                $error = "Gagal simpan data pembayaran: " . $e->getMessage();
                error_log("Update error: " . $e->getMessage());
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran PPDB - TK Pertiwi</title>
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
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .form-control, .form-select {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px;
        }
        .form-control:focus, .form-select:focus {
            border-color: #1E90FF;
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            width: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #104E8B, #009ACD);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center mb-4 text-primary">Pembayaran PPDB TK Pertiwi</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label for="nama_ortu" class="form-label">Nama Orang Tua</label>
                <input type="text" class="form-control" id="nama_ortu" value="<?php echo htmlspecialchars($_SESSION['user_name']); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="nominal" class="form-label">Nominal Pembayaran</label>
                <input type="text" class="form-control" id="nominal" value="Rp <?php echo number_format($nominal, 0, ',', '.'); ?>" disabled>
            </div>
            <div class="mb-3">
                <label for="metode_pembayaran" class="form-label">Metode Pembayaran *</label>
                <select class="form-select" id="metode_pembayaran" name="metode_pembayaran" required>
                    <option value="">Pilih Metode</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="QRIS">QRIS</option>
                    <option value="Tunai">Tunai</option>
                </select>
            </div>
            <div class="mb-3">
                <label for="bukti_pembayaran" class="form-label">Bukti Pembayaran (opsional)</label>
                <input type="file" class="form-control" id="bukti_pembayaran" name="bukti_pembayaran" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <button type="submit" class="btn btn-primary">Kirim Pembayaran</button>
        </form>
        <p class="mt-3 text-center">
            <a href="/project-semester-3-/index.php" class="text-primary">Kembali ke Beranda</a>
        </p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>