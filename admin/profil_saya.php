<?php
// Jalankan session lebih awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah output sebelum header()
ob_start();

// Pastikan user sudah login
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Koneksi ke database
$servername = "localhost";
$username = "root";
$password = "";
$dbname = "tk_pertiwi_db";

try {
    $pdo = new PDO("mysql:host=$servername;dbname=$dbname;charset=utf8mb4", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    die("Koneksi gagal: " . $e->getMessage());
}

// Ambil data user
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch(PDO::FETCH_ASSOC);

$success = "";
$error = "";

// Proses update data
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usernameBaru = $_POST['username'];
    $email = $_POST['email'];

    // Upload foto profil (opsional)
    $foto = $user['foto']; // Gunakan foto lama sebagai default

    if (!empty($_FILES['foto']['name'])) {
        // Path folder upload yang benar
        $target_dir = "../uploads/profil/";

        // Buat folder jika belum ada
        if (!is_dir($target_dir)) {
            mkdir($target_dir, 0777, true);
        }

        $foto_name = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto_name;

        // Validasi tipe file
        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png', 'gif'];

        // Validasi ukuran file (max 2MB)
        $maxSize = 2 * 1024 * 1024; // 2MB dalam bytes

        if (!in_array($fileType, $allowedTypes)) {
            $error = "Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.";
        } elseif ($_FILES['foto']['size'] > $maxSize) {
            $error = "Ukuran file terlalu besar! Maksimal 2MB.";
        } else {
            // Upload file
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                // Hapus foto lama jika ada
                if (!empty($user['foto']) && file_exists("../uploads/profil/" . $user['foto'])) {
                    unlink("../uploads/profil/" . $user['foto']);
                }
                $foto = $foto_name;
            } else {
                $error = "Gagal mengupload foto!";
            }
        }
    }

    // Jika tidak ada error upload, lanjutkan update
    if (empty($error)) {
        // Update data profil
        $update = $pdo->prepare("UPDATE users SET username = ?, email = ?, foto = ? WHERE id = ?");
        $update->execute([$usernameBaru, $email, $foto, $_SESSION['user_id']]);
        // Perbarui session dengan data baru
        $_SESSION['username'] = $usernameBaru;
        $_SESSION['foto'] = $foto;


        // Cek apakah user mau ubah password
        if (!empty($_POST['password_lama']) && !empty($_POST['password_baru']) && !empty($_POST['konfirmasi_password'])) {
            $password_lama = $_POST['password_lama'];
            $password_baru = $_POST['password_baru'];
            $konfirmasi_password = $_POST['konfirmasi_password'];

            // Validasi password lama
            if (password_verify($password_lama, $user['password'])) {
                if ($password_baru === $konfirmasi_password) {
                    if (strlen($password_baru) >= 6) {
                        $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                        $updatePass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                        $updatePass->execute([$password_hash, $_SESSION['user_id']]);
                        $success = "Profil dan password berhasil diperbarui!";
                    } else {
                        $error = "Password baru minimal 6 karakter!";
                    }
                } else {
                    $error = "Konfirmasi password tidak cocok!";
                }
            } else {
                $error = "Password lama salah!";
            }
        } else {
            $success = "Profil berhasil diperbarui!";
        }

        // Refresh data user setelah update
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

ob_end_flush();
?>

<!DOCTYPE html>
<html lang="id">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Profil - TK Pertiwi</title>

    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">

    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Poppins', sans-serif;
            background-color: #eaf6ff;
            min-height: 100vh;
            padding: 40px 20px;
            position: relative;
            overflow-x: hidden;
        }

        .edit-card {
            max-width: 650px;
            margin: 0 auto;
            background: white;
            border-radius: 20px;
            padding: 45px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border: 2px solid #d6eaff;
        }

        .edit-card h3 {
            color: #007bff;
            font-weight: 700;
            text-align: center;
            margin-bottom: 35px;
            font-size: 28px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 12px;
        }

        .edit-card h3 i {
            font-size: 32px;
            color: #007bff;
        }

        .icon-input {
            position: absolute;
            left: 18px;
            top: 50%;
            transform: translateY(-50%);
            color: #007bff;
            font-size: 20px;
            z-index: 10;
        }

        .form-control {
            padding-left: 50px;
            border-radius: 12px;
            border: 2px solid #d0d7de;
            height: 52px;
            font-size: 15px;
            transition: all 0.3s ease;
            background: #f8f9fa;
        }

        .form-control:focus {
            border-color: #007bff;
            box-shadow: 0 0 0 0.25rem rgba(0, 123, 255, 0.15);
            background: white;
        }

        label {
            font-weight: 600;
            color: #007bff;
            margin-bottom: 10px;
            display: block;
            font-size: 15px;
        }

        /* Profile Picture Preview */
        .profile-picture-section {
            text-align: center;
            margin-bottom: 35px;
            padding: 25px;
            background: linear-gradient(135deg, #f0f4ff 0%, #e8eef7 100%);
            border-radius: 20px;
        }

        .profile-picture-section > label {
            font-size: 17px;
            color: #007bff;
            margin-bottom: 20px;
        }

        .avatar-preview {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid #007bff;
            margin: 20px auto;
            display: block;
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
            transition: transform 0.3s ease;
        }

        .avatar-preview:hover {
            transform: scale(1.05);
        }

        .default-avatar {
            width: 160px;
            height: 160px;
            border-radius: 50%;
            background: linear-gradient(135deg, #007bff, #0056b3);
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 20px auto;
            border: 5px solid #007bff;
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.3);
        }

        .default-avatar i {
            font-size: 70px;
            color: white;
        }

        .file-upload-wrapper {
            position: relative;
            margin-top: 20px;
        }

        .file-upload-label {
            display: inline-block;
            padding: 14px 35px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border-radius: 12px;
            cursor: pointer;
            transition: all 0.3s ease;
            font-weight: 600;
            font-size: 15px;
            box-shadow: 0 5px 15px rgba(0, 123, 255, 0.3);
        }

        .file-upload-label:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 25px rgba(0, 123, 255, 0.4);
            background: linear-gradient(135deg, #0056b3, #007bff);
        }

        .file-upload-label i {
            margin-right: 10px;
            font-size: 18px;
        }

        input[type="file"] {
            display: none;
        }

        .file-name-display {
            margin-top: 12px;
            font-size: 14px;
            color: #007bff;
            font-weight: 500;
        }

        .btn-save {
            width: 100%;
            padding: 16px;
            background: linear-gradient(135deg, #007bff, #0056b3);
            color: white;
            border: none;
            border-radius: 12px;
            font-weight: 600;
            font-size: 17px;
            transition: all 0.3s ease;
            margin-top: 25px;
            box-shadow: 0 5px 20px rgba(0, 123, 255, 0.3);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .btn-save:hover {
            transform: translateY(-3px);
            box-shadow: 0 8px 30px rgba(0, 123, 255, 0.4);
            background: linear-gradient(135deg, #0056b3, #007bff);
        }

        .btn-save i {
            font-size: 20px;
        }

        .btn-cancel {
            width: 100%;
            padding: 14px;
            background: #e9ecef;
            color: #495057;
            border: 2px solid #d0d7de;
            border-radius: 12px;
            font-weight: 600;
            margin-top: 12px;
            text-decoration: none;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 8px;
            font-size: 16px;
        }

        .btn-cancel:hover {
            background: #dee2e6;
            color: #212529;
            border-color: #adb5bd;
            transform: translateY(-2px);
        }

        .btn-cancel i {
            font-size: 18px;
        }

        hr {
            margin: 35px 0;
            border: none;
            height: 2px;
            background: linear-gradient(90deg, transparent, #d0d7de, transparent);
        }

        .alert {
            border-radius: 12px;
            border: none;
            padding: 15px 20px;
            font-weight: 500;
            margin-bottom: 25px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }

        .alert i {
            margin-right: 8px;
            font-size: 18px;
        }

        .alert-success {
            background: linear-gradient(135deg, #d4edda, #c3e6cb);
            color: #155724;
        }

        .alert-danger {
            background: linear-gradient(135deg, #f8d7da, #f5c6cb);
            color: #721c24;
        }

        .password-section-title {
            text-align: center;
            margin-bottom: 25px;
            font-weight: 600;
            color: #007bff;
            font-size: 19px;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .password-section-title i {
            font-size: 24px;
            color: #007bff;
        }

        .text-muted {
            color: #6c757d !important;
            font-size: 13px;
        }

        /* Responsive */
        @media (max-width: 576px) {
            .edit-card {
                padding: 30px 20px;
            }

            .edit-card h3 {
                font-size: 24px;
            }

            .avatar-preview,
            .default-avatar {
                width: 130px;
                height: 130px;
            }

            .default-avatar i {
                font-size: 55px;
            }
        }
    </style>
</head>

<body>

    <div class="edit-card">

        <h3><i class="bi bi-person-circle"></i> Edit Profil</h3>

        <?php if ($success): ?>
            <div class="alert alert-success">
                <i class="bi bi-check-circle-fill"></i> <?= htmlspecialchars($success) ?>
            </div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger">
                <i class="bi bi-exclamation-triangle-fill"></i> <?= htmlspecialchars($error) ?>
            </div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data" id="profileForm">

            <!-- Profile Picture Section -->
            <div class="profile-picture-section">
                <label>Foto Profil</label>

                <?php if (!empty($user['foto'])): ?>
                    <img src="../uploads/profil/<?= htmlspecialchars($user['foto']) ?>" class="avatar-preview"
                        id="imagePreview" alt="Foto Profil">
                <?php else: ?>
                    <div class="default-avatar" id="defaultAvatar">
                        <i class="bi bi-person-fill"></i>
                    </div>
                    <img src="#" class="avatar-preview" id="imagePreview" alt="Preview" style="display: none;">
                <?php endif; ?>

                <div class="file-upload-wrapper">
                    <label for="fotoInput" class="file-upload-label">
                        <i class="bi bi-camera-fill"></i> Pilih Foto
                    </label>
                    <input type="file" name="foto" id="fotoInput" accept="image/*">
                    <div class="file-name-display" id="fileName"></div>
                </div>
                <small class="text-muted d-block mt-2">
                    Format: JPG, JPEG, PNG, GIF (Maks. 2MB)
                </small>
            </div>

            <label>Username</label>
            <div class="position-relative mb-3">
                <i class="bi bi-person icon-input"></i>
                <input type="text" name="username" class="form-control"
                    value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <label>Email</label>
            <div class="position-relative mb-3">
                <i class="bi bi-envelope icon-input"></i>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>"
                    required>
            </div>

            <hr>

            <div class="password-section-title">
                <i class="bi bi-shield-lock-fill"></i> Ubah Password (Opsional)
            </div>

            <label>Password Lama</label>
            <div class="position-relative mb-3">
                <i class="bi bi-lock icon-input"></i>
                <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama">
            </div>

            <label>Password Baru</label>
            <div class="position-relative mb-3">
                <i class="bi bi-lock-fill icon-input"></i>
                <input type="password" name="password_baru" class="form-control"
                    placeholder="Masukkan password baru (min. 6 karakter)">
            </div>

            <label>Konfirmasi Password</label>
            <div class="position-relative mb-4">
                <i class="bi bi-check2-circle icon-input"></i>
                <input type="password" name="konfirmasi_password" class="form-control"
                    placeholder="Ulangi password baru">
            </div>

            <button type="submit" class="btn-save">
                <i class="bi bi-save-fill"></i> Simpan Perubahan
            </button>

            <a href="admin_dashboard.php" class="btn-cancel">
                <i class="bi bi-x-circle"></i> Batal
            </a>

        </form>
    </div>

    <script>
        // Preview gambar sebelum upload
        document.getElementById('fotoInput').addEventListener('change', function (e) {
            const file = e.target.files[0];
            const fileName = document.getElementById('fileName');
            const imagePreview = document.getElementById('imagePreview');
            const defaultAvatar = document.getElementById('defaultAvatar');

            if (file) {
                // Validasi ukuran file (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('Ukuran file terlalu besar! Maksimal 2MB.');
                    this.value = '';
                    return;
                }

                // Validasi tipe file
                const allowedTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
                if (!allowedTypes.includes(file.type)) {
                    alert('Format file tidak didukung! Gunakan JPG, JPEG, PNG, atau GIF.');
                    this.value = '';
                    return;
                }

                // Tampilkan nama file
                fileName.textContent = '📁 ' + file.name;

                // Preview gambar
                const reader = new FileReader();
                reader.onload = function (event) {
                    imagePreview.src = event.target.result;
                    imagePreview.style.display = 'block';
                    if (defaultAvatar) {
                        defaultAvatar.style.display = 'none';
                    }
                };
                reader.readAsDataURL(file);
            } else {
                fileName.textContent = '';
            }
        });

        // Konfirmasi sebelum submit jika ada perubahan password
        document.getElementById('profileForm').addEventListener('submit', function (e) {
            const passLama = document.querySelector('input[name="password_lama"]').value;
            const passBaru = document.querySelector('input[name="password_baru"]').value;
            const passKonfirm = document.querySelector('input[name="konfirmasi_password"]').value;

            // Jika salah satu field password diisi tapi tidak lengkap
            if ((passLama || passBaru || passKonfirm) && (!passLama || !passBaru || !passKonfirm)) {
                e.preventDefault();
                alert('Harap lengkapi semua field password jika ingin mengubah password!');
                return false;
            }

            // Validasi panjang password baru
            if (passBaru && passBaru.length < 6) {
                e.preventDefault();
                alert('Password baru minimal 6 karakter!');
                return false;
            }
        });
    </script>

</body>

</html>