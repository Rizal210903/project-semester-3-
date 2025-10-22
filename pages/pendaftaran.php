<?php
session_start();

$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";
$error = ""; // Inisialisasi $error

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    error_log("Koneksi DB berhasil");
} catch(PDOException $e) {
    error_log("Koneksi gagal: " . $e->getMessage());
    die("Koneksi gagal: " . $e->getMessage());
}

$confirmation = false;
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nama_anak = trim($_POST['nama_anak'] ?? '');
    $nama_ortu = trim($_POST['nama_ortu'] ?? '');
    $tanggal_lahir_anak = trim($_POST['tanggal_lahir_anak'] ?? '');
    $alamat = trim($_POST['alamat'] ?? '');
    $nomor_telepon = trim($_POST['nomor_telepon'] ?? '');
    $email = trim($_POST['email'] ?? '');

    // Log input untuk debug
    error_log("Input: nama_anak=$nama_anak, nama_ortu=$nama_ortu, tanggal_lahir_anak=$tanggal_lahir_anak, alamat=$alamat, nomor_telepon=$nomor_telepon, email=$email");

    // Validasi kolom wajib
    if (empty($nama_anak) || empty($nama_ortu) || empty($tanggal_lahir_anak)) {
        $error = "Nama anak, nama orang tua, dan tanggal lahir wajib diisi!";
        error_log("Validasi gagal: $error");
    } else {
        // Handle file uploads
        $upload_dir = '../Uploads/';
        if (!is_dir($upload_dir)) {
            mkdir($upload_dir, 0755, true);
            error_log("Folder Uploads dibuat: $upload_dir");
        }
        $akta_kelahiran = '';
        $kartu_keluarga = '';
        $pas_foto = '';
        $surat_sehat = '';
        $allowed_types = ['image/jpeg', 'image/png', 'application/pdf'];
        $max_size = 2 * 1024 * 1024; // 2MB

        foreach (['akta_kelahiran', 'kartu_keluarga', 'pas_foto', 'surat_sehat'] as $field) {
            if (isset($_FILES[$field]) && $_FILES[$field]['error'] === UPLOAD_ERR_OK) {
                $file_type = $_FILES[$field]['type'];
                $file_size = $_FILES[$field]['size'];
                if (!in_array($file_type, $allowed_types)) {
                    $error = "File $field harus berupa PDF, JPG, atau PNG!";
                    error_log("File $field ditolak: tipe tidak diizinkan ($file_type)");
                } elseif ($file_size > $max_size) {
                    $error = "File $field terlalu besar, maksimum 2MB!";
                    error_log("File $field ditolak: ukuran terlalu besar ($file_size bytes)");
                } else {
                    $file_name = time() . '_' . $field . '_' . basename($_FILES[$field]['name']);
                    $file_path = $upload_dir . $file_name;
                    if (move_uploaded_file($_FILES[$field]['tmp_name'], $file_path)) {
                        $$field = $file_name;
                        error_log("File $field berhasil diupload: $file_path");
                    } else {
                        $error = "Gagal upload file $field!";
                        error_log("Gagal upload file $field: " . $_FILES[$field]['error']);
                    }
                }
            }
        }

        if (!$error) {
            $confirmation = true; // Aktifkan modal konfirmasi
        }
    }
}

if (isset($_POST['confirm_submit']) && !$error) {
    try {
        $stmt = $pdo->prepare("INSERT INTO pendaftaran (nama_anak, nama_ortu, tanggal_lahir_anak, alamat, nomor_telepon, email, akta_kelahiran, kartu_keluarga, pas_foto, surat_sehat, tanggal_daftar, status_pembayaran) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW(), 'belum_bayar')");
        $stmt->execute([$nama_anak, $nama_ortu, $tanggal_lahir_anak, $alamat, $nomor_telepon, $email, $akta_kelahiran, $kartu_keluarga, $pas_foto, $surat_sehat]);
        $last_id = $pdo->lastInsertId();
        error_log("Data berhasil disimpan, ID: $last_id");
        $_SESSION['pendaftaran_id'] = $last_id;
        $_SESSION['user_name'] = $nama_ortu;
        header('Location: /project-semester-3-/pages/payment.php');
        exit;
    } catch(PDOException $e) {
        $error = "Gagal simpan data: " . $e->getMessage();
        error_log("Insert error: " . $e->getMessage());
    }
}
?>

