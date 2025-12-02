<?php
session_start();
include '../includes/config.php';

// Cek dan buat tabel absensi_guru jika belum ada
$check_table = $conn->query("SHOW TABLES LIKE 'absensi_guru'");
if ($check_table->num_rows == 0) {
    $create_table = "CREATE TABLE `absensi_guru` (
      `id` int(11) NOT NULL AUTO_INCREMENT,
      `guru_id` int(11) NOT NULL,
      `tanggal` date NOT NULL,
      `status` enum('Hadir','Izin','Sakit','Alfa','Terlambat') NOT NULL,
      `waktu_absen` time DEFAULT NULL,
      `catatan` text,
      `created_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
      `updated_at` timestamp NULL DEFAULT NULL ON UPDATE CURRENT_TIMESTAMP,
      PRIMARY KEY (`id`),
      UNIQUE KEY `guru_tanggal` (`guru_id`,`tanggal`),
      KEY `guru_id` (`guru_id`),
      KEY `tanggal` (`tanggal`)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4";
    
    $conn->query($create_table);
} else {
    // Cek dan tambah kolom waktu_absen jika belum ada
    $check_column = $conn->query("SHOW COLUMNS FROM absensi_guru LIKE 'waktu_absen'");
    if ($check_column->num_rows == 0) {
        $conn->query("ALTER TABLE absensi_guru ADD COLUMN waktu_absen time DEFAULT NULL AFTER status");
    }
    
    // Update enum status untuk menambah Terlambat
    $conn->query("ALTER TABLE absensi_guru MODIFY status enum('Hadir','Izin','Sakit','Alfa','Terlambat') NOT NULL");
}

// Ambil tanggal hari ini atau dari parameter
$tanggal = isset($_GET['tanggal']) ? $_GET['tanggal'] : date('Y-m-d');

// Ambil data guru dari database
$q = $conn->query("SELECT * FROM guru ORDER BY nama ASC");
$teachers = $q->fetch_all(MYSQLI_ASSOC);

// Ambil data absensi untuk tanggal tertentu
$absensi_data = [];
$q_absen = $conn->query("SELECT * FROM absensi_guru WHERE tanggal = '$tanggal'");
if ($q_absen) {
    while ($row = $q_absen->fetch_assoc()) {
        $absensi_data[$row['guru_id']] = $row;
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Absen Guru - TK Pertiwi</title>

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
        
        .date-filter {
            background: #fff;
            padding: 20px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
            display: flex;
            align-items: center;
            gap: 15px;
            flex-wrap: wrap;
        }
        
        .teacher-grid { 
            display: grid; 
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr)); 
            gap: 20px; 
            margin-bottom: 30px;
        }
        .teacher-card {
            background-color: #fff; 
            border: 2px solid #d6eaff;
            border-radius: 10px; 
            padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); 
            transition: 0.3s;
        }
        .teacher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .teacher-header img {
            width: 50px; 
            height: 50px; 
            border-radius: 50%; 
            object-fit: cover;
            margin-right: 10px;
        }
        .teacher-header { 
            display: flex; 
            align-items: center; 
            margin-bottom: 12px; 
        }
        
        .status-options { 
            display: flex; 
            gap: 8px; 
            margin-top: 10px; 
            flex-wrap: wrap; 
        }
        .status-options label {
            cursor: pointer;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 12px;
            transition: 0.2s;
            border: 1px solid #ddd;
        }
        .status-options label:has(input:checked) {
            background-color: #007bff;
            color: white;
            border-color: #007bff;
        }
        .status-options input[type="radio"] {
            margin-right: 3px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 4px 12px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            margin-bottom: 8px;
        }
        .status-hadir { background-color: #d4edda; color: #155724; }
        .status-terlambat { background-color: #fff3cd; color: #856404; }
        .status-izin { background-color: #cfe2ff; color: #084298; }
        .status-sakit { background-color: #f8d7da; color: #721c24; }
        .status-alfa { background-color: #d6d8db; color: #383d41; }
        
        .waktu-info {
            display: flex;
            align-items: center;
            gap: 5px;
            font-size: 11px;
            color: #666;
            margin-top: 5px;
            background: #f8f9fa;
            padding: 4px 8px;
            border-radius: 5px;
        }
        
        .catatan-display {
            background-color: #f8f9fa;
            padding: 8px;
            border-radius: 5px;
            font-size: 12px;
            margin-top: 8px;
            color: #666;
        }
        
        .izin-box {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
        
        .summary-cards {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(160px, 1fr));
            gap: 15px;
            margin-bottom: 30px;
        }
        .summary-card {
            background: white;
            padding: 20px;
            border-radius: 10px;
            border: 2px solid #d6eaff;
            text-align: center;
            transition: all 0.3s;
        }
        .summary-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            border-color: #007bff;
        }
        .summary-number {
            font-size: 32px;
            font-weight: bold;
            color: #007bff;
        }
        .summary-label {
            font-size: 13px;
            color: #666;
            margin-top: 5px;
        }
        
        .waktu-badge {
            background: #e7f3ff;
            color: #0056b3;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 11px;
            font-weight: 600;
        }
        
        .late-indicator {
            background: #fff3cd;
            color: #856404;
            padding: 2px 8px;
            border-radius: 10px;
            font-size: 10px;
            margin-left: 5px;
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">DATA ABSEN GURU</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <h1 class="mb-4">Data Absen Guru</h1>

    <!-- Filter Tanggal -->
    <div class="date-filter">
        <i class="bi bi-calendar3 fs-4 text-primary"></i>
        <div>
            <label class="form-label mb-1 fw-bold">Pilih Tanggal</label>
            <input type="date" id="tanggalFilter" class="form-control" value="<?= $tanggal; ?>">
        </div>
        <button class="btn btn-primary" onclick="filterByDate()">
            <i class="bi bi-search"></i> Tampilkan
        </button>
        <button class="btn btn-success" onclick="simpanSemuaAbsen()">
            <i class="bi bi-save"></i> Simpan Absensi
        </button>
    </div>

    <?php
    // Hitung ringkasan absensi
    $hadir = 0; $izin = 0; $sakit = 0; $alfa = 0; $belum = 0; $terlambat = 0;
    foreach ($teachers as $t) {
        if (isset($absensi_data[$t['id']])) {
            $status = $absensi_data[$t['id']]['status'];
            if ($status == 'Hadir') $hadir++;
            elseif ($status == 'Izin') $izin++;
            elseif ($status == 'Sakit') $sakit++;
            elseif ($status == 'Alfa') $alfa++;
            elseif ($status == 'Terlambat') $terlambat++;
        } else {
            $belum++;
        }
    }
    ?>

    <!-- Ringkasan -->
    <div class="summary-cards">
        <div class="summary-card" onclick="showDetailModal('Hadir')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #28a745;"><?= $hadir; ?></div>
            <div class="summary-label">Hadir</div>
        </div>
        <div class="summary-card" onclick="showDetailModal('Terlambat')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #ffc107;"><?= $terlambat; ?></div>
            <div class="summary-label">Terlambat</div>
        </div>
        <div class="summary-card" onclick="showDetailModal('Izin')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #0d6efd;"><?= $izin; ?></div>
            <div class="summary-label">Izin</div>
        </div>
        <div class="summary-card" onclick="showDetailModal('Sakit')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #dc3545;"><?= $sakit; ?></div>
            <div class="summary-label">Sakit</div>
        </div>
        <div class="summary-card" onclick="showDetailModal('Alfa')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #6c757d;"><?= $alfa; ?></div>
            <div class="summary-label">Alfa</div>
        </div>
        <div class="summary-card" onclick="showDetailModal('Belum')" style="cursor: pointer;" title="Klik untuk melihat detail">
            <div class="summary-number" style="color: #17a2b8;"><?= $belum; ?></div>
            <div class="summary-label">Belum Absen</div>
        </div>
    </div>

  

   
</main>

<!-- Modal Detail Absensi -->
<div class="modal fade" id="detailModal" tabindex="-1" aria-hidden="true">
    <div class="modal-dialog modal-xl">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Detail Absensi</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info mb-3">
                    <i class="bi bi-calendar-check"></i> 
                    <strong>Tanggal:</strong> <span id="modalTanggal"></span>
                </div>
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead class="table-primary">
                            <tr>
                                <th width="50">No</th>
                                <th width="70">Foto</th>
                                <th>Nama Guru</th>
                                <th>Jabatan</th>
                                <th width="120">Waktu Absen</th>
                                <th width="120">Waktu Input</th>
                                <th>Catatan</th>
                            </tr>
                        </thead>
                        <tbody id="modalTableBody">
                            <!-- Data akan diisi oleh JavaScript -->
                        </tbody>
                    </table>
                </div>
                <div id="emptyMessage" class="text-center text-muted py-5" style="display: none;">
                    <i class="bi bi-inbox" style="font-size: 64px; opacity: 0.3;"></i>
                    <p class="mt-3 fs-5">Tidak ada data untuk ditampilkan</p>
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Tutup
                </button>
            </div>
        </div>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://code.jquery.com/jquery-3.7.1.min.js"></script>
<script>
    const menuToggle = document.querySelector('.menu-toggle');
    const sidebar = document.querySelector('.sidebar');
    const mainContent = document.querySelector('.main-content');

    menuToggle.addEventListener('click', () => {
        sidebar.classList.toggle('collapsed');
        mainContent.classList.toggle('collapsed');
    });

    function filterByDate() {
        const tanggal = document.getElementById('tanggalFilter').value;
        window.location.href = '?tanggal=' + tanggal;
    }

    // Data guru untuk modal (dari PHP)
    const teachersData = <?= json_encode($teachers); ?>;
    const absensiData = <?= json_encode($absensi_data); ?>;
    const currentDate = '<?= $tanggal; ?>';

    function showDetailModal(status) {
        const modalTitle = document.getElementById('modalTitle');
        const modalTanggal = document.getElementById('modalTanggal');
        const modalTableBody = document.getElementById('modalTableBody');
        const emptyMessage = document.getElementById('emptyMessage');
        
        // Set title dan tanggal
        modalTitle.innerHTML = `<i class="bi bi-people-fill"></i> Detail Guru ${status}`;
        modalTanggal.textContent = formatDate(currentDate);
        
        // Filter guru berdasarkan status
        let filteredTeachers = [];
        
        if (status === 'Belum') {
            // Guru yang belum absen
            filteredTeachers = teachersData.filter(teacher => !absensiData[teacher.id]);
        } else {
            // Guru dengan status tertentu
            filteredTeachers = teachersData.filter(teacher => {
                return absensiData[teacher.id] && absensiData[teacher.id].status === status;
            });
        }
        
        // Tampilkan data atau pesan kosong
        if (filteredTeachers.length === 0) {
            modalTableBody.innerHTML = '';
            emptyMessage.style.display = 'block';
        } else {
            emptyMessage.style.display = 'none';
            let html = '';
            
            filteredTeachers.forEach((teacher, index) => {
                const absen = absensiData[teacher.id];
                const catatan = absen ? (absen.catatan || '-') : '-';
                const foto = teacher.profile_image_url || 'default.png';
                const waktuAbsen = absen && absen.waktu_absen ? formatTime(absen.waktu_absen) : '-';
                const waktuInput = absen && absen.created_at ? formatDateTime(absen.created_at) : '-';
                
                // Tentukan class untuk waktu terlambat
                let waktuClass = '';
                if (absen && absen.waktu_absen && absen.status === 'Terlambat') {
                    waktuClass = 'text-warning fw-bold';
                }
                
                html += `
                    <tr>
                        <td class="text-center">${index + 1}</td>
                        <td class="text-center">
                            <img src="${foto}" alt="${teacher.nama}" 
                                 style="width: 45px; height: 45px; border-radius: 50%; object-fit: cover; border: 2px solid #007bff;">
                        </td>
                        <td><strong>${teacher.nama}</strong></td>
                        <td>${teacher.jabatan}</td>
                        <td class="text-center ${waktuClass}">
                            ${waktuAbsen !== '-' ? '<i class="bi bi-clock-fill"></i> ' + waktuAbsen : '-'}
                        </td>
                        <td class="text-center" style="font-size: 11px;">
                            ${waktuInput}
                        </td>
                        <td>${catatan}</td>
                    </tr>
                `;
            });
            
            modalTableBody.innerHTML = html;
        }
        
        // Tampilkan modal
        const modal = new bootstrap.Modal(document.getElementById('detailModal'));
        modal.show();
    }

    function formatDate(dateString) {
        const options = { weekday: 'long', year: 'numeric', month: 'long', day: 'numeric' };
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', options);
    }

    function formatTime(timeString) {
        if (!timeString || timeString === '-') return '-';
        const [hour, minute] = timeString.split(':');
        return `${hour}:${minute}`;
    }

    function formatDateTime(datetimeString) {
        if (!datetimeString) return '-';
        const date = new Date(datetimeString);
        const day = String(date.getDate()).padStart(2, '0');
        const month = String(date.getMonth() + 1).padStart(2, '0');
        const year = date.getFullYear();
        const hour = String(date.getHours()).padStart(2, '0');
        const minute = String(date.getMinutes()).padStart(2, '0');
        return `${day}/${month}/${year} ${hour}:${minute}`;
    }

    function simpanSemuaAbsen() {
        const tanggal = document.getElementById('tanggalFilter').value;
        const absensiData = [];

        document.querySelectorAll('.teacher-card').forEach(card => {
            const guruId = card.dataset.guruId;
            const statusRadio = card.querySelector(`input[name="status_${guruId}"]:checked`);
            const catatan = card.querySelector(`input[name="catatan_${guruId}"]`).value;
            const waktu = card.querySelector(`input[name="waktu_${guruId}"]`).value;

            if (statusRadio) {
                absensiData.push({
                    guru_id: guruId,
                    tanggal: tanggal,
                    status: statusRadio.value,
                    waktu_absen: waktu || null,
                    catatan: catatan
                });
            }
        });

        if (absensiData.length === 0) {
            alert('Tidak ada data absensi yang diisi!');
            return;
        }

        // Kirim data menggunakan AJAX
        $.ajax({
            url: 'simpan_absen_batch.php',
            method: 'POST',
            data: {
                absensi: JSON.stringify(absensiData)
            },
            success: function(response) {
                alert('Absensi berhasil disimpan!');
                location.reload();
            },
            error: function() {
                alert('Terjadi kesalahan saat menyimpan absensi');
            }
        });
    }

    // Auto-reload saat tanggal berubah
    document.getElementById('tanggalFilter').addEventListener('change', filterByDate);
</script>

</body>
</html>