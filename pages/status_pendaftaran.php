<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: ../login.php");
    exit;
}

// Koneksi database
require_once '../includes/config.php';

// Ambil data pendaftaran user
$user_id = $_SESSION['user_id'];
$query = "SELECT * FROM pendaftaran WHERE user_id = ?";
$stmt = $conn->prepare($query);
$stmt->bind_param("i", $user_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows == 0) {
    // Jika belum pernah daftar, redirect ke form pendaftaran
    $_SESSION['error'] = "Anda belum melakukan pendaftaran PPDB.";
    header("Location: pendaftaran.php");
    exit;
}

$data = $result->fetch_assoc();

// Fungsi untuk format status
function getStatusBadge($status) {
    $badges = [
        'pending' => ['text' => 'Menunggu Verifikasi', 'class' => 'warning', 'icon' => 'clock-history'],
        'diterima' => ['text' => 'Diterima', 'class' => 'success', 'icon' => 'check-circle-fill'],
        'ditolak' => ['text' => 'Ditolak', 'class' => 'danger', 'icon' => 'x-circle-fill']
    ];
    return $badges[$status] ?? ['text' => ucfirst($status), 'class' => 'secondary', 'icon' => 'info-circle'];
}

function getStatusPembayaranBadge($status) {
    $badges = [
        'belum_bayar' => ['text' => 'Belum Bayar', 'class' => 'danger', 'icon' => 'x-circle'],
        'menunggu_verifikasi' => ['text' => 'Menunggu Verifikasi', 'class' => 'warning', 'icon' => 'clock'],
        'dibayar' => ['text' => 'Sudah Dibayar', 'class' => 'success', 'icon' => 'check-circle']
    ];
    return $badges[$status] ?? ['text' => ucfirst(str_replace('_', ' ', $status)), 'class' => 'secondary', 'icon' => 'info-circle'];
}

