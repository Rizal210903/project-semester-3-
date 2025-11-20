<?php
// Jalankan session lebih awal
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Cegah output sebelum header() (hilangkan echo/print_r di header.php)
ob_start();

include '../includes/header.php';

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
    <title>Edit Profil - TK Pertiwi</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>
        body { background-color: #E0F6FF; }
        .edit-card {
            background: #fff;
            border-radius: 15px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.1);
            padding: 30px;
            max-width: 600px;
            margin: 50px auto;
        }
        .btn-save {
            background-color: #4682B4;
            color: white;
            border-radius: 8px;
        }
        .btn-save:hover {
            background-color: #315f86;
        }
    </style>
</head>
<body>

<div class="container">
    <div class="edit-card">
        <h4 class="text-center mb-4 text-primary">Edit Profil</h4>

        <?php if ($success): ?>
            <div class="alert alert-success"><?= htmlspecialchars($success) ?></div>
        <?php elseif ($error): ?>
            <div class="alert alert-danger"><?= htmlspecialchars($error) ?></div>
        <?php endif; ?>

        <form method="POST" enctype="multipart/form-data">
            <div class="mb-3">
                <label class="form-label">Username</label>
                <input type="text" name="username" class="form-control" value="<?= htmlspecialchars($user['username']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Nama Orang Tua</label>
                <input type="text" name="nama_ortu" class="form-control" value="<?= htmlspecialchars($user['nama_ortu']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Email</label>
                <input type="email" name="email" class="form-control" value="<?= htmlspecialchars($user['email']) ?>" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Foto Profil</label><br>
                <?php if (!empty($user['foto'])): ?>
                    <img src="../uploads/<?= htmlspecialchars($user['foto']) ?>" alt="Foto Profil" width="100" class="rounded-circle mb-2">
                <?php endif; ?>
                <input type="file" name="foto" class="form-control">
            </div>

            <hr class="my-4">

            <h5 class="text-primary mb-3">Ubah Password</h5>
            <div class="mb-3">
                <label class="form-label">Password Lama</label>
                <input type="password" name="password_lama" class="form-control" placeholder="Masukkan password lama">
            </div>

            <div class="mb-3">
                <label class="form-label">Password Baru</label>
                <input type="password" name="password_baru" class="form-control" placeholder="Masukkan password baru">
            </div>

            <div class="mb-3">
                <label class="form-label">Konfirmasi Password Baru</label>
                <input type="password" name="konfirmasi_password" class="form-control" placeholder="Ulangi password baru">
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-save px-4">Simpan</button>
                <a href="profil_saya.php" class="btn btn-secondary px-4">Batal</a>
            </div>
        </form>
    </div>
</div>

</body>
</html>
