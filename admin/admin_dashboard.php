<?php
session_start();

// Cek apakah admin sudah login
if (!isset($_SESSION['admin_username'])) {
    header("Location: /project-semester-3-/pages/login.php");
    exit;
}

include 'sidebar.php';

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

// --- Hitung jumlah notifikasi ---
$stmt_notif = $pdo->query("SELECT COUNT(*) FROM notifications WHERE is_read = 0");
$jumlah_notif = $stmt_notif->fetchColumn();

// Hitung total berdasarkan status_ppdb
$stmt_pending = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_ppdb = 'pending'");
$jumlah_pending = $stmt_pending->fetchColumn();

$stmt_diterima = $pdo->query("SELECT COUNT(*) FROM pendaftaran WHERE status_ppdb = 'diterima'");
$jumlah_diterima = $stmt_diterima->fetchColumn();

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
?>


<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Admin Dashboard - TK Pertiwi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet" />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet" />
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
            overflow-x: hidden;
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

        /* Agar box notifikasi tidak overflow */
        .notif-box {
            width: 320px !important;
            max-height: 400px;
            overflow-y: auto;
            border-radius: 8px;
        }

        /* Perbaiki item agar padding rapi */
        .notif-item {
            white-space: normal !important;
            word-wrap: break-word;
            line-height: 1.3;
        }

        /* Membuat teks wrap rapi */
        .text-wrap {
            white-space: normal;
            word-break: break-word;
        }


        .header .menu-toggle {
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
            transition: margin-left 0.3s ease;
        }

        .main-content.collapsed {
            margin-left: 70px;
        }

        h1 {
            font-size: 22px;
            margin-bottom: 20px;
            font-weight: 600;
            color: #333;
        }

        .card-container {
            display: flex;
            flex-wrap: wrap;
            gap: 20px;
            margin-bottom: 25px;
        }

        .card-box {
            flex: 1 1 250px;
            background-color: #fff;
            border-radius: 10px;
            padding: 20px;
            text-align: center;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
            position: relative;
        }

        .card-box::before {
            content: "";
            position: absolute;
            left: 0;
            top: 0;
            width: 5px;
            height: 100%;
            background-color: #007bff;
            border-radius: 10px 0 0 10px;
        }

        .card-box .number {
            font-size: 26px;
            font-weight: 700;
            color: #007bff;
        }

        .card-box .label {
            font-size: 14px;
            color: #555;
        }

        .graph-container {
            background-color: #fff;
            border-radius: 12px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0, 0, 0, 0.1);
        }

        .graph-container h2 {
            font-size: 18px;
            margin-bottom: 15px;
            color: #333;
        }
    </style>
</head>

<body>

    <?php
    $stmt = $pdo->query("
    SELECT id, message, type, created_at, is_read
    FROM notifications
    ORDER BY created_at DESC
    LIMIT 5
");
    $notifs = $stmt->fetchAll(PDO::FETCH_ASSOC);
    ?>

    <header class="header">
        <div class="menu-toggle">&#9776;</div>
        <div class="title">ADMIN DASHBOARD</div>
        <div class="icons">

            <!-- Notifikasi -->
            <div class="position-relative dropdown">
                <a href="#" class="text-dark" id="notifDropdown" data-bs-toggle="dropdown">
                    <i class="bi bi-bell fs-4 text-white"></i>
                    <?php if ($jumlah_notif > 0): ?>
                        <span id="notifCount" class="badge bg-danger"><?= $jumlah_notif ?></span>

                    <?php endif; ?>
                </a>

                <ul class="dropdown-menu dropdown-menu-end p-0 notif-box">
                    <li class="dropdown-header bg-primary text-white fw-bold py-2 px-3">Notifikasi</li>

                    <?php if (count($notifs) > 0): ?>
                        <?php foreach ($notifs as $notif): ?>
                            <li class="px-3 py-2 notif-item <?= $notif['is_read'] == 0 ? 'unread' : '' ?>">
                                <small class="text-muted d-block">
                                    <?= date('d M Y H:i', strtotime($notif['created_at'])) ?>
                                </small>

                                <span class="d-block text-wrap">
                                    <?= htmlspecialchars($notif['message'] ?? '') ?>
                                </span>
                            </li>
                            <li>
                                <hr class="dropdown-divider my-0">
                            </li>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <li class="dropdown-item text-center text-muted py-3">Tidak ada notifikasi</li>
                    <?php endif; ?>

                    <li class="text-center py-2">
                        <a href="notifications.php" class="dropdown-item text-primary fw-semibold">Lihat semua</a>
                    </li>
                </ul>
            </div>



            <!-- Profil -->
            <div>
                <a href="/project-semester-3-/admin/profil_saya.php" class="text-white">
                    <i class="bi bi-person-circle fs-4"></i>
                </a>
            </div>

        </div>
    </header>

    <!-- MAIN CONTENT -->
    <main class="main-content">
        <h1>Dashboard</h1>

        <div class="card-container">
            <div class="card-box">
                <div class="number"><?= $jumlah_pending; ?></div>
                <div class="label">Menunggu Verifikasi</div>
            </div>
            <div class="card-box">
                <div class="number"><?= $jumlah_diterima; ?></div>
                <div class="label">Diterima</div>
            </div>
        </div>

        <div class="graph-container">
            <h2>Grafik Pendaftaran</h2>
            <canvas id="myChart" height="100"></canvas>
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
        const tahun = <?= json_encode($tahun); ?>;
        const total = <?= json_encode($total); ?>;

        const ctx = document.getElementById('myChart').getContext('2d');
        new Chart(ctx, {
            type: 'line',
            data: {
                labels: tahun,
                datasets: [{
                    label: 'Jumlah Pendaftar',
                    data: total,
                    fill: true,
                    backgroundColor: 'rgba(0, 123, 255, 0.15)',
                    borderColor: '#007bff',
                    borderWidth: 2,
                    tension: 0.4,
                    pointBackgroundColor: '#007bff',
                    pointRadius: 4
                }]
            },
            options: {
                responsive: true,
                plugins: { legend: { display: false } },
                scales: { y: { beginAtZero: true, ticks: { stepSize: 1 } } }
            }
        });
    </script>
    <script>
        document.getElementById('notifDropdown').addEventListener('click', function () {

            // hilangkan badge dari UI langsung
            let badge = document.querySelector('#notifDropdown .badge');
            if (badge) badge.remove();

            // tandai semua read di database
            fetch('read_all_notifications.php');
        });
    </script>



</body>

</html>