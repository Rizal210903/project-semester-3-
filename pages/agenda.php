<?php
include '../includes/header.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi gagal: " . $e->getMessage());
}

$stmt = $pdo->query("SELECT * FROM agenda_kegiatan ORDER BY tanggal DESC");
$agenda = $stmt->fetchAll(PDO::FETCH_ASSOC);
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Agenda Kegiatan - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f4f9ff;
            overflow-x: hidden;
        }

        /* ====== GLOBAL SECTION STYLE (SAMA SEPERTI profil.php) ====== */
        .section-block {
            padding: 80px 0;
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
            position: relative;
        }

        .section-title {
            color: #000080;
            font-weight: 600;
            margin-bottom: 2rem;
        }

        /* ====== HERO SECTION ====== */
        .hero-section {
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        }

        .hero-section h1 {
            color: #000080;
            font-weight: 700;
        }

        .hero-section p {
            color: #000;
        }

        /* ====== AGENDA CARD ====== */
        .agenda-card {
            border: none;
            border-radius: 15px;
            background: #ffffff;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
        }

        .agenda-card:hover {
            transform: translateY(-6px);
            box-shadow: 0 10px 25px rgba(0,0,0,0.15);
        }

        .agenda-img {
            border-radius: 15px 15px 0 0;
            object-fit: cover;
            height: 220px;
            width: 100%;
        }

        .card-body {
            padding: 1.5rem;
        }

        .card-title {
            color: #000080;
            font-weight: 600;
        }

        .countdown {
            display: flex;
            gap: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            flex-wrap: wrap;
        }

        .countdown span {
            background: rgba(33,150,243,0.1);
            color: #007bff;
            padding: 5px 10px;
            border-radius: 6px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.05);
        }

        .expired {
            color: #dc3545;
            font-weight: 600;
        }

        /* ====== DIVIDERS (SAMA DENGAN profil.php) ====== */
        .divider {
            height: 100px;
            position: relative;
            overflow: hidden;
            background-attachment: fixed;
        }

        .divider-wave {
            background: url('https://www.svgrepo.com/show/492220/wave-top.svg') no-repeat center;
            background-size: cover;
        }

        .divider-wave2 {
            background: url('https://www.svgrepo.com/show/491975/wave.svg') no-repeat center;
            background-size: cover;
        }

        /* ====== RESPONSIVE ====== */
        @media (max-width: 768px) {
            .card-title { font-size: 1.1rem; }
            .card-text { font-size: 0.9rem; }
        }
    </style>
</head>

<body>
    <main class="container-fluid p-0">

        <!-- ====== HERO ====== -->
        <section class="hero-section text-center py-5">
            <div class="container py-5">
                <h1 class="display-5 animate__animated animate__fadeInDown">Agenda Kegiatan TK Pertiwi</h1>
                <p class="lead animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">Informasi kegiatan, acara tahunan, dan jadwal penting sekolah</p>
            </div>
            <div class="divider divider-wave"></div>
        </section>

        <!-- ====== AGENDA LIST ====== -->
        <section class="section-block agenda-section">
            <div class="container">
                <h2 class="section-title text-center animate__animated animate__fadeInDown">Agenda & Kegiatan</h2>
                <div class="row g-4">
                    <?php if (!empty($agenda)): ?>
                        <?php foreach ($agenda as $index => $item): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card agenda-card animate__animated animate__fadeInUp" style="animation-delay: <?= ($index * 0.2) ?>s;">
                                    <?php if (!empty($item['foto'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($item['foto']); ?>" class="card-img-top agenda-img" alt="<?= htmlspecialchars($item['judul']); ?>" loading="lazy"
                                             onerror="this.src='https://via.placeholder.com/400x250?text=Agenda+TK+Pertiwi'; this.onerror=null;">
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($item['judul']); ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($item['deskripsi']); ?></p>
                                        <p class="card-text"><small class="text-muted">Tanggal: <?= date('d-m-Y', strtotime($item['tanggal'])); ?></small></p>
                                        <p class="card-text"><small class="text-muted">Tipe: <?= ucfirst(htmlspecialchars($item['tipe'])); ?></small></p>
                                        <div class="countdown" id="countdown-<?= $item['id']; ?>"></div>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <p class="text-center text-muted">Belum ada agenda kegiatan yang tersedia.</p>
                    <?php endif; ?>
                </div>
            </div>
            <div class="divider divider-wave2"></div>
        </section>
    </main>

    <footer class="text-center py-4" style="background:#e7f1ff;">
        <p class="mb-0 text-muted">&copy; 2025 TK Pertiwi. Semua hak cipta dilindungi.</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php foreach ($agenda as $item): ?>
            const targetDate<?= $item['id']; ?> = new Date('<?= $item['tanggal']; ?> 00:00:00').getTime();
            const countdownElement = document.getElementById('countdown-<?= $item['id']; ?>');

            const interval = setInterval(() => {
                const now = new Date().getTime();
                const distance = targetDate<?= $item['id']; ?> - now;

                if (distance > 0) {
                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    countdownElement.innerHTML = `
                        <span>${days} Hari</span>
                        <span>${hours} Jam</span>
                        <span>${minutes} Menit</span>
                        <span>${seconds} Detik</span>
                    `;
                } else {
                    clearInterval(interval);
                    countdownElement.innerHTML = '<span class="expired">Acara Telah Berlangsung!</span>';
                }
            }, 1000);
        <?php endforeach; ?>
    });
    </script>
</body>
</html>
