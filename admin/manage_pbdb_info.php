<?php
// === KONEKSI DATABASE ===
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
        echo "<script>alert('Data PPDB berhasil diperbarui!'); window.location.href='manage_pbdb_info.php';</script>";
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
    <title>Kelola Informasi PPDB</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons/font/bootstrap-icons.css" rel="stylesheet">

    <!-- STYLE BARU AGAR MIRIP HALAMAN GALERI -->
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #DBF4FF; /* biru muda */
            margin: 0;
            overflow-x: hidden;
        }

        /* Header biru */
        .header {
            background-color: #2196F3;
            color: white;
            height: 60px;
            display: flex;
            align-items: center;
            padding: 0 25px;
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 999;
            font-size: 20px;
            font-weight: 600;
        }

        /* Wrapper Konten */
        .content-wrapper {
            margin-left: 250px;
            padding: 100px 40px 40px;
        }

        /* Styling Card */
        .card-custom {
            background: white;
            border-radius: 18px;
            padding: 25px;
            box-shadow: 0 3px 8px rgba(0,0,0,0.1);
        }

        /* Box Input Jadwal */
        .jadwal-item {
            background: #fff;
            border-radius: 12px;
            padding: 20px;
            border: 1px solid #dfe4ea;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .remove-jadwal {
            height: 45px;
        }

        @media(max-width: 768px){
            .content-wrapper{
                margin-left: 0;
                padding: 90px 20px;
            }
        }
    </style>
</head>

<body>

<!-- SIDEBAR -->
<?php include 'sidebar.php'; ?>

<!-- HEADER -->
<div class="header">
    <i class="bi bi-clipboard-check me-3"></i>
    Kelola Informasi PPDB
</div>

<!-- MAIN CONTENT -->
<div class="content-wrapper">

    <div class="card-custom">

        <form method="POST">

            <!-- Input Jadwal -->
            <h4 class="fw-bold text-primary mb-3">Jadwal PPDB</h4>

            <div id="jadwal-container">

                <?php if (!empty($jadwal)) { ?>
                    <?php foreach ($jadwal as $item) { ?>
                        <div class="row mb-3 jadwal-item">

                            <div class="col-md-5">
                                <label class="form-label fw-semibold">Nama Jadwal</label>
                                <input type="text" class="form-control" name="jadwal_nama[]" value="<?= $item['nama'] ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Mulai</label>
                                <input type="date" class="form-control" name="jadwal_mulai[]" value="<?= $item['mulai'] ?>" required>
                            </div>

                            <div class="col-md-3">
                                <label class="form-label fw-semibold">Tanggal Selesai</label>
                                <input type="date" class="form-control" name="jadwal_selesai[]" value="<?= $item['selesai'] ?>" required>
                            </div>

                            <div class="col-md-1 d-flex align-items-end">
                                <button type="button" class="btn btn-danger w-100 remove-jadwal">
                                    <i class="bi bi-x"></i>
                                </button>
                            </div>

                        </div>
                    <?php } ?>
                <?php } else { ?>

                    <div class="row mb-3 jadwal-item">

                        <div class="col-md-5">
                            <label class="form-label fw-semibold">Nama Jadwal</label>
                            <input type="text" class="form-control" name="jadwal_nama[]" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Mulai</label>
                            <input type="date" class="form-control" name="jadwal_mulai[]" required>
                        </div>

                        <div class="col-md-3">
                            <label class="form-label fw-semibold">Tanggal Selesai</label>
                            <input type="date" class="form-control" name="jadwal_selesai[]" required>
                        </div>

                        <div class="col-md-1 d-flex align-items-end">
                            <button type="button" class="btn btn-danger w-100 remove-jadwal"><i class="bi bi-x"></i></button>
                        </div>

                    </div>

                <?php } ?>

            </div>

            <button type="button" id="add-jadwal" class="btn btn-success mt-2 mb-4">
                <i class="bi bi-plus-lg me-2"></i>Tambah Jadwal
            </button>

            <!-- Persyaratan -->
            <h4 class="fw-bold text-primary mt-4">Persyaratan PPDB</h4>
            <textarea name="syarat_ppdb" class="form-control" rows="5" required><?= $syarat ?></textarea>

            <button type="submit" name="update_ppdb" class="btn btn-primary mt-4 px-4 py-2">
                <i class="bi bi-save me-2"></i> Simpan Perubahan
            </button>

        </form>

    </div>
</div>

<script>
// Tambah Jadwal
document.getElementById("add-jadwal").addEventListener("click", function () {
    let container = document.getElementById("jadwal-container");

    let block = document.createElement("div");
    block.classList.add("row", "mb-3", "jadwal-item");

    block.innerHTML = `
        <div class="col-md-5">
            <label class="form-label fw-semibold">Nama Jadwal</label>
            <input type="text" class="form-control" name="jadwal_nama[]" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Mulai</label>
            <input type="date" class="form-control" name="jadwal_mulai[]" required>
        </div>
        <div class="col-md-3">
            <label class="form-label fw-semibold">Tanggal Selesai</label>
            <input type="date" class="form-control" name="jadwal_selesai[]" required>
        </div>
        <div class="col-md-1 d-flex align-items-end">
            <button type="button" class="btn btn-danger w-100 remove-jadwal"><i class="bi bi-x"></i></button>
        </div>`;

    container.appendChild(block);
});

// Hapus Jadwal
document.addEventListener("click", function (e) {
    if (e.target.closest(".remove-jadwal")) {
        if (document.querySelectorAll(".jadwal-item").length > 1) {
            e.target.closest(".jadwal-item").remove();
        }
    }
});
</script>

</body>
</html>
