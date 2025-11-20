<?php
include '../includes/header.php';
include '../includes/config.php'; // koneksi ke database
?>
<main class="container-fluid p-0">
 
    <!-- ======================= SAMBUTAN KEPALA SEKOLAH ======================= -->
    <?php
    $kepala = $conn->query("SELECT * FROM galeri_foto WHERE kategori='kepala_sekolah' ORDER BY created_at DESC LIMIT 1");
    if ($kepala && $kepala->num_rows > 0) {
        $kepala_data = $kepala->fetch_assoc();
        $foto_kepala = '../uploads/' . $kepala_data['file_path'];
    } else {
        $foto_kepala = 'https://via.placeholder.com/200';
    }
    ?>
    <section class="section-block sambutan-section">
        <div class="container">
            <h1 class="section-title text-center">Profil Sekolah</h1>
            <h2 class="text-center mb-5 text-info animate__animated animate__zoomIn">Sambutan Kepala Sekolah</h2>

            <div class="row justify-content-center align-items-center">
                <div class="col-12 col-md-3 text-center">
                    <img src="<?= $foto_kepala ?>" alt="Kepala Sekolah"
                        class="img-fluid rounded-circle shadow-sm animate__animated animate__fadeIn"
                        style="max-width: 200px; width: 100%; border: 4px solid #2196F3;"
                        onerror="this.src='https://via.placeholder.com/200'; this.onerror=null;">
                </div>
                <div class="col-12 col-md-8">
                    <div class="sambutan-box animate__animated animate__fadeInUp">
                        <p class="lead text-muted">Assalamualaikum wr.wb.</p>
                        <p class="text-justify">Puji syukur kehadirat Allah SWT yang telah memberikan rahmat dan hidayah-Nya kepada kita semua, sehingga kita dapat terus berjuang memberikan pendidikan terbaik bagi generasi penerus bangsa.</p>
                        <p class="text-justify">Kami ucapkan selamat datang di website resmi TK Pertiwi. Website ini dibuat sebagai sarana informasi dan komunikasi antara sekolah dengan orang tua, siswa, serta masyarakat umum.</p>
                        <p class="lead text-muted">Wassalamualaikum wr.wb.</p>
                    </div>
                </div>
            </div>
        </div>
        <div class="divider divider-wave"></div>
    </section>

    <!-- ======================= VISI & MISI ======================= -->
    <section class="section-block visi-misi-section">
        <div class="container">
            <div class="row">
                <div class="col-md-6 animate__animated animate__fadeInLeft">
                    <h3 class="text-center mb-3 text-info">Visi</h3>
                    <p class="text-justify">Taman kanak-kanak yang unggul dalam prestasi, beriman, bertaqwa, berakhlak mulia, dan berbudaya lingkungan.</p>
                </div>
                <div class="col-md-6 animate__animated animate__fadeInRight">
                    <h3 class="text-center mb-3 text-info">Misi</h3>
                    <ol class="text-justify">
                        <li>Melaksanakan pembelajaran yang aktif, kreatif, dan menyenangkan.</li>
                        <li>Membentuk anak didik yang berakhlak mulia dan berbudaya lingkungan.</li>
                        <li>Meningkatkan prestasi anak didik melalui kegiatan edukatif dan inovatif.</li>
                    </ol>
                </div>
            </div>
        </div>
        <div class="divider divider-triangle"></div>
    </section>

   <!-- STRUKTUR SEKOLAH -->
<section class="struktur-sekolah py-5" style="background:#f8f9fa;">
  <div class="container text-center">
    <h2 class="mb-4 text-primary animate__animated animate__fadeInDown">Struktur Sekolah</h2>

    <div class="org-chart animate__animated animate__fadeInUp">

      <!-- Level 1 -->
      <div class="level level-1">
        <div class="box blue">Ketua Yayasan<br><strong>amel.sr</strong></div>
      </div>

      <!-- Garis -->
      <div class="line down"></div>

      <!-- Level 2 -->
      <div class="level level-2">
        <div class="box orange">Ketua Dewan Komite<br><strong>Anis Sanijah</strong></div>
        <div class="box green">Kepala Sekolah<br><strong>Wina P.</strong></div>
      </div>

      <div class="line down"></div>

      <!-- Level 3 -->
      <div class="level level-3">
        <div class="box purple">Pembina<br><strong>Dwi Rini</strong></div>
        <div class="box yellow">Bendahara<br><strong>Siti Nur</strong></div>
        <div class="box pink">Perpustakaan<br><strong>Nur Azizah</strong></div>
      </div>

      <div class="line down"></div>

      <!-- Level 4 -->
      <div class="level level-4">
        <div class="box teal">Guru A1<br><strong>Eva</strong></div>
        <div class="box teal">Guru A2<br><strong>Rina</strong></div>
        <div class="box teal">Guru B1<br><strong>Dina</strong></div>
        <div class="box teal">Guru B2<br><strong>Sari</strong></div>
        <div class="box teal">Guru A3<br><strong>Lina</strong></div>
      </div>

      <div class="line down"></div>

      <!-- Level 5 -->
      <div class="level level-5">
        <div class="box gray">Siswa<br><strong>→</strong> Masyarakat</div>
      </div>

    </div>
  </div>
</section>

<style>
/* ===== Struktur Sekolah ===== */
.org-chart {
  display: flex;
  flex-direction: column;
  align-items: center;
  position: relative;
}

.level {
  display: flex;
  justify-content: center;
  align-items: center;
  flex-wrap: wrap;
  gap: 1.5rem;
  position: relative;
}

