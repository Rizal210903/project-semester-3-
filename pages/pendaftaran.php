<?php
session_start();

// Cek login
if (!isset($_SESSION['user_id'])) {
    header("Location: /project-semester-3-/login.php");
    exit;
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
            padding: 60px 0;
            background: linear-gradient(135deg, #1E90FF, #00B7EB);
            color: white;
            text-align: center;
        }

        .form-card {
            margin-top: -60px;
            background: white;
            padding: 30px;
            border-radius: 20px;
            box-shadow: 0px 10px 30px rgba(0, 0, 0, 0.15);
        }

        .footer {
            margin-top: 60px;
            padding: 20px;
            background: #1E90FF;
            color: white;
            text-align: center;
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
            <p>Isi data dengan benar sesuai dokumen yang dimiliki</p>
        </div>
    </section>

    <!-- FORM CARD -->
    <div class="container">
        <div class="form-card">

            <h3 class="fw-bold mb-3">Form Pendaftaran</h3>
            <hr>

            <form action="pendaftaran_proses.php" method="POST" enctype="multipart/form-data">

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Anak</label>
                        <input type="text" name="nama_anak" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nama Orang Tua</label>
                        <input type="text" name="nama_ortu" value="<?= $_SESSION['nama_ortu'] ?? '' ?>"
                            class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Tanggal Lahir Anak</label>
                        <input type="date" name="tanggal_lahir_anak" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Nomor Telepon</label>
                        <input type="text" name="nomor_telepon" class="form-control" required>
                    </div>

                    <div class="col-12 mb-3">
                        <label class="form-label">Alamat Lengkap</label>
                        <textarea name="alamat" rows="3" class="form-control" required></textarea>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Email</label>
                        <input type="email" name="email" class="form-control" required>
                    </div>

                </div>

                <hr>
                <h5 class="fw-bold mb-3">Upload Dokumen</h5>

                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label class="form-label">Akta Kelahiran</label>
                        <input type="file" name="akta_kelahiran" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Kartu Keluarga</label>
                        <input type="file" name="kartu_keluarga" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Pas Foto Anak</label>
                        <input type="file" name="pas_foto" class="form-control" required>
                    </div>

                    <div class="col-md-6 mb-3">
                        <label class="form-label">Surat Keterangan Sehat</label>
                        <input type="file" name="surat_sehat" class="form-control" required>
                    </div>
                </div>

                <form action="proses_payment.php" method="post" enctype="multipart/form-data">
                    <input type="hidden" name="pendaftar_id" value="<?= $pendaftar_id ?>">
                    <input type="file" name="bukti_pembayaran" required>
                    <button type="submit" class="btn btn-primary px-4 py-2">
                        Kirim Pendaftaran
                    </button>
                </form>
            </form>
        </div>
    </div>

    <!-- FOOTER -->
    <div class="footer">
        <p class="mb-0">© 2025 TK Pertiwi - Sistem PPDB Online</p>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>