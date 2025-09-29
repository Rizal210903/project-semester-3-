<?php
include '../includes/header.php';
?>

<main class="container-fluid p-0">
    <!-- Header Selamat Datang -->
    <section class="hero-section text-center py-5" style="background: linear-gradient(135deg, #87CEEB, #A9E4EF);">
        <div class="container py-5">
            <div class="row align-items-center">
                <div class="col-12">
                    <?php
                    $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/TK-PERTIWI/img/logo_tk.png';
                    if (!file_exists($logoPath)) {
                        echo "<p style='color:red;'>Logo ga ketemu di: $logoPath</p>";
                    }
                    ?>
                    <img src="../img/logo_tk.png" alt="Logo TK Pertiwi" class="mb-3 img-fluid animate__animated animate__fadeIn" style="max-width: 200px; height: auto;" onerror="this.src='https://via.placeholder.com/200'; this.onerror=null;">
                    <h1 class="display-3 fw-bold text-white mb-3 animate__animated animate__slideInUp" style="text-shadow: 2px 2px #87CEEB;">Selamat Datang di situs Web</h1>
                    <h1 class="display-3 fw-bold text-white animate__animated animate__slideInUp" style="text-shadow: 2px 2px #87CEEB; animation-delay: 0.5s;">TAMAN KANAK KANAK PERTIWI</h1>
                </div>
            </div>
        </div>
    </section>

    <!-- Sambutan Kepala Sekolah -->
    <section class="sambutan-section py-5" style="background: linear-gradient(135deg, #D1F2EB, #E0F7FA);">
        <div class="container">
            <h2 class="text-center mb-5 text-info animate__animated animate__zoomIn" style="text-shadow: 1px 1px #87CEEB;">Sambutan Kepala Sekolah</h2>
            <div class="row justify-content-center align-items-center">
                <div class="col-md-3 text-center">
                    <?php
                    $kepalaPath = $_SERVER['DOCUMENT_ROOT'] . '/TK-PERTIWI/img/kepala_sekolah.jpg';
                    if (!file_exists($kepalaPath)) {
                        echo "<p style='color:red;'>Foto kepala sekolah ga ketemu di: $kepalaPath</p>";
                    }
                    ?>
                    <img src="../img/kepala_sekolah.jpg" alt="Foto Kepala Sekolah" class="img-fluid rounded-circle shadow-sm animate__animated animate__fadeIn" style="max-width: 200px; border: 5px solid #87CEEB;" onerror="this.src='https://via.placeholder.com/200'; this.onerror=null;">
                </div>
                <div class="col-md-8">
                    <div class="sambutan-box animate__animated animate__fadeInUp">
                        <p class="lead text-muted">Asalamualaikum wr.wb.</p>
                        <p class="text-justify">Puji syukur kehadirat Allah SWT yang telah memberikan rahmat dan hidayah-Nya kepada kita semua, sehingga kita masih diberi kesempatan untuk menjalankan aktivitas sehari-hari.</p>
                        <p class="text-justify">Kami ucapkan selamat datang di website resmi Taman Kanak-Kanak Pertiwi. Website ini dibuat sebagai media informasi dan komunikasi antara sekolah dengan orang tua/wali murid, siswa, dan masyarakat umum.</p>
                        <p class="text-justify">Terima kasih atas kunjungan Anda di website kami. Semoga website ini bermanfaat bagi kita semua.</p>
                        <p class="lead text-muted">Wassalamualaikum wr.wb.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Gallery Foto Ekstra Kurikuler -->
    <section class="gallery-section py-5" style="background: linear-gradient(135deg, #B0E0E6, #D1F2EB);">
        <div class="container">
            <h2 class="text-center mb-5 text-info animate__animated animate__bounce" style="text-shadow: 1px 1px #87CEEB;">Galeri Kegiatan</h2>
            <div class="text-center mb-4">
                <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('all')" style="background: rgba(135, 206, 235, 0.2); transition: transform 0.3s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';">Semua</button>
                <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('kegiatan')" style="background: rgba(135, 206, 235, 0.2); transition: transform 0.3s; animation-delay: 0.2s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';">Foto Kegiatan Siswa</button>
                <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('prestasi')" style="background: rgba(135, 206, 235, 0.2); transition: transform 0.3s; animation-delay: 0.4s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';">Prestasi</button>
                <button class="btn btn-outline-info mx-2 animate__animated animate__fadeIn" onclick="showCategory('ekstrakurikuler')" style="background: rgba(135, 206, 235, 0.2); transition: transform 0.3s; animation-delay: 0.6s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';">Ekstrakurikuler</button>
            </div>
            <div id="gallery-content" class="row g-4 justify-content-center">
                <!-- Default: Semua -->
                <div class="col-md-3 gallery-item all kegiatan"><img src="../uploads/ekstra1_edited.jpg" alt="Ekstra 1" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 0.2s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
                <div class="col-md-3 gallery-item all prestasi"><img src="../img/placeholder2.jpg" alt="Ekstra 2" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 0.4s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
                <div class="col-md-3 gallery-item all kegiatan"><img src="../img/placeholder3.jpg" alt="Ekstra 3" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 0.6s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
                <div class="col-md-3 gallery-item all prestasi"><img src="../img/placeholder4.jpg" alt="Ekstra 4" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 0.8s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
                <div class="col-md-3 gallery-item all ekstrakurikuler"><img src="../img/placeholder5.jpg" alt="Ekstra 5" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 1.0s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
                <div class="col-md-3 gallery-item all ekstrakurikuler"><img src="../img/placeholder6.jpg" alt="Ekstra 6" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="transition: transform 0.3s; animation-delay: 1.2s;" onmouseover="this.style.transform='scale(1.1) rotate(5deg)';" onmouseout="this.style.transform='scale(1)';"></div>
            </div>
        </div>
    </section>

    <!-- Peta Lokasi -->
    <section class="map-section py-5" style="background: linear-gradient(135deg, #D1F2EB, #E0F7FA);">
        <div class="container">
            <h2 class="text-center mb-5 text-info animate__animated animate__bounce" style="text-shadow: 1px 1px #87CEEB;">Lokasi Kami</h2>
            <div class="ratio ratio-16x9">
                <iframe src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.2639494126097!2d113.69934897432888!3d-8.17614258195306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd69425c12acb8b%3A0x86fdf63c580222fc!2sTK%20Pertiwi!5e0!3m2!1sid!2sid!4v1759136704596!5m2!1sid!2sid" width="600" height="450" style="border:0;" allowfullscreen="" loading="lazy" referrerpolicy="no-referrer-when-downgrade"></iframe>
            </div>
        </div>
    </section>
</main>

<style>
.gallery-item { display: none; }
.gallery-item.all { display: block; }
.gallery-item.kegiatan { display: none; }
.gallery-item.prestasi { display: none; }
.gallery-item.ekstrakurikuler { display: none; }

.sambutan-box {
    background: rgba(209, 242, 235, 0.95);
    padding: 20px;
    border-radius: 10px;
    border: 3px dashed #87CEEB;
    box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
}
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css"></script>
<script>
function showCategory(category) {
    const items = document.querySelectorAll('.gallery-item');
    items.forEach(item => {
        item.style.display = 'none';
        if (category === 'all' || item.classList.contains(category)) {
            item.style.display = 'block';
        }
    });
}
</script>

<?php
$footerPath = $_SERVER['DOCUMENT_ROOT'] . '/TK-PERTIWI/includes/footer.php';
if (!file_exists($footerPath)) {
    echo "<p style='color:red;'>Footer ga ketemu di: $footerPath</p>";
    exit;
}
include $footerPath;
?>