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

        /* Section */
        .form-section {
            background-color: #fff;
            border-radius: 10px;
            padding: 25px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            border-left: 5px solid #007bff;
        }

        /* Buttons */
        .btn-primary {
            background-color: #007bff;
            border: none;
            border-radius: 8px;
            font-weight: 500;
        }
        .btn-primary:hover {
            background-color: #006ae0;
        }

        /* Agenda Cards */
        .agenda-card {
            border-radius: 15px;
            padding: 15px;
            box-shadow: 0 3px 6px rgba(0,0,0,0.1);
            transition: transform 0.2s;
            color: #333;
        }
        .agenda-card:hover { transform: translateY(-3px); }
        .agenda-green { background-color: #A5D6A7; }
        .agenda-orange { background-color: #FFCC80; }

        /* Agenda List */
        .agenda-list .card {
            border-radius: 12px;
            margin-bottom: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
        }
        .icon-btn { border: none; background: none; cursor: pointer; }

        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 80px 20px; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="menu-toggle">&#9776;</div>
        <div class="title">KELOLA AGENDA</div>
        <div class="icons">
            <div><i class="bi bi-envelope"></i></div>
            <div><i class="bi bi-bell"></i></div>
            <div><i class="bi bi-person-circle"></i></div>
        </div>
    </header>

    <!-- Sidebar Include -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">

        <h1 class="mb-4">Kelola Agenda Kegiatan</h1>

        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
        <?php endif; ?>

        <!-- Acara Mendatang -->
        <h5 class="mb-3">Acara Mendatang</h5>
        <div class="d-flex flex-wrap gap-3 mb-4">
            <div class="agenda-card agenda-green flex-fill">
                <h5>Pameran Sekolah</h5>
                <p class="mb-0 text-muted">26 Oktober 2025</p>
            </div>
            <div class="agenda-card agenda-orange flex-fill">
                <h5>Pertemuan Orang Tua & Guru</h5>
                <p class="mb-0 text-muted">26 Oktober 2025</p>
            </div>
        </div>

        <!-- Tambah Agenda Baru -->
        <div class="form-section mb-4">
            <h5 class="mb-3">Tambah Acara Baru</h5>
            <form method="POST" enctype="multipart/form-data" class="row g-3">
                <div class="col-md-6">
                    <label class="form-label">Nama Acara</label>
                    <input type="text" name="judul" class="form-control" placeholder="Masukkan nama acara" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Deskripsi</label>
                    <input type="text" name="deskripsi" class="form-control" placeholder="Masukkan deskripsi" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Tipe</label>
                    <select name="tipe" class="form-select" required>
                        <option value="">Pilih tipe acara</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="acara_tahunan">Acara Tahunan</option>
                        <option value="pembagian_rapot">Pembagian Rapot</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label class="form-label">Foto (opsional)</label>
                    <input type="file" name="foto" class="form-control" accept=".jpg,.jpeg,.png">
                </div>
                <div class="col-12 text-end">
                    <button type="submit" name="add" class="btn btn-primary">Tambah Acara</button>
                </div>
            </form>
        </div>

        <!-- Daftar Semua Acara -->
        <h5 class="mb-3">Semua Acara</h5>
        <div class="agenda-list">
            <?php foreach ($agenda as $item): ?>
                <div class="card p-3 d-flex flex-row justify-content-between align-items-center">
                    <div class="d-flex align-items-center gap-3">
                        <div class="fs-3 text-primary"><i class="bi bi-calendar-event"></i></div>
                        <div>
                            <h6 class="mb-0"><?php echo htmlspecialchars($item['judul']); ?></h6>
                            <small class="text-muted"><?php echo date('d F Y', strtotime($item['tanggal'])); ?></small>
                        </div>
                    </div>
                    <div>
                        <form method="POST" style="display:inline;">
                            <input type="hidden" name="id" value="<?php echo $item['id']; ?>">
                            <button type="submit" name="delete" class="icon-btn text-danger"><i class="bi bi-trash fs-5"></i></button>
                        </form>
                    </div>
                </div>
            <?php endforeach; ?>
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
