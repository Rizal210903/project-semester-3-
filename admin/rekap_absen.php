<?php
session_start();
include '../includes/config.php';

// Ambil bulan dan tahun dari parameter atau default bulan ini
$bulan = isset($_GET['bulan']) ? $_GET['bulan'] : date('m');
$tahun = isset($_GET['tahun']) ? $_GET['tahun'] : date('Y');

// Ambil data guru
$q_guru = $conn->query("SELECT * FROM guru ORDER BY nama ASC");
$teachers = $q_guru->fetch_all(MYSQLI_ASSOC);

// Hitung jumlah hari dalam bulan
$jumlah_hari = cal_days_in_month(CAL_GREGORIAN, $bulan, $tahun);

// Ambil semua data absensi untuk bulan ini
$start_date = "$tahun-$bulan-01";
$end_date = "$tahun-$bulan-$jumlah_hari";

$q_absen = $conn->query("
    SELECT * FROM absensi_guru 
    WHERE tanggal BETWEEN '$start_date' AND '$end_date'
    ORDER BY tanggal ASC
");

$absensi_data = [];
if ($q_absen) {
    while ($row = $q_absen->fetch_assoc()) {
        $absensi_data[$row['guru_id']][$row['tanggal']] = $row;
    }
}

// Nama bulan dalam Indonesia
$nama_bulan = [
    '01' => 'Januari', '02' => 'Februari', '03' => 'Maret', '04' => 'April',
    '05' => 'Mei', '06' => 'Juni', '07' => 'Juli', '08' => 'Agustus',
    '09' => 'September', '10' => 'Oktober', '11' => 'November', '12' => 'Desember'
];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Bulanan - TK Pertiwi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        body {
            margin: 0;
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
        }
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
        .menu-toggle { font-size: 24px; cursor: pointer; margin-right: 20px; }
        .main-content { 
            margin-left: 250px; 
            padding: 90px 30px 30px; 
            transition: 0.3s; 
        }
        .main-content.collapsed { margin-left: 60px; }

        .filter-section {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .rekap-table-container {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            overflow-x: auto;
        }

        .rekap-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 12px;
            min-width: 1200px;
        }

        .rekap-table th,
        .rekap-table td {
            border: 1px solid #dee2e6;
            padding: 8px;
            text-align: center;
        }

        .rekap-table thead th {
            background: #007bff;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .rekap-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .rekap-table tbody tr:hover {
            background-color: #e7f3ff;
        }

        .nama-guru {
            text-align: left;
            font-weight: 600;
            min-width: 180px;
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
        }

        .nama-guru-header {
            position: sticky;
            left: 0;
            z-index: 15 !important;
        }

        .status-cell {
            font-size: 11px;
            font-weight: 600;
            width: 35px;
        }

        .hadir { background-color: #d4edda; color: #155724; }
        .terlambat { background-color: #fff3cd; color: #856404; }
        .izin { background-color: #cfe2ff; color: #084298; }
        .sakit { background-color: #f8d7da; color: #721c24; }
        .alfa { background-color: #d6d8db; color: #383d41; }
        .kosong { background-color: #f8f9fa; color: #999; }

        .summary-col {
            background-color: #e7f3ff;
            font-weight: 700;
            min-width: 50px;
        }

        .legend {
            display: flex;
            gap: 15px;
            flex-wrap: wrap;
            margin-bottom: 20px;
            padding: 15px;
            background: #f8f9fa;
            border-radius: 8px;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 13px;
        }

        .legend-color {
            width: 30px;
            height: 20px;
            border-radius: 4px;
            border: 1px solid #dee2e6;
        }

        .stats-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #d6eaff;
            text-align: center;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }

        .stat-number {
            font-size: 28px;
            font-weight: bold;
            color: #007bff;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }

        .print-button {
            position: fixed;
            bottom: 30px;
            right: 30px;
            z-index: 1000;
        }

        @media print {
            .header, .sidebar, .filter-section, .print-button, .btn {
                display: none !important;
            }
            .main-content {
                margin-left: 0 !important;
                padding: 20px !important;
            }
            body {
                background: white;
            }
            .rekap-table {
                font-size: 10px;
            }
        }

        .weekend {
            background-color: #ffe5e5 !important;
        }

        .today {
            background-color: #fff9c4 !important;
            font-weight: bold;
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">REKAP ABSENSI BULANAN GURU</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1>Rekap Absensi Guru - <?= $nama_bulan[$bulan] ?> <?= $tahun ?></h1>
        <a href="manage_teachers.php" class="btn btn-secondary">
            <i class="bi bi-arrow-left"></i> Kembali
        </a>
    </div>

    <!-- Filter Bulan & Tahun -->
    <div class="filter-section">
        <div class="row align-items-end">
            <div class="col-md-3">
                <label class="form-label fw-bold">Bulan</label>
                <select id="bulanFilter" class="form-select">
                    <?php foreach ($nama_bulan as $key => $value): ?>
                        <option value="<?= $key ?>" <?= $bulan == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="col-md-3">
                <label class="form-label fw-bold">Tahun</label>
                <select id="tahunFilter" class="form-select">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= $tahun == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="col-md-3">
                <button class="btn btn-primary w-100" onclick="applyFilter()">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
            <div class="col-md-3">
                <button class="btn btn-success w-100" onclick="exportToExcel()">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
            </div>
        </div>
    </div>

    <?php
    // Hitung statistik
    $total_hadir = 0;
    $total_terlambat = 0;
    $total_izin = 0;
    $total_sakit = 0;
    $total_alfa = 0;

    foreach ($teachers as $guru) {
        if (isset($absensi_data[$guru['id']])) {
            foreach ($absensi_data[$guru['id']] as $tanggal => $data) {
                switch ($data['status']) {
                    case 'Hadir': $total_hadir++; break;
                    case 'Terlambat': $total_terlambat++; break;
                    case 'Izin': $total_izin++; break;
                    case 'Sakit': $total_sakit++; break;
                    case 'Alfa': $total_alfa++; break;
                }
            }
        }
    }
    ?>

    <!-- Statistik -->
    <div class="stats-cards">
        <div class="stat-card">
            <div class="stat-number" style="color: #28a745;"><?= $total_hadir ?></div>
            <div class="stat-label">Total Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #ffc107;"><?= $total_terlambat ?></div>
            <div class="stat-label">Total Terlambat</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #0d6efd;"><?= $total_izin ?></div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #dc3545;"><?= $total_sakit ?></div>
            <div class="stat-label">Total Sakit</div>
        </div>
        <div class="stat-card">
            <div class="stat-number" style="color: #6c757d;"><?= $total_alfa ?></div>
            <div class="stat-label">Total Alfa</div>
        </div>
    </div>

    <!-- Legend -->
    <div class="legend">
        <div class="legend-item">
            <div class="legend-color hadir"></div>
            <span>H = Hadir</span>
        </div>
        <div class="legend-item">
            <div class="legend-color terlambat"></div>
            <span>T = Terlambat</span>
        </div>
        <div class="legend-item">
            <div class="legend-color izin"></div>
            <span>I = Izin</span>
        </div>
        <div class="legend-item">
            <div class="legend-color sakit"></div>
            <span>S = Sakit</span>
        </div>
        <div class="legend-item">
            <div class="legend-color alfa"></div>
            <span>A = Alfa</span>
        </div>
        <div class="legend-item">
            <div class="legend-color kosong"></div>
            <span>- = Tidak Ada Data</span>
        </div>
    </div>

    <!-- Tabel Rekap -->
    <div class="rekap-table-container">
        <table class="rekap-table" id="rekapTable">
            <thead>
                <tr>
                    <th class="nama-guru-header" rowspan="2">Nama Guru</th>
                    <th colspan="<?= $jumlah_hari ?>">Tanggal</th>
                    <th colspan="5">Jumlah</th>
                </tr>
                <tr>
                    <?php for ($i = 1; $i <= $jumlah_hari; $i++): 
                        $date = "$tahun-$bulan-" . str_pad($i, 2, '0', STR_PAD_LEFT);
                        $dayOfWeek = date('w', strtotime($date));
                        $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                        $isToday = ($date == date('Y-m-d'));
                        $class = $isToday ? 'today' : ($isWeekend ? 'weekend' : '');
                    ?>
                        <th class="<?= $class ?>"><?= $i ?></th>
                    <?php endfor; ?>
                    <th class="summary-col">H</th>
                    <th class="summary-col">T</th>
                    <th class="summary-col">I</th>
                    <th class="summary-col">S</th>
                    <th class="summary-col">A</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($teachers as $guru): 
                    // Hitung jumlah per status untuk guru ini
                    $h = $t = $i = $s = $a = 0;
                ?>
                    <tr>
                        <td class="nama-guru"><?= htmlspecialchars($guru['nama'] ?? '') ?></td>
                    
                        
                        <?php for ($d = 1; $d <= $jumlah_hari; $d++): 
                            $tanggal = "$tahun-$bulan-" . str_pad($d, 2, '0', STR_PAD_LEFT);
                            $dayOfWeek = date('w', strtotime($tanggal));
                            $isWeekend = ($dayOfWeek == 0 || $dayOfWeek == 6);
                            $isToday = ($tanggal == date('Y-m-d'));
                            
                            if (isset($absensi_data[$guru['id']][$tanggal])) {
                                $status = $absensi_data[$guru['id']][$tanggal]['status'];
                                $waktu = $absensi_data[$guru['id']][$tanggal]['waktu_absen'];
                                
                                // Hitung
                                switch ($status) {
                                    case 'Hadir': $h++; $label = 'H'; break;
                                    case 'Terlambat': $t++; $label = 'T'; break;
                                    case 'Izin': $i++; $label = 'I'; break;
                                    case 'Sakit': $s++; $label = 'S'; break;
                                    case 'Alfa': $a++; $label = 'A'; break;
                                    default: $label = '-';
                                }
                                
                                $cellClass = strtolower($status);
                                $title = $status . ($waktu ? ' (' . substr($waktu, 0, 5) . ')' : '');
                            } else {
                                $label = '-';
                                $cellClass = 'kosong';
                                $title = 'Tidak ada data';
                            }
                            
                            $bgClass = $isToday ? 'today' : ($isWeekend ? 'weekend' : '');
                        ?>
                            <td class="status-cell <?= $cellClass ?> <?= $bgClass ?>" title="<?= $title ?>">
                                <?= $label ?>
                            </td>
                        <?php endfor; ?>
                        
                        <td class="summary-col"><?= $h ?></td>
                        <td class="summary-col"><?= $t ?></td>
                        <td class="summary-col"><?= $i ?></td>
                        <td class="summary-col"><?= $s ?></td>
                        <td class="summary-col"><?= $a ?></td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>

    <!-- Print Button -->
    <button class="btn btn-primary btn-lg print-button" onclick="window.print()">
        <i class="bi bi-printer"></i> Cetak
    </button>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    function applyFilter() {
        const bulan = document.getElementById('bulanFilter').value;
        const tahun = document.getElementById('tahunFilter').value;
        window.location.href = `?bulan=${bulan}&tahun=${tahun}`;
    }

    function exportToExcel() {
        const table = document.getElementById('rekapTable');
        const wb = XLSX.utils.table_to_book(table, {sheet: "Rekap Absensi"});
        const bulan = document.getElementById('bulanFilter').options[document.getElementById('bulanFilter').selectedIndex].text;
        const tahun = document.getElementById('tahunFilter').value;
        XLSX.writeFile(wb, `Rekap_Absensi_${bulan}_${tahun}.xlsx`);
    }
</script>

</body>
</html>