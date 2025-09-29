<?php include '../includes/header.php'; ?>

<main class="container-fluid p-0">
    <!-- Header Info PPDB -->
    <section class="hero-section text-center py-5 position-relative" style="background: linear-gradient(135deg, #45B7D1, #4ECDC4); overflow: hidden;">
        <div class="container py-5">
            <h1 class="display-4 text-white animate__animated animate__bounceIn" style="text-shadow: 2px 2px #333;">Info PPDB TK Pertiwi</h1>
            <p class="lead text-white animate__animated animate__fadeInUp" style="animation-delay: 0.5s;">Informasi Penerimaan Peserta Didik Baru Tahun 2025/2026</p>
            <!-- Dekorasi Bola Memantul -->
            <div class="bouncing-balls">
                <div class="ball" style="background: #FF9999; left: 10%; animation-delay: 0s;"></div>
                <div class="ball" style="background: #A9E4EF; left: 30%; animation-delay: 0.3s;"></div>
                <div class="ball" style="background: #FFD700; right: 10%; animation-delay: 0.6s;"></div>
            </div>
        </div>
    </section>

    <!-- Konten Info PPDB -->
    <section class="info-section py-5 position-relative" style="background: #E0F7FA;">
        <div class="container">
            <div class="row">
                <div class="col-md-12">
                    <h2 class="text-center mb-4 text-info animate__animated animate__fadeInUp">Jadwal PPDB</h2>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Pendaftaran Online: 1 September - 30 September 2025</li>
                        <li class="list-group-item">Verifikasi Dokumen: 1 Oktober - 5 Oktober 2025</li>
                        <li class="list-group-item">Pengumuman Kelulusan: 10 Oktober 2025</li>
                        <li class="list-group-item">Daftar Ulang: 11 Oktober - 15 Oktober 2025</li>
                    </ul>

                    <h2 class="text-center mb-4 mt-5 text-info animate__animated animate__fadeInUp">Syarat Pendaftaran</h2>
                    <ul class="list-group list-group-flush">
                        <li class="list-group-item">Fotokopi Akta Kelahiran</li>
                        <li class="list-group-item">Fotokopi Kartu Keluarga</li>
                        <li class="list-group-item">Pas Foto 3x4 (2 lembar)</li>
                        <li class="list-group-item">Surat Keterangan Sehat dari Dokter</li>
                    </ul>

                    <div class="text-center mt-5 position-relative">
                        <div class="dropdown advanced-dropdown">
                            <button class="btn btn-primary btn-lg dropdown-toggle custom-dropdown" type="button" id="ppdbDropdown" data-bs-toggle="dropdown" aria-expanded="false">
                                <span class="dropdown-text">Pilih Aksi PPDB</span>
                                <span class="dropdown-icon"><i class="bi bi-chevron-down"></i></span>
                            </button>
                            <ul class="dropdown-menu custom-dropdown-menu animate__animated animate__fadeIn" id="ppdbMenu">
                                <li><a class="dropdown-item action-item" href="/TK-PERTIWI/pages/pendaftaran.php" data-action="register">Daftar Sekarang</a></li>
                                <li><a class="dropdown-item action-item" href="/TK-PERTIWI/pages/cek_status.php" data-action="check">Cek Status Pendaftaran</a></li>
                                <li class="dropdown-divider"></li>
                                <li><a class="dropdown-item action-item disabled" href="#" data-action="guide">Panduan PPDB (Segera Hadir)</a></li>
                            </ul>
                            <!-- Dekorasi Bintang -->
                            <div class="decorative-stars">
                                <div class="star" style="top: -10px; left: 20%;"></div>
                                <div class="star" style="bottom: -10px; right: 20%;"></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</main>

<?php include '../includes/footer.php'; ?>

