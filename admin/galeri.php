<?php
session_start();
include '../includes/config.php';

// Proses hapus foto
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $getFile = $conn->query("SELECT file_path FROM galeri_foto WHERE id = $id");
    if ($getFile && $getFile->num_rows > 0) {
        $data = $getFile->fetch_assoc();
        $file = "../uploads/" . $data['file_path'];
        if (file_exists($file)) unlink($file);
    }
    $conn->query("DELETE FROM galeri_foto WHERE id = $id");
    echo "<script>alert('Foto berhasil dihapus!'); window.location='galeri.php';</script>";
    exit;
}

// Hitung statistik
$stats = [];
$result_stats = $conn->query("SELECT kategori, COUNT(*) as jumlah FROM galeri_foto GROUP BY kategori");
if ($result_stats) {
    while ($row = $result_stats->fetch_assoc()) {
        $stats[$row['kategori']] = $row['jumlah'];
    }
}
$total = $conn->query("SELECT COUNT(*) as total FROM galeri_foto")->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Galeri - TK Pertiwi</title>

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
        }

        .stat-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border-color: #007bff;
        }

        .stat-icon {
            font-size: 40px;
            margin-bottom: 15px;
            color: #007bff;
        }

        .stat-number {
            font-size: 36px;
            font-weight: bold;
            margin-bottom: 5px;
            color: #007bff;
        }

        .stat-label {
            font-size: 13px;
            color: #666;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Upload Form */
        .upload-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-bottom: 30px;
        }

        .section-title {
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

        .upload-box {
            border: 3px dashed #d6eaff;
            border-radius: 12px;
            padding: 50px 20px;
            text-align: center;
            background: #f8f9fa;
            cursor: pointer;
            transition: all 0.3s;
            position: relative;
            overflow: hidden;
        }

        .upload-box:hover {
            border-color: #007bff;
            background: #e7f3ff;
        }

        .upload-box i {
            font-size: 60px;
            color: #007bff;
            margin-bottom: 15px;
        }

        .upload-box p {
            margin: 0;
            color: #666;
            font-weight: 500;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 14px;
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

        .btn-upload {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-upload:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        /* Gallery Grid */
        .gallery-container {
            background: white;
            padding: 30px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
        }

        .gallery-grid {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 20px;
            margin-top: 20px;
        }

        .gallery-card {
            background: white;
            border: 2px solid #e7f3ff;
            border-radius: 12px;
            overflow: hidden;
            transition: all 0.3s;
            position: relative;
        }

        .gallery-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 20px rgba(0,0,0,0.15);
            border-color: #007bff;
        }

        .gallery-img {
            width: 100%;
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: all 0.3s;
        }

        .gallery-card:hover .gallery-img {
            transform: scale(1.05);
        }

        .gallery-body {
            padding: 15px;
        }

        .gallery-title {
            font-size: 15px;
            font-weight: 600;
            color: #333;
            margin-bottom: 5px;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .gallery-category {
            display: inline-block;
            padding: 4px 12px;
            background: #e7f3ff;
            color: #007bff;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            text-transform: capitalize;
            margin-bottom: 10px;
        }

        .gallery-date {
            font-size: 11px;
            color: #999;
            display: flex;
            align-items: center;
            gap: 5px;
            margin-bottom: 10px;
        }

        .btn-delete {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            color: white;
            padding: 8px 15px;
            border-radius: 8px;
            font-size: 12px;
            font-weight: 600;
            transition: all 0.3s;
            width: 100%;
        }

        .btn-delete:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        /* Filter */
        .filter-container {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
            flex-wrap: wrap;
        }

        .filter-btn {
            padding: 8px 20px;
            border: 2px solid #e0e0e0;
            background: white;
            border-radius: 20px;
            font-size: 13px;
            font-weight: 600;
            color: #666;
            cursor: pointer;
            transition: all 0.3s;
        }

        .filter-btn:hover {
            border-color: #007bff;
            color: #007bff;
        }

        .filter-btn.active {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border-color: #007bff;
            color: white;
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

        /* Modal */
        .modal-img {
            width: 100%;
            border-radius: 10px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 90px 15px 30px;
            }
            
            .gallery-grid {
                grid-template-columns: repeat(auto-fill, minmax(150px, 1fr));
                gap: 15px;
            }
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">KELOLA GALERI FOTO</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-images"></i> Kelola Galeri Foto</h1>
        <p>Manajemen foto dan dokumentasi kegiatan TK Pertiwi</p>
    </div>

    <?php
    // Alert messages
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $judul = $_POST['judul'] ?? '';
        $deskripsi = $_POST['deskripsi'] ?? '';
        $kategori = $_POST['kategori'] ?? 'kegiatan';
        $uploaded_by = $_SESSION['user_id'] ?? null;

        $targetDir = "../uploads/";
        if (!is_dir($targetDir)) mkdir($targetDir, 0777, true);

        $fileName = time() . "_" . basename($_FILES["file"]["name"]);
        $targetFilePath = $targetDir . $fileName;
        $fileType = strtolower(pathinfo($targetFilePath, PATHINFO_EXTENSION));

        $allowTypes = array('jpg','jpeg','png','gif');
        if (in_array($fileType, $allowTypes)) {
            if (move_uploaded_file($_FILES["file"]["tmp_name"], $targetFilePath)) {
                $stmt = $conn->prepare("INSERT INTO galeri_foto (judul, deskripsi, kategori, file_path, uploaded_by) VALUES (?, ?, ?, ?, ?)");
                $stmt->bind_param("ssssi", $judul, $deskripsi, $kategori, $fileName, $uploaded_by);
                $stmt->execute();
                echo "<div class='alert alert-success alert-dismissible fade show' role='alert'>
                    <i class='bi bi-check-circle me-2'></i>Foto berhasil diunggah!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
            } else {
                echo "<div class='alert alert-danger alert-dismissible fade show' role='alert'>
                    <i class='bi bi-x-circle me-2'></i>Gagal mengunggah file!
                    <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
                </div>";
            }
        } else {
            echo "<div class='alert alert-warning alert-dismissible fade show' role='alert'>
                <i class='bi bi-exclamation-triangle me-2'></i>Format file tidak didukung!
                <button type='button' class='btn-close' data-bs-dismiss='alert'></button>
            </div>";
        }
    }
    ?>

    <!-- Stats Cards -->
    <div class="stats-container">
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-images"></i>
            </div>
            <div class="stat-number"><?= $total; ?></div>
            <div class="stat-label">Total Foto</div>
        </div>
        <div class="stat-card">
            <div class="stat-icon">
                <i class="bi bi-diagram-3"></i>
            </div>
            <div class="stat-number"><?= count($stats); ?></div>
            <div class="stat-label">Kategori</div>
        </div>
    </div>

    <!-- Upload Form -->
    <div class="upload-container">
        <div class="section-title">
            <i class="bi bi-cloud-upload"></i>
            Unggah Foto Baru
        </div>

        <form method="POST" enctype="multipart/form-data">
            <div class="row g-3 mb-3">
                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-card-heading text-primary"></i> Judul Foto
                    </label>
                    <input type="text" name="judul" class="form-control" placeholder="Contoh: Kegiatan Outing Class" required>
                </div>

                <div class="col-md-6">
                    <label class="form-label">
                        <i class="bi bi-tag text-primary"></i> Kategori
                    </label>
                    <select name="kategori" class="form-select" required>
                        <option value="">-- Pilih Kategori --</option>
                        <option value="guru">Foto Guru</option>
                        <option value="kepala_sekolah">Foto Kepala Sekolah</option>
                        <option value="kegiatan">Kegiatan</option>
                        <option value="prestasi">Prestasi</option>
                        <option value="ekstrakurikuler">Ekstrakurikuler</option>
                        <option value="lainnya">Lainnya</option>
                    </select>
                </div>
            </div>

            <div class="mb-3">
                <label class="form-label">
                    <i class="bi bi-text-paragraph text-primary"></i> Deskripsi
                </label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tambahkan deskripsi singkat..."></textarea>
            </div>

            <div class="mb-3">
                <div class="upload-box" onclick="document.getElementById('fileInput').click();">
                    <i class="bi bi-cloud-arrow-up"></i>
                    <p class="mt-2"><strong>Klik untuk memilih file</strong></p>
                    <p class="text-muted mb-0">Format: JPG, JPEG, PNG, GIF (Max 5MB)</p>
                    <input type="file" id="fileInput" name="file" hidden required accept="image/*">
                </div>
            </div>

            <button type="submit" class="btn-upload">
                <i class="bi bi-upload me-2"></i>Unggah Foto
            </button>
        </form>
    </div>

    <!-- Gallery -->
    <div class="gallery-container">
        <div class="section-title">
            <i class="bi bi-grid-3x3-gap"></i>
            Daftar Foto Galeri
        </div>

        <!-- Filter Buttons -->
        <div class="filter-container">
            <button class="filter-btn active" onclick="filterGallery('all')">Semua</button>
            <button class="filter-btn" onclick="filterGallery('guru')">Guru</button>
            <button class="filter-btn" onclick="filterGallery('kepala_sekolah')">Kepala Sekolah</button>
            <button class="filter-btn" onclick="filterGallery('kegiatan')">Kegiatan</button>
            <button class="filter-btn" onclick="filterGallery('prestasi')">Prestasi</button>
            <button class="filter-btn" onclick="filterGallery('ekstrakurikuler')">Ekstrakurikuler</button>
            <button class="filter-btn" onclick="filterGallery('lainnya')">Lainnya</button>
        </div>

        <div class="gallery-grid">
            <?php
            $result = $conn->query("SELECT * FROM galeri_foto ORDER BY created_at DESC");
            if ($result && $result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo '
                    <div class="gallery-card" data-category="'.$row['kategori'].'">
                        <img src="../uploads/'.$row['file_path'].'" class="gallery-img" 
                             onclick="showImageModal(this.src, \''.$row['judul'].'\')" 
                             alt="'.$row['judul'].'">
                        <div class="gallery-body">
                            <span class="gallery-category">'.ucwords(str_replace('_', ' ', $row['kategori'])).'</span>
                            <div class="gallery-title">'.$row['judul'].'</div>
                            <div class="gallery-date">
                                <i class="bi bi-calendar"></i>
                                '.date('d M Y', strtotime($row['created_at'])).'
                            </div>
                            <a href="galeri.php?delete='.$row['id'].'" class="btn-delete" 
                               onclick="return confirm(\'Yakin ingin menghapus foto ini?\');">
                                <i class="bi bi-trash"></i> Hapus
                            </a>
                        </div>
                    </div>';
                }
            } else {
                echo '
                <div class="empty-state" style="grid-column: 1 / -1;">
                    <i class="bi bi-inbox"></i>
                    <h4>Belum Ada Foto</h4>
                    <p>Unggah foto pertama Anda menggunakan form di atas</p>
                </div>';
            }
            ?>
        </div>
    </div>

</main>

<!-- Modal Image Preview -->
<div class="modal fade" id="imageModal" tabindex="-1">
    <div class="modal-dialog modal-dialog-centered modal-lg">
        <div class="modal-content">
            <div class="modal-header bg-primary text-white">
                <h5 class="modal-title" id="modalTitle">Preview Foto</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body p-0">
                <img src="" id="modalImage" class="modal-img" alt="Preview">
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

    // Show file name when selected
    document.getElementById('fileInput').addEventListener('change', function(e) {
        if (e.target.files.length > 0) {
            const fileName = e.target.files[0].name;
            document.querySelector('.upload-box p:first-of-type').innerHTML = 
                '<strong><i class="bi bi-check-circle text-success"></i> ' + fileName + '</strong>';
        }
    });

    // Filter Gallery
    function filterGallery(category) {
        const cards = document.querySelectorAll('.gallery-card');
        const buttons = document.querySelectorAll('.filter-btn');
        
        // Update active button
        buttons.forEach(btn => btn.classList.remove('active'));
        event.target.classList.add('active');
        
        // Filter cards
        cards.forEach(card => {
            if (category === 'all' || card.getAttribute('data-category') === category) {
                card.style.display = 'block';
            } else {
                card.style.display = 'none';
            }
        });
    }

    // Show Image Modal
    function showImageModal(src, title) {
        document.getElementById('modalImage').src = src;
        document.getElementById('modalTitle').textContent = title;
        new bootstrap.Modal(document.getElementById('imageModal')).show();
    }
</script>

</body>
</html>