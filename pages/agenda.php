<?php include '../includes/header.php'; ?>

<main class="container-fluid p-0">
    <!-- Header Agenda -->
    <section class="hero-section text-center py-5" style="background: linear-gradient(135deg, #FFD700, #FF6B6B);">
        <div class="container py-5">
            <h1 class="display-4 text-white animate__animated animate__bounceIn" style="text-shadow: 2px 2px #333;">Agenda Kegiatan</h1>
        </div>
    </section>

    <!-- Countdown Timer -->
    <section class="countdown-section py-5" style="background: #FFF3E0;">
        <div class="container text-center">
            <h2 class="mb-4 text-success animate__animated animate__fadeInUp">Hari Anak Nasional 2026</h2>
            <p class="lead mb-4">Sisa waktu sampai 23 Juli 2026:</p>
            <div id="countdown" class="d-flex justify-content-center gap-3 mb-4" style="font-size: 2rem; font-weight: bold; color: #FF4500;"></div>
            <p class="text-muted">Siap-siap bareng anak-anak untuk perayaan seru!</p>

            <script>
                // Set target date (23 Juli 2026, 00:00 WIB)
                const targetDate = new Date("July 23, 2026 00:00:00 GMT+0700").getTime();

                // Update countdown every 1 second
                const countdown = setInterval(() => {
                    const now = new Date().getTime();
                    const distance = targetDate - now;

                    const days = Math.floor(distance / (1000 * 60 * 60 * 24));
                    const hours = Math.floor((distance % (1000 * 60 * 60 * 24)) / (1000 * 60 * 60));
                    const minutes = Math.floor((distance % (1000 * 60 * 60)) / (1000 * 60));
                    const seconds = Math.floor((distance % (1000 * 60)) / 1000);

                    document.getElementById("countdown").innerHTML =
                        `<div>${days}<br>Hari</div>` +
                        `<div>${hours}<br>Jam</div>` +
                        `<div>${minutes}<br>Menit</div>` +
                        `<div>${seconds}<br>Detik</div>`;

                    if (distance < 0) {
                        clearInterval(countdown);
                        document.getElementById("countdown").innerHTML = "Event Sudah Dimulai!";
                    }
                }, 1000);
            </script>
        </div>
    </section>

    <!-- Agenda Tambahan (Opsional) -->
    <section class="agenda-list py-5" style="background: #E0F7FA;">
        <div class="container">
            <h2 class="text-center mb-4 text-info animate__animated animate__slideInDown">Jadwal Lain</h2>
            <ul class="list-group">
                <li class="list-group-item">Pembagian Raport: 15 Desember 2025</li>
                <li class="list-group-item">Lomba Lari: 10 Januari 2026</li>
            </ul>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>