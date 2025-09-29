<?php
// Ambil nama dari parameter
session_start();
$name = $_SESSION['user_name'] ?? $_GET['name'] ?? 'Pengguna';
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Terima Kasih - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #E0F7FA, #A7FFEB);
            font-family: 'Arial', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            overflow: hidden;
            position: relative;
        }
        .thank-you-container {
            text-align: center;
            padding: 40px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            max-width: 650px;
            position: relative;
            overflow: hidden;
            animation: float 4s ease-in-out infinite;
        }
        .message-card {
            background: linear-gradient(135deg, #FFFFFF, #F0F8FF);
            padding: 25px;
            border-radius: 15px;
            border: 2px solid #1E90FF;
            box-shadow: inset 0 0 10px rgba(30, 144, 255, 0.2);
            margin: 20px 0;
            animation: pulseGlow 2s infinite alternate;
        }
        .thank-you-container h2 {
            color: #1E90FF;
            font-size: 2.8rem;
            margin-bottom: 15px;
            text-shadow: 3px 3px 6px rgba(30, 144, 255, 0.4);
            animation: bounceIn 1s ease-out;
        }
        .thank-you-container p {
            color: #333;
            font-size: 1.2rem;
            margin-bottom: 20px;
            line-height: 1.6;
            animation: fadeInUp 1.5s ease-out;
        }
        .confetti {
            position: absolute;
            width: 12px;
            height: 12px;
            background: #FF6B6B;
            border-radius: 50%;
            animation: fall 3s infinite linear;
        }
        .confetti:nth-child(2) { background: #4ECDC4; left: 20%; animation-delay: 0.5s; }
        .confetti:nth-child(3) { background: #FF9F55; left: 40%; animation-delay: 1s; }
        .confetti:nth-child(4) { background: #45B7D1; left: 60%; animation-delay: 1.5s; }
        .confetti:nth-child(5) { background: #FFD700; left: 80%; animation-delay: 2s; }
        .star {
            position: absolute;
            width: 6px;
            height: 6px;
            background: #FFD700;
            clip-path: polygon(50% 0%, 61% 35%, 98% 35%, 68% 57%, 79% 91%, 50% 70%, 21% 91%, 32% 57%, 2% 35%, 39% 35%);
            animation: twinkle 2s infinite alternate;
        }
        .star:nth-child(1) { top: 10%; left: 10%; }
        .star:nth-child(2) { top: 20%; right: 15%; animation-delay: 0.3s; }
        .star:nth-child(3) { bottom: 15%; left: 20%; animation-delay: 0.6s; }
        .btn-primary {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            border: none;
            padding: 14px 35px;
            font-size: 1.2rem;
            border-radius: 25px;
            transition: all 0.4s ease;
            text-transform: uppercase;
            letter-spacing: 1px;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #104E8B, #009ACD);
            transform: scale(1.1) rotate(2deg);
            box-shadow: 0 5px 15px rgba(0, 183, 235, 0.5);
        }
        @keyframes float {
            0%, 100% { transform: translateY(0); }
            50% { transform: translateY(-20px); }
        }
        @keyframes pulseGlow {
            from { box-shadow: inset 0 0 5px rgba(30, 144, 255, 0.2); }
            to { box-shadow: inset 0 0 20px rgba(30, 144, 255, 0.5); }
        }
        @keyframes bounceIn {
            from { transform: scale(0.8); opacity: 0; }
            to { transform: scale(1); opacity: 1; }
        }
        @keyframes fadeInUp {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        @keyframes fall {
            0% { top: -10%; opacity: 1; }
            100% { top: 100%; opacity: 0; }
        }
        @keyframes twinkle {
            from { opacity: 0.4; }
            to { opacity: 1; }
        }
    </style>
</head>
<body>
    <div class="thank-you-container">
        <?php
        if (isset($_GET['success']) && $_GET['success'] == 1) {
            echo '<div class="message-card">';
            echo "<h2 class='animate__animated animate__bounceIn'>Terima Kasih, $name!</h2>";
            echo "<p class='animate__animated animate__fadeInUp' style='animation-delay: 0.5s;'>Pendaftaran Anda telah berhasil. Data telah disimpan pada " . date('d-m-Y H:i:s') . ".</p>";
            echo "<p class='animate__animated animate__fadeInUp' style='animation-delay: 1s;'>Hubungi kami di <a href='https://wa.me/6281234567890' class='text-success fw-bold' target='_blank'>+6281234567890</a> jika ada pertanyaan.</p>";
            echo '</div>';
            echo '<a href="/tk-pertiwi/" class="btn btn-primary animate__animated animate__pulse" style="animation-delay: 1.5s;">Kembali ke Beranda</a>';
            // Tambahin confetti dan star dekorasi
            for ($i = 1; $i <= 5; $i++) {
                echo "<div class='confetti' style='left: " . ($i * 15) . "%; animation-duration: " . (2 + $i * 0.5) . "s;'></div>";
            }
            for ($i = 1; $i <= 3; $i++) {
                echo "<div class='star'></div>";
            }
        } else {
            echo '<div class="message-card">';
            echo '<h2 class="animate__animated animate__shakeX">Halaman Tidak Valid</h2>';
            echo '<p class="animate__animated animate__fadeIn">Sepertinya Anda tidak datang dari proses pendaftaran. <a href="/tk-pertiwi/pages/pendaftaran.php" class="text-primary fw-bold">Kembali ke Form Pendaftaran</a></p>';
            echo '</div>';
        }
        ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
</body>
</html>