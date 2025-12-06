<?php
session_start();
// Cek apakah admin sudah login
if (!isset($_SESSION['admin_username'])) {
    header("Location: /project-semester-3-/pages/login.php");
    exit;
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil session admin
$admin_username = $_SESSION['admin_username'] ?? 'Admin';
$foto = $_SESSION['foto'] ?? null;

// Tentukan foto profil admin (cek file benar-benar ada)
$admin_profile_img = null;
if ($foto) {
    $upload_path = __DIR__ . '/../uploads/profil/' . $foto;
    if (file_exists($upload_path)) {
        $admin_profile_img = '/project-semester-3-/uploads/profil/' . $foto;
    }
}

// --- Hitung jumlah notifikasi ---
$stmt_notif = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
$jumlah_notif = $stmt_notif->fetchColumn();

// Hitung total berdasarkan status_ppdb
$stmt_pending = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_ppdb = 'pending'");
$jumlah_pending = $stmt_pending->fetchColumn();

$stmt_diterima = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_ppdb = 'diterima'");
$jumlah_diterima = $stmt_diterima->fetchColumn();

$stmt_ditolak = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_ppdb = 'ditolak'");
$jumlah_ditolak = $stmt_ditolak->fetchColumn();

$stmt_total = $pdo->query("SELECT COUNT(*) FROM pendaftaran");
$jumlah_total = $stmt_total->fetchColumn();

// Ambil data pendaftaran per tahun untuk grafik
$stmt_chart = $pdo->query("
    SELECT YEAR(tanggal_daftar) AS tahun, COUNT(*) AS total
    FROM pendaftaran
    GROUP BY YEAR(tanggal_daftar)
    ORDER BY tahun ASC
");

$tahun = [];
$total = [];
while ($row = $stmt_chart->fetch(PDO::FETCH_ASSOC)) {
    $tahun[] = $row['tahun'];
    $total[] = $row['total'];
}

// Ambil notifikasi terbaru
$stmt = $pdo->query("
    SELECT id, message, type, created_at, is_read
    FROM notifications
    ORDER BY created_at DESC
    LIMIT 5
");
$notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - TK Pertiwi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
        }

        .header {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            height: 60px;
            background-color: #007bff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: #fff;
            z-index: 1000;
        }

        .menu-toggle {
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }

        .header .title {
            font-size: 18px;
            font-weight: 600;
        }

        .header .icons {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .main-content {
            margin-left: 250px;
            padding: 90px 30px 30px;
            transition: 0.3s;
        }

        .main-content.collapsed {
            margin-left: 60px;
        }

        /* Navbar Profile */
        .navbar-profile-pic {
            border-radius: 50%;
            object-fit: cover;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            position: relative;
            overflow: hidden;
            transition: all 0.3s;
        }

        .stat-card::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #007bff 0%, #0056b3 100%);
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0, 0, 0, 0.15);
            border-color: #007bff;
        }

        .stat-card-header {
            display: flex;
            align-items: center;
            justify-content: space-between;
            margin-bottom: 15px;
        }

        .stat-icon {
            width: 50px;
            height: 50px;
            border-radius: 10px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 24px;
            color: white;
        }

        .stat-icon.pending {
            background: linear-gradient(135deg, #ffc107 0%, #ff9800 100%);
        }

        .stat-icon.diterima {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
        }

        .stat-icon.ditolak {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
        }

        .stat-icon.total {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            color: #333;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Chart Container */
        .chart-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .chart-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e7f3ff;
        }

        .notif-badge {
            position: absolute;
            top: -5px;
            right: -5px;
            min-width: 20px;
            height: 20px;
            background: #dc3545;
            color: white;
            border-radius: 10px;
            font-size: 11px;
            font-weight: bold;
            display: flex;
            align-items: center;
            justify-content: center;
            padding: 0 6px;
        }

        .notif-box {
            width: 350px !important;
            max-height: 450px;
            overflow-y: auto;
            border-radius: 10px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.15);
        }

        .notif-item {
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f0;
            transition: all 0.3s;
        }

        .notif-item:hover {
            background: #f8f9fa;
        }

        .notif-item.unread {
            background: #e7f3ff;
        }

        .text-wrap {
            white-space: normal;
            word-break: break-word;
            line-height: 1.4;
        }

        .welcome-section {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .welcome-icon {
            width: 70px;
            height: 70px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 15px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 35px;
            color: white;
        }

        .welcome-text h2 {
            margin: 0 0 5px 0;
            font-size: 22px;
            font-weight: 700;
            color: #333;
        }

        .welcome-text p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        @media (max-width:768px) {
            .main-content {
                margin-left: 0;
                padding: 90px 15px 30px;
            }

            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }

            .notif-box {
                width: 300px !important;
            }
        }
    </style>
</head>

<body>

    <!-- Header -->
    <header class="header">
        <div class="menu-toggle">&#9776;</div>
        <div class="title">ADMIN DASHBOARD</div>
        <div class="icons">
            <!-- Notifikasi -->
            <div class="position-relative dropdown">
                <a href="#" class="text-white" id="notifDropdown" data-bs-toggle="dropdown"
                    style="text-decoration: none;">
                    <i class="bi bi-bell fs-4"></i>
                    <?php if ($jumlah_notif > 0): ?>
                        <span class="notif-badge"><?= $jumlah_notif ?></span>
                    <?php endif; ?>
                </a>
                <ul class="dropdown-menu dropdown-menu-end p-0 notif-box">
                    <li class="dropdown-header bg-primary text-white fw-bold py-3 px-3">
                        <i class="bi bi-bell-fill me-2"></i>Notifikasi
                    </li>
                    <?php if (count($notifs) > 0): ?>
                        <?php foreach ($notifs as $notif): ?>
                            <li class="notif-item <?= $notif['is_read'] == 0 ? 'unread' : '' ?>">
                                <small class="text-muted d-block mb-1"><i class="bi bi-clock"></i>
                                    <?= date('d M Y H:i', strtotime($notif['created_at'])) ?></small>
                                <span class="d-block text-wrap"><?= htmlspecialchars($notif['message'] ?? '') ?></span>
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="dropdown-item text-center text-muted py-4">
                            <i class="bi bi-inbox fs-3 d-block mb-2"></i>
                            Tidak ada notifikasi
                        </li>
                    <?php endif; ?>
                    <li class="text-center py-2 border-top">
                        <a href="notifications.php" class="dropdown-item text-primary fw-semibold"><i
                                class="bi bi-arrow-right-circle"></i> Lihat Semua</a>
                    </li>
                </ul>
            </div>

            <!-- Profil Admin -->
            <div>
                <a href="/project-semester-3-/admin/profil_saya.php" class="d-flex align-items-center text-white"
                    style="text-decoration: none;">
                    <?php if ($admin_profile_img): ?>
                        <img src="<?= $admin_profile_img ?>" alt="Profil Admin"
                            style="width:38px; height:38px; border-radius:50%; object-fit:cover; margin-right:8px;">
                    <?php else: ?>
                        <i class="bi bi-person-circle fs-4 me-2"></i>
                    <?php endif; ?>
                    <span><?= htmlspecialchars($_SESSION['admin_username']) ?></span>
                </a>
            </div>
        </div>
    </header>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <!-- Page Header -->
        <div class="page-header">
            <h1><i class="bi bi-speedometer2"></i> Dashboard Admin</h1>
            <p>Selamat datang di panel administrasi TK Pertiwi</p>
        </div>

        <!-- Welcome Section -->
        <div class="welcome-section">
            <div class="welcome-icon"><i class="bi bi-person-badge"></i></div>
            <div class="welcome-text">
                <h2>Selamat Datang, <?= htmlspecialchars($_SESSION['admin_username'] ?? 'Admin') ?>!</h2>
                <p>Kelola semua data dan informasi TK Pertiwi dengan mudah</p>
            </div>
        </div>

        <!-- Statistics Cards -->
        <div class="stats-container">
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-number"><?= $jumlah_pending ?></div>
                        <div class="stat-label">Menunggu Verifikasi</div>
                    </div>
                    <div class="stat-icon pending"><i class="bi bi-hourglass-split"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-number"><?= $jumlah_diterima ?></div>
                        <div class="stat-label">Diterima</div>
                    </div>
                    <div class="stat-icon diterima"><i class="bi bi-check-circle-fill"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-number"><?= $jumlah_ditolak ?></div>
                        <div class="stat-label">Ditolak</div>
                    </div>
                    <div class="stat-icon ditolak"><i class="bi bi-x-circle-fill"></i></div>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-card-header">
                    <div>
                        <div class="stat-number"><?= $jumlah_total ?></div>
                        <div class="stat-label">Total Pendaftar</div>
                    </div>
                    <div class="stat-icon total"><i class="bi bi-people-fill"></i></div>
                </div>
            </div>
        </div>

        <!-- Chart -->
        <div class="chart-container">
            <div class="chart-title"><i class="bi bi-bar-chart-line-fill text-primary"></i> Grafik Pendaftaran Per Tahun
            </div>
            <canvas id="myChart" height="80"></canvas>
        </div>
    </main>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Sidebar toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });

        // Chart.js
        const tahun = <?= json_encode($tahun) ?>;
        const total = <?= json_encode($total) ?>;
        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tahun,
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: total,
                    fill: true,
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    borderColor: '#007bff',
                    borderWidth: 3,
                    tension: 0.4,
                    pointBackgroundColor: '#007bff',
                    pointBorderColor: '#fff',
                    pointBorderWidth: 2,
                    pointRadius: 5,
                    pointHoverRadius: 7
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: true
            }
        });

        // Mark notifications as read
        document.getElementById('notifDropdown').addEventListener('click', function () {
            let badge = document.querySelector('.notif-badge');
            if (badge) { setTimeout(() => badge.remove(), 500); }
            fetch('read_all_notifications.php');
        });
    </script>

</body>

</html>