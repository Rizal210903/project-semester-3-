<?php
// === KONEKSI DATABASE (sementara statis) ===
$teachers = [
    ["nama" => "Michel dbeb", "kelas" => "Guru kelas A", "foto" => "https://randomuser.me/api/portraits/women/1.jpg", "status" => "Hadir"],
    ["nama" => "Feby dwi", "kelas" => "Guru kelas A", "foto" => "https://randomuser.me/api/portraits/women/2.jpg", "status" => "Izin"],
    ["nama" => "Feby dwi", "kelas" => "Guru kelas A", "foto" => "https://randomuser.me/api/portraits/women/3.jpg", "status" => "Izin"],
    ["nama" => "Chika putri", "kelas" => "Guru kelas B", "foto" => "https://randomuser.me/api/portraits/women/4.jpg", "status" => "Sakit"],
    ["nama" => "Slebewww", "kelas" => "Guru kelas B", "foto" => "https://randomuser.me/api/portraits/women/5.jpg", "status" => "Alfa"],
    ["nama" => "Slebewww", "kelas" => "Guru kelas B", "foto" => "https://randomuser.me/api/portraits/women/6.jpg", "status" => "Alfa"],
];
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
            position: fixed; top: 0; left: 0; right: 0;
            height: 60px; background-color: #007bff;
            display: flex; align-items: center; padding: 0 20px;
            color: #fff; z-index: 1000;
        }
        .menu-toggle { font-size: 24px; cursor: pointer; margin-right: 20px; }
        .main-content { margin-left: 250px; padding: 90px 30px 30px; transition: 0.3s; }
        .main-content.collapsed { margin-left: 60px; }
        .teacher-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: 20px; }
        .teacher-card {
            background-color: #fff; border: 2px solid #d6eaff;
            border-radius: 10px; padding: 15px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1); transition: 0.3s;
        }
        .teacher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .teacher-header img {
            width: 50px; height: 50px; border-radius: 50%; object-fit: cover;
        }
        .status-options { display: flex; gap: 15px; margin-top: 10px; flex-wrap: wrap; }
        .izin-box {
            background: #fff;
            padding: 25px;
            border-radius: 12px;
            border: 2px solid #d6eaff;
            box-shadow: 0 3px 10px rgba(0,0,0,0.1);
            margin-top: 50px;
        }
    </style>
</head>

<body>

<!-- Header -->
<header class="header">
    <div class="menu-toggle">&#9776;</div>
    <div class="title">DATA ABSEN GURU</div>
    <div class="icons ms-auto d-flex gap-4">
    
    </div>
</header>

<!-- Sidebar -->
<?php include 'sidebar.php'; ?>

<!-- Main Content -->
<main class="main-content">

    <h1 class="mb-4">Data Absen Guru</h1>

    <div class="teacher-grid">
        <?php foreach ($teachers as $t): ?>
            <div class="teacher-card">
                <div class="teacher-header">
                    <img src="<?= $t['foto']; ?>">
                    <div>
                        <div class="fw-bold"><?= $t['nama']; ?></div>
                        <div class="text-muted" style="font-size:13px"><?= $t['kelas']; ?></div>
                    </div>
                </div>

                <div class="status-options">
                    <label><input type="radio" name="status_<?= $t['nama']; ?>"> Hadir</label>
                    <label><input type="radio" name="status_<?= $t['nama']; ?>"> Izin</label>
                    <label><input type="radio" name="status_<?= $t['nama']; ?>"> Sakit</label>
                    <label><input type="radio" name="status_<?= $t['nama']; ?>"> Alfa</label>
                </div>

                <div class="note-box mt-2">
                    <input type="text" class="form-control" placeholder="Catatan..">
                </div>
            </div>
        <?php endforeach; ?>
    </div>


    <!-- ============================= -->
    <!-- === FORM IZIN DI BAWAHNYA === -->
    <!-- ============================= -->

    <div class="izin-box mt-5">
        <h3 class="mb-3"><i class="bi bi-pencil-square"></i> Input Izin Guru</h3>

        <form method="POST" action="simpan_izin.php">

            <div class="row">
                <div class="col-md-6 mb-3">
                    <label class="form-label">Nama Guru</label>
                    <select name="nama" class="form-select" required>
                        <option value="">-- Pilih Guru --</option>
                        <?php foreach($teachers as $t): ?>
                            <option value="<?= $t['nama']; ?>"><?= $t['nama']; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Tanggal</label>
                    <input type="date" name="tanggal" class="form-control" required>
                </div>

                <div class="col-md-6 mb-3">
                    <label class="form-label">Jenis Izin</label>
                    <select name="jenis" class="form-select" required>
                        <option value="Izin">Izin</option>
                        <option value="Sakit">Sakit</option>
                        <option value="Alfa">Alfa</option>
                    </select>
                </div>

                <div class="col-md-12 mb-3">
                    <label class="form-label">Catatan</label>
                    <textarea name="catatan" class="form-control" rows="4" placeholder="Tambahkan catatan jika diperlukan..."></textarea>
                </div>
            </div>

            <button class="btn btn-primary px-4 py-2">
                <i class="bi bi-save"></i> Simpan Izin
            </button>

        </form>
    </div>

</main>

<script>
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
