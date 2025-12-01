<?php
include '../includes/header.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

/* ==========================================================
   AMBIL DATA PPDB DARI DATABASE (JSON)
========================================================== */

$stmt = $pdo->prepare("SELECT content FROM ppdb_info WHERE id = 1 LIMIT 1");
$stmt->execute();

$data = $stmt->fetchColumn();
$data = $data ? json_decode($data, true) : ["jadwal" => [], "syarat" => ""];

$jadwal = $data["jadwal"] ?? [];
$syarat = $data["syarat"] ?? "";
?>
<!DOCTYPE html>
<html lang="id">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Info PPDB TK Pertiwi</title>

<!-- SweetAlert2 -->
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

<style>
    body {
        font-family: 'Poppins', sans-serif;
        background: #f4f9ff;
        overflow-x: hidden;
    }
    .hero-section {
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        text-align: center;
        padding: 100px 0;
    }
    .hero-section h1 {
        color: #000080;
        font-weight: 700;
    }
    .section-block {
        padding: 80px 0;
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
    }
    .section-title {
        color: #000080;
        font-weight: 600;
        margin-bottom: 2rem;
        text-align: center;
    }
    .card-info {
        background: #ffffff;
        border-radius: 15px;
        border: none;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
        padding: 25px;
    }
    .list-group-item {
        border: none;
        border-bottom: 1px solid #e7f1ff;
        background: transparent;
        font-size: 1rem;
    }
</style>

</head>
<body>

<main class="container-fluid p-0">

    <section class="hero-section">
        <div class="container">
            <h1 class="display-5">Info PPDB TK Pertiwi</h1>
            <p>Informasi Penerimaan Peserta Didik Baru Tahun 2025/2026</p>
        </div>
        <div class="divider divider-wave"></div>
    </section>

    <section class="section-block">
        <div class="container">
            <div class="row">

                <!-- =============================== -->
                <!--           JADWAL PPDB          -->
                <!-- =============================== -->
                <div class="col-lg-6 mb-4">
                    <div class="card-info h-100">
                        <h2 class="section-title">Jadwal PPDB</h2>
                        <ul class="list-group list-group-flush">
                            <?php if (empty($jadwal)): ?>
                                <li class="list-group-item">Jadwal PPDB belum diisi admin.</li>
                            <?php else: ?>
                                <?php foreach ($jadwal as $item): ?>
                                    <li class="list-group-item">
                                        <strong><?= htmlspecialchars(ucwords($item["nama"])) ?></strong>:
                                        <?= date("j F Y", strtotime($item["mulai"])) ?> -
                                        <?= date("j F Y", strtotime($item["selesai"])) ?>
                                    </li>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </ul>
                    </div>
                </div>

                <!-- =============================== -->
                <!--     SYARAT PENDAFTARAN         -->
                <!-- =============================== -->
                <div class="col-lg-6 mb-4">
                    <div class="card-info h-100">
                        <h2 class="section-title">Syarat Pendaftaran</h2>
                        <ul class="list-group list-group-flush">
                            <?php 
                                if (empty(trim($syarat))) {
                                    echo '<li class="list-group-item">Syarat PPDB belum diisi admin.</li>';
                                } else {
                                    $syarat_list = preg_split('/\r\n|\r|\n/', $syarat);
                                    foreach ($syarat_list as $s):
                            ?>
                                <li class="list-group-item"><?= htmlspecialchars($s) ?></li>
                            <?php endforeach; } ?>
                        </ul>
                    </div>
                </div>

            </div>

            <!-- =============================== -->
            <!--         DROPDOWN AKSI           -->
            <!-- =============================== -->
            <div class="text-center mt-5">
                <div class="dropdown">
                    <button class="btn btn-primary btn-lg dropdown-toggle" data-bs-toggle="dropdown">
                        Pilih Aksi PPDB
                    </button>
                    <ul class="dropdown-menu">

                        <!-- FITUR DAFTAR SEKARANG -->
                        <li><a class="dropdown-item" href="#" onclick="daftarSekarang(event)">Daftar Sekarang</a></li>

                        <!-- CEK STATUS -->
                        <li><a class="dropdown-item" href="#" onclick="cekStatus(event)">Cek Status Pendaftaran</a></li>

                        <li><a class="dropdown-item" href="panduan_pbdb.php">Panduan PPDB</a></li>
                    </ul>
                </div>
            </div>

        </div>
    </section>

</main>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/project-semester-3-/includes/footer.php';
if (file_exists($footerPath)) {
    include $footerPath;
}
?>

<!-- ===================================== -->
<!--   POPUP + CEK STATUS + DAFTAR SEKARANG -->
<!-- ===================================== -->
<script>
// Ambil user_id dari PHP
let userId = <?= isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null' ?>;

// ============================
//   FITUR DAFTAR SEKARANG
// ============================
function daftarSekarang(event) {
    event.preventDefault();

    if (userId === null) {
        Swal.fire({
            icon: 'warning',
            title: 'Login Diperlukan',
            text: 'Silakan login terlebih dahulu untuk melakukan pendaftaran.',
            confirmButtonText: 'OK'
        });
        return;
    }

    window.location.href = 'pendaftaran.php';
}

// ============================
//   FITUR CEK STATUS
// ============================
function cekStatus(event) {
    event.preventDefault();

    if (userId === null) {
        Swal.fire({
            icon: 'warning',
            title: 'Anda Belum Login',
            text: 'Silakan login terlebih dahulu.',
            confirmButtonText: 'OK'
        });
        return;
    }

    fetch('cek_status_daftar.php')
        .then(res => res.json())
        .then(data => {

            if (data.status === 'terdaftar') {
                window.location.href = 'status.php';
            } else {
                Swal.fire({
                    icon: 'warning',
                    title: 'Belum Melakukan Pendaftaran',
                    text: 'Silakan melakukan pendaftaran terlebih dahulu.',
                    confirmButtonText: 'Mengerti'
                });
            }
        })
        .catch(err => {
            Swal.fire({
                icon: 'error',
                title: 'Terjadi Kesalahan',
                text: 'Gagal mengecek status.',
            });
        });
}
</script>

</body>
</html>
