<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Koneksi database
require_once '../includes/config.php';

// Cek apakah user sudah pernah mendaftar
$user_id = $_SESSION['user_id'];
$query_cek = "SELECT * FROM pendaftaran WHERE user_id = ?";
$stmt = $conn->prepare($query_cek);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

$sudah_daftar = $result->num_rows > 0;
$data_pendaftaran = null;

if ($sudah_daftar) {
    $data_pendaftaran = $result->fetch_assoc();
}

// Fungsi untuk format status
function formatStatus($status) {
    $status_map = [
        'pending' => ['label' => 'Menunggu Verifikasi', 'class' => 'warning'],
        'diterima' => ['label' => 'Diterima', 'class' => 'success'],
        'ditolak' => ['label' => 'Ditolak', 'class' => 'danger']
    ];
    return $status_map[$status] ?? ['label' => ucfirst($status), 'class' => 'secondary'];
}

function formatStatusPembayaran($status) {
    $status_map = [
        'belum_bayar' => ['label' => 'Belum Bayar', 'class' => 'danger'],
        'menunggu_verifikasi' => ['label' => 'Menunggu Verifikasi', 'class' => 'warning'],
        'dibayar' => ['label' => 'Sudah Dibayar', 'class' => 'success']
    ];
    return $status_map[$status] ?? ['label' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'secondary'];
}
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Form Pendaftaran PPDB - TK Pertiwi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <!-- Sweet Alert -->
    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>

    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #f1f7ff;
        }

        .navbar {
            background: #1E90FF !important;
        }

        .hero {
            padding: 60px 0 80px;
            background: linear-gradient(135deg, #1E90FF, #00B7EB);
            color: white;
            text-align: center;
        }

        .content-wrapper {
            margin-top: -40px;
            margin-bottom: 60px;
        }

        .form-card {
            background: white;
            padding: 35px 40px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e9f2;
        }

        .alert-registered {
            background: linear-gradient(135deg, #fff3cd 0%, #ffe9a8 100%);
            border-radius: 15px;
            border: 2px solid #ffc107;
            padding: 25px;
            margin-bottom: 0;
        }

        .alert-registered h4 {
            color: #856404;
            font-weight: 700;
            margin-bottom: 15px;
        }

        .alert-registered p {
            color: #856404;
            margin-bottom: 0;
        }

        .status-card {
            background: #f8f9fa;
            border-radius: 12px;
            padding: 20px;
            margin-top: 20px;
            border: 1px solid #dee2e6;
        }

        .status-card .row > div {
            padding: 10px 0;
        }

        .info-label {
            font-weight: 600;
            color: #495057;
            display: block;
            margin-bottom: 5px;
            font-size: 0.9rem;
        }

        .info-value {
            color: #212529;
            font-size: 1rem;
        }

        .badge {
            padding: 8px 16px;
            font-size: 0.85rem;
            font-weight: 600;
        }

        .btn-action {
            padding: 10px 24px;
            border-radius: 8px;
            font-weight: 600;
        }

        .form-label {
            font-weight: 600;
            color: #495057;
            margin-bottom: 8px;
        }

        .form-control, .form-select {
            border-radius: 8px;
            border: 1px solid #ced4da;
            padding: 10px 15px;
        }

        .form-control:focus {
            border-color: #1E90FF;
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        }

        .info-box {
            background: #e7f3ff;
            border-left: 4px solid #1E90FF;
            padding: 15px 20px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .info-box i {
            color: #1E90FF;
            margin-right: 8px;
        }

        .section-title {
            font-weight: 700;
            color: #1E3050;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1E90FF;
        }

        .footer {
            margin-top: 60px;
            padding: 20px;
            background: #1E90FF;
            color: white;
            text-align: center;
        }

        .btn-primary {
            background: #1E90FF;
            border: none;
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
        }

        .btn-primary:hover {
            background: #1873CC;
        }

        .access-code-box {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border: 2px solid #17a2b8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
        }

        .access-code-box h4 {
            font-family: 'Courier New', monospace;
            font-size: 2rem;
            color: #0c5460;
            font-weight: 700;
            margin: 10px 0;
            letter-spacing: 3px;
        }
    </style>

</head>

<body>

    <!-- NAVBAR -->
    <nav class="navbar navbar-expand-lg navbar-dark">
        <div class="container">
            <a class="navbar-brand fw-bold" href="#">TK Pertiwi</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <h1 class="fw-bold">Pendaftaran Peserta Didik Baru</h1>
            <p class="mb-0">Isi data dengan benar sesuai dokumen yang dimiliki</p>
        </div>
    </section>

    <!-- CONTENT -->
    <div class="container content-wrapper">
        <div class="form-card">

            <?php if ($sudah_daftar): 
                $status_ppdb = formatStatus($data_pendaftaran['status_ppdb']);
                $status_bayar = formatStatusPembayaran($data_pendaftaran['status_pembayaran']);
            ?>
                <!-- Alert jika sudah pernah mendaftar -->
                <div class="alert-registered">
                    <div class="d-flex align-items-center mb-3">
                        <i class="bi bi-exclamation-triangle-fill fs-1 me-3" style="color: #856404;"></i>
                        <div>
                            <h4 class="mb-1">Akun Sudah Terdaftar!</h4>
                            <p>Akun Anda sudah pernah digunakan untuk mendaftar PPDB. Setiap akun hanya dapat melakukan 1 kali pendaftaran.</p>
                        </div>
                    </div>
                </div>

                <!-- Status Card -->
                <div class="status-card">
                    <h5 class="fw-bold mb-3"><i class="bi bi-info-circle text-primary"></i> Informasi Pendaftaran Anda</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-person-fill text-primary"></i> Nama Anak</span>
                            <div class="info-value"><?= htmlspecialchars($data_pendaftaran['nama_anak']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-people-fill text-primary"></i> Nama Orang Tua</span>
                            <div class="info-value"><?= htmlspecialchars($data_pendaftaran['nama_ortu']) ?></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-calendar-fill text-primary"></i> Tanggal Lahir</span>
                            <div class="info-value"><?= date('d F Y', strtotime($data_pendaftaran['tanggal_lahir_anak'])) ?></div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-clock-history text-primary"></i> Tanggal Daftar</span>
                            <div class="info-value"><?= date('d F Y, H:i', strtotime($data_pendaftaran['tanggal_daftar'])) ?> WIB</div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-clipboard-check-fill text-primary"></i> Status PPDB</span>
                            <div class="info-value">
                                <span class="badge bg-<?= $status_ppdb['class'] ?>">
                                    <?= $status_ppdb['label'] ?>
                                </span>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <span class="info-label"><i class="bi bi-credit-card-fill text-primary"></i> Status Pembayaran</span>
                            <div class="info-value">
                                <span class="badge bg-<?= $status_bayar['class'] ?>">
                                    <?= $status_bayar['label'] ?>
                                </span>
                            </div>
                        </div>
                    </div>

                    <?php if (!empty($data_pendaftaran['access_code'])): ?>
                    <div class="access-code-box mt-3">
                        <span class="info-label"><i class="bi bi-key-fill"></i> Kode Akses Anda</span>
                        <h4><?= htmlspecialchars($data_pendaftaran['access_code']) ?></h4>
                        <small class="text-muted">Simpan kode ini untuk melihat status pendaftaran</small>
                    </div>
                    <?php endif; ?>

                    <hr class="my-4">

                    <div class="d-flex gap-2 flex-wrap justify-content-center">
                        <a href="status_pendaftaran.php" class="btn btn-primary btn-action">
                            <i class="bi bi-eye-fill"></i> Lihat Detail Pendaftaran
                        </a>
                        <a href="/project-semester-3-/pages/index.php" class="btn btn-secondary btn-action">
                            <i class="bi bi-house-fill"></i> Kembali ke Beranda
                        </a>
                        <?php if ($data_pendaftaran['status_pembayaran'] == 'belum_bayar'): ?>
                        <a href="payment.php" class="btn btn-success btn-action">
                            <i class="bi bi-credit-card-fill"></i> Lakukan Pembayaran
                        </a>
                        <?php endif; ?>
                    </div>
                </div>

            <?php else: ?>
                <!-- Form Pendaftaran -->
                <h3 class="section-title"><i class="bi bi-pencil-square"></i> Form Pendaftaran PPDB</h3>

                <?php if (isset($_SESSION['error'])): ?>
                    <div class="alert alert-danger alert-dismissible fade show" role="alert">
                        <i class="bi bi-exclamation-circle-fill"></i> <?= $_SESSION['error'] ?>
                        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                    </div>
                    <?php unset($_SESSION['error']); ?>
                <?php endif; ?>

                <form action="pendaftaran_proses.php" method="POST" enctype="multipart/form-data" id="formPendaftaran">

                    <h5 class="fw-bold mb-3 mt-4"><i class="bi bi-person-badge-fill text-primary"></i> Data Anak</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Lengkap Anak <span class="text-danger">*</span></label>
                            <input type="text" name="nama_anak" class="form-control" placeholder="Contoh: Ahmad Fauzi" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tanggal Lahir Anak <span class="text-danger">*</span></label>
                            <input type="date" name="tanggal_lahir_anak" class="form-control" required>
                        </div>
                    </div>

                    <hr class="my-4">
                    
                    <h5 class="fw-bold mb-3"><i class="bi bi-people-fill text-primary"></i> Data Orang Tua</h5>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Orang Tua <span class="text-danger">*</span></label>
                            <input type="text" name="nama_ortu" value="<?= htmlspecialchars($_SESSION['nama_ortu'] ?? '') ?>"
                                class="form-control" placeholder="Contoh: Budi Santoso" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nomor Telepon <span class="text-danger">*</span></label>
                            <input type="text" name="nomor_telepon" class="form-control" placeholder="Contoh: 081234567890" pattern="[0-9]{10,13}" required>
                            <small class="text-muted">Format: 10-13 digit angka</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Email <span class="text-danger">*</span></label>
                            <input type="email" name="email" class="form-control" placeholder="contoh@email.com" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Alamat Lengkap <span class="text-danger">*</span></label>
                            <textarea name="alamat" rows="3" class="form-control" placeholder="Jl. Contoh No. 123, RT/RW, Kelurahan, Kecamatan" required></textarea>
                        </div>
                    </div>

                    <hr class="my-4">
                    <h5 class="fw-bold mb-3"><i class="bi bi-file-earmark-arrow-up-fill text-primary"></i> Upload Dokumen</h5>
                    
                    <div class="info-box">
                        <small>
                            <i class="bi bi-info-circle-fill"></i> 
                            <strong>Catatan:</strong> Semua dokumen wajib diupload. Format yang diterima: JPG, PNG, PDF (Max: 2MB per file)
                        </small>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Akta Kelahiran <span class="text-danger">*</span></label>
                            <input type="file" name="akta_kelahiran" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Kartu Keluarga <span class="text-danger">*</span></label>
                            <input type="file" name="kartu_keluarga" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Pas Foto Anak (3x4) <span class="text-danger">*</span></label>
                            <input type="file" name="pas_foto" class="form-control" accept=".jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: JPG, PNG (Max: 1MB)</small>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Surat Keterangan Sehat <span class="text-danger">*</span></label>
                            <input type="file" name="surat_sehat" class="form-control" accept=".pdf,.jpg,.jpeg,.png" required>
                            <small class="text-muted">Format: PDF, JPG, PNG (Max: 2MB)</small>
                        </div>
                    </div>

                    <div class="text-center mt-4">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-send-fill"></i> Kirim Pendaftaran
                        </button>
                    </div>
                </form>
            <?php endif; ?>

        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p class="mb-0">© 2025 TK Pertiwi - Sistem PPDB Online</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Konfirmasi sebelum submit
        document.getElementById('formPendaftaran')?.addEventListener('submit', function(e) {
            e.preventDefault();
            
            Swal.fire({
                title: 'Konfirmasi Pendaftaran',
                html: `
                    <p>Pastikan semua data yang Anda masukkan sudah benar.</p>
                    <p class="text-danger mb-0"><strong>Catatan:</strong> Setelah mengirim, Anda akan diarahkan ke halaman pembayaran.</p>
                `,
                icon: 'question',
                showCancelButton: true,
                confirmButtonColor: '#1E90FF',
                cancelButtonColor: '#6c757d',
                confirmButtonText: '<i class="bi bi-check-circle"></i> Ya, Kirim!',
                cancelButtonText: '<i class="bi bi-x-circle"></i> Cek Lagi'
            }).then((result) => {
                if (result.isConfirmed) {
                    Swal.fire({
                        title: 'Mengirim...',
                        html: 'Mohon tunggu, sedang memproses pendaftaran Anda.',
                        allowOutsideClick: false,
                        didOpen: () => {
                            Swal.showLoading();
                        }
                    });
                    this.submit();
                }
            });
        });

        // Validasi ukuran file
        document.querySelectorAll('input[type="file"]').forEach(input => {
            input.addEventListener('change', function() {
                const maxSize = this.accept.includes('foto') || this.name === 'pas_foto' ? 1 * 1024 * 1024 : 2 * 1024 * 1024;
                if (this.files[0] && this.files[0].size > maxSize) {
                    Swal.fire({
                        icon: 'error',
                        title: 'File Terlalu Besar',
                        text: `Ukuran file maksimal ${maxSize / 1024 / 1024}MB`
                    });
                    this.value = '';
                }
            });
        });
    </script>
</body>

</html>