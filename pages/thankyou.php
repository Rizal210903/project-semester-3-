<?php
session_start();
error_log("Memulai thankyou.php");

// Cek apakah header.php ada
$header_path = __DIR__ . '/../';
if (file_exists($header_path)) {
    
    error_log("header.php ditemukan dan di-include: $header_path");
} else {
    error_log("header.php TIDAK ditemukan di: $header_path");
    die("Error: File header.php tidak ditemukan");
}

// Cek session
if (!isset($_SESSION['pendaftaran_id']) || !isset($_SESSION['user_name'])) {
    error_log("Session pendaftaran_id atau user_name tidak ada, redirect ke pendaftaran.php");
    header('Location: /project-semester-3-/pages/pendaftaran.php');
    exit;
}
error_log("Session valid: pendaftaran_id=" . $_SESSION['pendaftaran_id'] . ", user_name=" . $_SESSION['user_name']);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - TK Pertiwi</title>
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
        .thankyou-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 600px;
            width: 100%;
            text-align: center;
        }
        .btn-primary {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            width: 100%;
            margin-top: 20px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #104E8B, #009ACD);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }
        .text-primary {
            color: #1E90FF !important;
        }
    </style>
</head>
<body>
    <div class="thankyou-container">
        <h2 class="text-primary mb-4">Terima Kasih!</h2>
        <p>Selamat, pembayaran Anda telah berhasil dikirim, <?php echo htmlspecialchars($_SESSION['user_name']); ?>!</p>
        <p>Mohon menunggu verifikasi dari pihak sekolah. Anda dapat memeriksa status pendaftaran melalui menu <strong>Info PPDB</strong> di <a href="/project-semester-3-/pages/status.php" class="text-primary">Cek Status Pendaftaran</a>.</p>
        <a href="/project-semester-3-/pages/index.php" class="btn btn-primary">Kembali ke Beranda</a>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>