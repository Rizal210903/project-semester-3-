<?php
include '../includes/header.php';
include '../includes/config.php';

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

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
            overflow-x: hidden;
        }
         
        /* ===== HERO SECTION ===== */
        .hero-section {
            background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        }
        .hero-section h1 {
            color: #000080;
            font-weight: 700;
        }

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

        .card-title {
            color: #000080;
            font-weight: 600;
        }

        .countdown {
            display: flex;
            justify-content: start;
            gap: 8px;
            margin-top: 10px;
            font-size: 0.9rem;
            font-weight: 600;
            flex-wrap: wrap;
        }
        .countdown span {
            background: rgba(0, 123, 255, 0.1);
            color: #007bff;
            padding: 6px 10px;
            border-radius: 8px;
            box-shadow: 0 2px 6px rgba(0,0,0,0.05);
        }
        .expired {
            color: #dc3545;
            font-weight: 700;
        }

        .divider {
            height: 100px;
            position: relative;
            overflow: hidden;
        }
        .divider-wave {
            background: url('https://www.svgrepo.com/show/492220/wave-top.svg') no-repeat center;
            background-size: cover;
        }
        .divider-wave2 {
            background: url('https://www.svgrepo.com/show/491975/wave.svg') no-repeat center;
            background-size: cover;
        }

        @media (max-width: 768px) {
            .card-title { font-size: 1.1rem; }
            .card-text { font-size: 0.9rem; }
        }
    </style>
</head>

<body>
    <main class="container-fluid p-0">

        <!-- ===== HERO ===== -->
        <section class="hero-section text-center py-5">
            <div class="container py-5">
                <h1 class="display-5 animate__animated animate__fadeInDown">Agenda Kegiatan TK Pertiwi</h1>
                <p class="lead animate__animated animate__fadeInUp" style="animation-delay: 0.2s;">
                    Informasi kegiatan, acara tahunan, dan jadwal penting sekolah
                </p>
            </div>
            <div class="divider divider-wave"></div>
        </section>

        <!-- ===== AGENDA LIST ===== -->
        <section class="section-block agenda-section">
            <div class="container">
                <h2 class="section-title text-center animate__animated animate__fadeInDown">Agenda & Kegiatan</h2>
                <div class="row g-4">
                    <?php if (!empty($agenda)): ?>
                        <?php foreach ($agenda as $index => $item): ?>
                            <div class="col-md-4 col-sm-6">
                                <div class="card agenda-card animate__animated animate__fadeInUp" style="animation-delay: <?= ($index * 0.2) ?>s;">
                                    <?php if (!empty($item['foto'])): ?>
                                        <img src="../uploads/<?= htmlspecialchars($item['foto']); ?>"
                                             class="card-img-top agenda-img"
                                             alt="<?= htmlspecialchars($item['judul']); ?>"
                                             loading="lazy"
                                             onerror="this.src='https://via.placeholder.com/400x250?text=Agenda+TK+Pertiwi'; this.onerror=null;">
                                    <?php endif; ?>

                                    <div class="card-body">
                                        <h5 class="card-title"><?= htmlspecialchars($item['judul']); ?></h5>
                                        <p class="card-text"><?= htmlspecialchars($item['deskripsi']); ?></p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-calendar-event"></i>
                                                <?= date('d F Y', strtotime($item['tanggal'])); ?>
                                                <?php if (!empty($item['waktu'])): ?>
                                                    <br><i class="bi bi-clock"></i> <?= date('H:i', strtotime($item['waktu'])); ?>
                                                <?php endif; ?> 
                                                
                                                </small>
                                        </p>
                                        <p class="card-text">
                                            <small class="text-muted">
                                                <i class="bi bi-tag"></i> <?= ucfirst(htmlspecialchars($item['tipe'])); ?>
                                            </small>
                                        </p>

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

    <script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php foreach ($agenda as $item): 
            $tanggal = $item['tanggal'];
            $waktu = !empty($item['waktu']) ? $item['waktu'] : "00:00:00";
            $datetime = $tanggal . " " . $waktu;
        ?>
            const targetDate<?= $item['id']; ?> = new Date('<?= $datetime; ?>').getTime();
            const countdownElement = document.getElementById('countdown-<?= $item['id']; ?>');

            if (!countdownElement) return;

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
