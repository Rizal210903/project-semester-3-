<?php
session_start();
include '../includes/config.php';

// Ambil data guru dari database
$q = $conn->query("SELECT * FROM guru ORDER BY fullname ASC");
$teachers = $q->fetch_all(MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Rekap Absensi Guru - TK Pertiwi</title>

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

        /* Page Header */
        .page-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            padding: 30px;
            border-radius: 15px;
            color: white;
            margin-bottom: 30px;
            box-shadow: 0 5px 20px rgba(0, 123, 255, 0.4);
        }

        .page-header h1 {
            margin: 0;
            font-size: 28px;
            font-weight: 700;
        }

        .page-header p {
            margin: 5px 0 0 0;
            opacity: 0.9;
            font-size: 14px;
        }

        /* Filter Section */
        .filter-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .filter-title {
            font-size: 18px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .filter-row {
            display: flex;
            gap: 15px;
            align-items: end;
            flex-wrap: wrap;
        }

        .filter-item {
            flex: 1;
            min-width: 200px;
        }

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .stat-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            text-align: center;
            transition: all 0.3s;
            cursor: default;
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border-color: #007bff;
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .stat-hadir { color: #28a745; }
        .stat-terlambat { color: #ffc107; }
        .stat-izin { color: #0d6efd; }
        .stat-alfa { color: #6c757d; }

        /* Rekap Table */
        .rekap-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .rekap-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 25px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .rekap-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .action-buttons {
            display: flex;
            gap: 10px;
            flex-wrap: wrap;
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #dee2e6;
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
            padding: 10px;
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

        .nama-guru-col {
            text-align: left;
            font-weight: 600;
            min-width: 200px;
            position: sticky;
            left: 0;
            background: white;
            z-index: 5;
        }

        .nama-guru-col:hover {
            background: #e7f3ff;
        }

        .nama-guru-header {
            position: sticky;
            left: 0;
            z-index: 15 !important;
        }

        .status-cell {
            font-size: 11px;
            font-weight: 600;
            width: 40px;
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
            min-width: 60px;
        }

        .weekend {
            background-color: #ffe5e5 !important;
        }

        .today {
            background-color: #fff9c4 !important;
            font-weight: bold;
        }

        /* Legend */
        .legend {
            display: flex;
            gap: 20px;
            flex-wrap: wrap;
            margin: 25px 0;
            padding: 20px;
            background: #f8f9fa;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .legend-item {
            display: flex;
            align-items: center;
            gap: 10px;
            font-size: 13px;
            font-weight: 500;
        }

        .legend-color {
            width: 35px;
            height: 25px;
            border-radius: 5px;
            border: 1px solid #dee2e6;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 11px;
        }

        /* Loading */
        .loading-overlay {
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(255, 255, 255, 0.9);
            display: flex;
            align-items: center;
            justify-content: center;
            z-index: 9999;
            display: none;
        }

        .loading-content {
            text-align: center;
        }

        .spinner {
            border: 4px solid #f3f3f3;
            border-top: 4px solid #007bff;
            border-radius: 50%;
            width: 50px;
            height: 50px;
            animation: spin 1s linear infinite;
            margin: 0 auto 15px;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        /* Empty State */
        .empty-state {
            text-align: center;
            padding: 60px 20px;
            color: #999;
        }

        .empty-state i {
            font-size: 80px;
            opacity: 0.3;
            margin-bottom: 20px;
        }

        .empty-state h4 {
            color: #666;
            margin-bottom: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 90px 15px 30px;
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .filter-item {
                width: 100%;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .action-buttons {
                width: 100%;
            }
            
            .action-buttons button {
                flex: 1;
            }
        }

        @media print {
            .header, .filter-container, .action-buttons, .menu-toggle {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .rekap-container {
                box-shadow: none;
                border: 1px solid #000;
            }
            
            .page-header {
                background: #333 !important;
                -webkit-print-color-adjust: exact;
                print-color-adjust: exact;
            }
        }
    </style>
</head>

<body>

<!-- Loading Overlay -->
<div class="loading-overlay" id="loadingOverlay">
    <div class="loading-content">
        <div class="spinner"></div>
        <p>Memuat data rekap absensi...</p>
    </div>
</div>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">REKAP ABSENSI GURU</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-calendar2-month"></i> Rekap Absensi Guru</h1>
        <p>Laporan lengkap kehadiran guru per bulan</p>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <div class="filter-title">
            <i class="bi bi-funnel-fill"></i>
            Filter Periode
        </div>
        <div class="filter-row">
            <div class="filter-item">
                <label class="form-label mb-1 fw-bold">Bulan</label>
                <select id="bulanRekap" class="form-select">
                    <?php 
                    $nama_bulan = [
                        '01'=>'Januari', '02'=>'Februari', '03'=>'Maret', '04'=>'April',
                        '05'=>'Mei', '06'=>'Juni', '07'=>'Juli', '08'=>'Agustus',
                        '09'=>'September', '10'=>'Oktober', '11'=>'November', '12'=>'Desember'
                    ];
                    foreach ($nama_bulan as $key => $value): 
                    ?>
                        <option value="<?= $key ?>" <?= date('m') == $key ? 'selected' : '' ?>><?= $value ?></option>
                    <?php endforeach; ?>
                </select>
            </div>
            <div class="filter-item">
                <label class="form-label mb-1 fw-bold">Tahun</label>
                <select id="tahunRekap" class="form-select">
                    <?php for ($y = date('Y'); $y >= 2020; $y--): ?>
                        <option value="<?= $y ?>" <?= date('Y') == $y ? 'selected' : '' ?>><?= $y ?></option>
                    <?php endfor; ?>
                </select>
            </div>
            <div class="filter-item" style="flex: 0 0 auto;">
                <button class="btn btn-primary btn-lg" onclick="loadRekap()">
                    <i class="bi bi-search"></i> Tampilkan
                </button>
            </div>
        </div>
    </div>

    <!-- Stats Cards (TANPA SAKIT) -->
    <div class="stats-container" id="statsContainer">
        <div class="stat-card">
            <div class="stat-icon stat-hadir">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-number stat-hadir" id="statHadir">0</div>
            <div class="stat-label">Total Hadir</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-terlambat">
                <i class="bi bi-clock-fill"></i>
            </div>
            <div class="stat-number stat-terlambat" id="statTerlambat">0</div>
            <div class="stat-label">Total Terlambat</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-izin">
                <i class="bi bi-file-text-fill"></i>
            </div>
            <div class="stat-number stat-izin" id="statIzin">0</div>
            <div class="stat-label">Total Izin</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon stat-alfa">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-number stat-alfa" id="statAlfa">0</div>
            <div class="stat-label">Total Alfa</div>
        </div>
    </div>

    <!-- Rekap Table -->
    <div class="rekap-container">
        <div class="rekap-header">
            <div class="rekap-title">
                <i class="bi bi-table"></i>
                <span id="periodTitle">Rekap Bulan <?= $nama_bulan[date('m')] ?> <?= date('Y') ?></span>
            </div>
            <div class="action-buttons">
                <button class="btn btn-success" onclick="exportToExcel()">
                    <i class="bi bi-file-excel"></i> Export Excel
                </button>
                <button class="btn btn-info text-white" onclick="window.print()">
                    <i class="bi bi-printer"></i> Cetak
                </button>
            </div>
        </div>

        <!-- Legend (TANPA SAKIT) -->
        <div class="legend">
            <div class="legend-item">
                <div class="legend-color hadir">H</div>
                <span>Hadir</span>
            </div>
            <div class="legend-item">
                <div class="legend-color terlambat">T</div>
                <span>Terlambat</span>
            </div>
            <div class="legend-item">
                <div class="legend-color izin">I</div>
                <span>Izin</span>
            </div>
            <div class="legend-item">
                <div class="legend-color alfa">A</div>
                <span>Alfa</span>
            </div>
            <div class="legend-item">
                <div class="legend-color kosong">-</div>
                <span>Tidak Ada Data</span>
            </div>
            <div class="legend-item">
                <div class="legend-color weekend" style="width: 35px; height: 25px;"></div>
                <span>Akhir Pekan</span>
            </div>
        </div>

        <!-- Table Container -->
        <div class="table-wrapper">
            <div id="rekapTableContainer">
                <div class="empty-state">
                    <i class="bi bi-calendar2-month"></i>
                    <h4>Silakan pilih periode dan klik Tampilkan</h4>
                    <p>Data rekap absensi akan ditampilkan di sini</p>
                </div>
            </div>
        </div>
    </div>

</main>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/xlsx/0.18.5/xlsx.full.min.js"></script>
<script>
    // Sidebar Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    // Load rekap saat halaman dimuat
    window.addEventListener('load', function() {
        loadRekap();
    });

    // Load Rekap Data
    function loadRekap() {
        const bulan = document.getElementById('bulanRekap').value;
        const tahun = document.getElementById('tahunRekap').value;
        const loading = document.getElementById('loadingOverlay');
        const container = document.getElementById('rekapTableContainer');
        
        loading.style.display = 'flex';
        
        $.ajax({
            url: 'get_rekap_absen.php',
            method: 'GET',
            data: { bulan: bulan, tahun: tahun },
            dataType: 'json',
            success: function(response) {
                loading.style.display = 'none';
                
                if (response.status === 'success') {
                    // Update stats
                    updateStats(response.stats);
                    
                    // Update period title
                    const namaBulan = document.getElementById('bulanRekap').options[document.getElementById('bulanRekap').selectedIndex].text;
                    document.getElementById('periodTitle').textContent = `Rekap Bulan ${namaBulan} ${tahun}`;
                    
                    // Generate table
                    container.innerHTML = generateTableHTML(response.data, bulan, tahun);
                } else {
                    container.innerHTML = `
                        <div class="empty-state">
                            <i class="bi bi-exclamation-triangle"></i>
                            <h4>Data Tidak Ditemukan</h4>
                            <p>${response.message}</p>
                        </div>
                    `;
                }
            },
            error: function() {
                loading.style.display = 'none';
                container.innerHTML = `
                    <div class="empty-state">
                        <i class="bi bi-x-circle"></i>
                        <h4>Gagal Memuat Data</h4>
                        <p>Terjadi kesalahan saat mengambil data rekap</p>
                    </div>
                `;
            }
        });
    }

    // Update Statistics (TANPA SAKIT)
    function updateStats(stats) {
        const hadirEl = document.getElementById('statHadir');
        const terlambatEl = document.getElementById('statTerlambat');
        const izinEl = document.getElementById('statIzin');
        const alfaEl = document.getElementById('statAlfa');
        
        if (hadirEl) hadirEl.textContent = stats.hadir || 0;
        if (terlambatEl) terlambatEl.textContent = stats.terlambat || 0;
        if (izinEl) izinEl.textContent = stats.izin || 0;
        if (alfaEl) alfaEl.textContent = stats.alfa || 0;
    }

    // Generate Table HTML
    function generateTableHTML(data, bulan, tahun) {
        const jumlahHari = new Date(tahun, bulan, 0).getDate();
        const today = new Date().toISOString().split('T')[0];
        
        let html = '<table class="rekap-table" id="rekapTableExport"><thead><tr>';
        html += '<th class="nama-guru-header" rowspan="2">Nama Guru</th>';
        html += `<th colspan="${jumlahHari}">Tanggal</th>`;
        html += '<th colspan="5">Jumlah</th></tr><tr>';
        
        // Header tanggal dengan marker hari ini dan weekend
        for (let i = 1; i <= jumlahHari; i++) {
            const date = `${tahun}-${bulan}-${String(i).padStart(2, '0')}`;
            const dayOfWeek = new Date(date).getDay();
            const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
            const isToday = (date === today);
            
            let cssClass = '';
            let icon = '';
            
            if (isToday) {
                cssClass = 'today';
                icon = ' ⭐';
            } else if (isWeekend) {
                cssClass = 'weekend';
            }
            
            html += `<th class="${cssClass}" title="${getDayName(dayOfWeek)}">${i}${icon}</th>`;
        }
        
        // Header summary
        html += '<th class="summary-col">H</th>';
        html += '<th class="summary-col">T</th>';
        html += '<th class="summary-col">I</th>';
        html += '<th class="summary-col">S</th>';
        html += '<th class="summary-col">A</th>';
        html += '</tr></thead><tbody>';
        
        // Data guru
        if (data.length === 0) {
            html += '<tr><td colspan="' + (jumlahHari + 6) + '" class="text-center py-4">Tidak ada data guru</td></tr>';
        } else {
            data.forEach(guru => {
                html += '<tr>';
                html += `<td class="nama-guru-col">${guru.fullname}</td>`;
                
                // Status per hari
                for (let i = 1; i <= jumlahHari; i++) {
                    const tanggal = `${tahun}-${bulan}-${String(i).padStart(2, '0')}`;
                    const dayOfWeek = new Date(tanggal).getDay();
                    const isWeekend = (dayOfWeek === 0 || dayOfWeek === 6);
                    const isToday = (tanggal === today);
                    
                    const absen = guru.absensi[tanggal];
                    let label = '-';
                    let cssClass = 'kosong';
                    let title = 'Tidak ada data';
                    
                    if (absen) {
                        switch(absen.status) {
                            case 'Hadir': 
                                label = 'H'; 
                                cssClass = 'hadir'; 
                                title = 'Hadir';
                                break;
                            case 'Terlambat': 
                                label = 'T'; 
                                cssClass = 'terlambat'; 
                                title = 'Terlambat';
                                break;
                            case 'Izin': 
                                label = 'I'; 
                                cssClass = 'izin'; 
                                title = 'Izin';
                                break;
                            case 'Sakit': 
                                label = 'S'; 
                                cssClass = 'sakit'; 
                                title = 'Sakit';
                                break;
                            case 'Alfa': 
                                label = 'A'; 
                                cssClass = 'alfa'; 
                                title = 'Alfa';
                                break;
                        }
                        
                        if (absen.waktu_absen) {
                            title += ' (' + absen.waktu_absen.substring(0,5) + ')';
                        }
                        
                        if (absen.catatan) {
                            title += ' - ' + absen.catatan;
                        }
                    }
                    
                    const bgClass = isToday ? 'today' : (isWeekend ? 'weekend' : '');
                    html += `<td class="status-cell ${cssClass} ${bgClass}" title="${title}">${label}</td>`;
                }
                
                // Jumlah per status
                html += `<td class="summary-col">${guru.summary.hadir}</td>`;
                html += `<td class="summary-col">${guru.summary.terlambat}</td>`;
                html += `<td class="summary-col">${guru.summary.izin}</td>`;
                html += `<td class="summary-col">${guru.summary.sakit}</td>`;
                html += `<td class="summary-col">${guru.summary.alfa}</td>`;
                html += '</tr>';
            });
        }
        
        html += '</tbody></table>';
        return html;
    }

    // Get Day Name
    function getDayName(dayIndex) {
        const days = ['Minggu', 'Senin', 'Selasa', 'Rabu', 'Kamis', 'Jumat', 'Sabtu'];
        return days[dayIndex];
    }

    // Export to Excel
    function exportToExcel() {
        const table = document.getElementById('rekapTableExport');
        if (!table) {
            alert('Tidak ada data untuk diexport');
            return;
        }
        
        const bulan = document.getElementById('bulanRekap').options[document.getElementById('bulanRekap').selectedIndex].text;
        const tahun = document.getElementById('tahunRekap').value;
        
        const wb = XLSX.utils.table_to_book(table, {sheet: "Rekap Absensi"});
        XLSX.writeFile(wb, `Rekap_Absensi_Guru_${bulan}_${tahun}.xlsx`);
    }
</script>

</body>
</html>