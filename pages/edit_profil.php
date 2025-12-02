<?php
// Jalankan session lebih awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah output sebelum header() (hilangkan echo/print_r di header.php)
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
    $nama_ortu = $_POST['nama_ortu'];
    $email = $_POST['email'];

    // Upload foto (opsional)
    $foto = isset($user['foto']) ? $user['foto'] : null;
    if (!empty($_FILES['foto']['name'])) {
        $target_dir = "../uploads/";
        if (!is_dir($target_dir)) mkdir($target_dir, 0777, true);

        $foto_name = time() . '_' . basename($_FILES['foto']['name']);
        $target_file = $target_dir . $foto_name;

        $fileType = strtolower(pathinfo($target_file, PATHINFO_EXTENSION));
        $allowedTypes = ['jpg', 'jpeg', 'png'];

        if (in_array($fileType, $allowedTypes)) {
            if (move_uploaded_file($_FILES['foto']['tmp_name'], $target_file)) {
                $foto = $foto_name;
            }
        }
    }

    // Update data profil (tanpa password dulu)
    $update = $pdo->prepare("UPDATE users SET username = ?, nama_ortu = ?, email = ?, foto = ? WHERE id = ?");
    $update->execute([$usernameBaru, $nama_ortu, $email, $foto, $_SESSION['user_id']]);

    // Cek apakah user mau ubah password
    if (!empty($_POST['password_lama']) && !empty($_POST['password_baru']) && !empty($_POST['konfirmasi_password'])) {
        $password_lama = $_POST['password_lama'];
        $password_baru = $_POST['password_baru'];
        $konfirmasi_password = $_POST['konfirmasi_password'];

        // Validasi password lama
        if (password_verify($password_lama, $user['password'])) {
            if ($password_baru === $konfirmasi_password) {
                $password_hash = password_hash($password_baru, PASSWORD_DEFAULT);
                $updatePass = $pdo->prepare("UPDATE users SET password = ? WHERE id = ?");
                $updatePass->execute([$password_hash, $_SESSION['user_id']]);
                $success = "Profil dan password berhasil diperbarui!";
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
    body {
        height: 100%;
        min-height: 100vh;
        display: flex;
        justify-content: center;
        padding-top: 50px;
        padding-bottom: 50px;
        background: linear-gradient(180deg, #f9fcff 0%, #e7f1ff 100%);
        font-family: 'Poppins', sans-serif;
    }

    .edit-card {
        width: 600px;
        padding: 40px;
        border-radius: 30px;
        background: rgba(255, 255, 255, 0.25);
        backdrop-filter: blur(30px);
        -webkit-backdrop-filter: blur(30px);
        border: 1.5px solid rgba(255, 255, 255, 0.55);
        box-shadow: 0 20px 50px rgba(0, 0, 0, 0.18);
        transition: .3s ease;
    }

    .edit-card:hover {
        background: rgba(255, 255, 255, 0.32);
        border: 1.5px solid rgba(255, 255, 255, 0.75);
        transform: scale(1.01);
    }

    .edit-card h3 {
        font-weight: 700;
        text-align: center;
        margin-bottom: 25px;
        color: #003366;
    }

    .form-control {
        padding-left: 45px;
        height: 45px;
        border-radius: 12px;
        border: 1px solid #ccc;
        font-size: 16px;
        background: rgba(255, 255, 255, 0.55);
        backdrop-filter: blur(12px);
        -webkit-backdrop-filter: blur(12px);
    }

    .form-control:focus {
        border: 1px solid #0066ff;
        background: rgba(255, 255, 255, 0.75);
        box-shadow: 0 0 8px rgba(0, 110, 255, 0.3);
    }

    .icon-input {
        position: absolute;
        left: 15px;
        top: 11px;
        font-size: 20px;
        color: #324b81;
    }

    .avatar-preview {
        width: 120px;
        height: 120px;
        object-fit: cover;
        border-radius: 50%;
        margin: 10px auto 25px;
        display: block;
        border: 4px solid rgba(255,255,255,0.7);
        box-shadow: 0 10px 25px rgba(0,0,0,0.25);
    }

    .btn-save {
        width: 100%;
        height: 50px;
        border-radius: 12px;
        background: #005BBB;
        color: white;
        font-size: 18px;
        font-weight: 600;
        border: none;
        transition: .25s ease;
    }

    .btn-save:hover {
        background: #004099;
        transform: scale(1.03);
    }

    .btn-cancel {
        width: 100%;
        height: 50px;
        margin-top: 10px;
        border-radius: 12px;
        border: 1px solid #324b81;
        background: white;
        color: #324b81;
        font-weight: 600;
        transition: .25s ease;
    }

    .btn-cancel:hover {
        background: #e4eeff;
        transform: scale(1.02);
    }

    .alert {
        border-radius: 12px;
        padding: 10px;
    }

    label {
        font-weight: 600;
        color: #003366;
        margin-bottom: 5px;
    }

    hr {
        border-top: 1px solid #ccd5e0;
        margin: 30px 0;
    }
</style>

</head>

<body>

<div class="edit-card">

    <h3>Edit Profil</h3>

    <?php if ($success): ?>
        <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
    <?php elseif ($error): ?>
        <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
    <?php endif; ?>

    <form method="POST" enctype="multipart/form-data">

        <label>Username</label>
        <div class="position-relative mb-3">
            <i class="bi bi-person icon-input"></i>
            <input type="text" name="username" class="form-control"
                   value="<?= htmlspecialchars($user['username']) ?>" required>
        </div>

        <label>Nama Orang Tua</label>
        <div class="position-relative mb-3">
            <i class="bi bi-people icon-input"></i>
            <input type="text" name="nama_ortu" class="form-control"
                   value="<?= htmlspecialchars($user['nama_ortu']) ?>" required>
        </div>

        <label>Email</label>
        <div class="position-relative mb-3">
            <i class="bi bi-envelope icon-input"></i>
            <input type="email" name="email" class="form-control"
                   value="<?= htmlspecialchars($user['email']) ?>" required>
        </div>

        <label>Foto Profil</label>
        <?php if (!empty($user['foto'])): ?>
            <img src="../uploads/<?= htmlspecialchars($user['foto']) ?>" class="avatar-preview">
        <?php endif; ?>

        <input type="file" name="foto" class="form-control mb-3">

        <hr>

        <h5 class="text-center mb-3" style="font-weight:600;color:#003366;">Ubah Password</h5>

        <label>Password Lama</label>
        <div class="position-relative mb-3">
            <i class="bi bi-lock icon-input"></i>
            <input type="password" name="password_lama" class="form-control" placeholder="••••••••">
        </div>

        <label>Password Baru</label>
        <div class="position-relative mb-3">
            <i class="bi bi-lock-fill icon-input"></i>
            <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru">
        </div>

        <label>Konfirmasi Password</label>
        <div class="position-relative mb-4">
            <i class="bi bi-check2-circle icon-input"></i>
            <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password">
        </div>

        <button type="submit" class="btn-save">Simpan Perubahan</button>

        <a href="index.php" class="btn-cancel d-block text-center">Batal</a>

    </form>
</div>

</body>
</html>


