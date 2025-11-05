<?php
session_start();
include '../includes/config.php'; // koneksi database

// Proses hapus foto
if (isset($_GET['delete'])) {
    $id = intval($_GET['delete']);
    $getFile = $conn->query("SELECT file_path FROM galeri_foto WHERE id = $id");
    if ($getFile && $getFile->num_rows > 0) {
        $data = $getFile->fetch_assoc();
        $file = "../uploads/" . $data['file_path'];
        if (file_exists($file)) unlink($file); // hapus file fisik
    }
    $conn->query("DELETE FROM galeri_foto WHERE id = $id");
    echo "<script>alert('Foto berhasil dihapus!'); window.location='galeri.php';</script>";
    exit;
}
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
            font-family: 'Poppins', sans-serif;
            background-color: #E0F7FA;
            margin: 0;
            overflow-x: hidden;
        }
        .header {
            background-color: #2196F3;
            color: #fff;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: space-between;
            padding: 0 20px;
            position: fixed;
            width: 100%;
            top: 0;
            left: 0;
            z-index: 1000;
        }
        .header .logo { font-weight: 600; font-size: 18px; }
        .main-content {
            margin-left: 250px;
            padding: 100px 40px 40px;
            transition: margin-left 0.3s ease;
        }
        .sidebar.collapsed ~ .main-content { margin-left: 60px; }
        .card-upload {
            background-color: #fff;
            border-radius: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            padding: 30px;
        }
        .upload-box {
            border: 2px dashed #ccc;
            border-radius: 10px;
            text-align: center;
            padding: 40px 20px;
            transition: border-color 0.3s ease;
            cursor: pointer;
        }
        .upload-box:hover { border-color: #2196F3; }
        .upload-box i { font-size: 40px; color: #2196F3; }
        .btn-upload {
            background-color: #2196F3;
            color: #fff;
            border: none;
            border-radius: 8px;
            padding: 10px 25px;
        }
        .btn-upload:hover { background-color: #1976D2; }
        .gallery-img {
            height: 200px;
            object-fit: cover;
            border-radius: 10px;
        }
        .card h6 { font-size: 14px; margin-top: 5px; font-weight: 600; }
        .card small { font-size: 12px; color: #666; }
        .btn-delete {
            background-color: #e74c3c;
            border: none;
            color: white;
            padding: 5px 10px;
            border-radius: 5px;
            font-size: 13px;
            transition: 0.2s;
        }
        .btn-delete:hover {
            background-color: #c0392b;
        }
        @media (max-width: 768px) {
            .main-content { margin-left: 0; padding: 90px 20px; }
        }
    </style>
</head>
<body>

<!-- Header -->
<div class="header">
    <div class="logo d-flex align-items-center">
        <i class="bi bi-images me-3 fs-4"></i> KELOLA GALERI
    </div>
    <div class="icons">
        <i class="bi bi-person-circle"></i>
    </div>
</div>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Konten Utama -->
<div class="main-content">
    <h4 class="fw-bold mb-4">Unggah Foto Baru</h4>

    <?php
    // Proses upload
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
                echo "<div class='alert alert-success'>Foto berhasil diunggah!</div>";
            } else {
                echo "<div class='alert alert-danger'>Gagal mengunggah file!</div>";
            }
        } else {
            echo "<div class='alert alert-warning'>Format file tidak didukung!</div>";
        }
    }
    ?>

    <!-- Form Upload -->
    <div class="card-upload mb-5">
        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Judul Foto</label>
                <input type="text" name="judul" class="form-control" placeholder="Contoh: Kegiatan Outing Class">
            </div>

            <div class="mb-3">
                <label class="form-label">Deskripsi</label>
                <textarea name="deskripsi" class="form-control" rows="3" placeholder="Tambahkan deskripsi singkat..."></textarea>
            </div>

            <div class="mb-3">
                <label class="form-label">Kategori</label>
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

            <div class="mb-3 upload-box" onclick="document.getElementById('fileInput').click();">
                <i class="bi bi-cloud-arrow-up"></i>
                <p>Klik untuk memilih file (JPG, PNG, GIF)</p>
                <input type="file" id="fileInput" name="file" hidden required>
            </div>

            <button type="submit" class="btn btn-upload">Unggah Foto</button>
        </form>
    </div>

    <!-- Daftar Foto -->
    <h4 class="fw-bold mb-3">Daftar Foto Galeri</h4>
    <div class="row">
        <?php
        $result = $conn->query("SELECT * FROM galeri_foto ORDER BY created_at DESC");
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                echo '
                <div class="col-md-3 mb-4 text-center">
                    <div class="card shadow-sm border-0">
                        <img src="../uploads/'.$row['file_path'].'" class="gallery-img card-img-top">
                        <div class="card-body">
                            <h6>'.$row['judul'].'</h6>
                            <small class="text-muted">'.ucwords($row['kategori']).'</small>
                            <div class="mt-2">
                                <a href="galeri.php?delete='.$row['id'].'" class="btn-delete" onclick="return confirm(\'Yakin ingin menghapus foto ini?\');">
                                    <i class="bi bi-trash"></i> Hapus
                                </a>
                            </div>
                        </div>
                    </div>
                </div>';
            }
        } else {
            echo "<p class='text-muted'>Belum ada foto di galeri.</p>";
        }
        ?>
    </div>
</div>

</body>
</html>
