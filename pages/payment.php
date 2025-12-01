<?php
session_start();
// Menggunakan __DIR__ untuk path yang lebih aman. 
// Asumsi: Anda berada di folder 'pages' dan 'includes' ada di level atas.
include __DIR__ . "/../includes/config.php"; 

// Pastikan user login
if (!isset($_SESSION['user_id'])) {
    die("Harus login terlebih dahulu.");
}

$user_id = $_SESSION['user_id'];
$pendaftar_id = 0; // Inisialisasi

// --- 1. Ambil Email User (MENGGUNAKAN PREPARED STATEMENT) ---
$stmt_user = $conn->prepare("SELECT email FROM users WHERE id = ? LIMIT 1");
if (!$stmt_user) {
    die("Error menyiapkan statement user: " . $conn->error);
}

$stmt_user->bind_param("i", $user_id); 
$stmt_user->execute();
$q_user = $stmt_user->get_result();

if ($q_user->num_rows == 0) {
    $stmt_user->close();
    die("User tidak ditemukan.");
}
$user = $q_user->fetch_assoc();
$email_user = $user['email'];
$stmt_user->close();    


// --- 2. Ambil ID Pendaftar (MENGGUNAKAN PREPARED STATEMENT) ---
$stmt_pendaftar = $conn->prepare("
    SELECT id FROM pendaftaran 
    WHERE user_id = ?
    ORDER BY id DESC LIMIT 1
");
$stmt_pendaftar->bind_param("i", $user_id); // pakai user_id sebagai integer


$stmt_pendaftar->execute();
$q_pendaftar = $stmt_pendaftar->get_result();

if ($q_pendaftar->num_rows == 0) {
    $stmt_pendaftar->close();
    die("Data pendaftaran tidak ditemukan.");
}

$pendaftar = $q_pendaftar->fetch_assoc();
$pendaftar_id = $pendaftar['id'];
$stmt_pendaftar->close();
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<title>Pembayaran Formulir</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

<style>
/* Style tetap sama */
body {
    background: #eef2f7;
    font-family: 'Poppins', sans-serif;
}
.card-payment {
    max-width: 550px;
    background: white;
    padding: 32px;
    margin: 40px auto;
    border-radius: 20px;
    box-shadow: 0px 10px 40px rgba(0,0,0,0.15);
}
.hidden { display: none; }
.va-number { font-size: 26px; font-weight: bold; cursor: pointer; }
</style>
</head>

<body>

<div class="card-payment">
    <h3 class="text-center mb-4 fw-bold">Pembayaran Formulir</h3>

    <form method="POST" action="/project-semester-3-/pages/process_payment.php" enctype="multipart/form-data">

        <input type="hidden" name="pendaftar_id" value="<?= $pendaftar_id ?>" required> 

        <label class="form-label">Jumlah Pembayaran</label>
        <input type="text" name="jumlah" class="form-control mb-3" value="150000" readonly>

        <label class="form-label">Metode Pembayaran</label>
        <select id="payment_method" name="metode" class="form-select mb-3" required>
            <option value="">-- Pilih Metode Pembayaran --</option>
            <option value="qris">QRIS</option>
            <option value="bri">Transfer BRI</option>
            <option value="bca">Transfer BCA</option>
            <option value="mandiri">Transfer Mandiri</option>
        </select>

        <div id="qr_box" class="hidden text-center">
            <h5 class="fw-semibold">QRIS Code</h5>
            <img src="../img/qris.png" alt="QR Code" class="qr-img" width="240">
            <p class="text-muted small">Scan menggunakan aplikasi keuangan Anda</p>
        </div>

        <div id="va_box" class="hidden text-center">
            <img id="bank_logo" src="" width="120" class="mb-1">
            <h6 class="fw-semibold">Virtual Account</h6>
            <p id="va_number" class="va-number"></p>
            <p class="text-muted small">Klik nomor untuk menyalin</p>
        </div>

        <label class="form-label mt-3">Upload Bukti Pembayaran</label>

        <!-- FIX ERROR: NAME HARUS SAMA DENGAN YANG DICEK DI process_payment.php -->
        <input type="file" name="bukti_pembayaran" class="form-control" accept="image/*,.pdf" required>

       <button type="submit" class="btn btn-primary w-100 mt-4 py-2">Simpan Pembayaran</button>

        <!-- Tombol Bayar Nanti -->
     <a href="/project-semester-3-/pages/status.php" 
     class="btn btn-secondary w-100 mt-3 py-2">
        Bayar Nanti
</a>

    </form>
</div>

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
        alert("Nomor Virtual Account disalin: " + this.textContent);
    }).catch(err => {
        console.error('Gagal menyalin: ', err);
    });
});
</script>

</body>
</html>