$status_ppdb = getStatusBadge($data['status_ppdb']);
$status_bayar = getStatusPembayaranBadge($data['status_pembayaran']);
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Status Pendaftaran - TK Pertiwi</title>

    <!-- Bootstrap -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Bootstrap Icons -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.min.css">

    <!-- Google Font -->
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

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

        .status-card {
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.1);
            border: 1px solid #e5e9f2;
            margin-bottom: 25px;
        }

        .status-header {
            background: linear-gradient(135deg, #1E90FF, #00B7EB);
            color: white;
            padding: 25px;
            border-radius: 15px;
            margin-bottom: 25px;
            text-align: center;
        }

        .status-header h4 {
            font-weight: 700;
            margin: 0;
        }

        .info-row {
            padding: 15px 0;
            border-bottom: 1px solid #e9ecef;
        }

        .info-row:last-child {
            border-bottom: none;
        }

        .info-label {
            font-weight: 600;
            color: #6c757d;
            font-size: 0.9rem;
        }

        .info-value {
            color: #212529;
            font-size: 1rem;
            margin-top: 5px;
        }

        .badge-custom {
            padding: 10px 20px;
            font-size: 1rem;
            font-weight: 600;
            border-radius: 8px;
        }

        .document-box {
            background: #f8f9fa;
            border: 1px solid #dee2e6;
            border-radius: 10px;
            padding: 15px;
            margin-bottom: 15px;
            transition: all 0.3s;
        }

        .document-box:hover {
            background: #e9ecef;
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0,0,0,0.1);
        }

        .document-box i {
            font-size: 2rem;
            color: #1E90FF;
        }

        .timeline {
            position: relative;
            padding: 20px 0;
        }

        .timeline-item {
            padding: 20px;
            background: white;
            border-radius: 10px;
            margin-bottom: 15px;
            border-left: 4px solid #1E90FF;
            box-shadow: 0 2px 8px rgba(0,0,0,0.05);
        }

        .timeline-date {
            font-size: 0.85rem;
            color: #6c757d;
        }

        .access-code-display {
            background: linear-gradient(135deg, #d1ecf1 0%, #bee5eb 100%);
            border: 2px solid #17a2b8;
            border-radius: 12px;
            padding: 20px;
            text-align: center;
            margin: 20px 0;
        }

        .access-code-display h3 {
            font-family: 'Courier New', monospace;
            font-size: 2.5rem;
            color: #0c5460;
            font-weight: 700;
            margin: 10px 0;
            letter-spacing: 5px;
        }

        .alert-status {
            border-radius: 12px;
            padding: 20px;
            margin-bottom: 25px;
        }

        .footer {
            margin-top: 60px;
            padding: 20px;
            background: #1E90FF;
            color: white;
            text-align: center;
        }

        .btn-custom {
            padding: 12px 30px;
            border-radius: 8px;
            font-weight: 600;
            text-decoration: none;
            display: inline-block;
            transition: all 0.3s;
        }

        .btn-custom:hover {
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
        }

        .section-title {
            font-weight: 700;
            color: #1E3050;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid #1E90FF;
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
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../pages/index.php">Beranda</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="status_pendaftaran.php">Status Pendaftaran</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- HERO -->
    <section class="hero">
        <div class="container">
            <h1 class="fw-bold">Status Pendaftaran PPDB</h1>
            <p class="mb-0">Informasi lengkap tentang pendaftaran Anda</p>
        </div>
    </section>

    <!-- CONTENT -->
    <div class="container content-wrapper">

        <?php if (isset($_SESSION['success'])): ?>
        <div class="alert alert-success alert-dismissible fade show" role="alert">
            <i class="bi bi-check-circle-fill"></i> <?= $_SESSION['success'] ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['success']); endif; ?>

        <!-- Status Header -->
        <div class="status-header">
            <h4><i class="bi bi-<?= $status_ppdb['icon'] ?>"></i> Status Pendaftaran Anda</h4>
            <div class="mt-3">
                <span class="badge bg-<?= $status_ppdb['class'] ?> badge-custom">
                    <?= $status_ppdb['text'] ?>
                </span>
            </div>
        </div>

        <!-- Alert berdasarkan status -->
        <?php if ($data['status_ppdb'] == 'pending'): ?>
        <div class="alert alert-warning alert-status">
            <h5><i class="bi bi-hourglass-split"></i> Sedang Dalam Proses Verifikasi</h5>
            <p class="mb-0">Pendaftaran Anda sedang dalam proses verifikasi oleh admin. Harap menunggu, kami akan segera menghubungi Anda.</p>
        </div>
        <?php elseif ($data['status_ppdb'] == 'diterima'): ?>
        <div class="alert alert-success alert-status">
            <h5><i class="bi bi-check-circle-fill"></i> Selamat! Pendaftaran Anda Diterima</h5>
            <p class="mb-0">Silakan hubungi sekolah untuk informasi lebih lanjut mengenai tahap selanjutnya.</p>
        </div>
        <?php elseif ($data['status_ppdb'] == 'ditolak'): ?>
        <div class="alert alert-danger alert-status">
            <h5><i class="bi bi-x-circle-fill"></i> Pendaftaran Ditolak</h5>
            <p class="mb-0">Maaf, pendaftaran Anda tidak dapat diproses. Silakan hubungi admin untuk informasi lebih lanjut.</p>
        </div>
        <?php endif; ?>

        <div class="row">
            <!-- Informasi Pribadi -->
            <div class="col-lg-6">
                <div class="status-card">
                    <h5 class="section-title"><i class="bi bi-person-badge-fill"></i> Informasi Anak</h5>
                    
                    <div class="info-row">
                        <div class="info-label">Nama Lengkap</div>
                        <div class="info-value"><?= htmlspecialchars($data['nama_anak']) ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Tanggal Lahir</div>
                        <div class="info-value"><?= date('d F Y', strtotime($data['tanggal_lahir_anak'])) ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Umur</div>
                        <div class="info-value">
                            <?php 
                                $lahir = new DateTime($data['tanggal_lahir_anak']);
                                $today = new DateTime();
                                $umur = $today->diff($lahir);
                                echo $umur->y . ' tahun ' . $umur->m . ' bulan';
                            ?>
                        </div>
                    </div>
                </div>

                <div class="status-card">
                    <h5 class="section-title"><i class="bi bi-people-fill"></i> Informasi Orang Tua</h5>
                    
                    <div class="info-row">
                        <div class="info-label">Nama Orang Tua</div>
                        <div class="info-value"><?= htmlspecialchars($data['nama_ortu']) ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Nomor Telepon</div>
                        <div class="info-value"><?= htmlspecialchars($data['nomor_telepon']) ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Email</div>
                        <div class="info-value"><?= htmlspecialchars($data['email']) ?></div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Alamat</div>
                        <div class="info-value"><?= htmlspecialchars($data['alamat']) ?></div>
                    </div>
                </div>
            </div>

            <!-- Status & Dokumen -->
            <div class="col-lg-6">
                <div class="status-card">
                    <h5 class="section-title"><i class="bi bi-clipboard-check-fill"></i> Status Pendaftaran</h5>
                    
                    <div class="info-row">
                        <div class="info-label">Tanggal Daftar</div>
                        <div class="info-value"><?= date('d F Y, H:i', strtotime($data['tanggal_daftar'])) ?> WIB</div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Status PPDB</div>
                        <div class="info-value">
                            <span class="badge bg-<?= $status_ppdb['class'] ?>">
                                <i class="bi bi-<?= $status_ppdb['icon'] ?>"></i> <?= $status_ppdb['text'] ?>
                            </span>
                        </div>
                    </div>

                    <div class="info-row">
                        <div class="info-label">Status Pembayaran</div>
                        <div class="info-value">
                            <span class="badge bg-<?= $status_bayar['class'] ?>">
                                <i class="bi bi-<?= $status_bayar['icon'] ?>"></i> <?= $status_bayar['text'] ?>
                            </span>
                        </div>
                    </div>

                    <?php if (!empty($data['tanggal_pembayaran'])): ?>
                    <div class="info-row">
                        <div class="info-label">Tanggal Pembayaran</div>
                        <div class="info-value"><?= date('d F Y, H:i', strtotime($data['tanggal_pembayaran'])) ?> WIB</div>
                    </div>
                    <?php endif; ?>

                   
                    >
                </div>

                <div class="status-card">
                    <h5 class="section-title"><i class="bi bi-file-earmark-text-fill"></i> Dokumen Terupload</h5>
                    
                    <div class="row">
                        <div class="col-md-6">
                            <div class="document-box text-center">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <div class="mt-2">
                                    <small class="d-block fw-bold">Akta Kelahiran</small>
                                    <?php if (!empty($data['akta_kelahiran'])): ?>
                                        <a href="../uploads/dokumen/<?= $data['akta_kelahiran'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="document-box text-center">
                                <i class="bi bi-file-earmark-pdf"></i>
                                <div class="mt-2">
                                    <small class="d-block fw-bold">Kartu Keluarga</small>
                                    <?php if (!empty($data['kartu_keluarga'])): ?>
                                        <a href="../uploads/dokumen/<?= $data['kartu_keluarga'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="document-box text-center">
                                <i class="bi bi-image"></i>
                                <div class="mt-2">
                                    <small class="d-block fw-bold">Pas Foto</small>
                                    <?php if (!empty($data['pas_foto'])): ?>
                                        <a href="../uploads/dokumen/<?= $data['pas_foto'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <div class="col-md-6">
                            <div class="document-box text-center">
                                <i class="bi bi-file-earmark-medical"></i>
                                <div class="mt-2">
                                    <small class="d-block fw-bold">Surat Sehat</small>
                                    <?php if (!empty($data['surat_sehat'])): ?>
                                        <a href="../uploads/dokumen/<?= $data['surat_sehat'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                            <i class="bi bi-eye"></i> Lihat
                                        </a>
                                    <?php else: ?>
                                        <span class="text-muted">-</span>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>

                        <?php if (!empty($data['bukti_pembayaran'])): ?>
                        <div class="col-md-6">
                            <div class="document-box text-center">
                                <i class="bi bi-receipt"></i>
                                <div class="mt-2">
                                    <small class="d-block fw-bold">Bukti Pembayaran</small>
                                    <a href="../uploads/dokumen/<?= $data['bukti_pembayaran'] ?>" target="_blank" class="btn btn-sm btn-outline-primary mt-2">
                                        <i class="bi bi-eye"></i> Lihat
                                    </a>
                                </div>
                            </div>
                        </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>

        <!-- Action Buttons -->
        <div class="status-card text-center">
            <h5 class="section-title"><i class="bi bi-gear-fill"></i> Aksi</h5>
            <div class="d-flex gap-3 justify-content-center flex-wrap">
                <a href="../pages/index.php" class="btn btn-primary btn-custom">
                    <i class="bi bi-house-fill"></i> Kembali ke Beranda
                </a>
                <button onclick="window.print()" class="btn btn-info btn-custom">
                    <i class="bi bi-printer-fill"></i> Cetak Status
                </button>
                <?php if ($data['status_pembayaran'] == 'belum_bayar'): ?>
                <a href="../pages/payment.php" class="btn btn-success btn-custom">
                    <i class="bi bi-credit-card-fill"></i> Lakukan Pembayaran
                </a>
                <?php endif; ?>
            </div>
        </div>

    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p class="mb-0">© 2025 TK Pertiwi - Sistem PPDB Online</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

</body>

</html>