.box {
  background: #fff;
  border-radius: 12px;
  box-shadow: 0 5px 15px rgba(0,0,0,0.1);
  padding: 1rem 1.5rem;
  min-width: 160px;
  text-align: center;
  font-weight: 500;
  transition: all 0.3s ease;
  border-top: 5px solid transparent;
}

.box:hover {
  transform: scale(1.05);
  box-shadow: 0 8px 20px rgba(0,0,0,0.15);
}

/* Garis antar level */
.line.down {
  width: 3px;
  height: 30px;
  background: #888;
  margin: 0.5rem 0;
  border-radius: 2px;
}

/* Warna tiap jabatan */
.box.blue    { border-color:#007bff; }
.box.orange  { border-color:#ff8c00; }
.box.green   { border-color:#28a745; }
.box.purple  { border-color:#6f42c1; }
.box.yellow  { border-color:#ffc107; }
.box.pink    { border-color:#e83e8c; }
.box.teal    { border-color:#20c997; }
.box.gray    { border-color:#6c757d; }

/* Responsif */
@media (max-width: 768px) {
  .level {
    flex-direction: column;
  }
  .box {
    width: 80%;
  }
}
</style>

    <!-- ======================= FOTO GURU ======================= -->
    <section class="section-block foto-guru-section">
        <div class="container">
            <h3 class="text-center mb-4 text-info">Foto Guru</h3>
            <div class="row g-4 justify-content-center">
                <?php
                $guru = $conn->query("SELECT * FROM galeri_foto WHERE kategori='guru' ORDER BY created_at DESC");
                if ($guru->num_rows > 0) {
                    while ($g = $guru->fetch_assoc()) {
                        echo '
                        <div class="col-6 col-md-3 text-center animate__animated animate__zoomIn">
                            <div class="guru-card shadow-sm p-2 rounded">
                                <img src="../uploads/'.$g['file_path'].'" alt="'.$g['judul'].'" 
                                    class="img-fluid rounded"
                                    onerror="this.src=\'https://via.placeholder.com/250\'; this.onerror=null;">
                                <p class="mt-2 fw-semibold" style="color:#000;">'.$g['judul'].'</p>
                            </div>
                        </div>';
                    }
                } else {
                    echo "<p class='text-muted text-center'>Belum ada foto guru diunggah.</p>";
                }
                ?>
            </div>
        </div>
        <div class="divider divider-wave2"></div>
    </section>

    <!-- ======================= SEJARAH ======================= -->
    <section class="section-block sejarah-section">
        <div class="container">
            <h3 class="text-center mb-3 text-info animate__animated animate__fadeInDown">Sejarah TK Pertiwi</h3>
            <p class="text-justify">Pada suatu hari cerah di tahun 2010, sekelompok pendidik berhati mulia di Jakarta memulai perjalanan penuh harapan dengan mendirikan TK Pertiwi...</p>
            <p class="text-justify">Tahun demi tahun, perjuangan itu membuahkan hasil...</p>
            <p class="text-justify">Kini, setelah lebih dari satu dekade, TK Pertiwi telah menjadi rumah bagi ratusan anak yang melangkah percaya diri menuju masa depan...</p>
        </div>
    </section>
</main>


<!-- ======================= STYLING ======================= -->
<style>
/* --- Section Style --- */
.section-block {
    padding: 80px 0;
    background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
    position: relative;
}

/* --- Title --- */
.section-title {
    color: #000080;
    font-weight: 600;
    margin-bottom: 2rem;
}

/* --- Sambutan Box --- */
.sambutan-box {
    background: rgba(255, 255, 255, 0.95);
    padding: 25px;
    border-radius: 15px;
    border: 2px solid #000080;
    box-shadow: 0 5px 15px rgba(0,0,0,0.1);
}

/* --- Struktur Card --- */
.struktur-card {
    border: none;
    border-radius: 15px;
    background: #ffffff;
    transition: transform 0.3s;
}
.struktur-card:hover {
    transform: translateY(-5px);
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
}

/* --- Foto Guru Card --- */
.guru-card {
    background: #ffffff;
    border: 1px solid #d0e2ff;
    transition: all 0.3s;
}
.guru-card:hover {
    transform: scale(1.05);
    box-shadow: 0 6px 20px rgba(0,0,0,0.1);
}

/* --- Dividers --- */
.divider {
    height: 100px;
    position: relative;
    overflow: hidden;
}

.divider-wave {
    background: url('https://www.svgrepo.com/show/492220/wave-top.svg') no-repeat center;
    background-size: cover;
}

.divider-triangle {
    background: url('https://www.svgrepo.com/show/453970/triangle-wave.svg') no-repeat center;
    background-size: cover;
}

.divider-curve {
    background: url('https://www.svgrepo.com/show/492221/wave-bottom.svg') no-repeat center;
    background-size: cover;
}

.divider-wave2 {
    background: url('https://www.svgrepo.com/show/491975/wave.svg') no-repeat center;
    background-size: cover;
}

/* --- Parallax Effect --- */
.divider {
    background-attachment: fixed;
}

/* --- Responsiveness --- */
@media (max-width: 768px) {
    .sambutan-section .row { text-align: center; flex-direction: column; }
    .sambutan-section img { margin-bottom: 20px; }
}
</style>

<?php include '../includes/footer.php'; ?>

<!-- ======================= SCRIPT ======================= -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
<script src="https://cdnjs.cloudflare.com/ajax/libs/animate.css/4.1.1/animate.min.js"></script>
