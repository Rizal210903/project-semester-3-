<?php
session_start();
include '../includes/config.php';

// =======================
// AMBIL DATA DARI DATABASE
// =======================
$query = $conn->query("SELECT * FROM ppdb_info WHERE id = 1 LIMIT 1");

if ($query->num_rows > 0) {
    $row = $query->fetch_assoc();
    $info = json_decode($row['content'], true);
} else {
    $info = [
        "jadwal" => [],
        "syarat" => ""
    ];
}

$jadwal = $info["jadwal"] ?? [];
$syarat = $info["syarat"] ?? "";

// =======================
// PROSES UPDATE DATA
// =======================
if (isset($_POST['update_ppdb'])) {

    $names   = $_POST['jadwal_nama'] ?? [];
    $mulai   = $_POST['jadwal_mulai'] ?? [];
    $selesai = $_POST['jadwal_selesai'] ?? [];

    $jadwal_data = [];

    for ($i = 0; $i < count($names); $i++) {
        if (!empty($names[$i])) {
            $jadwal_data[] = [
                "nama"    => $names[$i],
                "mulai"   => $mulai[$i],
                "selesai" => $selesai[$i]
            ];
        }
    }

    $syarat_input = $_POST['syarat_ppdb'] ?? "";

    $content_json = json_encode([
        "jadwal" => $jadwal_data,
        "syarat" => $syarat_input
    ], JSON_PRETTY_PRINT);

    $stmt = $conn->prepare("UPDATE ppdb_info SET content = ? WHERE id = 1");
    $stmt->bind_param("s", $content_json);

    if ($stmt->execute()) {
        echo "<script>alert('Data PPDB berhasil diperbarui!'); window.location.href='manage_ppdb_info.php';</script>";
        exit;
    } else {
        echo "Error: " . $stmt->error;
    }
}
?>
<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Kelola Informasi PPDB - TK Pertiwi</title>

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

        /* Form Container */
        .form-container {
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

        /* Jadwal Item */
        .jadwal-container {
            display: flex;
            flex-direction: column;
            gap: 20px;
            margin-bottom: 20px;
        }

        .jadwal-item {
            background: #f8f9fa;
            border: 2px solid #e7f3ff;
            border-radius: 12px;
            padding: 20px;
            transition: all 0.3s;
            position: relative;
        }

        .jadwal-item:hover {
            border-color: #007bff;
            box-shadow: 0 4px 12px rgba(0,123,255,0.15);
        }

        .jadwal-item::before {
            content: '';
            position: absolute;
            left: 0;
            top: 0;
            height: 100%;
            width: 5px;
            background: linear-gradient(180deg, #007bff 0%, #0056b3 100%);
            border-radius: 12px 0 0 12px;
        }

        .form-label {
            font-weight: 600;
            color: #333;
            margin-bottom: 8px;
            font-size: 13px;
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

        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }

        /* Buttons */
        .btn-add {
            background: linear-gradient(135deg, #28a745 0%, #20c997 100%);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            color: white;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-add:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(40, 167, 69, 0.4);
        }

        .btn-remove {
            background: linear-gradient(135deg, #dc3545 0%, #c82333 100%);
            border: none;
            border-radius: 8px;
            padding: 10px;
            color: white;
            width: 100%;
            height: 45px;
            font-size: 20px;
            transition: all 0.3s;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .btn-remove:hover {
            transform: scale(1.05);
            box-shadow: 0 4px 12px rgba(220, 53, 69, 0.4);
        }

        .btn-save {
            background: linear-gradient(135deg, #007bff 0%, #0056b3 100%);
            border: none;
            border-radius: 10px;
            padding: 14px 40px;
            color: white;
            font-weight: 600;
            font-size: 16px;
            transition: all 0.3s;
        }

        .btn-save:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.4);
        }

        /* Info Box */
        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #007bff;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 25px;
        }

        .info-box i {
            color: #007bff;
            font-size: 20px;
            margin-right: 10px;
        }

        .info-box p {
            margin: 0;
            color: #666;
            font-size: 14px;
        }

        /* Responsive */
        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 90px 15px 30px;
            }

            .jadwal-item .row > div:last-child {
                margin-top: 15px;
            }

            .btn-remove {
                width: 100%;
            }
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">KELOLA INFORMASI PPDB</div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <!-- Page Header -->
    <div class="page-header">
        <h1><i class="bi bi-clipboard-check"></i> Kelola Informasi PPDB</h1>
        <p>Manajemen jadwal dan persyaratan penerimaan peserta didik baru</p>
    </div>

    <!-- Form Container -->
    <div class="form-container">
        
        <form method="POST">

            <!-- Info Box -->
            <div class="info-box">
                <i class="bi bi-info-circle-fill"></i>
                <strong>Informasi:</strong>
                <p class="mt-1">Atur jadwal dan persyaratan PPDB yang akan ditampilkan di halaman publik. Pastikan semua informasi terisi dengan benar.</p>
            </div>

            <!-- Jadwal Section -->
            <div class="section-title">
                <i class="bi bi-calendar-range-fill"></i>
                Jadwal PPDB
            </div>

            <div class="jadwal-container" id="jadwal-container">

                <?php if (!empty($jadwal)) { ?>
                    <?php foreach ($jadwal as $item) { ?>
                        <div class="jadwal-item">
                            <div class="row g-3">
                                <div class="col-md-5">
                                    <label class="form-label">
                                        <i class="bi bi-tag text-primary"></i> Nama Jadwal
                                    </label>
                                    <input type="text" class="form-control" name="jadwal_nama[]" 
                                           value="<?= htmlspecialchars($item['nama']) ?>" 
                                           placeholder="Contoh: Pendaftaran Gelombang 1" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-plus text-success"></i> Tanggal Mulai
                                    </label>
                                    <input type="date" class="form-control" name="jadwal_mulai[]" 
                                           value="<?= $item['mulai'] ?>" required>
                                </div>

                                <div class="col-md-3">
                                    <label class="form-label">
                                        <i class="bi bi-calendar-x text-danger"></i> Tanggal Selesai
                                    </label>
                                    <input type="date" class="form-control" name="jadwal_selesai[]" 
                                           value="<?= $item['selesai'] ?>" required>
                                </div>

                                <div class="col-md-1 d-flex align-items-end">
                                    <button type="button" class="btn-remove remove-jadwal" title="Hapus Jadwal">
                                        <i class="bi bi-trash"></i>
                                    </button>
                                </div>
                            </div>
                        </div>
                    <?php } ?>
                <?php } else { ?>
                    <!-- Default Empty Form -->
                    <div class="jadwal-item">
                        <div class="row g-3">
                            <div class="col-md-5">
                                <label class="form-label">
                                    <i class="bi bi-tag text-primary"></i> Nama Jadwal
                                </label>
                                <input type="text" class="form-control" name="jadwal_nama[]" 
                                       placeholder="Contoh: Pendaftaran Gelombang 1" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-plus text-success"></i> Tanggal Mulai
                                </label>
                                <input type="date" class="form-control" name="jadwal_mulai[]" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label">
                                    <i class="bi bi-calendar-x text-danger"></i> Tanggal Selesai
                                </label>
                                <input type="date" class="form-control" name="jadwal_selesai[]" required>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn-remove remove-jadwal" title="Hapus Jadwal">
                                    <i class="bi bi-trash"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                <?php } ?>

            </div>

            <button type="button" id="add-jadwal" class="btn-add">
                <i class="bi bi-plus-circle me-2"></i>Tambah Jadwal Baru
            </button>

            <!-- Persyaratan Section -->
            <div class="section-title mt-5">
                <i class="bi bi-list-check"></i>
                Persyaratan PPDB
            </div>

            <label class="form-label">
                <i class="bi bi-file-text text-primary"></i> Deskripsi Persyaratan
            </label>
            <textarea name="syarat_ppdb" class="form-control" 
                      placeholder="Tuliskan persyaratan pendaftaran di sini..."
                      required><?= htmlspecialchars($syarat) ?></textarea>

            <div class="mt-4 d-flex gap-3">
                <button type="submit" name="update_ppdb" class="btn-save">
                    <i class="bi bi-save me-2"></i> Simpan Perubahan
                </button>
                <button type="button" onclick="window.location.reload()" class="btn btn-secondary" style="border-radius: 10px; padding: 14px 30px;">
                    <i class="bi bi-arrow-clockwise me-2"></i> Reset
                </button>
            </div>

        </form>

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

    // Tambah Jadwal
    document.getElementById("add-jadwal").addEventListener("click", function () {
        let container = document.getElementById("jadwal-container");

        let block = document.createElement("div");
        block.classList.add("jadwal-item");

        block.innerHTML = `
            <div class="row g-3">
                <div class="col-md-5">
                    <label class="form-label">
                        <i class="bi bi-tag text-primary"></i> Nama Jadwal
                    </label>
                    <input type="text" class="form-control" name="jadwal_nama[]" 
                           placeholder="Contoh: Pendaftaran Gelombang 1" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="bi bi-calendar-plus text-success"></i> Tanggal Mulai
                    </label>
                    <input type="date" class="form-control" name="jadwal_mulai[]" required>
                </div>
                <div class="col-md-3">
                    <label class="form-label">
                        <i class="bi bi-calendar-x text-danger"></i> Tanggal Selesai
                    </label>
                    <input type="date" class="form-control" name="jadwal_selesai[]" required>
                </div>
                <div class="col-md-1 d-flex align-items-end">
                    <button type="button" class="btn-remove remove-jadwal" title="Hapus Jadwal">
                        <i class="bi bi-trash"></i>
                    </button>
                </div>
            </div>`;

        container.appendChild(block);

        // Animate new item
        block.style.opacity = '0';
        block.style.transform = 'translateY(-20px)';
        setTimeout(() => {
            block.style.transition = 'all 0.3s';
            block.style.opacity = '1';
            block.style.transform = 'translateY(0)';
        }, 10);
    });

    // Hapus Jadwal
    document.addEventListener("click", function (e) {
        if (e.target.closest(".remove-jadwal")) {
            const items = document.querySelectorAll(".jadwal-item");
            if (items.length > 1) {
                const item = e.target.closest(".jadwal-item");
                item.style.opacity = '0';
                item.style.transform = 'translateX(50px)';
                setTimeout(() => {
                    item.remove();
                }, 300);
            } else {
                alert("Minimal harus ada 1 jadwal!");
            }
        }
    });
</script>

</body>
</html>