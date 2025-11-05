<?php
include '../includes/header.php';
include '../includes/config.php'; // koneksi ke database
?>

<main class="container-fluid p-0">
    <section class="profil-section py-5" style="background: #FFFFFF;">
        <div class="container">
            <h1 class="text-center mb-5" style="color: #000080; font-family: 'Poppins', sans-serif; font-size: clamp(1.5rem, 4vw, 2.5rem); animation: fadeIn 1s;">
                Profil Sekolah
            </h1>
            
            <!-- SAMBUTAN KEPALA SEKOLAH -->
            <?php
            $kepala = $conn->query("SELECT * FROM galeri_foto WHERE kategori='kepala_sekolah' ORDER BY created_at DESC LIMIT 1");
            $kepala_data = $kepala->fetch_assoc();
            $foto_kepala = $kepala_data ? '../uploads/' . $kepala_data['file_path'] : 'https://via.placeholder.com/200';
            ?>
            <section class="sambutan-section mb-5" style="background: #FFFFFF;">
                <div class="container">
                    <h2 class="text-center mb-5 text-info animate__animated animate__zoomIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.2rem, 3vw, 2rem);">
                        Sambutan Kepala Sekolah
                    </h2>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-md-3 text-center">
                            <img src="<?= $foto_kepala ?>" alt="Kepala Sekolah" 
                                 class="img-fluid rounded-circle shadow-sm animate__animated animate__fadeIn"
                                 style="max-width: 200px; width: 100%; border: 4px solid #2196F3;"
                                 onerror="this.src='https://via.placeholder.com/200'; this.onerror=null;">
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="sambutan-box animate__animated">
                                <p class="lead text-muted animate__animated animate__fadeInUp" style="font-size: 1.1rem;">Assalamualaikum wr.wb.</p>
                                <p class="text-justify animate__animated animate__fadeInUp" style="color: #000;">Puji syukur kehadirat Allah SWT yang telah memberikan rahmat dan hidayah-Nya kepada kita semua, sehingga kita dapat terus berjuang memberikan pendidikan terbaik bagi generasi penerus bangsa.</p>
                                <p class="text-justify animate__animated animate__fadeInUp" style="color: #000;">Kami ucapkan selamat datang di website resmi TK Pertiwi. Website ini dibuat sebagai sarana informasi dan komunikasi antara sekolah dengan orang tua, siswa, serta masyarakat umum.</p>
                                <p class="lead text-muted animate__animated animate__fadeInUp" style="font-size: 1.1rem;">Wassalamualaikum wr.wb.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <!-- VISI & MISI -->
            <div class="row">
                <div class="col-md-6">
                    <h3 class="text-center mb-3 text-info">Visi</h3>
                    <p class="text-justify">Taman kanak-kanak yang unggul dalam prestasi, beriman, bertaqwa, berakhlak mulia, dan berbudaya lingkungan.</p>
                </div>
                <div class="col-md-6">
                    <h3 class="text-center mb-3 text-info">Misi</h3>
                    <ol class="text-justify">
                        <li>Melaksanakan pembelajaran yang aktif, kreatif, dan menyenangkan.</li>
                        <li>Membentuk anak didik yang berakhlak mulia dan berbudaya lingkungan.</li>
                        <li>Meningkatkan prestasi anak didik melalui kegiatan edukatif dan inovatif.</li>
                    </ol>
                </div>
            </div>

            <!-- STRUKTUR SEKOLAH -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-3 text-info">Struktur Sekolah</h3>
                    <ul class="list-group list-group-flush animate__animated animate__fadeInUp">
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Kepala Sekolah
                            <span class="badge bg-primary rounded-pill">Anis Sanijah, S.Pd</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Wakil Kepala Sekolah
                            <span class="badge bg-primary rounded-pill">Muhammad Rizal, P.Di</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Guru Kelas A
                            <span class="badge bg-primary rounded-pill">Nama Guru A</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            Guru Kelas B
                            <span class="badge bg-primary rounded-pill">Nama Guru B</span>
                        </li>
                    </ul>
                </div>
            </div>

            <!-- FOTO GURU -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-4 text-info">Foto Guru</h3>
                    <div class="row g-4 justify-content-center">
                        <?php
                        $guru = $conn->query("SELECT * FROM galeri_foto WHERE kategori='guru' ORDER BY created_at DESC");
                        if ($guru->num_rows > 0) {
                            while ($g = $guru->fetch_assoc()) {
                                echo '
                                <div class="col-6 col-md-3 text-center">
                                    <img src="../uploads/'.$g['file_path'].'" alt="'.$g['judul'].'" 
                                         class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" 
                                         style="max-width:100%; height:auto;"
                                         onerror="this.src=\'https://via.placeholder.com/250\'; this.onerror=null;">
                                    <p class="mt-2" style="color:#000;">'.$g['judul'].'</p>
                                </div>';
                            }
                        } else {
                            echo "<p class='text-muted text-center'>Belum ada foto guru diunggah.</p>";
                        }
                        ?>
                    </div>
                </div>
            </div>

            <!-- SEJARAH TK PERTIWI -->
            <div class="row mt-5">
            <div class="col-12 sejarah-section">
            <h3 class="text-center mb-3 text-info animate__animated">Sejarah TK Pertiwi</h3>
            <p class="text-justify animate__animated">Pada suatu hari cerah di tahun 2010, sekelompok pendidik berhati mulia di Jakarta memulai perjalanan penuh harapan dengan mendirikan TK Pertiwi. Awalnya, hanya ada satu kelas sederhana di sebuah gedung kecil, dihuni oleh 15 anak kecil yang penuh rasa ingin tahu dan dua guru yang berdedikasi.</p>
            <p class="text-justify animate__animated">Tahun demi tahun, perjuangan itu membuahkan hasil. Pada 2015, dengan dukungan masyarakat dan orang tua, TK Pertiwi pindah ke lokasi baru yang lebih luas dan nyaman, dilengkapi ruang kelas modern, taman bermain yang penuh warna, dan laboratorium mini.</p>
            <p class="text-justify animate__animated">Kini, setelah lebih dari satu dekade, TK Pertiwi telah menjadi rumah bagi ratusan anak yang melangkah percaya diri menuju masa depan. Setiap langkah, dari ruang sederhana hingga kehadiran digital di website ini, adalah bukti nyata komitmen kami dalam mencetak generasi berakhlak, kreatif, dan cerdas.</p>
    </div>
</div>

    </section>
</main>

<?php include '../includes/footer.php'; ?>

<!-- STYLING -->
<style>
    
.sejarah-section {
    opacity: 0;
    transform: translateY(50px);
    transition: all 0.8s ease-out;
}

.sejarah-section.animate__fadeInUp {
    opacity: 1;
    transform: translateY(0);
}

    body { font-family: 'Poppins', sans-serif; }
    .sambutan-box {
        background: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 10px;
        border: 2px solid #000080;
        box-shadow: 0 5px 15px rgba(0,0,0,0.1);
    }
    img:hover { transform: scale(1.05); transition: 0.3s; }
    @media (max-width: 768px) {
        .sambutan-section .row { text-align: center; flex-direction: column; }
        .sambutan-section img { margin-bottom: 20px; }
    }
</style>

<script>
document.addEventListener('DOMContentLoaded', () => {
    const sejarah = document.querySelector('.sejarah-section');

    const observer = new IntersectionObserver(entries => {
        entries.forEach(entry => {
            if (entry.isIntersecting) {
                sejarah.classList.add('animate__fadeInUp');
                observer.unobserve(sejarah);
            }
        });
    }, { threshold: 0.3 });

    observer.observe(sejarah);
});
</script>


<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
