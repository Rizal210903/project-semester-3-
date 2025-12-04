<?php
session_start();
require_once '../includes/config.php';

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

$user_id = $_SESSION['user_id'];

// Ambil data pendaftaran user
$stmt_pendaftar = $conn->prepare("
    SELECT id, nama_anak, status_pembayaran 
    FROM pendaftaran 
    WHERE user_id = ?
    ORDER BY id DESC 
    LIMIT 1
");
$stmt_pendaftar->bind_param("i", $user_id);
$stmt_pendaftar->execute();
$result = $stmt_pendaftar->get_result();

if ($result->num_rows == 0) {
    $_SESSION['error'] = "Data pendaftaran tidak ditemukan. Silakan daftar terlebih dahulu.";
    header("Location: pendaftaran.php");
    exit;
}

$pendaftar = $result->fetch_assoc();
$pendaftar_id = $pendaftar['id'];
$nama_anak = $pendaftar['nama_anak'];
$status_pembayaran = $pendaftar['status_pembayaran'];

// Jika sudah bayar, redirect ke status
if ($status_pembayaran !== 'belum_bayar') {
    $_SESSION['info'] = "Anda sudah melakukan pembayaran sebelumnya.";
    header("Location: status_pendaftaran.php");
    exit;
}

$stmt_pendaftar->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pembayaran Formulir - TK Pertiwi</title>
    
    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">
    
    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            background: #f1f7ff;
            font-family: 'Poppins', sans-serif;
        }
        
        .navbar {
            background: #1E90FF !important;
        }
        
        .card-payment {
            max-width: 650px;
            background: white;
            padding: 40px;
            margin: 60px auto;
            border-radius: 20px;
            box-shadow: 0px 10px 40px rgba(0,0,0,0.15);
        }
        
        .hidden { 
            display: none; 
        }
        
        .va-number { 
            font-size: 28px; 
            font-weight: bold; 
            cursor: pointer;
            font-family: 'Courier New', monospace;
            letter-spacing: 2px;
            color: #1E90FF;
        }
        
        .qr-img {
            border: 3px solid #1E90FF;
            border-radius: 15px;
            padding: 10px;
            background: white;
        }
        
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #1E90FF;
            padding: 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }
        
        .payment-info {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe9a8 100%);
            border: 2px solid #ffc107;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }
        
        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }
        
        .btn-primary {
            background: #1E90FF;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }
        
        .btn-primary:hover {
            background: #1873CC;
        }
    </style>
</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TK Pertiwi</a>
        </div>
    </nav>

    <div class="card-payment">
        <h3 class="text-center mb-2 fw-bold"><i class="bi bi-credit-card-fill text-primary"></i> Pembayaran Formulir</h3>
        <p class="text-center text-muted mb-4">Silakan pilih metode pembayaran dan upload bukti transfer</p>

        <?php if (isset($_SESSION['success'])): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['success']); ?>
        <?php endif; ?>

        <?php if (isset($_SESSION['error'])): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="bi bi-exclamation-circle-fill"></i> <?= $_SESSION['error'] ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>

        <div class="payment-info">
            <div class="row">
                <div class="col-md-6">
                    <small class="text-muted">Nama Anak:</small>
                    <div class="fw-bold"><?= htmlspecialchars($nama_anak) ?></div>
                </div>
                <div class="col-md-6">
                    <small class="text-muted">Biaya Pendaftaran:</small>
                    <div class="fw-bold text-danger">Rp 150.000</div>
                </div>
            </div>
        </div>

        <form method="POST" action="process_payment.php" enctype="multipart/form-data" id="paymentForm">

            <input type="hidden" name="pendaftar_id" value="<?= $pendaftar_id ?>"> 
            <input type="hidden" name="jumlah" value="150000">

            <div class="mb-4">
                <label class="form-label"><i class="bi bi-wallet2"></i> Metode Pembayaran <span class="text-danger">*</span></label>
                <select id="payment_method" name="metode" class="form-select" required>
                    <option value="">-- Pilih Metode Pembayaran --</option>
                    <option value="qris">QRIS</option>
                    <option value="bri">Transfer BRI</option>
                    <option value="bca">Transfer BCA</option>
                    <option value="mandiri">Transfer Mandiri</option>
                </select>
            </div>

            <!-- BOX QRIS -->
            <div id="qr_box" class="hidden text-center mb-4">
                <h5 class="fw-semibold mb-3"><i class="bi bi-qr-code"></i> QRIS Code</h5>
                <img src="../img/qris.png" alt="QR Code" class="qr-img" width="280">
                <p class="text-muted small mt-2">Scan menggunakan aplikasi keuangan Anda</p>
            </div>

            <!-- BOX VIRTUAL ACCOUNT -->
            <div id="va_box" class="hidden text-center mb-4">
                <img id="bank_logo" src="" width="140" class="mb-2">
                <h6 class="fw-semibold mb-2">Virtual Account</h6>
                <p id="va_number" class="va-number mb-1"></p>
                <p class="text-muted small">
                    <i class="bi bi-info-circle"></i> Klik nomor untuk menyalin
                </p>
            </div>

            <div class="mb-4">
                <label class="form-label"><i class="bi bi-file-earmark-arrow-up"></i> Upload Bukti Pembayaran <span class="text-danger">*</span></label>
                <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf" required>
                <small class="text-muted">Format: JPG, PNG, PDF (Max: 2MB)</small>
            </div>

            <div class="info-box">
                <small>
                    <i class="bi bi-info-circle-fill"></i> 
                    <strong>Catatan:</strong> Setelah upload bukti pembayaran, admin akan memverifikasi dalam 1x24 jam.
                </small>
            </div>

            <div class="d-grid gap-2">
                <button type="submit" class="btn btn-primary py-3">
                    <i class="bi bi-send-fill"></i> Kirim Bukti Pembayaran
                </button>
                
                <a href="status_pendaftaran.php" class="btn btn-secondary py-2">
                    <i class="bi bi-clock-history"></i> Bayar Nanti
                </a>
            </div>

        </form>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        const BANK = {
            bri: { va: "1122334455667788", logo: "../img/bri.png" },
            bca: { va: "1234567890123456", logo: "../img/bca.png" },
            mandiri: { va: "9876543210987654", logo: "../img/mandiri.png" }
        };

        let methodSelect = document.getElementById("payment_method");
        let qrBox = document.getElementById("qr_box");
        let vaBox = document.getElementById("va_box");
        let bankLogo = document.getElementById("bank_logo");
        let vaNumber = document.getElementById("va_number");

        // Pilih metode
        methodSelect.addEventListener("change", function () {
            qrBox.classList.add("hidden");
            vaBox.classList.add("hidden");

            let m = this.value;

            if (m === "qris") {
                qrBox.classList.remove("hidden");
            } else if (BANK[m]) {
                bankLogo.src = BANK[m].logo; 
                vaNumber.textContent = BANK[m].va;
                vaBox.classList.remove("hidden");
            }
        });

        // Fungsi untuk menyalin VA Number
        vaNumber.addEventListener('click', function() {
            navigator.clipboard.writeText(this.textContent).then(() => {
                alert("✓ Nomor Virtual Account berhasil disalin: " + this.textContent);
            }).catch(err => {
                console.error('Gagal menyalin: ', err);
            });
        });

        // Validasi ukuran file
        document.querySelector('input[name="bukti_pembayaran"]').addEventListener('change', function() {
            const maxSize = 2 * 1024 * 1024; // 2MB
            if (this.files[0] && this.files[0].size > maxSize) {
                alert('Ukuran file terlalu besar! Maksimal 2MB');
                this.value = '';
            }
        });
    </script>

</body>
</html>