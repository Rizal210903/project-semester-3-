<?php
session_start();
include '../includes/config.php';

// Ambil data pendaftar
$result = $conn->query("SELECT * FROM pendaftaran ORDER BY tanggal_daftar DESC");

// Hitung statistik
$stats = [
    'pending' => 0,
    'diterima' => 0,
    'ditolak' => 0,
    'total' => 0
];

$result_stats = $conn->query("SELECT status_ppdb, COUNT(*) as jumlah FROM pendaftaran GROUP BY status_ppdb");
if ($result_stats) {
    while ($row = $result_stats->fetch_assoc()) {
        $stats[$row['status_ppdb']] = $row['jumlah'];
        $stats['total'] += $row['jumlah'];
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola PPDB - TK Pertiwi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

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

        .stat-pending { color: #ffc107; }
        .stat-diterima { color: #28a745; }
        .stat-ditolak { color: #dc3545; }

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

        .search-box {
            position: relative;
            flex: 1;
            min-width: 250px;
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
        }

        .table-wrapper {
            overflow-x: auto;
            border-radius: 10px;
            border: 1px solid #dee2e6;
        }

        .ppdb-table {
            width: 100%;
            border-collapse: collapse;
            font-size: 13px;
        }

        .ppdb-table th,
        .ppdb-table td {
            border: 1px solid #dee2e6;
            padding: 12px;
            text-align: center;
        }

        .ppdb-table thead th {
            background: #007bff;
            color: white;
            font-weight: 600;
            position: sticky;
            top: 0;
            z-index: 10;
        }

        .ppdb-table tbody tr:nth-child(even) {
            background-color: #f8f9fa;
        }

        .ppdb-table tbody tr:hover {
            background-color: #e7f3ff;
        }

        .ppdb-table td {
            vertical-align: middle;
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

        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }

        .status-diterima {
            background-color: #d4edda;
            color: #155724;
        }

        .status-ditolak {
            background-color: #f8d7da;
            color: #721c24;
        }

        /* Action Form */
        .action-form {
            display: flex;
            flex-direction: column;
            gap: 8px;
        }

        .action-form select {
            font-size: 12px;
            padding: 6px 10px;
            border-radius: 6px;
            border: 2px solid #e0e0e0;
        }

        .action-form select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .btn-action {
            font-size: 12px;
            padding: 8px 15px;
            border-radius: 8px;
            font-weight: 600;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            color: white;
            transition: all 0.3s;
        }

        .btn-action:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,123,255,0.3);
        }

        /* Document Button */
        .btn-doc {
            font-size: 11px;
            padding: 6px 12px;
            border-radius: 6px;
            transition: all 0.3s;
        }

        .btn-doc:hover {
            transform: scale(1.05);
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
            
            .stats-container {
                grid-template-columns: repeat(2, 1fr);
            }
            
            .filter-row {
                flex-direction: column;
            }
            
            .search-box {
                width: 100%;
            }
        }

        @media print {
            .header, .filter-container, .stats-container, .menu-toggle, .action-form {
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
    <div class="title">KELOLA PPDB (PENERIMAAN PESERTA DIDIK BARU)</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-people-fill"></i> Kelola PPDB</h1>
        <p>Manajemen pendaftaran dan penerimaan peserta didik baru</p>
    </div>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card" onclick="filterByStatus('pending')">
            <div class="stat-icon stat-pending">
                <i class="bi bi-hourglass-split"></i>
            </div>
            <div class="stat-number stat-pending"><?= $stats['pending']; ?></div>
            <div class="stat-label">Pending</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('diterima')">
            <div class="stat-icon stat-diterima">
                <i class="bi bi-check-circle-fill"></i>
            </div>
            <div class="stat-number stat-diterima"><?= $stats['diterima']; ?></div>
            <div class="stat-label">Diterima</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('ditolak')">
            <div class="stat-icon stat-ditolak">
                <i class="bi bi-x-circle-fill"></i>
            </div>
            <div class="stat-number stat-ditolak"><?= $stats['ditolak']; ?></div>
            <div class="stat-label">Ditolak</div>
        </div>
        <div class="stat-card" onclick="filterByStatus('all')">
            <div class="stat-icon" style="color: #007bff;">
                <i class="bi bi-clipboard-data"></i>
            </div>
            <div class="stat-number" style="color: #007bff;"><?= $stats['total']; ?></div>
            <div class="stat-label">Total Pendaftar</div>
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
                <label class="form-label mb-1 fw-bold">Status PPDB</label>
                <select id="statusFilter" class="form-select" onchange="filterTable()">
                    <option value="all">Semua Status</option>
                    <option value="pending">Pending</option>
                    <option value="diterima">Diterima</option>
                    <option value="ditolak">Ditolak</option>
                </select>
            </div>
            <div class="search-box">
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
                <span>Data Pendaftar</span>
            </div>
        </div>

        <div class="table-wrapper">
            <table class="ppdb-table" id="ppdbTable">
                <thead>
                    <tr>
                        <th width="50">No</th>
                        <th>Nama Anak</th>
                        <th>Nama Orang Tua</th>
                        <th width="120">Tanggal Daftar</th>
                        <th width="100">Kartu Keluarga</th>
                        <th width="100">Akta Kelahiran</th>
                        <th width="100">Pas Foto</th>
                        <th width="100">Surat Sehat</th>
                        <th width="120">Status</th>
                        <th width="150">Aksi</th>
                    </tr>
                </thead>
                <tbody id="tableBody">
                    <?php if ($result->num_rows > 0): 
                        $no = 1;
                        while ($row = $result->fetch_assoc()): 
                    ?>
                    <tr id="row-<?= $row['id']; ?>" data-status="<?= $row['status_ppdb']; ?>">
                        <td><?= $no++; ?></td>
                        <td style="text-align: left; font-weight: 600;">
                            <?= htmlspecialchars($row['nama_anak']); ?>
                        </td>
                        <td style="text-align: left;">
                            <?= htmlspecialchars($row['nama_ortu']); ?>
                        </td>
                        <td>
                            <i class="bi bi-calendar-event text-primary"></i>
                            <?= date('d M Y', strtotime($row['tanggal_daftar'])); ?>
                        </td>

                        <!-- Dokumen -->
                        <?php foreach(['kartu_keluarga','akta_kelahiran','pas_foto','surat_sehat'] as $dok): ?>
                        <td>
                            <?php if ($row[$dok]): ?>
                                <a href="../uploads/<?= $row[$dok]; ?>" target="_blank" class="btn btn-outline-primary btn-sm btn-doc">
                                    <i class="bi bi-eye"></i> Lihat
                                </a>
                            <?php else: ?>
                                <span class="badge bg-secondary">Belum ada</span>
                            <?php endif; ?>
                        </td>
                        <?php endforeach; ?>

                        <!-- Status PPDB -->
                        <td class="status-td">
                            <span class="badge-status status-<?= $row['status_ppdb']; ?>">
                                <?= ucfirst($row['status_ppdb']); ?>
                            </span>
                        </td>

                        <!-- Aksi -->
                        <td>
                            <form class="ubahStatusForm action-form" data-id="<?= $row['id']; ?>">
                                <select name="status" class="form-select">
                                    <option value="pending" <?= $row['status_ppdb']=='pending'?'selected':''; ?>>Pending</option>
                                    <option value="diterima" <?= $row['status_ppdb']=='diterima'?'selected':''; ?>>Diterima</option>
                                    <option value="ditolak" <?= $row['status_ppdb']=='ditolak'?'selected':''; ?>>Ditolak</option>
                                </select>
                                <button type="submit" class="btn btn-action">
                                    <i class="bi bi-check2-circle"></i> Simpan
                                </button>
                            </form>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr id="emptyRow">
                        <td colspan="10">
                            <div class="empty-state">
                                <i class="bi bi-inbox"></i>
                                <h4>Belum Ada Data Pendaftar</h4>
                                <p>Data pendaftar akan muncul di sini</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>

</main>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
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

    // Filter by Status (from stat cards)
    function filterByStatus(status) {
        document.getElementById('statusFilter').value = status;
        filterTable();
    }

    // Filter Table
    function filterTable() {
        const statusFilter = document.getElementById('statusFilter').value.toLowerCase();
        const searchInput = document.getElementById('searchInput').value.toLowerCase();
        const table = document.getElementById('ppdbTable');
        const rows = table.getElementsByTagName('tr');
        
        let visibleCount = 0;
        
        for (let i = 1; i < rows.length; i++) {
            const row = rows[i];
            
            // Skip empty row
            if (row.id === 'emptyRow') continue;
            
            const status = row.getAttribute('data-status');
            const namaAnak = row.cells[1].textContent.toLowerCase();
            const namaOrtu = row.cells[2].textContent.toLowerCase();
            
            let showRow = true;
            
            // Filter by status
            if (statusFilter !== 'all' && status !== statusFilter) {
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

    // AJAX Update Status
    $(document).ready(function() {
        $(".ubahStatusForm").on("submit", function(e) {
            e.preventDefault();
            let form = $(this);
            let id = form.data("id");
            let status = form.find("select[name='status']").val();

            $.ajax({
                url: "update_status.php",
                method: "POST",
                data: { id: id, status: status, ubah_status: true },
                dataType: "json",
                success: function(response) {
                    if(response.success) {
                        // Update badge
                        form.closest("tr").find(".status-td").html(
                            `<span class="badge-status status-${status}">${status.charAt(0).toUpperCase() + status.slice(1)}</span>`
                        );
                        
                        // Update data-status attribute
                        form.closest("tr").attr("data-status", status);
                        
                        Swal.fire({
                            icon: "success",
                            title: "Berhasil!",
                            text: response.message,
                            timer: 1500,
                            showConfirmButton: false
                        });
                        
                        // Reload after 1.5s to update stats
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        Swal.fire({
                            icon: "error",
                            title: "Gagal!",
                            text: response.message
                        });
                    }
                },
                error: function() {
                    Swal.fire({
                        icon: "error",
                        title: "Gagal!",
                        text: "Terjadi kesalahan server."
                    });
                }
            });
        });
    });
</script>

</body>
</html>