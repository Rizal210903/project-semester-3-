<?php
include '../includes/header.php';
?>

<main class="container-fluid p-0">
    <section class="profil-section py-5" style="background: #E0F7FA;">
        <div class="container">
            <h1 class="text-center mb-5 text-primary animate__animated animate__fadeIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.5rem, 4vw, 2.5rem);">Profil Sekolah</h1>
            
            <!-- Sambutan Kepala Sekolah -->
            <section class="sambutan-section mb-5">
                <div class="container">
                    <h2 class="text-center mb-5 text-info animate__animated animate__zoomIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.2rem, 3vw, 2rem);">Sambutan Kepala Sekolah</h2>
                    <div class="row justify-content-center align-items-center">
                        <div class="col-12 col-md-3 text-center position-relative">
                            <?php
                            $kepala_path = __DIR__ . '/../../img/kepala_sekolah.jpg';
                            if (!file_exists($kepala_path)) {
                               
                            }
                            ?>
                            <img src="/project-semester-3-/img/kepala_sekolah.jpg" alt="Foto Kepala Sekolah" class="img-fluid rounded-circle shadow-sm animate__animated animate__fadeIn" style="max-width: 200px; width: 100%;" onerror="this.src='https://via.placeholder.com/200'; this.onerror=null;">
                        </div>
                        <div class="col-12 col-md-8">
                            <div class="sambutan-box animate__animated">
                                <p class="lead text-muted animate__animated animate__fadeInUp" style="font-size: clamp(1rem, 2.5vw, 1.2rem); animation-delay: 0.2s;">Asalamualaikum wr.wb.</p>
                                <p class="text-justify animate__animated animate__fadeInUp" style="font-size: clamp(0.9rem, 2vw, 1.1rem); animation-delay: 0.4s;">Puji syukur kehadirat Allah SWT yang telah memberikan rahmat dan hidayah-Nya kepada kita semua, sehingga kita masih diberi kesempatan untuk menjalankan aktivitas sehari-hari.</p>
                                <p class="text-justify animate__animated animate__fadeInUp" style="font-size: clamp(0.9rem, 2vw, 1.1rem); animation-delay: 0.6s;">Kami ucapkan selamat datang di website resmi Taman Kanak-Kanak Pertiwi. Website ini dibuat sebagai media informasi dan komunikasi antara sekolah dengan orang tua/wali murid, siswa, dan masyarakat umum.</p>
                                <p class="text-justify animate__animated animate__fadeInUp" style="font-size: clamp(0.9rem, 2vw, 1.1rem); animation-delay: 0.8s;">Terima kasih atas kunjungan Anda di website kami. Semoga website ini bermanfaat bagi kita semua.</p>
                                <p class="lead text-muted animate__animated animate__fadeInUp" style="font-size: clamp(1rem, 2.5vw, 1.2rem); animation-delay: 1s;">Wassalamualaikum wr.wb.</p>
                            </div>
                        </div>
                    </div>
                </div>
            </section>

            <div class="row">
                <div class="col-12 col-md-6">
                    <h3 class="text-center mb-3 text-success animate__animated animate__fadeInUp" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55;">Visi</h3>
                    <p class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333;">Taman kanak-kanak yang unggul dalam prestasi, beriman, bertaqwa, berakhlak mulia, dan berbudaya lingkungan.</p>
                </div>
                <div class="col-12 col-md-6">
                    <h3 class="text-center mb-3 text-success animate__animated animate__fadeInUp" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55; animation-delay: 0.2s;">Misi</h3>
                    <ol class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333; animation-delay: 0.4s;">
                        <li>Melaksanakan pembelajaran yang aktif, inovatif, kreatif, efektif, menyenangkan, gembira, dan bermakna.</li>
                        <li>Membentuk anak didik yang beriman, bertaqwa, berakhlak mulia, dan berbudaya lingkungan.</li>
                        <li>Meningkatkan prestasi anak didik melalui berbagai kegiatan.</li>
                    </ol>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-3 text-warning animate__animated animate__fadeInUp" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55; animation-delay: 0.6s;">Tujuan</h3>
                    <ol class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333; animation-delay: 0.8s;">
                        <li>Melaksanakan pengembangan sekolah berbasis potensi lokal berbasis IT.</li>
                        <li>Meningkatkan Mutu Guru dengan sertifikasi guru dan peningkatan kompetensi.</li>
                        <li>Pembangunan Sarana Prasarana yang mendukung pembelajaran.</li>
                        <li>Optimalisasi Hubungan dengan Masyarakat.</li>
                        <li>Menciptakan lingkungan sekolah yang nyaman, aman, dan menyenangkan.</li>
                        <li>Meningkatkan prestasi siswa melalui berbagai kegiatan ekstrakurikuler.</li>
                    </ol>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-3 text-danger animate__animated animate__bounceIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FFD700; animation-delay: 1s;">Struktur Sekolah</h3>
                    <ul class="list-group list-group-flush animate__animated animate__fadeInUp" style="animation-delay: 1.2s;">
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                            Kepala Sekolah
                            <span class="badge bg-primary rounded-pill">Anis Sanijah, S.pd</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                            Wakil Kepala Sekolah
                            <span class="badge bg-primary rounded-pill">Muhammad Rizal P.di</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                            Guru Kelas A
                            <span class="badge bg-primary rounded-pill">Nama Guru A</span>
                        </li>
                        <li class="list-group-item d-flex justify-content-between align-items-center" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                            Guru Kelas B
                            <span class="badge bg-primary rounded-pill">Nama Guru B</span>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-3 text-primary animate__animated animate__bounceIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55; animation-delay: 1.4s;">Foto Guru</h3>
                    <div class="row g-4 justify-content-center">
                        <div class="col-6 col-md-3">
                            <img src="/project-semester-3-/img/guru1.jpg" alt="Guru 1" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="max-width: 100%; height: auto; animation-delay: 1.6s;" onerror="this.src='https://via.placeholder.com/250'; this.onerror=null;">
                            <p class="text-center mt-2" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">Fatimah Laila, S.pd</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="/project-semester-3-/img/guru2.jpg" alt="Guru 2" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="max-width: 100%; height: auto; animation-delay: 1.8s;" onerror="this.src='https://via.placeholder.com/250'; this.onerror=null;">
                            <p class="text-center mt-2" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">Pudji Sugiarti, S.pd</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="/project-semester-3-/img/guru3.jpg" alt="Guru 3" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="max-width: 100%; height: auto; animation-delay: 2s;" onerror="this.src='https://via.placeholder.com/250'; this.onerror=null;">
                            <p class="text-center mt-2" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">Widiawati, S.pd</p>
                        </div>
                        <div class="col-6 col-md-3">
                            <img src="/project-semester-3-/img/guru4.jpg" alt="Guru 4" class="img-fluid rounded shadow-sm animate__animated animate__zoomIn" style="max-width: 100%; height: auto; animation-delay: 2.2s;" onerror="this.src='https://via.placeholder.com/250'; this.onerror=null;">
                            <p class="text-center mt-2" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">Wina Puspita Sari, S.pd</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Sejarah TK Pertiwi -->
            <div class="row mt-5">
                <div class="col-12">
                    <h3 class="text-center mb-3 text-warning animate__animated animate__bounceIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55; animation-delay: 2.4s;">Sejarah TK Pertiwi</h3>
                    <p class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333; animation-delay: 2.6s;">
                        Pada suatu hari cerah di tahun 2010, sekelompok pendidik berhati mulia di Jakarta memulai perjalanan penuh harapan dengan mendirikan TK Pertiwi. Awalnya, hanya ada satu kelas sederhana di sebuah gedung kecil, dihuni oleh 15 anak kecil yang penuh rasa ingin tahu dan dua guru yang berdedikasi. Dengan semangat membangun fondasi pendidikan yang kuat, mereka menabur benih mimpi untuk menciptakan tempat belajar yang hangat dan bermakna bagi anak-anak.
                    </p>
                    <p class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333; animation-delay: 2.8s;">
                        Tahun berlalu, dan perjuangan itu mulai berbuah. Pada 2015, setelah mendapat dukungan luar biasa dari orang tua dan komunitas lokal, TK Pertiwi pindah ke lokasi baru yang lebih luas. Gedung baru itu berdiri megah dengan ruang kelas ber-AC, taman bermain yang penuh warna, dan laboratorium mini tempat anak-anak mengejar kreativitas mereka. Di sini, kurikulum inovatif mulai dijalankan, menggabungkan permainan seru dengan nilai-nilai agama dan budaya lokal, seolah mengajak setiap anak menari dalam irama pembelajaran yang unik. Bahkan, kebanggaan datang saat kami meraih juara lomba seni anak tingkat kota pada 2022, bukti bahwa mimpi kami terus tumbuh.
                    </p>
                    <p class="text-justify animate__animated animate__slideInLeft" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.2rem); line-height: 1.8; color: #333; animation-delay: 3s;">
                        Kini, setelah lebih dari satu dekade, TK Pertiwi telah menjadi rumah bagi ratusan anak yang kini melangkah percaya diri ke jenjang pendidikan lebih tinggi. Setiap langkah, dari kelas kecil di masa lalu hingga website ini yang hadir di tangan Anda, adalah bukti komitmen kami. Kami tak berhenti bermimpi—setiap hari adalah kesempatan baru untuk menciptakan generasi cerdas, berakhlak, dan penuh warna, bersama wali murid dan masyarakat yang kami cintai.
                    </p>
                </div>
            </div>

            <!-- Kontak Singkat -->
            <div class="row mt-5">
                <div class="col-12 text-center">
                    <h3 class="text-center mb-3 text-primary animate__animated animate__bounceIn" style="font-family: 'Poppins', sans-serif; font-size: clamp(1.1rem, 2.5vw, 1.5rem); text-shadow: 2px 2px #FF9F55; animation-delay: 3.2s;">Kontak Kami</h3>
                    <p class="lead" style="font-family: 'Poppins', sans-serif; font-size: clamp(0.9rem, 2vw, 1.1rem);">
                        Hubungi kami untuk informasi lebih lanjut:<br>
                        Telepon: +62 812-3456-7890<br>
                        Email: info@tkpertiwi.com<br>
                        Alamat: Jl. WR Supratman No.6, Kauman, Kepatihan, Kec. Kaliwates, Kabupaten Jember, Jawa Timur 68131
                    </p>
                    <a href="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3949.2639494126097!2d113.69934897432888!3d-8.17614258195306!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x2dd69425c12acb8b%3A0x86fdf63c580222fc!2sTK%20Pertiwi!5e0!3m2!1sid!2sid!4v1759136704596!5m2!1sid!2sid" class="btn btn-info mt-3" target="_blank" style="font-size: clamp(0.9rem, 2vw, 1.1rem);">Lihat Peta</a>
                </div>
            </div>
        </div>  
    </section>
