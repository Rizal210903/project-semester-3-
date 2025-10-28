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

        .menu-toggle {
            font-size: 24px;
            cursor: pointer;
            margin-right: 20px;
        }

        .title {
            font-size: 18px;
            font-weight: 600;
        }

        .icons {
            margin-left: auto;
            display: flex;
            align-items: center;
            gap: 20px;
        }

        /* Layout */
        .main-content {
            margin-left: 250px;
            padding: 90px 30px 30px;
            transition: margin-left 0.3s;
        }

        .main-content.collapsed {
            margin-left: 60px;
        }

        /* Search box */
        .search-box {
            width: 100%;
            max-width: 400px;
            background-color: #fff;
            border: 1px solid #ccc;
            border-radius: 8px;
            padding: 10px 15px;
            font-size: 14px;
            margin-bottom: 25px;
            display: flex;
            align-items: center;
            gap: 8px;
        }

        .search-box input {
            border: none;
            outline: none;
            flex: 1;
        }

        /* Card Guru */
        .teacher-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(250px, 1fr));
            gap: 20px;
        }

        .teacher-card {
            background-color: #fff;
            border: 2px solid #d6eaff;
            border-radius: 10px;
            padding: 15px;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            box-shadow: 0 2px 6px rgba(0,0,0,0.1);
            transition: 0.3s;
        }

        .teacher-card:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }

        .teacher-header {
            display: flex;
            align-items: center;
            gap: 10px;
        }

        .teacher-header img {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
        }

        .teacher-info {
            display: flex;
            flex-direction: column;
        }

        .teacher-name {
            font-weight: 600;
            font-size: 15px;
            margin-bottom: 3px;
        }

        .teacher-class {
            font-size: 13px;
            color: #777;
        }

        /* Status radio buttons */
        .status-options {
            display: flex;
            gap: 15px;
            margin-top: 10px;
            flex-wrap: wrap;
        }

        .status-options label {
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 5px;
            cursor: pointer;
        }

        input[type="radio"] {
            appearance: none;
            width: 15px;
            height: 15px;
            border-radius: 50%;
            border: 2px solid #ccc;
            outline: none;
            cursor: pointer;
        }

        input[type="radio"]:checked#hadir {
            border-color: #2ecc71;
            background-color: #2ecc71;
        }

        input[type="radio"]:checked#izin {
            border-color: #f1c40f;
            background-color: #f1c40f;
        }

        input[type="radio"]:checked#sakit {
            border-color: #3498db;
            background-color: #3498db;
        }

        input[type="radio"]:checked#alfa {
            border-color: #e74c3c;
            background-color: #e74c3c;
        }

        /* Catatan */
        .note-box {
            margin-top: 10px;
        }

        .note-box input {
            width: 100%;
            border: 1px solid #ccc;
            border-radius: 6px;
            padding: 6px 8px;
            font-size: 13px;
        }

        @media (max-width: 768px) {
            .main-content {
                margin-left: 0;
                padding: 80px 15px;
            }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="header">
        <div class="menu-toggle">&#9776;</div>
        <div class="title">DATA ABSEN GURU</div>
        <div class="icons">
            <div><i class="bi bi-envelope"></i></div>
            <div><i class="bi bi-bell"></i></div>
            <div><i class="bi bi-person-circle"></i></div>
        </div>
    </header>

    <!-- Sidebar -->
    <?php include 'sidebar.php'; ?>

    <!-- Main Content -->
    <main class="main-content">
        <h1 class="mb-4">Data Absen Guru</h1>

        <div class="search-box">
            <i class="bi bi-search"></i>
            <input type="text" placeholder="Cari nama guru...">
        </div>

        <div class="teacher-grid">
            <?php foreach ($teachers as $t): ?>
                <div class="teacher-card">
                    <div class="teacher-header">
                        <img src="<?= htmlspecialchars($t['foto']); ?>" alt="Foto Guru">
                        <div class="teacher-info">
                            <div class="teacher-name"><?= htmlspecialchars($t['nama']); ?></div>
                            <div class="teacher-class"><?= htmlspecialchars($t['kelas']); ?></div>
                        </div>
                    </div>

                    <div class="status-options">
                        <label><input type="radio" name="status_<?= $t['nama']; ?>" id="hadir" <?= $t['status']=="Hadir"?"checked":""; ?>> Hadir</label>
                        <label><input type="radio" name="status_<?= $t['nama']; ?>" id="izin" <?= $t['status']=="Izin"?"checked":""; ?>> Izin</label>
                        <label><input type="radio" name="status_<?= $t['nama']; ?>" id="sakit" <?= $t['status']=="Sakit"?"checked":""; ?>> Sakit</label>
                        <label><input type="radio" name="status_<?= $t['nama']; ?>" id="alfa" <?= $t['status']=="Alfa"?"checked":""; ?>> Alfa</label>
                    </div>

                    <div class="note-box">
                        <input type="text" placeholder="Catatan..">
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
