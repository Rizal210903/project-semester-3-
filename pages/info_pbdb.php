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
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil jadwal PPDB
$stmt = $pdo->query("SELECT content FROM ppdb_info WHERE type = 'jadwal' ORDER BY display_order");
$jadwal = $stmt->fetchAll(PDO::FETCH_COLUMN);

// Ambil syarat PPDB
$stmt = $pdo->query("SELECT content FROM ppdb_info WHERE type = 'syarat' ORDER BY display_order");
$syarat = $stmt->fetchAll(PDO::FETCH_COLUMN);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Info PPDB - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f9ff;
            overflow-x: hidden;
        }

        /* ===== HERO SECTION ===== */
        .hero-section {
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
            position: relative;
            text-align: center;
            padding: 100px 0;
        }

        .hero-section h1 {
            color: #000080;
            font-weight: 700;
        }

        .hero-section p {
            color: #000;
            font-size: 1.1rem;
        }

        /* ===== SECTION BLOCK ===== */
        .section-block {
            padding: 80px 0;
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
            position: relative;
        }

        .section-title {
            color: #000080;
            font-weight: 600;
            margin-bottom: 2rem;
            text-align: center;
        }

        /* ===== CARD INFO ===== */
        .card-info {
            background: #ffffff;
            border-radius: 15px;
            border: none;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 25px;
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .card-info:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .list-group-item {
            border: none;
            border-bottom: 1px solid #e7f1ff;
            background: transparent;
            font-size: 1rem;
        }

        .list-group-item:last-child {
            border-bottom: none;
        }

        /* ===== DROPDOWN ===== */
        .btn-primary {
            background-color: #45B7D1;
            border: none;
            padding: 10px 25px;
            font-weight: 500;
            border-radius: 8px;
            transition: background 0.3s ease;
        }

        .btn-primary:hover {
            background-color: #319ab7;
        }

        .dropdown-menu {
            border: none;
            border-radius: 12px;
            box-shadow: 0 6px 15px rgba(0,0,0,0.15);
            padding: 10px;
        }

        .dropdown-item {
            border-radius: 6px;
            padding: 8px 15px;
            transition: background 0.3s ease;
        }

        .dropdown-item:hover {
            background-color: #45B7D1;
            color: #fff;
        }

        .dropdown-item.disabled {
            color: #888;
            pointer-events: none;
        }

        /* ===== DIVIDERS ===== */
        .divider {
            height: 100px;
            position: relative;
            overflow: hidden;
        }

        .divider-wave {
            background: url('https://www.svgrepo.com/show/492220/wave-top.svg') no-repeat center;
            background-size: cover;
        }

        .divider-wave2 {
            background: url('https://www.svgrepo.com/show/491975/wave.svg') no-repeat center;
            background-size: cover;
        }

        footer {
            background: #e7f1ff;
            color: #000;
            padding: 20px;
            text-align: center;
        }

        @media (max-width: 768px) {
            .card-info { margin-bottom: 20px; }
        }
    </style>
</head>

<body>
    <main class="container-fluid p-0">

        <!-- ===== HERO ===== -->
        <section class="hero-section animate__animated animate__fadeInDown">
            <div class="container">
                <h1 class="display-5 animate__animated animate__fadeInDown">Info PPDB TK Pertiwi</h1>
                <p class="animate__animated animate__fadeInUp">Informasi Penerimaan Peserta Didik Baru Tahun 2025/2026</p>
            </div>
            <div class="divider divider-wave"></div>
        </section>

        <!-- ===== JADWAL & SYARAT ===== -->
        <section class="section-block">
            <div class="container">
                <div class="row">
                    <!-- JADWAL -->
                    <div class="col-lg-6 mb-4 animate__animated animate__fadeInUp">
                        <div class="card-info h-100">
                            <h2 class="section-title">Jadwal PPDB</h2>
                            <ul class="list-group list-group-flush">
                                <?php if (empty($jadwal)): ?>
                                    <li class="list-group-item">Data jadwal belum tersedia.</li>
                                <?php else: ?>
                                    <?php $delay = 1; foreach ($jadwal as $item): ?>
                                        <li class="list-group-item animate__animated animate__slideInUp" style="animation-delay: 0.<?= $delay; ?>s;">
                                            <?= htmlspecialchars($item); ?>
                                        </li>
                                        <?php $delay++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>

                    <!-- SYARAT -->
                    <div class="col-lg-6 mb-4 animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                        <div class="card-info h-100">
                            <h2 class="section-title">Syarat Pendaftaran</h2>
                            <ul class="list-group list-group-flush">
                                <?php if (empty($syarat)): ?>
                                    <li class="list-group-item">Data syarat belum tersedia.</li>
                                <?php else: ?>
                                    <?php $delay = 1; foreach ($syarat as $item): ?>
                                        <li class="list-group-item animate__animated animate__slideInUp" style="animation-delay: 0.<?= $delay; ?>s;">
                                            <?= htmlspecialchars($item); ?>
                                        </li>
                                        <?php $delay++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- DROPDOWN AKSI -->
                <div class="text-center mt-5 animate__animated animate__fadeInUp" style="animation-delay: 0.4s;">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-lg dropdown-toggle" type="button" id="ppdbDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                            Pilih Aksi PPDB <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu" aria-labelledby="ppdbDropdown">
                            <li><a class="dropdown-item" href="/project-semester-3-/pages/pendaftaran.php">Daftar Sekarang</a></li>
                            <li><a class="dropdown-item" href="/project-semester-3-/pages/status.php">Cek Status Pendaftaran</a></li>
                            <li><a class="dropdown-item disabled" href="#">Panduan PPDB (Segera Hadir)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
            <div class="divider divider-wave2"></div>
        </section>
    </main>

    <footer>
        <p>&copy; 2025 TK Pertiwi. Semua hak cipta dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>
