<?php
include '../includes/header.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
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
        body { font-family: 'Poppins', sans-serif; background: #FFFFFF; }
        .hero-section {
            background: url('/project-semester-3-/img/ppdb-bg.jpg') no-repeat center/cover;
            position: relative;
            min-height: 300px;
        }
        .hero-section div {
            position: absolute; top: 0; left: 0; width: 100%; height: 100%;
            background: #FFFFFF;
        }
        .info-section { background: #FFFFFF; padding: 40px 0; }
        .card-info { background: #fff; border: 2px solid #45B7D1; border-radius: 10px; padding: 20px; }
        .dropdown-menu {
            background: #FFFFFF; border: 2px solid #ffffff; border-radius: 10px;
            box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1); z-index: 1001;
        }
        .dropdown-item { cursor: pointer; }
        .dropdown-item:hover { background: #45B7D1; color: #fff; }
        .dropdown-item.disabled { color: #888; cursor: not-allowed; pointer-events: none; }
        .modal-content { border-radius: 10px; }

        @media (max-width: 768px) {
            .hero-section { min-height: 200px; }
            .hero-section .container { padding: 20px; }
            .card-info { margin-bottom: 20px; }
            .dropdown-menu { width: 100%; }
        }
    </style>
</head>
<body>
    <main class="container-fluid p-0">
        <!-- Hero Section -->
        <section class="hero-section text-center py-5">
        
            <div class="container py-5 position-relative">
                <h1 class="display-4 text-animate__animated animate__fadeIn" style="color: #000080ff; font-family: 'Poppins', sans-serif;">Info PPDB TK Pertiwi</h1>
                <p class="lead text-black animate__animated animate__fadeIn" style="color: #000000ff; font-family: 'Poppins', sans-serif;">Informasi Penerimaan Peserta Didik Baru Tahun 2025/2026</p>
            </div>
        </section>

        <!-- Info Section -->
        <section class="info-section">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!-- Jadwal PPDB -->
                        <div class="card-info mb-4 animate__animated animate__fadeInUp">
                            <h2 class="text-center mb-4" style="color: #000080; font-size: clamp(1.2rem, 3vw, 1.8rem); font-family: 'Poppins', sans-serif;">Jadwal PPDB</h2>
                            <ul class="list-group list-group-flush">
                                <?php if (empty($jadwal)): ?>
                                    <li class="list-group-item animate__animated animate__slideInUp">Data jadwal belum tersedia.</li>
                                <?php else: ?>
                                    <?php $delay = 1; foreach ($jadwal as $item): ?>
                                        <li class="list-group-item animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo $delay; ?>s;">
                                            <?php echo htmlspecialchars($item); ?>
                                        </li>
                                        <?php $delay++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>

                        <!-- Syarat Pendaftaran -->
                        <div class="card-info animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                            <h2 class="text-center mb-4" style="color: #000080; font-size: clamp(1.2rem, 3vw, 1.8rem); font-family: 'Poppins', sans-serif;">Syarat Pendaftaran</h2>
                            <ul class="list-group list-group-flush">
                                <?php if (empty($syarat)): ?>
                                    <li class="list-group-item animate__animated animate__slideInUp">Data syarat belum tersedia.</li>
                                <?php else: ?>
                                    <?php $delay = 1; foreach ($syarat as $item): ?>
                                        <li class="list-group-item animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo $delay; ?>s;">
                                            <?php echo htmlspecialchars($item); ?>
                                        </li>
                                        <?php $delay++; ?>
                                    <?php endforeach; ?>
                                <?php endif; ?>
                            </ul>
                        </div>
                    </div>
                </div>

                <!-- Dropdown Aksi -->
                <div class="text-center mt-5">
                    <div class="dropdown">
                        <button class="btn btn-primary btn-lg dropdown-toggle" type="button" id="ppdbDropdown" data-bs-toggle="dropdown" aria-expanded="false" style="background-color: #45B7D1; border: none;">
                            Pilih Aksi PPDB <i class="bi bi-chevron-down"></i>
                        </button>
                        <ul class="dropdown-menu" id="ppdbMenu" aria-labelledby="ppdbDropdown">
                            <li><a class="dropdown-item" href="#" data-bs-toggle="modal" data-bs-target="#loginAlertModal">Daftar Sekarang</a></li>
                            <li><a class="dropdown-item" href="/project-semester-3-/pages/status.php">Cek Status Pendaftaran</a></li>
                            <li><a class="dropdown-item disabled" href="#">Panduan PPDB (Segera Hadir)</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </section>
    </main>

    <footer class="text-center py-3" style="background: #e2e7e8ff; color: #000000ff;">
        <p>&copy; 2025 TK Pertiwi Semua hak cipta dilindungi</p>
    </footer>

    <!-- Modal Peringatan Login -->
    <div class="modal fade" id="loginAlertModal" tabindex="-1" aria-labelledby="loginAlertModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="loginAlertModalLabel">Peringatan</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p>Silakan login terlebih dahulu untuk melanjutkan pendaftaran.</p>
                    <a href="/project-semester-3-/pages/login.php" class="btn btn-primary">Login/Register</a>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            console.log('Page loaded, checking functionality');
            console.log(typeof bootstrap === 'object' ? 'Bootstrap JS loaded' : 'Bootstrap JS not loaded');
        });
    </script>
</body>
</html>