<style>
.bouncing-balls, .decorative-stars {
    position: absolute;
    width: 100%;
    height: 100%;
    top: 0;
    left: 0;
    pointer-events: none;
}
.ball {
    position: absolute;
    width: 20px;
    height: 20px;
    border-radius: 50%;
    animation: bounce 2s infinite ease-in-out;
    box-shadow: 0 0 10px rgba(255, 255, 255, 0.5);
}
.ball:nth-child(1) { left: 10%; animation-delay: 0s; }
.ball:nth-child(2) { left: 30%; animation-delay: 0.3s; }
.ball:nth-child(3) { right: 10%; animation-delay: 0.6s; }
@keyframes bounce {
    0%, 100% { transform: translateY(0); }
    50% { transform: translateY(-40px); }
}

.star {
    position: absolute;
    width: 6px;
    height: 6px;
    background: #FFD700;
    border-radius: 50%;
    box-shadow: 0 0 8px #FFD700;
    animation: twinkle 2s infinite alternate;
}
@keyframes twinkle {
    from { opacity: 0.4; }
    to { opacity: 1; }
}

.advanced-dropdown {
    display: inline-block;
    position: relative;
}
.custom-dropdown {
    background: linear-gradient(135deg, #45B7D1, #4ECDC4);
    border: 3px solid #FFD700;
    border-radius: 20px;
    padding: 12px 25px;
    font-size: 1.3rem;
    color: #fff;
    transition: all 0.4s ease;
    overflow: hidden;
    position: relative;
    box-shadow: 0 4px 12px rgba(0, 0, 0, 0.1);
}
.custom-dropdown:hover {
    transform: scale(1.1) rotate(3deg);
    box-shadow: 0 8px 20px rgba(0, 0, 0, 0.2);
}
.custom-dropdown::before {
    content: '';
    position: absolute;
    top: 50%;
    left: 50%;
    width: 0;
    height: 0;
    background: rgba(255, 215, 0, 0.2);
    border-radius: 50%;
    transform: translate(-50%, -50%);
    transition: width 0.6s ease, height 0.6s ease;
}
.custom-dropdown:hover::before {
    width: 200px;
    height: 200px;
}
.custom-dropdown .dropdown-icon {
    margin-left: 10px;
    transition: transform 0.3s ease;
}
.custom-dropdown[aria-expanded="true"] .dropdown-icon {
    transform: rotate(180deg);
}
.custom-dropdown-menu {
    background: #E0F7FA;
    border: 2px solid #45B7D1;
    border-radius: 15px;
    padding: 10px 0;
    box-shadow: 0 6px 18px rgba(0, 0, 0, 0.15);
    min-width: 250px;
    animation-duration: 0.5s;
}
.custom-dropdown-menu .dropdown-item {
    padding: 12px 20px;
    color: #333;
    font-weight: 500;
    transition: all 0.3s ease;
}
.custom-dropdown-menu .dropdown-item:hover,
.custom-dropdown-menu .dropdown-item:focus {
    background: #45B7D1;
    color: #fff;
    transform: translateX(15px);
}
.custom-dropdown-menu .dropdown-item.disabled {
    color: #888;
    cursor: not-allowed;
}
.custom-dropdown-menu .dropdown-item.disabled:hover {
    background: none;
    transform: none;
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const dropdown = document.querySelector('#ppdbDropdown');
    const menu = document.querySelector('#ppdbMenu');

    dropdown.addEventListener('click', () => {
        menu.classList.add('animate__fadeIn');
        menu.classList.remove('animate__fadeOut');
    });

    // Animasi tutup dropdown
    const items = document.querySelectorAll('.action-item');
    items.forEach(item => {
        item.addEventListener('click', () => {
            if (!item.classList.contains('disabled')) {
                menu.classList.remove('animate__fadeIn');
                menu.classList.add('animate__fadeOut');
                setTimeout(() => {
                    dropdown.setAttribute('aria-expanded', 'false');
                }, 500);
            }
        });
    });

    // Animasi bola dinamis
    const balls = document.querySelectorAll('.ball');
    balls.forEach(ball => {
        ball.style.top = `${Math.random() * 50 + 20}%`;
    });
});
</script>

<?php include '../includes/footer.php'; ?>