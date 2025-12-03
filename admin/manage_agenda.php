<?php
session_start();

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

// === VARIABEL ===
$success = '';
$error = '';
$upload_dir = '../Uploads/';

// === PROSES TAMBAH & HAPUS ===
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add'])) {
        $judul = trim($_POST['judul'] ?? '');
        $deskripsi = trim($_POST['deskripsi'] ?? '');
        $tanggal = trim($_POST['tanggal'] ?? '');
        $tipe = trim($_POST['tipe'] ?? '');
        
        if (empty($judul) || empty($deskripsi) || empty($tanggal) || empty($tipe)) {
            $error = "Semua kolom wajib diisi!";
        } else {
            $file_name = '';
            if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
                $file_name = time() . '_' . basename($_FILES['foto']['name']);
                move_uploaded_file($_FILES['foto']['tmp_name'], $upload_dir . $file_name);
            }
            $stmt = $pdo->prepare("INSERT INTO agenda_kegiatan (judul, deskripsi, tanggal, tipe, foto) VALUES (?, ?, ?, ?, ?)");
            $stmt->execute([$judul, $deskripsi, $tanggal, $tipe, $file_name]);
            $success = "Agenda berhasil ditambahkan!";
        }
    }

    if (isset($_POST['delete'])) {
        $id = $_POST['id'];
        $stmt = $pdo->prepare("SELECT foto FROM agenda_kegiatan WHERE id = ?");
        $stmt->execute([$id]);
        $agenda = $stmt->fetch(PDO::FETCH_ASSOC);
        if ($agenda && $agenda['foto']) {
            $file_path = $upload_dir . $agenda['foto'];
            if (file_exists($file_path)) unlink($file_path);
        }
        $pdo->prepare("DELETE FROM agenda_kegiatan WHERE id = ?")->execute([$id]);
        $success = "Agenda berhasil dihapus!";
    }
}

// === AMBIL DATA ===
$stmt = $pdo->query("SELECT * FROM agenda_kegiatan ORDER BY tanggal DESC");
$agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);

