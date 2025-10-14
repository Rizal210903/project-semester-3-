<?php
session_start();
include '../includes/header.php';

// Debugging ke log, bukan layar
ini_set('display_errors', 0);
ini_set('display_startup_errors', 0);
ini_set('log_errors', 1);
error_reporting(E_ALL);

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";
$status_message = "";
$email = "";
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Koneksi DB berhasil");
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    $response = ['status' => 'error', 'message' => 'Koneksi database gagal'];
    if ($is_ajax) {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        die("Koneksi database gagal");
    }
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = trim($_POST['email']);
    try {
        // Cek struktur tabel untuk debug
        $stmt = $pdo->query("DESCRIBE pendaftaran");
        $columns = $stmt->fetchAll(PDO::FETCH_COLUMN);
        error_log("Kolom tabel pendaftaran: " . implode(", ", $columns));

        $stmt = $pdo->prepare("SELECT id, nama_anak, nama_ortu, email, status_pembayaran, metode_pembayaran, tanggal_pembayaran, bukti_pembayaran FROM pendaftaran WHERE email = ?");
        $stmt->execute([$email]);
        $row = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($row) {
            $status = $row['status_pembayaran'];
            $pendaftaran_id = $row['id'];

            // Simpan ke status_history
            try {
                $stmt = $pdo->prepare("INSERT INTO status_history (pendaftaran_id, email, status) VALUES (?, ?, ?)");
                $stmt->execute([$pendaftaran_id, $email, $status]);
                error_log("Riwayat status disimpan untuk pendaftaran_id: $pendaftaran_id");
            } catch(PDOException $e) {
                error_log("Gagal simpan riwayat: " . $e->getMessage());
                $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Gagal menyimpan riwayat: ' . htmlspecialchars($e->getMessage()) . '</p>';
            }

            // Tentukan pesan berdasarkan status
            if ($status == "dibayar") {
                $status_message = '<h5 style="color: #32CD32;">Status: Diterima</h5><p>Pembayaran diterima. Silakan hubungi sekolah untuk langkah selanjutnya.</p>';
            } elseif ($status == "menunggu_verifikasi") {
                $status_message = '<h5 style="color: #FFD700;">Status: Menunggu Verifikasi</h5><p>Mohon tunggu konfirmasi dari sekolah.</p>';
            } elseif ($status == "belum_bayar") {
                $status_message = '<h5 style="color: #FF4500;">Status: Belum Bayar</h5><p>Silakan lakukan pembayaran di <a href="/project-semester-3-/pages/payment.php" class="btn btn-success">Halaman Pembayaran</a>.</p>';
            } elseif ($status == "ditolak") {
                $status_message = '<h5 style="color: #FF4500;">Status: Ditolak</h5><p>Maaf, pendaftaran ditolak.</p>';
            }
        } else {
            $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Email tidak ditemukan.</p>';
        }
    } catch(PDOException $e) {
        error_log("Query gagal: " . $e->getMessage());
        $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Query gagal: ' . htmlspecialchars($e->getMessage()) . '</p>';
    }

    if ($is_ajax) {
        // Ambil riwayat untuk AJAX
        $history_html = '';
        try {
            $stmt = $pdo->query("SELECT email, status, checked_at FROM status_history ORDER BY checked_at DESC LIMIT 5");
            $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
            if ($history) {
                foreach ($history as $row) {
                    $history_html .= "<tr><td>" . htmlspecialchars($row["email"]) . "</td><td>" . htmlspecialchars($row["status"]) . "</td><td>" . htmlspecialchars($row["checked_at"]) . "</td></tr>";
                }
            } else {
                $history_html = "<tr><td colspan='3' class='text-center'>Belum ada riwayat</td></tr>";
            }
        } catch(PDOException $e) {
            error_log("Gagal ambil history: " . $e->getMessage());
            $history_html = "<tr><td colspan='3' class='text-center'>Gagal memuat riwayat: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
        }

        $response = [
            'status' => $status_message ? 'success' : 'error',
            'status_message' => $status_message,
            'history_html' => $history_html
        ];
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cek Status Pendaftaran TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        .header-section {
            background: linear-gradient(135deg, #4ECDC4, #45B7D1);
            padding: 20px;
            position: relative;
        }
        .form-section, .history-section {
            background: #F0F8FF;
            padding: 40px 0;
        }
        .card {
            border-radius: 10px;
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }
        .btn-primary {
            background-color: #1E90FF;
            border: none;
            transition: background-color 0.3s;
        }
        .btn-primary:hover {
            background-color: #4ECDC4;
        }
        #resultPanel {
            display: none;
            animation: slideUp 0.5s ease-in-out;
        }
        @keyframes slideUp {
            from { transform: translateY(20px); opacity: 0; }
            to { transform: translateY(0); opacity: 1; }
        }
        #loadingSpinner {
            display: none;
        }
        .logo {
            width: 100px;
            position: absolute;
            top: 10px;
            left: 10px;
        }
    </style>
</head>
<body>
    <section class="header-section text-center py-4">
        <div class="container">
            <h1 class="display-4 text-white" style="text-shadow: 2px 2px #333;">Cek Status Pendaftaran TK Pertiwi</h1>
            <img src="../img/logo_tk.png" alt="Logo TK Pertiwi" class="logo">
        </div>
    </section>

    <section class="form-section py-5">
        <div class="container">
            <div class="card p-4 shadow-sm" style="max-width: 500px; margin: 0 auto; border-radius: 10px;">
                <h3 class="text-center mb-4">Masukkan Email Pendaftaran Anak Anda</h3>
                <form method="POST" id="statusForm">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" value="<?php echo htmlspecialchars($email); ?>" required>
                    </div>
                    <button type="submit" class="btn btn-primary w-100">Cek Status</button>
                    <div id="loadingSpinner" class="text-center mt-3">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Loading...</span>
                        </div>
                    </div>
                </form>
                <div id="resultPanel" class="mt-4 p-3 border rounded"><?php echo $status_message; ?></div>
            </div>
        </div>
    </section>

    <section class="history-section py-5">
        <div class="container">
            <h4 class="mb-4">Riwayat Cek Status</h4>
            <table class="table table-striped" style="background: #FFFFFF;" id="historyTable">
                <thead>
                    <tr>
                        <th>Email</th>
                        <th>Status</th>
                        <th>Tanggal Cek</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    try {
                        $stmt = $pdo->query("SELECT email, status, checked_at FROM status_history ORDER BY checked_at DESC LIMIT 5");
                        $history = $stmt->fetchAll(PDO::FETCH_ASSOC);
                        if ($history) {
                            foreach ($history as $row) {
                                echo "<tr><td>" . htmlspecialchars($row["email"]) . "</td><td>" . htmlspecialchars($row["status"]) . "</td><td>" . htmlspecialchars($row["checked_at"]) . "</td></tr>";
                            }
                        } else {
                            echo "<tr><td colspan='3' class='text-center'>Belum ada riwayat</td></tr>";
                        }
                    } catch(PDOException $e) {
                        echo "<tr><td colspan='3' class='text-center'>Gagal memuat riwayat: " . htmlspecialchars($e->getMessage()) . "</td></tr>";
                        error_log("Gagal ambil history: " . $e->getMessage());
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('statusForm').addEventListener('submit', async function(e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById('loadingSpinner');