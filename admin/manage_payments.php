<?php
// === KONEKSI DATABASE ===
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// === AMBIL DATA PEMBAYARAN ===
// (Sementara statis, nanti bisa diubah sesuai tabel pembayaran di database)
$payments = [
    ["nama" => "Aisyah Putri", "pembayaran" => "Biaya Pendaftaran", "tanggal" => "25/09/2025", "total" => "Rp 500.000", "status" => "Lunas"],
    ["nama" => "Aisyah Putri", "pembayaran" => "Iuran Sekolah", "tanggal" => "25/09/2025", "total" => "Rp 200.000", "status" => "Lunas"],
    ["nama" => "Aisyah Putri", "pembayaran" => "Biaya Pendaftaran", "tanggal" => "25/09/2025", "total" => "Rp 500.000", "status" => "Pending"],
    ["nama" => "Aisyah Putri", "pembayaran" => "Biaya Pendaftaran", "tanggal" => "25/09/2025", "total" => "Rp 500.000", "status" => "Lunas"],
    ["nama" => "Aisyah Putri", "pembayaran" => "Iuran Sekolah", "tanggal" => "25/09/2025", "total" => "Rp 200.000", "status" => "Lunas"],
    ["nama" => "Aisyah Putri", "pembayaran" => "Biaya Pendaftaran", "tanggal" => "25/09/2025", "total" => "Rp 500.000", "status" => "Pending"],
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Pembayaran - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
        }

        /* Header */
        .header {
            position: fixed;
            top: 0; left: 0; right: 0;
            height: 60px;
            background-color: #007bff;
            display: flex;
            align-items: center;
            padding: 0 20px;
            color: #fff;
            z-index: 1000;
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

        /* Main Layout */
        .main-content {
            margin-left: 250px;
            padding: 90px 30px 30px;
            transition: margin-left 0.3s;
        }
        .main-content.collapsed {
            margin-left: 60px;
        }

        /* Box */
        .content-box {
            background-color: #dff4ff;
            border-radius: 10px;
            padding: 20px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
        }

        /* Filter section */
        .filters {
            display: flex;
            flex-wrap: wrap;
            gap: 10px;
            margin-bottom: 15px;
        }

        .filter {
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            min-width: 180px;
            cursor: pointer;
            font-size: 14px;
        }

        .search-box {
            flex: 1;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 10px;
            font-size: 14px;
        }

        /* Table */
        .table-container {
            background-color: #fff;
            border-radius: 10px;
            overflow-x: auto;
            padding: 10px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            min-width: 800px;
        }

        thead {
            background-color: #f5faff;
        }

        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #eee;
            font-size: 14px;
        }

        tr:nth-child(even) {
            background-color: #f8f8f8;
        }

        th {
            color: #333;
            font-weight: 600;
        }

        /* Buttons */
        .button-group {
            display: flex;
            gap: 15px;
            margin-top: 25px;
            justify-content: center;
        }

        .btn-download {
            border: none;
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            transition: 0.3s;
        }

        .btn-pdf {
            background-color: #007bff;
            color: white;
        }

        .btn-pdf:hover {
            background-color: #005ec9;
        }

        .btn-csv {
            background-color: #cce5ff;
            color: #007bff;
        }

        .btn-csv:hover {
            background-color: #b3daff;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 80px 15px;
            }

            .filters {
                flex-direction: column;
            }

            .button-group {
                flex-direction: column;
                align-items: stretch;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <div class="header">
        <div class="logo d-flex align-items-center">
            <i class="bi bi-list me-3 fs-4"></i> KELOLA PEMBAYARAN
        </div>
        <div class="icons">
            <i class="bi bi-bell"></i>
            <i class="bi bi-envelope"></i>
            <i class="bi bi-person-circle"></i>
        </div>
    </div>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="mb-4">Tabel Pembayaran</h1>

        <div class="content-box">

            <!-- Filter Bar -->
            <div class="filters">
                <div class="filter">Filter Periode <i class="bi bi-chevron-down"></i></div>
                <div class="filter">Filter Jenis Pembayaran <i class="bi bi-chevron-down"></i></div>
                <div class="filter">Filter Status <i class="bi bi-chevron-down"></i></div>
                <input type="text" class="search-box" placeholder="Search">
            </div>

            <!-- Table -->
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Nama</th>
                            <th>Pembayaran</th>
                            <th>Tanggal</th>
                            <th>Total</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($payments as $index => $p): ?>
                        <tr>
                            <td><?= $index + 1; ?></td>
                            <td><?= htmlspecialchars($p['nama']); ?></td>
                            <td><?= htmlspecialchars($p['pembayaran']); ?></td>
                            <td><?= htmlspecialchars($p['tanggal']); ?></td>
                            <td><?= htmlspecialchars($p['total']); ?></td>
                            <td><?= htmlspecialchars($p['status']); ?></td>
                        </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>

            <!-- Buttons -->
            <div class="button-group">
                <button class="btn-download btn-pdf"><i class="bi bi-file-earmark-pdf"></i> Unduh PDF</button>
                <button class="btn-download btn-csv"><i class="bi bi-file-earmark-spreadsheet"></i> Unduh CSV</button>
            </div>
        </div>
    </main>

        <script>
        // Sidebar toggle
        const menuToggle = document.querySelector('.menu-toggle');
        const sidebar = document.querySelector('.sidebar');
        const mainContent = document.querySelector('.main-content');
        menuToggle.addEventListener('click', () => {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('collapsed');
        });
    </script>

</body>
</html>