// Ambil 2 acara terdekat
$stmt_upcoming = $pdo->query("SELECT * FROM agenda_kegiatan WHERE tanggal >= CURDATE() ORDER BY tanggal ASC LIMIT 2");
$upcoming = $stmt_upcoming->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Agenda - TK Pertiwi</title>

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

        /* Alert Messages */
        .alert {
            border-radius: 10px;
            border: none;
            padding: 15px 20px;
            margin-bottom: 25px;
            animation: slideDown 0.3s ease-out;
        }

        @keyframes slideDown {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }

        /* Upcoming Events Section */
        .upcoming-section {
            margin-bottom: 30px;
        }

        .upcoming-title {
            font-size: 20px;
            font-weight: 600;
            color: #333;
            margin-bottom: 20px;
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .upcoming-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }

        .upcoming-card {
            border-radius: 15px;
            padding: 25px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.1);
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .upcoming-card::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            width: 5px;
            height: 100%;
            background: linear-gradient(180deg, #fff 0%, rgba(255,255,255,0.5) 100%);
        }

        .upcoming-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
        }

        .upcoming-card.green { 
            background: linear-gradient(135deg, #A5D6A7 0%, #81C784 100%);
            color: #1b5e20;
        }
        
        .upcoming-card.orange { 
            background: linear-gradient(135deg, #FFCC80 0%, #FFB74D 100%);
            color: #e65100;
        }

        .upcoming-card h5 {
            font-size: 18px;
            font-weight: 700;
            margin-bottom: 10px;
        }

        .upcoming-card .date {
            display: flex;
            align-items: center;
            gap: 8px;
            font-size: 14px;
            opacity: 0.9;
        }

        /* Form Section */
        .form-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .form-title {
            font-size: 20px;
            font-weight: 600;
            color: #007bff;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 10px;
            padding-bottom: 15px;
            border-bottom: 2px solid #e7f3ff;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 2px solid #e0e0e0;
            padding: 10px 15px;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.2rem rgba(0, 123, 255, 0.15);
        }

        .btn-primary {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        /* Agenda List */
        .agenda-list-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .agenda-list-title {
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

        .agenda-item {
            background: white;
            border: 2px solid #e7f3ff;
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            transition: all 0.3s;
        }

        .agenda-item:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
            transform: translateX(5px);
        }

        .agenda-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .agenda-icon {
            width: 60px;
            height: 60px;
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-radius: 12px;
            display: flex;
            align-items: center;
            justify-content: center;
            color: white;
            font-size: 28px;
        }

        .agenda-details h6 {
            margin: 0 0 5px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
        }

        .agenda-details .date {
            font-size: 13px;
            color: #666;
            display: flex;
            align-items: center;
            gap: 5px;
        }

        .agenda-details .type {
            display: inline-block;
            padding: 3px 10px;
            background: #e7f3ff;
            color: #007bff;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            margin-top: 5px;
        }

        .agenda-actions {
            display: flex;
            gap: 10px;
        }

        .btn-delete {
            background: none;
            border: none;
            color: #dc3545;
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;
            padding: 5px 10px;
            border-radius: 8px;
        }

        .btn-delete:hover {
            background: #fff5f5;
            transform: scale(1.1);
        }

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

            .upcoming-grid {
                grid-template-columns: 1fr;
            }

            .agenda-item {
                flex-direction: column;
                align-items: flex-start;
                gap: 15px;
            }

            .agenda-actions {
                width: 100%;
                justify-content: flex-end;
            }
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">KELOLA AGENDA KEGIATAN</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-calendar-event"></i> Kelola Agenda Kegiatan</h1>
        <p>Manajemen agenda dan acara sekolah TK Pertiwi</p>
    </div>

    <!-- Alert Messages -->
    <?php if ($success): ?>
        <div class="alert alert-success d-flex align-items-center">
            <i class="bi bi-check-circle-fill fs-4 me-3"></i>
            <div><?= $success; ?></div>
        </div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger d-flex align-items-center">
            <i class="bi bi-exclamation-triangle-fill fs-4 me-3"></i>
            <div><?= $error; ?></div>
        </div>
    <?php endif; ?>

    <!-- Upcoming Events -->
    <div class="upcoming-section">
        <div class="upcoming-title">
            <i class="bi bi-star-fill text-warning"></i>
            Acara Mendatang
        </div>
        <div class="upcoming-grid">
            <?php if (count($upcoming) > 0): ?>
                <?php 
                $colors = ['green', 'orange', 'blue', 'purple'];
                foreach ($upcoming as $index => $event): 
                    $color = $colors[$index % count($colors)];
                ?>
                    <div class="upcoming-card <?= $color; ?>">
                        <h5><?= htmlspecialchars($event['judul']); ?></h5>
                        <div class="date">
                            <i class="bi bi-calendar-check"></i>
                            <?= date('d F Y', strtotime($event['tanggal'])); ?>
                        </div>
                    </div>
                <?php endforeach; ?>
            <?php else: ?>
                <div class="upcoming-card green">
                    <h5>Belum Ada Acara Mendatang</h5>
                    <div class="date">
                        <i class="bi bi-info-circle"></i>
                        Tambahkan acara baru di bawah
                    </div>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Add New Event Form -->
    <div class="form-container">
        <div class="form-title">
            <i class="bi bi-plus-circle-fill"></i>
            Tambah Acara Baru
        </div>
        <form method="POST" enctype="multipart/form-data" class="row g-3">
            <div class="col-md-6">
                <label class="form-label">
                    <i class="bi bi-card-heading text-primary"></i> Nama Acara
                </label>
                <input type="text" name="judul" class="form-control" placeholder="Masukkan nama acara" required>
            </div>
            <div class="col-md-6">
                <label class="form-label">
                    <i class="bi bi-text-paragraph text-primary"></i> Deskripsi
                </label>
                <input type="text" name="deskripsi" class="form-control" placeholder="Masukkan deskripsi" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-calendar-date text-primary"></i> Tanggal
                </label>
                <input type="date" name="tanggal" class="form-control" required>
            </div>
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-tag text-primary"></i> Tipe Acara
                </label>
                <select name="tipe" class="form-select" required>
                    <option value="">Pilih tipe acara</option>
                    <option value="kegiatan">Kegiatan</option>
                    <option value="acara_tahunan">Acara Tahunan</option>
                    <option value="pembagian_rapot">Pembagian Rapot</option>
                </select>
            </div>
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-image text-primary"></i> Foto (opsional)
                </label>
                <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
            </div>
            <div class="col-12 text-end">
                <button type="submit" name="add" class="btn btn-primary">
                    <i class="bi bi-plus-lg"></i> Tambah Acara
                </button>
            </div>
        </form>
    </div>

    <!-- All Events List -->
    <div class="agenda-list-container">
        <div class="agenda-list-title">
            <i class="bi bi-list-ul"></i>
            Semua Acara (<?= count($agenda); ?>)
        </div>

        <?php if (count($agenda) > 0): ?>
            <?php foreach ($agenda as $item): ?>
                <div class="agenda-item">
                    <div class="agenda-info">
                        <div class="agenda-icon">
                            <i class="bi bi-calendar-event"></i>
                        </div>
                        <div class="agenda-details">
                            <h6><?= htmlspecialchars($item['judul']); ?></h6>
                            <div class="date">
                                <i class="bi bi-clock"></i>
                                <?= date('d F Y', strtotime($item['tanggal'])); ?>
                            </div>
                            <span class="type"><?= ucfirst(str_replace('_', ' ', $item['tipe'])); ?></span>
                        </div>
                    </div>
                    <div class="agenda-actions">
                        <form method="POST" style="display:inline;" onsubmit="return confirm('Yakin ingin menghapus acara ini?');">
                            <input type="hidden" name="id" value="<?= $item['id']; ?>">
                            <button type="submit" name="delete" class="btn-delete" title="Hapus">
                                <i class="bi bi-trash-fill"></i>
                            </button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php else: ?>
            <div class="empty-state">
                <i class="bi bi-calendar-x"></i>
                <h4>Belum Ada Agenda</h4>
                <p>Tambahkan acara baru menggunakan form di atas</p>
            </div>
        <?php endif; ?>
    </div>

</main>

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

    // Auto hide alert after 5 seconds
    setTimeout(() => {
        const alerts = document.querySelectorAll('.alert');
        alerts.forEach(alert => {
            alert.style.transition = 'opacity 0.5s';
            alert.style.opacity = '0';
            setTimeout(() => alert.remove(), 500);
        });
    }, 5000);
</script>

</body>
</html>