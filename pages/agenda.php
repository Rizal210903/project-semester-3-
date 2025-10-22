<?php
include '../includes/header.php';

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
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
            background: #FFFFFF;
        }
        .agenda-card {
            border: none;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.08);
            margin-bottom: 20px;
        }
        .agenda-img {
            max-width: 100%;
            border-radius: 10px;
        }
        .card-header {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            color: white;
            font-weight: 600;
            border: none;
        }
        .countdown {
            display: flex;
            gap: 10px;
            font-size: 0.9rem;
            color: #007bff;
            font-weight: 600;
        }
        .countdown span {
            background: rgba(255, 255, 255, 0.9);
            padding: 5px 10px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .expired {
            color: #dc3545;
            font-weight: 600;
        }

        @media (max-width: 768px) {
            .card-title { font-size: 1.1rem; }
            .card-text { font-size: 0.9rem; }
            .countdown { flex-wrap: wrap; }
        }
    </style>
</head>
<body>
    <main class="container-fluid p-0">
        <section class="hero-section text-center py-5">
            <div class="container py-5">
                <h1 class="display-4 animate__animated animate__fadeIn" style="color: #000080;">Agenda Kegiatan TK Pertiwi</h1>
                <p class="lead animate__animated animate__fadeIn" style="color: #000000ff; animation-delay: 0.2s;">Informasi kegiatan, acara tahunan, dan jadwal pembagian rapot</p>
            </div>
        </section>

        <section class="agenda-section py-5">
            <div class="container">
                <div class="row">
                    <?php foreach ($agenda as $index => $item): ?>
                        <div class="col-md-4">
                            <div class="card agenda-card animate__animated animate__fadeInUp" style="animation-delay: 0.<?php echo ($index + 1) * 2; ?>s;">
                                <?php if ($item['foto']): ?>
                                    <img src="/project-semester-3-/uploads/<?php echo htmlspecialchars($item['foto']); ?>" class="card-img-top agenda-img" alt="<?php echo htmlspecialchars($item['judul']); ?>">
                                <?php endif; ?>
                                <div class="card-body">
                                    <h5 class="card-title animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo ($index + 1) * 2 + 0.1; ?>s;"><?php echo htmlspecialchars($item['judul']); ?></h5>
                                    <p class="card-text animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo ($index + 1) * 2 + 0.2; ?>s;"><?php echo htmlspecialchars($item['deskripsi']); ?></p>
                                    <p class="card-text animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo ($index + 1) * 2 + 0.3; ?>s;"><small class="text-muted">Tanggal: <?php echo date('d-m-Y', strtotime($item['tanggal'])); ?></small></p>
                                    <p class="card-text animate__animated animate__slideInUp" style="animation-delay: 0.<?php echo ($index + 1) * 2 + 0.4; ?>s;"><small class="text-muted">Tipe: <?php echo ucfirst($item['tipe']); ?></small></p>
                                    <div class="countdown animate__animated animate__fadeIn" id="countdown-<?php echo $item['id']; ?>" style="animation-delay: 0.<?php echo ($index + 1) * 2 + 0.5; ?>s;"></div>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </section>
    </main>

    <footer class="text-center py-3">
        <p class="animate__animated animate__fadeIn" style="animation-delay: 0.2s;">&copy; 2025 TK Pertiwi Semua hak cipta dilindungi</p>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    document.addEventListener('DOMContentLoaded', () => {
        <?php foreach ($agenda as $item): ?>
            // Set tanggal target untuk agenda
            const targetDate<?php echo $item['id']; ?> = new Date('<?php echo $item['tanggal']; ?> 00:00:00').getTime();
            
            // Update countdown setiap detik
            const countdownFunction<?php echo $item['id']; ?> = setInterval(function() {
                const now = new Date().getTime();
                const distance = targetDate<?php echo $item['id']; ?> - now;

                // Hitung hari, jam, menit, dan detik
                const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                // Tampilkan countdown
                const countdownElement = document.getElementById('countdown-<?php echo $item['id']; ?>');
                if (distance > 0) {
                    countdownElement.innerHTML = `
                        <span>${days} Hari</span>
                        <span>${hours} Jam</span>
                        <span>${minutes} Menit</span>
                        <span>${seconds} Detik</span>
                    `;
                } else {
                    clearInterval(countdownFunction<?php echo $item['id']; ?>);
                    countdownElement.innerHTML = '<span class="expired">Acara Telah Berlangsung!</span>';
                }
            }, 1000);
        <?php endforeach; ?>
    });
    </script>
</body>
</html>