<!DOCTYPE html>
<html lang="id">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pendaftaran - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.3/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background: #E0F7FA;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
        }
        .form-container {
            background: rgba(255, 255, 255, 0.95);
            border-radius: 20px;
            box-shadow: 0 15px 40px rgba(0, 0, 0, 0.15);
            padding: 40px;
            max-width: 600px;
            width: 100%;
        }
        .form-control {
            border-radius: 10px;
            border: 2px solid #e0e0e0;
            padding: 12px;
        }
        .form-control:focus {
            border-color: #1E90FF;
            box-shadow: 0 0 0 0.2rem rgba(30, 144, 255, 0.25);
        }
        .btn-primary {
            background: linear-gradient(45deg, #1E90FF, #00B7EB);
            border: none;
            border-radius: 10px;
            padding: 12px 30px;
            width: 100%;
        }
        .btn-primary:hover {
            background: linear-gradient(45deg, #104E8B, #009ACD);
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(30, 144, 255, 0.4);
        }
        .modal-content {
            border-radius: 15px;
        }
        .modal-header {
            background: #1E90FF;
            color: white;
        }
        .modal-footer .btn {
            border-radius: 10px;
        }
    </style>
</head>
<body>
    <div class="form-container">
        <h2 class="text-center mb-4 text-primary">Form Pendaftaran TK Pertiwi</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data" id="registrationForm">
            <div class="mb-3">
                <label for="nama_anak" class="form-label">Nama Anak *</label>
                <input type="text" class="form-control" id="nama_anak" name="nama_anak" value="<?php echo htmlspecialchars($nama_anak ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="nama_ortu" class="form-label">Nama Orang Tua *</label>
                <input type="text" class="form-control" id="nama_ortu" name="nama_ortu" value="<?php echo htmlspecialchars($nama_ortu ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="tanggal_lahir_anak" class="form-label">Tanggal Lahir Anak *</label>
                <input type="date" class="form-control" id="tanggal_lahir_anak" name="tanggal_lahir_anak" value="<?php echo htmlspecialchars($tanggal_lahir_anak ?? ''); ?>" required>
            </div>
            <div class="mb-3">
                <label for="alamat" class="form-label">Alamat</label>
                <textarea class="form-control" id="alamat" name="alamat"><?php echo htmlspecialchars($alamat ?? ''); ?></textarea>
            </div>
            <div class="mb-3">
                <label for="nomor_telepon" class="form-label">Nomor Telepon</label>
                <input type="text" class="form-control" id="nomor_telepon" name="nomor_telepon" value="<?php echo htmlspecialchars($nomor_telepon ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" value="<?php echo htmlspecialchars($email ?? ''); ?>">
            </div>
            <div class="mb-3">
                <label for="akta_kelahiran" class="form-label">Fotokopi Akta Kelahiran</label>
                <input type="file" class="form-control" id="akta_kelahiran" name="akta_kelahiran" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
                <label for="kartu_keluarga" class="form-label">Fotokopi Kartu Keluarga</label>
                <input type="file" class="form-control" id="kartu_keluarga" name="kartu_keluarga" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
                <label for="pas_foto" class="form-label">Pas Foto 3x4</label>
                <input type="file" class="form-control" id="pas_foto" name="pas_foto" accept=".jpg,.jpeg,.png">
            </div>
            <div class="mb-3">
                <label for="surat_sehat" class="form-label">Surat Keterangan Sehat</label>
                <input type="file" class="form-control" id="surat_sehat" name="surat_sehat" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <button type="submit" class="btn btn-primary">Daftar Sekarang</button>
        </form>
        <p class="mt-3 text-center">
            <a href="/project-semester-3-/index.php" class="text-primary">Kembali ke Beranda</a>
        </p>
    </div>

    <!-- Modal Konfirmasi -->
    <div class="modal fade" id="confirmationModal" tabindex="-1" aria-labelledby="confirmationModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="confirmationModalLabel">Konfirmasi Data Pendaftaran</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><strong>Nama Anak:</strong> <span id="confirmNamaAnak"></span></p>
                    <p><strong>Nama Orang Tua:</strong> <span id="confirmNamaOrtu"></span></p>
                    <p><strong>Tanggal Lahir Anak:</strong> <span id="confirmTanggalLahirAnak"></span></p>
                    <p><strong>Alamat:</strong> <span id="confirmAlamat"></span></p>
                    <p><strong>Nomor Telepon:</strong> <span id="confirmNomorTelepon"></span></p>
                    <p><strong>Email:</strong> <span id="confirmEmail"></span></p>
                    <p><strong>Akta Kelahiran:</strong> <span id="confirmAktaKelahiran"></span></p>
                    <p><strong>Kartu Keluarga:</strong> <span id="confirmKartuKeluarga"></span></p>
                    <p><strong>Pas Foto:</strong> <span id="confirmPasFoto"></span></p>
                    <p><strong>Surat Sehat:</strong> <span id="confirmSuratSehat"></span></p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <form method="POST" id="confirmForm">
                        <input type="hidden" name="nama_anak" id="hiddenNamaAnak">
                        <input type="hidden" name="nama_ortu" id="hiddenNamaOrtu">
                        <input type="hidden" name="tanggal_lahir_anak" id="hiddenTanggalLahirAnak">
                        <input type="hidden" name="alamat" id="hiddenAlamat">
                        <input type="hidden" name="nomor_telepon" id="hiddenNomorTelepon">
                        <input type="hidden" name="email" id="hiddenEmail">
                        <input type="hidden" name="akta_kelahiran" id="hiddenAktaKelahiran">
                        <input type="hidden" name="kartu_keluarga" id="hiddenKartuKeluarga">
                        <input type="hidden" name="pas_foto" id="hiddenPasFoto">
                        <input type="hidden" name="surat_sehat" id="hiddenSuratSehat">
                        <input type="hidden" name="confirm_submit" value="1">
                        <button type="submit" class="btn btn-primary">Konfirmasi dan Lanjut</button>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        document.getElementById('registrationForm').addEventListener('submit', function(e) {
            e.preventDefault(); // Cegah submit langsung

            // Ambil nilai dari form
            const namaAnak = document.getElementById('nama_anak').value;
            const namaOrtu = document.getElementById('nama_ortu').value;
            const tanggalLahirAnak = document.getElementById('tanggal_lahir_anak').value;
            const alamat = document.getElementById('alamat').value;
            const nomorTelepon = document.getElementById('nomor_telepon').value;
            const email = document.getElementById('email').value;
            const aktaKelahiran = document.getElementById('akta_kelahiran').files[0]?.name || 'Tidak diunggah';
            const kartuKeluarga = document.getElementById('kartu_keluarga').files[0]?.name || 'Tidak diunggah';
            const pasFoto = document.getElementById('pas_foto').files[0]?.name || 'Tidak diunggah';
            const suratSehat = document.getElementById('surat_sehat').files[0]?.name || 'Tidak diunggah';

            // Isi modal dengan data
            document.getElementById('confirmNamaAnak').textContent = namaAnak;
            document.getElementById('confirmNamaOrtu').textContent = namaOrtu;
            document.getElementById('confirmTanggalLahirAnak').textContent = tanggalLahirAnak;
            document.getElementById('confirmAlamat').textContent = alamat || 'Tidak diisi';
            document.getElementById('confirmNomorTelepon').textContent = nomorTelepon || 'Tidak diisi';
            document.getElementById('confirmEmail').textContent = email || 'Tidak diisi';
            document.getElementById('confirmAktaKelahiran').textContent = aktaKelahiran;
            document.getElementById('confirmKartuKeluarga').textContent = kartuKeluarga;
            document.getElementById('confirmPasFoto').textContent = pasFoto;
            document.getElementById('confirmSuratSehat').textContent = suratSehat;

            // Isi hidden input untuk submit terpisah
            document.getElementById('hiddenNamaAnak').value = namaAnak;
            document.getElementById('hiddenNamaOrtu').value = namaOrtu;
            document.getElementById('hiddenTanggalLahirAnak').value = tanggalLahirAnak;
            document.getElementById('hiddenAlamat').value = alamat;
            document.getElementById('hiddenNomorTelepon').value = nomorTelepon;
            document.getElementById('hiddenEmail').value = email;
            document.getElementById('hiddenAktaKelahiran').value = aktaKelahiran === 'Tidak diunggah' ? '' : aktaKelahiran;
            document.getElementById('hiddenKartuKeluarga').value = kartuKeluarga === 'Tidak diunggah' ? '' : kartuKeluarga;
            document.getElementById('hiddenPasFoto').value = pasFoto === 'Tidak diunggah' ? '' : pasFoto;
            document.getElementById('hiddenSuratSehat').value = suratSehat === 'Tidak diunggah' ? '' : suratSehat;

            // Tampilkan modal
            const modal = new bootstrap.Modal(document.getElementById('confirmationModal'));
            modal.show();
        });
    </script>
</body>
</html>