</main>

<?php
include '../includes/footer.php';
?>

<style>
    body {
        font-family: 'Poppins', sans-serif;
    }
    .sambutan-box {
        background: rgba(255, 255, 255, 0.9);
        padding: 15px;
        border-radius: 10px;
        border: 2px solid #87CEEB;
        box-shadow: 0 5px 15px rgba(0, 0, 0, 0.1);
    }
    .sambutan-section img {
        max-width: 200px;
        width: 100%;
        border: 5px solid #87CEEB;
        filter: drop-shadow(0 0 10px #87CEEB);
    }
    .profil-section .row img {
        transition: transform 0.3s;
        max-width: 100%;
        height: auto;
    }
    .profil-section .row img:hover {
        transform: scale(1.1);
    }

    /* Media Queries untuk Responsivitas Tambahan */
    @media (max-width: 768px) {
        .sambutan-box {
            padding: 10px;
        }
        .sambutan-section .row {
            flex-direction: column;
            text-align: center;
        }
        .sambutan-section img {
            margin-bottom: 20px;
        }
        .row .col-md-6, .row .col-md-3 {
            margin-bottom: 20px;
        }
        .list-group-item {
            flex-direction: column;
            text-align: center;
        }
        .list-group-item .badge {
            margin-top: 10px;
        }
    }
</style>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>