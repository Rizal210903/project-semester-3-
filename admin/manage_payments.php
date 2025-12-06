<?php
session_start();
include '../includes/config.php';
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

        /* Stats Cards */
        .stats-container {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
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
            cursor: pointer;
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

        .stat-belum { color: #ffc107; }
        .stat-menunggu { color: #17a2b8; }
        .stat-dibayar { color: #28a745; }

        /* Table Container */
        .table-container {
            background: white;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .table-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            flex-wrap: wrap;
            gap: 15px;
        }

        .table-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            display: flex;
            align-items: center;
            gap: 10px;
            flex: 1;
        }

        .search-box {
            position: relative;
            max-width: 300px;
        }

        .search-box input {
            padding-left: 40px;
        }

        .search-box i {
            position: absolute;
            left: 15px;
            top: 50%;
            transform: translateY(-50%);
            color: #999;
        }

        /* Table Styles */
        .table-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .payment-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }

        .payment-table th,
        .payment-table td {
            border: 1px solid #dee2e6;
            padding: 15px;
            text-align: center;
        }

        .payment-table thead th {
            background: #007bff;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .payment-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .payment-table tbody tr:hover {
            background-color: #e7f3ff;
        }

        .payment-table td {
            vertical-align: middle;
        }

        /* Image Styles */
        .bukti-img {
            width: 80px;
            height: 60px;
            object-fit: cover;
            border-radius: 8px;
            cursor: pointer;
            transition: all 0.3s;
            border: 2px solid #dee2e6;
        }

        .bukti-img:hover {
            transform: scale(1.1);
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        .img-error {
            width: 80px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            background: #f8d7da;
            border-radius: 8px;
            color: #721c24;
            font-size: 10px;
            text-align: center;
            padding: 5px;
        }

        /* Status Badges */
        .badge-status {
            display: inline-block;
            padding: 8px 16px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            text-transform: capitalize;
        }

        .status-belum_bayar {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-menunggu_verifikasi {
            background-color: #d1ecf1;
            color: #0c5460;
        }

        .status-dibayar {
            background-color: #d4edda;
            color: #155724;
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

        /* Modal Styles */
        .modal-content {
            border-radius: 15px;
            overflow: hidden;
        }

        .modal-header {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            color: white;
            border: none;
        }

        .modal-img {
            width: 100%;
            border-radius: 0;
            max-height: 80vh;
            object-fit: contain;
        }

        /* Auto Refresh Indicator */
        .refresh-indicator {
            display: flex;
            align-items: center;
            gap: 8px;
            padding: 8px 15px;
            background: #e7f3ff;
            border-radius: 20px;
            font-size: 12px;
            color: #0056b3;
        }

        .refresh-dot {
            width: 8px;
            height: 8px;
            background: #28a745;
            border-radius: 50%;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.3; }
        }

        /* Debug Info */
        .debug-info {
            background: #fff3cd;
            border: 1px solid #ffc107;
            padding: 10px;
            border-radius: 8px;
            margin-bottom: 20px;
            font-size: 12px;
            display: none; /* Set to 'block' untuk debugging */
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 90px 15px 30px;
            }
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .search-box {
                max-width: 100%;
            }
        }

        @media print {
            .header, .filter-container, .stats-container, .menu-toggle, .search-box {
                display: none !important;
            }
            
            .main-content {
                margin-left: 0;
                padding: 20px;
            }
            
            .table-container {
                box-shadow: none;
                border: 1px solid #000;
            }
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">KELOLA PEMBAYARAN SISWA</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-wallet2"></i> Kelola Pembayaran</h1>
        <p>Manajemen pembayaran pendaftaran siswa baru</p>
    </div>

    <?php
    // Hitung statistik pembayaran
    $stats = [
        'belum_bayar' => 0,
        'menunggu_verifikasi' => 0,
        'dibayar' => 0,
        'total' => 0
    ];

    $result_stats = $conn->query("SELECT status_pembayaran, COUNT(*) as jumlah FROM payments GROUP BY status_pembayaran");
    if ($result_stats) {
        while ($row = $result_stats->fetch_assoc()) {
            $stats[$row['status_pembayaran']] = $row['jumlah'];
            $stats['total'] += $row['jumlah'];
        }
    }
    ?>

    <!-- Debug Info (uncomment untuk debugging) -->
    <!-- <div class="debug-info">
        <strong>Debug Info:</strong><br>
        Current File: <?php echo __FILE__; ?><br>
        Document Root: <?php echo $_SERVER['DOCUMENT_ROOT']; ?><br>
        Upload Path: ../uploads/pembayaran/
    </div> -->

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card" onclick="filterByStatus('belum_bayar')">
            <div class="stat-icon stat-belum">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-number stat-belum"><?= $stats['belum_bayar']; ?></div>
            <div class="stat-label">Belum Bayar</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('menunggu_verifikasi')">
            <div class="stat-icon stat-menunggu">
                <i class="bi bi-clock-history"></i>
            </div>
            <div class="stat-number stat-menunggu"><?= $stats['menunggu_verifikasi']; ?></div>
            <div class="stat-label">Menunggu Verifikasi</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('dibayar')">
            <div class="stat-icon stat-dibayar">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-number stat-dibayar"><?= $stats['dibayar']; ?></div>
            <div class="stat-label">Sudah Dibayar</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('all')">
            <div class="stat-icon" style="color: #007bff;">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="stat-number" style="color: #007bff;"><?= $stats['total']; ?></div>
            <div class="stat-label">Total Pembayaran</div>
        </div>
    </div>

    <!-- Filter Section -->
    <div class="filter-container">
        <div class="filter-title">
            <i class="bi bi-funnel-fill"></i>
            Filter & Pencarian
        </div>
        <div class="filter-row">
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label mb-1 fw-bold">Status Pembayaran</label>
                <select id="statusFilter" class="form-select" onchange="filterTable()">
                    <option value="all">Semua Status</option>
                    <option value="belum_bayar">Belum Bayar</option>
                    <option value="menunggu_verifikasi">Menunggu Verifikasi</option>
                    <option value="dibayar">Sudah Dibayar</option>
                </select>
            </div>
            <div style="flex: 1; min-width: 200px;">
                <label class="form-label mb-1 fw-bold">Metode Pembayaran</label>
                <select id="metodeFilter" class="form-select" onchange="filterTable()">
                    <option value="all">Semua Metode</option>
                    <option value="Transfer Bank">Transfer Bank</option>
                    <option value="E-Wallet">E-Wallet</option>
                    <option value="Cash">Cash</option>
                </select>
            </div>
            <div class="search-box" style="flex: 1; min-width: 250px;">
                <label class="form-label mb-1 fw-bold">Cari Data</label>
                <i class="bi bi-search"></i>
                <input type="text" id="searchInput" class="form-control" placeholder="Cari nama anak/orang tua..." onkeyup="filterTable()">
            </div>
        </div>
    </div>

    <!-- Table Container -->
    <div class="table-container">
        <div class="table-header">
            <div class="table-title">
                <i class="bi bi-table"></i>
                <span>Data Pembayaran</span>
            </div>
            <div class="refresh-indicator">
                <div class="refresh-dot"></div>
                <span>Auto refresh aktif</span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="payment-table" id="paymentTable">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Anak</th>
                        <th>Nama Orang Tua</th>
                        <th>Metode Pembayaran</th>
                        <th width="130">Tanggal Pembayaran</th>
                        <th width="120">Bukti Pembayaran</th>
                        <th width="150">Status</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php
                    $result = $conn->query("SELECT * FROM payments ORDER BY tanggal_bayar DESC"); 
                    if ($result && $result->num_rows > 0):
                        $no = 1;
                        while ($row = $result->fetch_assoc()):
                            $statusClass = "status-" . $row['status_pembayaran'];
                            
                            // PERBAIKAN PATH GAMBAR
                            // Sesuaikan dengan struktur folder: uploads/pembayaran/
                            $image_src = '';
                            if (!empty($row['bukti_path'])) {
                                // Hapus '../uploads/pembayaran/' jika sudah ada di database
                                $filename = basename($row['bukti_path']);
                                $image_src = '../uploads/pembayaran/' . $filename;
                                
                                // Alternative: jika di database sudah full path
                                // $image_src = $row['bukti_path'];
                            }
                    ?>
                    <tr data-status="<?= $row['status_pembayaran']; ?>" data-metode="<?= htmlspecialchars($row['metode_pembayaran']); ?>">
                        <td><?= $no++; ?></td>
                        <td style="text-align: left; font-weight: 600;"><?= htmlspecialchars($row['nama_anak']); ?></td>
                        <td style="text-align: left;"><?= htmlspecialchars($row['nama_ortu']); ?></td>
                        <td>
                            <?php if ($row['metode_pembayaran']): ?>
                                <span class="badge bg-info text-white">
                                    <i class="bi bi-credit-card"></i> <?= htmlspecialchars($row['metode_pembayaran']); ?>
                                </span>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if ($row['tanggal_bayar']): ?>
                                <i class="bi bi-calendar-event text-primary"></i>
                                <?= date('d M Y', strtotime($row['tanggal_bayar'])); ?>
                            <?php else: ?>
                                <span class="text-muted">-</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <?php if (!empty($image_src) && file_exists($image_src)): ?>
                                <img src="<?= htmlspecialchars($image_src); ?>" 
                                    alt="Bukti Pembayaran" 
                                    class="bukti-img"
                                    onclick="showModal('<?= htmlspecialchars($image_src); ?>', '<?= htmlspecialchars($row['nama_anak']); ?>')"
                                    onerror="this.parentElement.innerHTML='<div class=\'img-error\'>Gambar tidak ditemukan</div>'">
                            <?php elseif (!empty($row['bukti_path'])): ?>
                                <div class="img-error">
                                    <small>File tidak ditemukan<br><?= htmlspecialchars(basename($row['bukti_path'])); ?></small>
                                </div>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum upload</span>
                            <?php endif; ?>
                        </td>
                        <td>
                            <span class="badge-status <?= $statusClass; ?>">
                                <?php
                                $statusText = str_replace('_', ' ', $row['status_pembayaran']);
                                echo ucwords($statusText);
                                ?>
                            </span>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr id="emptyRow">
                        <td colspan="7">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h4>Belum Ada Data Pembayaran</h4>
                                <p>Data pembayaran akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<!-- Modal Bukti Pembayaran -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">
                    <i class="bi bi-image"></i> Bukti Pembayaran - <span id="modalNama"></span>
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="modal-img" alt="Bukti Pembayaran">
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script>
    // Sidebar Toggle
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    // Show Modal
    function showModal(src, nama) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalNama').textContent = nama;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }

    // Filter by Status (from stat cards)
    function filterByStatus(status) {
        document.getElementById('statusFilter').value = status;
        filterTable();
    }

    // Filter Table
    function filterTable() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const metodeFilter = document.getElementById('metodeFilter').value.toLowerCase();
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('paymentTable');
        const rows = table.getElementsByTagName('tr');
        
        let visibleCount = 0;
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            
            // Skip empty row
            if (row.id === 'emptyRow') continue;
            
            const status = row.getAttribute('data-status');
            const metode = row.getAttribute('data-metode');
            const namaAnak = row.cells[1].textContent.toLowerCase();
            const namaOrtu = row.cells[2].textContent.toLowerCase();
            
            let showRow = true;
            
            // Filter by status
            if (statusFilter !== 'all' && status !== statusFilter) {
                showRow = false;
            }
            
            // Filter by metode
            if (metodeFilter !== 'all' && metode !== metodeFilter) {
                showRow = false;
            }
            
            // Filter by search
            if (searchInput && !namaAnak.includes(searchInput) && !namaOrtu.includes(searchInput)) {
                showRow = false;
            }
            
            row.style.display = showRow ? '' : 'none';
            
            if (showRow) {
                visibleCount++;
                // Update numbering
                row.cells[0].textContent = visibleCount;
            }
        }
        
        // Show/hide empty state
        const emptyRow = document.getElementById('emptyRow');
        if (emptyRow) {
            emptyRow.style.display = visibleCount === 0 ? '' : 'none';
        }
    }

    // Auto refresh every 30 seconds (optional)
    // setInterval(() => location.reload(), 30000);
</script>

</body>
</html>