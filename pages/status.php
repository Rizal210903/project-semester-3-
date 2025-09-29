<?php
// Aktifkan debugging untuk memudahkan pelacakan error
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

// Koneksi ke database
$conn = new mysqli("localhost", "root", "", "tk_pertiwi_db");

if ($conn->connect_error) {
    $response = ['status' => 'error', 'message' => 'Koneksi database gagal: ' . $conn->connect_error];
    if (isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest') {
        header('Content-Type: application/json');
        echo json_encode($response);
        exit;
    } else {
        die($response['message']);
    }
}

$status_message = "";
$email = "";
$is_ajax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['email'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $sql = "SELECT status, reason_reject FROM siswa WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result === false) {
        $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Query gagal: ' . $conn->error . '</p>';
    } elseif ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $status = $row["status"];
        $reason_reject = $row["reason_reject"];

        // Simpan ke riwayat
        $status_save = $conn->real_escape_string($status);
        $insert_query = $conn->query("INSERT INTO status_history (email, status, checked_at) VALUES ('$email', '$status_save', NOW())");
        if (!$insert_query) {
            $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Gagal menyimpan riwayat: ' . $conn->error . '</p>';
        } else {
            // Tentukan pesan berdasarkan status
            if ($status == "diterima") {
                $status_message = '<h5 style="color: #32CD32;">Status: Diterima</h5><p>Silakan lanjutkan pembayaran SPP</p><a href="/TK-PERTIWI/pages/pembayaran.php" class="btn btn-success">Lihat Detail Pembayaran</a>';
            } elseif ($status == "pending") {
                $status_message = '<h5 style="color: #FFD700;">Status: Pending</h5><p>Mohon tunggu konfirmasi dari sekolah</p>';
            } elseif ($status == "ditolak") {
                $status_message = '<h5 style="color: #FF4500;">Status: Ditolak</h5><p>Maaf, pendaftaran ditolak. Alasan: ' . ($reason_reject ? htmlspecialchars($reason_reject) : "Tidak ada alasan") . '</p>';
            }
        }
    } else {
        $status_message = '<h5 style="color: #FF4500;">Error</h5><p>Email tidak ditemukan</p>';
    }

    if ($is_ajax) {
        // Untuk request AJAX, kembalikan JSON dengan status_message dan HTML tabel riwayat
        $history_html = '';
        $history_result = $conn->query("SELECT email, status, checked_at FROM status_history ORDER BY checked_at DESC LIMIT 5");
        if ($history_result->num_rows > 0) {
            while ($row = $history_result->fetch_assoc()) {
                $history_html .= "<tr><td>" . htmlspecialchars($row["email"]) . "</td><td>" . htmlspecialchars($row["status"]) . "</td><td>" . htmlspecialchars($row["checked_at"]) . "</td></tr>";
            }
        } else {
            $history_html = "<tr><td colspan='3' class='text-center'>Belum ada riwayat</td></tr>";
        }

        $response = [
            'status' => 'success',
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
<html lang="en">
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
    <!-- Header -->
    <section class="header-section text-center py-4">
        <div class="container">
            <h1 class="display-4 text-white" style="text-shadow: 2px 2px #333;">Cek Status Pendaftaran TK Pertiwi</h1>
            <img src="../img/logo_tk.png" alt="Logo TK Pertiwi" class="logo">
        </div>
    </section>

    <!-- Form Input -->
    <section class="form-section py-5">
        <div class="container">
            <div class="card p-4 shadow-sm" style="max-width: 500px; margin: 0 auto; border-radius: 10px;">
                <h3 class="text-center mb-4">Masukkan Email Pendaftaran Anak Anda</h3>
                <form method="POST" id="statusForm">
                    <div class="form-group mb-3">
                        <label for="email" class="form-label">Email:</label>
                        <input type="email" class="form-control" id="email" name="email" placeholder="contoh@email.com" required>
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

    <!-- Tabel Riwayat -->
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
                    $history_result = $conn->query("SELECT email, status, checked_at FROM status_history ORDER BY checked_at DESC LIMIT 5");
                    if ($history_result->num_rows > 0) {
                        while ($row = $history_result->fetch_assoc()) {
                            echo "<tr><td>" . htmlspecialchars($row["email"]) . "</td><td>" . htmlspecialchars($row["status"]) . "</td><td>" . htmlspecialchars($row["checked_at"]) . "</td></tr>";
                        }
                    } else {
                        echo "<tr><td colspan='3' class='text-center'>Belum ada riwayat</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </section>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('statusForm').addEventListener('submit', function(e) {
            e.preventDefault();
            const loadingSpinner = document.getElementById('loadingSpinner');
            const resultPanel = document.getElementById('resultPanel');
            const historyTableBody = document.querySelector('#historyTable tbody');
            const form = this;

            loadingSpinner.style.display = 'block';
            resultPanel.style.display = 'none';

            const formData = new FormData(form);
            fetch(form.action, {
                method: 'POST',
                body: formData,
                headers: {
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                if (!response.ok) {
                    throw new Error('Network response was not ok: ' + response.status);
                }
                return response.json();
            })
            .then(data => {
                loadingSpinner.style.display = 'none';
                if (data.status === 'error') {
                    resultPanel.innerHTML = '<h5 style="color: #FF4500;">Error</h5><p>' + data.message + '</p>';
                } else {
                    resultPanel.innerHTML = data.status_message;
                    historyTableBody.innerHTML = data.history_html;
                }
                resultPanel.style.display = 'block';
            })
            .catch(error => {
                loadingSpinner.style.display = 'none';
                resultPanel.style.display = 'block';
                resultPanel.innerHTML = '<h5 style="color: #FF4500;">Error</h5><p>Gagal memproses permintaan: ' + error.message + '</p>';
            });
        });
    </script>
</body>
</html>

<?php $conn->close(); ?>